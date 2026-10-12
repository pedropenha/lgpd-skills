---
name: marco-civil-platform
description: Implement obligations of an application provider under Marco Civil da Internet (Lei 12.965/2014 + Decreto 8.771/2016) — 6-month application access logs, judicial-order-only record disclosure, intermediary liability for UGC (Art. 19), notice-and-takedown for intimate content (Art. 21), terms-of-use requirements. Use when user mentions 'Marco Civil', 'takedown', 'conteúdo de terceiros', 'UGC', 'logs de acesso', 'responsabilidade de plataforma', or when the app hosts user-generated content or needs a takedown/legal-request workflow. Complements lgpd-* skills (which cover LGPD/ECA Digital; this covers the intermediary-liability layer).
---

# Marco Civil — Provedor de Aplicação

Lei 12.965/2014 + Decreto 8.771/2016. Aplicável a **provedores de aplicações** (não de conexão): plataformas SaaS, apps, sites com usuários no Brasil — especialmente com **conteúdo gerado por usuários (UGC)**.

> Complementa `lgpd-*`: a LGPD regula o tratamento de dados; o Marco Civil regula a **responsabilidade intermediária** e a **guarda/sigilo de registros**.

## Quando aplica

1. A aplicação é oferecida ao público no Brasil? → sim, aplica.
2. Usuários publicam conteúdo (aulas, posts, arquivos, comentários)? → Art. 19/21 são centrais.
3. Guarda logs de acesso à aplicação? → Art. 15 + Decreto 8.771.

## Obrigações principais

### 1. Guarda de registros de acesso a aplicações (Art. 15 + Dec. 8.771, Art. 13)

- **Prazo: 6 meses**, sob sigilo, em ambiente seguro e controlado.
- O que registrar por acesso: **data/hora, IP do terminal, identificador da aplicação, portas lógicas** (Dec. 8.771, Art. 13).
- Aplicação: tabela `access_logs` ou serviço de log dedicado — append-only, retenção automática aos 6 meses (integrar com `lgpd-audit-logging` + `lgpd-retention-erasure`, que já encoda esse prazo).
- Logs de **conexão** (1 ano, Art. 13) são do provedor de conexão/hospedagem — para a aplicação vale o de 6 meses.

### 2. Sigilo e acesso aos registros (Arts. 10, 22)

- Registros de acesso a aplicações: só fornecer **mediante ordem judicial** (Art. 22 — o juiz pode autorizar acesso parcial aos dados necessários à instrução processual).
- **Nunca** entregar logs a polícia, parte adversa ou o próprio titular sem ordem judicial — exceção: dados cadastrais (nome, filiação, endereço) podem ser requeridos por autoridade policial/administrativa **sem** ordem judicial (Art. 10, §3º; Dec. 8.771 Art. 10).
- Workflow: todo pedido externo → registrar em `.lgpd/legal-requests/log.md` → classificar (dado cadastral / registro de acesso / conteúdo) → exigir ordem judicial quando aplicável → escalar ao jurídico.

### 3. Responsabilidade por UGC (Art. 19)

- Regra: provedor **não responde** por conteúdo de terceiros a priori — não há dever geral de monitoramento (Art. 18, II; Súmula? não — é a lei).
- Passa a responder se **não cumprir ordem judicial específica** de remoção, no prazo e escopo determinados.
- **Implementação**: processo de takedown — canal de denúncia → triagem → se conteúdo ilícito "à primeira vista" não é claro, aguardar ordem judicial → ao receber ordem: remoção no prazo exato, escopo exato (não mais, não menos), documentar cumprimento.
- **ECA Digital sobrepõe**: para crimes contra crianças/adolescentes, a Lei 15.211/2025 exige remoção imediata + comunicação a autoridades, **sem** ordem judicial — ver `lgpd-eca-digital-minors`. Em conflito, ECA vence.

### 4. Conteúdo íntimo não consentido (Art. 21)

- Exceção à ordem judicial: mediante **notificação do titular** (ou representante) com dados suficientes para identificar o material, o provedor deve **remover diligentemente**.
- Implementar: formulário de denúncia dedicado, SLA interno (24-48h), verificação de identidade do notificante, registro do atendimento.

### 5. Termos de uso (Art. 7º, VIII)

- Contratos devem ser **claros e completos**: âmbito da licença do conteúdo do usuário, responsabilidades, regras de moderação e takedown, condições de suspensão de conta.
- Para UGC educacional (ex: aulas criadas por professores): termos devem cobrir licença de uso do conteúdo, titularidade e remoção.

## Workflow de implementação

1. `access_logs` com campos Dec. 8.771 + expurgo automático 6 meses
2. Endpoint/canal de denúncia UGC + formulário Art. 21 separado
3. Runbook de pedidos legais (`.lgpd/legal-requests/`)
4. Revisão de termos de uso (clareza, licença UGC, moderação)
5. Integração ECA Digital se menores
6. Documentar em `.lgpd/marco-civil.md`

## Sanções

Art. 12: advertência, multa até **10% do faturamento do grupo econômico no Brasil**, suspensão temporária ou proibição de atividades — apuradas conforme natureza/gravidade, arrecadação ao FICLIST (Fundeb).

## Status update

```markdown
## Marco Civil ✓
- Logs de acesso (6m): {implementado/pendente}
- Canal takedown UGC + formulário Art. 21: {status}
- Runbook pedidos legais: {status}
- Termos de uso revisados: {data}
- Integração ECA: {sim/n/a}
```
