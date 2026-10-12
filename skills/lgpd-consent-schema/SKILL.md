---
name: lgpd-consent-schema
description: Design the database schema for a versioned, append-only consent ledger that satisfies LGPD Art. 8 (free, informed, unambiguous consent for a specific purpose) and Art. 9 (information rights). Use when user asks for "consent schema", "consent ledger", "schema de consentimento", "Eloquent consentimento", "consent management", or is integrating consent capture with Laravel, Sanctum, JWT, or any auth library. Outputs a Laravel migration + integration spec. Also produces the structure needed to support DSAR revocation (Art. 18, IX).
---

# Consent Schema (Laravel + Eloquent)

Implementação técnica do consentimento como base legal (Art. 7º, I e Art. 11, I), versionado, auditável e revogável.

## Princípios

1. **Append-only** — nunca update destrutivo. Revogar = inserir novo registro com `status=REVOKED`.
2. **Versionado pela política** — todo consentimento aponta para uma versão específica do texto consentido.
3. **Específico por finalidade** — uma entrada por (titular, finalidade, versão da política).
4. **Evidência forte** — hash do payload (timestamp, IP, user-agent, UI snapshot).
5. **Revogação fácil** — Art. 8º, § 5º: "O consentimento pode ser revogado a qualquer momento mediante manifestação expressa do titular, por procedimento gratuito e facilitado."

## Migration Laravel de referência

Ver `assets/consent-migration.php` — cria `policy_versions`, `consent_records`, `guardians` e `minor_links` (ECA Digital).

## Integração com Laravel

Campos de usuário para ECA Digital e hooks no fluxo de registro:

```php
// app/Models/User.php
protected function casts(): array
{
    return [
        'date_of_birth' => 'date',        // ECA Digital — verificação de idade
        'parental_consent_ref' => 'string',
    ];
}
```

```php
// app/Http/Controllers/Auth/RegisterController.php (ou Fortify/Jetstream/custom)
public function store(RegisterRequest $request)
{
    // 1. Validar idade (ECA Digital se aplicável — ver lgpd-eca-digital-minors)
    // 2. NÃO criar registro de consentimento aqui — fazer em POST /api/consent/grant
    //    porque consentimento exige UX dedicada (não pode ser "enterrado" no signup)
    return User::create($request->validated());
}
```

## Captura de consentimento na UI

Princípios:
- **Não pré-marcar** caixas (Art. 8º, § 4º — manifestação livre).
- **Granular por finalidade** — uma caixa por finalidade, não "concordo com tudo".
- **Linguagem clara** — leiga, sem juridiquês.
- **Link para política** — versão exata do texto consentido.
- **Idade** — se < 18, fluxo ECA Digital (ver `lgpd-eca-digital-minors`).

Exemplo de payload do endpoint `POST /api/consent/grant`:

```json
{
  "userId": 123,
  "policyVersionId": "01JXXX...",
  "purposes": [
    { "purpose": "marketing_email", "granted": true },
    { "purpose": "personalization", "granted": false },
    { "purpose": "analytics", "granted": true }
  ],
  "evidence": {
    "ip": "192.0.2.1",
    "userAgent": "...",
    "locale": "pt-BR",
    "channel": "web",
    "uiSnapshotHash": "sha256:..."
  }
}
```

No Flutter e no Nuxt, colete `channel` como `MOBILE_FLUTTER` ou `WEB` e envie o
mesmo payload — o ledger é agnóstico ao cliente.

## Revogação

Endpoint `POST /api/consent/revoke`:

```php
function revokeConsent(int $userId, string $purpose, Request $request): void
{
    // 1. Cria registro append-only com status=REVOKED, revoked_at=now
    ConsentRecord::create([
        'subject_id' => $userId,
        'purpose' => $purpose,
        'status' => 'REVOKED',
        'granted_at' => now(),          // marker — não é o grant original
        'revoked_at' => now(),
        'policy_version_id' => currentPolicyVersionId(),
        'legal_basis' => 'CONSENT',
        'channel' => 'API',
        'evidence_hash' => hashEvidence($request),
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

    // 2. Propaga para operadores (Datadog, Mixpanel, etc.) — via queue
    PropagateConsentRevocation::dispatch($userId, $purpose);

    // 3. Bloqueia tratamentos futuros (flag consultada pelos serviços)
}
```

## Helper de query

```php
// app/Services/ConsentService.php
final class ConsentService
{
    public function hasActiveConsent(int $subjectId, string $purpose): bool
    {
        $latest = ConsentRecord::where('subject_id', $subjectId)
            ->where('purpose', $purpose)
            ->latest('granted_at')
            ->first();

        return $latest?->status === 'GRANTED';
    }
}
```

**Sempre use este helper antes de tratar dados em finalidade baseada em consentimento.**

## Versionamento de política

Toda mudança material no texto da política de privacidade = nova `policy_versions` row. Consentimentos antigos **não migram automaticamente** — você precisa re-coletar para a nova versão se a mudança afetar finalidades existentes (Art. 8º, § 6º).

## Audit log

Cada operação (grant, revoke, propagate) deve gerar entrada em `audit_log_entries` (skill `lgpd-audit-logging`).

## Status update

```markdown
## F3 — Consent schema ✓
- Schema implementado em `database/migrations/*_create_consent_tables.php`
- Endpoints: POST /api/consent/grant, POST /api/consent/revoke, GET /api/consent
- ConsentService + observer de grant/revoke configurados
- Propagação para operadores: {lista}
- Próximo: lgpd-privacy-policy
```
