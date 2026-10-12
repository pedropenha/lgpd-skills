---
name: lgpd-encryption-keys
description: Configure encryption at rest, in transit, and key management aligned with LGPD Art. 46 (security duty) and the ANPD's Guia de Segurança da Informação para ATPP. Use when user asks 'criptografia LGPD', 'KMS', 'TDE', 'encryption at rest', 'rotação de chaves', 'pgcrypto', 'column-level encryption'. Tailored for Laravel/PHP + Flutter/Nuxt stack.
---

# Encryption & Key Management

Art. 46 LGPD + Guia de Segurança da Informação para ATPP (ANPD, 2021).

## Camadas

### 1. Em trânsito
- **TLS 1.3** em toda comunicação (app/web ↔ API Laravel, serviço ↔ serviço)
- **HSTS** habilitado (`max-age=31536000; includeSubDomains; preload`)
- **Certificate pinning** no app Flutter (`dio` + `CertificatePinningInterceptor`, ou `http_certificate_pinning`) — não aplicável a web (Nuxt usa TLS do browser)
- **mTLS** para comunicação entre serviços internos quando viável

### 2. Em repouso
- **TDE (Transparent Data Encryption)** no banco — habilitado pelo provider (AWS RDS, GCP Cloud SQL) para Postgres/MySQL
- **Volume encryption** (EBS, persistent disks) — geralmente padrão hoje
- **Backups criptografados** — mesma chave do volume, ou KMS dedicado
- **Storage de arquivos** (S3, GCS): server-side encryption (SSE-KMS preferível a SSE-S3)

### 3. Nível de aplicação (campos sensíveis)
Para CPF, biometria, dados de saúde, tokens de auth — criptografia adicional em coluna:

```php
// app/Models/User.php — cast 'encrypted' usa Crypt (AES-256-GCM, chave APP_KEY)
protected function casts(): array
{
    return [
        'cpf' => 'encrypted',
        'cpf_index' => 'hashed', // blind index determinístico p/ lookup
    ];
}
```

Para criptografia manual ou chave própria (não a `APP_KEY`):

```php
// app/Services/FieldEncryption.php
final class FieldEncryption
{
    public static function encrypt(string $plain, string $key): string
    {
        $iv = random_bytes(12); // 96-bit IV para GCM
        $tag = '';
        $enc = openssl_encrypt($plain, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        return base64_encode($iv.$tag.$enc);
    }

    public static function decrypt(string $encoded, string $key): string
    {
        $buf = base64_decode($encoded);
        $iv = substr($buf, 0, 12);
        $tag = substr($buf, 12, 16);
        $enc = substr($buf, 28);
        return openssl_decrypt($enc, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    }
}
```

**Atenção:** cast `encrypted` é não-determinístico — não dá para fazer `where('cpf', ...)`. Para busca, mantenha um **blind index** (HMAC-SHA-256 do valor em coluna separada, ex: `cpf_index`) e consulte por ele.

## KMS

Use KMS gerenciado (AWS KMS, GCP KMS, Azure Key Vault) ou HashiCorp Vault para chaves de aplicação.

- **Chaves separadas por finalidade** (PII identidade vs. PII financeiro vs. PII saúde)
- **Rotação anual** no mínimo (data key automática + master key manual)
- **Acesso por IAM com least privilege**
- **Log de uso da chave** (CloudTrail / equivalente)
- **Backup das chaves** (KMS já faz, mas atestar)

Em Laravel, injete a chave do KMS via `.env`/secret manager — nunca hardcode nem commite `APP_KEY` ou data keys.

## Crypto-shredding

Para suportar Art. 18, VI (eliminação):
- Cada titular tem **uma chave de envelope** (encrypted by master key)
- Dados sensíveis cifrados com a chave do titular
- Eliminação = **deletar a chave do titular** no KMS
- Dado em repouso torna-se irrecuperável sem necessidade de varrer 100% do storage

Útil quando há retenção legal de outros campos mas o restante deve ser eliminável.

## Laravel + senhas

Em `config/hashing.php`:
- `bcrypt` com `'rounds' => 12` ou mais (default do Laravel)
- OU driver `argon` — Argon2id com `'memory' => 65536` (64MB), `'threads' => 2`, `'time' => 4`

## Status update

```markdown
## F7 — Encryption ✓
- TLS 1.3 + HSTS confirmados
- TDE habilitado no banco
- KMS configurado com 3 chaves (identidade, financeiro, saúde)
- Casts 'encrypted' + blind indexes nos campos críticos
- Crypto-shredding implementado para campos elimináveis
- Rotação anual em calendário
- Próximo: lgpd-retention-erasure
```
