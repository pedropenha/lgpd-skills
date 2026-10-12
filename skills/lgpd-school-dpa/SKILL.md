---
name: lgpd-school-dpa
description: Draft or review the school-to-platform Data Processing Agreement (DPA) for edtech — school/rede de ensino as controller, platform as operador (LGPD Art. 39) processing student data including minors. Use when user mentions 'DPA escola', 'contrato operador escola', 'edtech LGPD', 'dados de alunos', 'escola controladora', or onboarding a new school/rede/secretaria de educação as a client. Extends lgpd-dpa with education-specific clauses: minors, ECA Digital, purpose limitation to pedagogy, DSAR routing via school, data return/deletion at contract end.
---

# DPA Escolar — Escola (controladora) → Plataforma (operadora)

Cenário edtech B2B: a **escola/rede/secretaria** é a controladora dos dados dos alunos; a plataforma é **operadora** (Art. 39 LGPD — trata segundo instruções documentadas da controladora). Base: template `lgpd-dpa/assets/dpa-template.md`, mais as cláusulas específicas abaixo.

> Para o papel invertido (plataforma contratando vendors) use `lgpd-dpa` e `lgpd-vendor-audit`.

## Cláusulas específicas de edtech (além das 12 padrão)

### 1. Dados de menores
- Escopo declarado: dados de alunos incluem crianças e adolescentes (Art. 14 LGPD + Lei 15.211/2025).
- **Proibição absoluta**: perfilamento publicitário, ads direcionadas, venda ou monetização de dados de alunos, uso para treinar modelos de IA sem autorização expressa da controladora.
- Consentimento parental quando aplicável é responsabilidade da **controladora** (a plataforma provê os mecanismos — ver `lgpd-eca-digital-minors`).

### 2. Limitação de finalidade
- Tratamento **exclusivamente** para finalidades pedagógicas/gestão escolar definidas no contrato.
- Analytics agregados só com anonimização irreversível (Art. 12 — fora do escopo LGPD).

### 3. Papéis e canalização
- **DSAR**: pedidos de titulares/responsáveis vão à escola; plataforma assiste tecnicamente (export, correção, eliminação) no prazo combinado — titular tem 15 dias (Art. 19, II), então SLA interno ≤ 5 dias úteis.
- **Incidentes**: plataforma notifica a escola em **≤ 48h** (a escola é quem comunica ANPD nos 3 dias úteis — Res. 15/2024).
- **Contato único**: canal nomeado para comunicações de privacidade.

### 4. Sub-operadores
- Lista anexada e versionada (hosting, analytics, email, storage).
- Mudança de sub-operador → aviso prévio (15-30 dias) + direito de objeção da escola.
- Cadeia: sub-operadores ficam sob as mesmas obrigações (Art. 39, §1º).

### 5. Segurança (anexo técnico)
- Criptografia em trânsito e repouso, segregação por escola/tenant, controle de acesso por perfil, logs de auditoria (ver `lgpd-encryption-keys`, `lgpd-audit-logging`).
- Pentest/hardening periódico; evidência anual à controladora.

### 6. Retenção, retorno e eliminação
- **Fim de contrato**: exportação completa dos dados à escola em formato aberto (CSV/JSON) + eliminação certificada em ≤ 90 dias, exceto retenção legal.
- Aluno desligado/transfere → dado sai do acesso do professor antigo.
- Anos letivos: defina a regra de arquivamento por ciclo anual.

### 7. Transferência internacional
- Declarar região de hospedagem; se fora do Brasil, cláusulas Res. 19/2024 (ver `lgpd-international-transfer`).

### 8. Auditoria e RIPD
- Escola pode auditar 1x/ano (questionário + evidências técnicas em vez de auditoria in loco para SaaS multi-tenant).
- Plataforma coopera com RIPD da escola e com o ECA Digital (relatórios, verificação de idade).

## Template

Ver `assets/dpa-school-template.md` — as 12 cláusulas Art. 39 do `lgpd-dpa` + as 8 seções acima já redigidas.

## Checklist de onboarding de escola nova

- [ ] DPA assinado antes de provisionar o tenant
- [ ] Lista de sub-operadores anexada e atual
- [ ] Canal de privacidade da escola registrado
- [ ] Runbook DSAR testado (export + eliminação)
- [ ] Se menores: fluxo ECA/verificação alinhado com a política da escola
- [ ] `.lgpd/vendors/schools/` atualizado com a nova controladora

## Status update

```markdown
## DPA Escolar ✓
- Controladoras com DPA ativo: {N}
- Template usado: dpa-school-v{N}
- Sub-operadores divulgados: {lista}
- Pendências: {lista}
```
