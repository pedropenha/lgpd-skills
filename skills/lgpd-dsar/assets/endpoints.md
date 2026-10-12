# DSAR Endpoints — Laravel + Flutter/Nuxt

Rotas (routes/api.php, grupo `auth:sanctum` ou middleware equivalente):

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [DsarController::class, 'show']);
    Route::get('/me/export', [DsarController::class, 'export']);
    Route::patch('/me/profile', [DsarController::class, 'updateProfile']);
    Route::post('/me/erasure', [DsarController::class, 'erasure']);
    Route::post('/consent/revoke', [ConsentController::class, 'revoke']);
});
```

## GET /api/me

Resposta imediata em formato simplificado (Art. 19, I).

```php
// app/Http/Controllers/DsarController.php
public function show(Request $request)
{
    $user = $request->user();

    $consents = ConsentRecord::where('subject_id', $user->id)
        ->where('status', 'GRANTED')
        ->get(['purpose', 'granted_at']);

    $activeProcessing = ProcessingActivity::forSubject($user->id)
        ->get(['name', 'purpose', 'legal_basis']);

    AuditLogger::log([
        'actor_id' => $user->id, 'actor_type' => 'USER',
        'action' => 'DSAR_RECEIVED', 'resource' => "User:{$user->id}",
        'purpose' => 'confirmation_simplified',
    ], $request);

    return response()->json([
        'confirmation' => 'Yes, we process your personal data.',
        'activities' => $activeProcessing,
        'consents' => $consents,
        'encarregado' => [
            'name' => config('lgpd.dpo_name'),
            'email' => config('lgpd.dpo_email'),
        ],
        'yourRightsUrl' => '/privacy/your-rights',
    ]);
}
```

## GET /api/me/export

Acesso completo + portabilidade. Job assíncrono (Laravel queue) se o volume for grande.

```php
public function export(Request $request)
{
    $format = $request->query('format', 'json'); // json | csv | portable

    // Volume pequeno: sync. Volume grande: BuildUserDataBundle::dispatch()
    // e responder com URL de download em ~5min (Storage::temporaryUrl)
    $bundle = app(BuildUserDataBundle::class)->handle($request->user(), $format);

    AuditLogger::log([
        'actor_id' => $request->user()->id, 'actor_type' => 'USER',
        'action' => 'DSAR_FULFILLED', 'resource' => "User:{$request->user()->id}",
        'purpose' => 'export',
        'payload' => ['bundle_hash' => hash('sha256', $bundle)],
    ], $request);

    return response($bundle, 200, [
        'Content-Type' => 'application/json',
        'Content-Disposition' => 'attachment; filename="my-data-'.now()->timestamp.'.json"',
    ]);
}
```

## PATCH /api/me/profile

Correção (Art. 18, III) — whitelist via Form Request.

```php
// app/Http/Requests/UpdateProfileRequest.php
public function rules(): array
{
    return [
        'full_name' => ['sometimes', 'string', 'max:255'],
        'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
        'address_line1' => ['sometimes', 'nullable', 'string', 'max:255'],
        'city' => ['sometimes', 'nullable', 'string', 'max:100'],
    ];
}

public function updateProfile(UpdateProfileRequest $request)
{
    $user = $request->user();
    $before = $user->toArray();
    $updates = $request->validated(); // só campos da whitelist

    $user->update($updates);

    AuditLogger::log([
        'actor_id' => $user->id, 'actor_type' => 'USER',
        'action' => 'DSAR_FULFILLED', 'resource' => "User:{$user->id}",
        'purpose' => 'correction',
        'payload' => [
            'fields' => array_keys($updates),
            'before_hash' => hash('sha256', json_encode($before)),
            'after_hash' => hash('sha256', json_encode($user->fresh())),
        ],
    ], $request);

    return response()->json(['updated' => array_keys($updates)]);
}
```

## POST /api/me/erasure

Eliminação (Art. 18, VI) com checagem de retenção legal.

```php
public function erasure(Request $request)
{
    $request->validate(['confirmation' => ['required', 'in:EXCLUIR MINHA CONTA']]);
    $user = $request->user();

    // 1. Verificar retenções legais obrigatórias
    $retentions = app(LegalRetentionChecker::class)->for($user);
    // ex: ["fiscal:5y", "trabalhista:5y"]

    if ($retentions->isNotEmpty()) {
        // Bloqueio lógico + anonimização parcial
        $user->update([
            'email' => "anonymized-{$user->id}@deleted.local",
            'full_name' => 'Usuário removido',
            'cpf' => null,              // crypto-shred
            'phone' => null,
            'deleted_at' => now(),
            'retention_until' => $retentions->max('until'),
        ]);
    } else {
        // Hard delete + cascata (foreign keys onDelete cascade + limpeza manual)
        app(HardDeleteUserCascade::class)->handle($user);
    }

    // 2. Propagar a operadores
    PropagateErasure::dispatch($user->id);

    // 3. Revogar todas as sessões/tokens
    $user->tokens()->delete(); // Sanctum

    AuditLogger::log([
        'actor_id' => $user->id, 'actor_type' => 'USER',
        'action' => 'DSAR_FULFILLED', 'resource' => "User:{$user->id}",
        'purpose' => 'erasure',
        'payload' => [
            'type' => $retentions->isNotEmpty() ? 'blocked_then_anonymized' : 'hard_deleted',
            'retentions' => $retentions,
        ],
    ], $request);

    return response()->json([
        'status' => 'ok',
        'type' => $retentions->isNotEmpty() ? 'anonymized' : 'deleted',
        'retentions' => $retentions,
    ]);
}
```

## POST /api/consent/revoke

Revogação de consentimento (Art. 18, IX).

```php
// app/Http/Controllers/ConsentController.php
public function revoke(Request $request)
{
    $request->validate(['purpose' => ['required', 'string']]);
    $user = $request->user();

    // Registro append-only de revogação
    ConsentRecord::create([
        'subject_id' => $user->id,
        'purpose' => $request->purpose,
        'status' => 'REVOKED',
        'granted_at' => now(),   // marker, não o grant original
        'revoked_at' => now(),
        'legal_basis' => 'CONSENT',
        'policy_version_id' => currentPolicyVersionId(),
        'channel' => 'API',
        'evidence_hash' => hashEvidence($request),
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

    PropagateConsentRevocation::dispatch($user->id, $request->purpose);

    AuditLogger::log([
        'actor_id' => $user->id, 'actor_type' => 'USER',
        'action' => 'DSAR_FULFILLED', 'resource' => "User:{$user->id}",
        'purpose' => 'consent_revoke',
        'payload' => ['purpose' => $request->purpose],
    ], $request);

    return response()->json(['status' => 'revoked', 'purpose' => $request->purpose]);
}
```

## Flutter — tela "Meus Dados"

```dart
// lib/features/privacy/privacy_screen.dart
class PrivacyScreen extends StatelessWidget {
  Future<void> _exportData() async {
    final res = await dio.get('/api/me/export');
    // salvar com path_provider + compartilhar com share_plus
  }

  Future<void> _requestErasure(BuildContext context) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Excluir conta'),
        content: const Text(
          'Esta ação é irreversível para os dados não retidos por '
          'obrigação legal. Confira a política. Digite '
          'EXCLUIR MINHA CONTA para confirmar.'),
        // TextField de confirmação + ações
      ),
    );
    if (confirmed == true) {
      await dio.post('/api/me/erasure',
          data: {'confirmation': 'EXCLUIR MINHA CONTA'});
    }
  }

  @override
  Widget build(BuildContext context) {
    return Column(children: [
      ElevatedButton(onPressed: _exportData, child: const Text('Exportar meus dados')),
      ElevatedButton(onPressed: () => context.push('/privacy/consents'),
          child: const Text('Gerenciar consentimentos')),
      ElevatedButton(onPressed: () => context.push('/privacy/profile'),
          child: const Text('Corrigir dados')),
      ElevatedButton(
        onPressed: () => _requestErasure(context),
        style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
        child: const Text('Excluir minha conta'),
      ),
    ]);
  }
}
```

## Nuxt 2 — página "Meus Dados"

Mesma estrutura em `/pages/privacy/index.vue`: chamadas via `$axios`/`fetch`
aos mesmos endpoints, dialog de confirmação com input de texto para
`EXCLUIR MINHA CONTA`, e link para `/privacy/your-rights`.

## Alerts de SLA

No Laravel scheduler (`app/Console/Kernel.php` ou `routes/console.php`):

- Dia 10: alerta amarelo no ticket
- Dia 13: alerta vermelho + escalonamento ao Encarregado
- Dia 15: bloqueio do tratamento se ainda não atendido + comunicação ao titular explicando o atraso (sempre desfavorável — evitar a todo custo)

```php
Schedule::job(CheckDsarSla::class)->daily();
```
