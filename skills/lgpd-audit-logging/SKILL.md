---
name: lgpd-audit-logging
description: Design and implement immutable, hash-chained audit logging for accountability (LGPD Art. 6, X) and incident registration (Res. 15/2024 Art. 10, 5-year retention). Use when user asks 'audit log', 'log de auditoria', 'logging LGPD', 'accountability log', or as part of audit pipeline. Outputs schema + middleware patterns for Laravel/PHP.
---

# Audit Logging

Log imutável encadeado para prova de conformidade (Art. 6º, X — responsabilização) e registro de incidentes (Art. 10 Res. 15/2024 — 5 anos).

## Princípios

1. **Append-only** — sem updates nem deletes
2. **Hash chain** — cada entry referencia o hash da anterior (tamper-evident)
3. **Minimização** — log evento e hash do payload, NÃO o payload em claro
4. **PII out** — nunca logar PII completa (mascarar)
5. **Retenção** — mínimo 5 anos para eventos relacionados a incidentes; 6m+ para eventos correntes

## Eventos a logar

- Autenticação (login, logout, MFA, falhas)
- Acesso a dados pessoais (especialmente sensíveis)
- Mudanças em consentimento (grant, revoke)
- DSAR (recebimento, fulfillment, rejeição)
- Mudanças de configuração de privacidade
- Acesso administrativo a dados de outros usuários
- Exports e downloads em massa
- Mudanças em RBAC

## Migration + Model

```php
Schema::create('audit_log_entries', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->unsignedBigInteger('actor_id')->nullable();   // null se sistema
    $table->string('actor_type');                          // USER | ADMIN | SYSTEM | OPERATOR
    $table->string('action');                              // "READ", "WRITE", "DELETE", "EXPORT", "DSAR_FULFILLED", "CONSENT_REVOKED"
    $table->string('resource');                            // "User:123", "Order:456"
    $table->unsignedBigInteger('subject_id')->nullable();  // titular afetado
    $table->string('legal_basis')->nullable();
    $table->string('purpose')->nullable();
    $table->ipAddress('ip_address')->nullable();
    $table->text('user_agent')->nullable();
    $table->string('payload_hash');                        // SHA-256 do payload (sem PII)
    $table->string('prev_hash')->nullable();               // hash do registro anterior
    $table->string('self_hash');                           // SHA-256(prevHash|timestamp|action|resource|payloadHash)
    $table->timestamp('timestamp');

    $table->index(['subject_id', 'timestamp']);
    $table->index(['actor_id', 'timestamp']);
    $table->index(['action', 'timestamp']);
});
```

```php
// app/Models/AuditLogEntry.php — sem updated_at, sem soft deletes (append-only)
class AuditLogEntry extends Model
{
    public $timestamps = false;
    protected $guarded = [];
}
```

## Service + middleware Laravel

```php
// app/Services/AuditLogger.php
final class AuditLogger
{
    public static function log(array $entry, ?Request $request = null): AuditLogEntry
    {
        $lastHash = AuditLogEntry::latest('timestamp')->value('self_hash');

        $payloadHash = hash('sha256', json_encode($entry['payload'] ?? []));
        $timestamp = now();

        $selfHash = hash('sha256', implode('|', [
            $lastHash ?? '',
            $timestamp->toIso8601String(),
            $entry['action'],
            $entry['resource'],
            $payloadHash,
        ]));

        return AuditLogEntry::create([
            'actor_id' => $entry['actor_id'] ?? null,
            'actor_type' => $entry['actor_type'],
            'action' => $entry['action'],
            'resource' => $entry['resource'],
            'subject_id' => $entry['subject_id'] ?? null,
            'legal_basis' => $entry['legal_basis'] ?? null,
            'purpose' => $entry['purpose'] ?? null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'payload_hash' => $payloadHash,
            'prev_hash' => $lastHash,
            'self_hash' => $selfHash,
            'timestamp' => $timestamp,
        ]);
    }
}
```

Para acesso a dados sensíveis, um middleware dedicado (`audit.sensitive`) ou
observers Eloquent (`retrieved` em models sensíveis) cobrem os eventos de leitura.

## Verificação de integridade

Console command agendado (semanal) que percorre todas as entries em ordem e
verifica que cada `self_hash` bate com `prev_hash` + campos. Qualquer
divergência = tampering ou bug.

## Status update

```markdown
## F6 — Audit logging ✓
- Schema implementado (migration audit_log_entries)
- AuditLogger service + middleware ativos em rotas críticas
- Verificação de integridade agendada (semanal)
- Retenção configurada (5 anos para eventos críticos)
- Próximo: lgpd-encryption-keys
```
