# lgpd-skills

> Conjunto modular e orquestrado de **Agent Skills** — funciona em **Claude Code, Codex, Gemini CLI, Cursor e OpenCode** — que cobre, ponta-a-ponta, conformidade com a **Lei nº 13.709/2018 (LGPD)**, resoluções da ANPD aplicáveis e a **Lei nº 15.211/2025 (ECA Digital)**.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](./LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](./CONTRIBUTING.md)
[![LGPD](https://img.shields.io/badge/LGPD-Lei%2013.709%2F2018-blue)](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm)
[![ECA Digital](https://img.shields.io/badge/ECA%20Digital-Lei%2015.211%2F2025-blue)](https://www.planalto.gov.br/ccivil_03/_ato2023-2026/2025/lei/l15211.htm)

---

> **EN — Quick overview:** An Agent Skills bundle (works in Claude Code, Codex, Gemini CLI, Cursor, and OpenCode) that runs an end-to-end Brazilian privacy-law compliance audit on your codebase. One orchestrator skill (`lgpd-audit`) chains 18 specialized sub-skills covering legal basis, data mapping, ROPA, DPIA (RIPD), consent ledger, DSAR endpoints, incident response (3 business days notification), encryption, retention, vendor DPAs, international transfer clauses, and ECA Digital (online safety for minors). Outputs versioned artifacts under `.lgpd/`. Triggered by phrases like *"audit our LGPD compliance"* or *"we had a data breach"*. MIT licensed.

---

## 🍴 Sobre este fork (pedropenha/lgpd-skills)

Fork de [`goul4rt/lgpd-skills`](https://github.com/goul4rt/lgpd-skills) (MIT, pin no commit `d85d79a`, v1.2.0) com duas frentes de mudança:

1. **Stack adaptada**: os templates de código saíram de Prisma/Next.js/Better Auth/React Native para **PHP/Laravel + Flutter + Vue/Nuxt 2** — stack dos projetos onde o bundle é usado (edtech).
2. **Cobertura ampliada**: de 19 para **22 skills** — três novas skills criadas para o cenário edtech B2B brasileiro (Marco Civil, DPA escolar, acessibilidade legal).

Detalhes de atribuição e licenças em [`FORK-NOTES.md`](./FORK-NOTES.md). Histórico em [`CHANGELOG.md`](./CHANGELOG.md) (v1.3.0 adaptação de stack, v1.4.0 novas skills).

### Proveniência — o que cada fonte trouxe

| Fonte | Licença | O que trouxe |
|-------|---------|--------------|
| [`goul4rt/lgpd-skills`](https://github.com/goul4rt/lgpd-skills) | MIT | As 19 skills originais: maestro + pipeline completo LGPD/ANPD/ECA Digital, templates ROPA/RIPD/DPA, normative-reference |
| [`evolution-foundation/evo-nexus`](https://github.com/evolution-foundation/evo-nexus) | Apache-2.0 | Base do checklist Marco Civil (logs, responsabilidade de plataforma) que informou `marco-civil-platform` |
| [`AlissonSantos1/brasil-legal-skills`](https://github.com/AlissonSantos1/brasil-legal-skills) | MIT | Referência de conteúdo LGPD/Marco Civil/cookies que informou `marco-civil-platform` |
| [`hamzabellouch/agent-skills`](https://github.com/hamzabellouch/agent-skills) | MIT | `scorm-xapi-interoperability` (instalada separada do bundle — ver [Skills complementares](#skills-complementares)) |

### As 22 skills — o que cada uma cobre

| Skill | Cobertura | Origem |
|-------|-----------|--------|
| `lgpd-audit` | **Maestro**: orquestra auditoria ponta-a-ponta com 4 pipelines (greenfield/legacy/híbrido/incidente), checkpoints humanos e artefatos em `.lgpd/` | upstream |
| `lgpd-anonymization` | Anonimização e pseudonimização (Art. 5, XI; Art. 13, §4) — receitas SQL/Python, k-anonymity, tokenização | upstream |
| `lgpd-audit-logging` | Log imutável hash-chained (Art. 6, X + Res. 15/2024 — 5 anos). **Adaptada**: migration Eloquent + AuditLogger PHP | upstream → Laravel |
| `lgpd-consent-schema` | Ledger de consentimento append-only e versionado (Art. 8). **Adaptada**: migration Laravel completa + Guardian/MinorLink | upstream → Laravel |
| `lgpd-data-mapping` | Inventário de tratamentos (input do ROPA). **Adaptada**: anotações `@lgpd:` em docblocks PHP + script de extração | upstream → PHP |
| `lgpd-dpa` | DPA genérico Art. 39 — 12 cláusulas para contratos com operadores | upstream |
| `lgpd-dpo-encarregado` | Designação do Encarregado/DPO (Art. 41 + Res. 18/2024), isenção ATPP | upstream |
| `lgpd-dsar` | Direitos do titular (Art. 18, 9 direitos, prazo 15 dias). **Adaptada**: controllers Laravel + tela Flutter + notas Nuxt | upstream → Laravel/Flutter |
| `lgpd-eca-digital-minors` | ECA Digital (Lei 15.211/2025): verificação de idade real, vinculação a responsável até 16, ban de profiling/loot boxes, relatórios. **Adaptada**: notas Flutter/Nuxt/PHP | upstream → +stack notes |
| `lgpd-encryption-keys` | Criptografia em trânsito/repouso, KMS, crypto-shredding. **Adaptada**: openssl PHP + casts `encrypted` + blind index | upstream → PHP |
| `lgpd-incident-response` | Runbook de incidente (Art. 48 + Res. 15/2024): 3 dias úteis ANPD, 12 itens, notificação a titulares | upstream |
| `lgpd-international-transfer` | Transferência internacional (Arts. 33–36 + Cláusulas-Padrão Res. 19/2024) | upstream |
| `lgpd-legacy-retrofit` | Gap analysis de sistema legado (Pipeline B) → `.lgpd/gaps.md` | upstream |
| `lgpd-legal-basis` | Escolha de base legal por atividade (Art. 7/11) + LIA para legítimo interesse | upstream |
| `lgpd-privacy-policy` | Política de privacidade versionada (Art. 9, 7 elementos) — checkpoint jurídico | upstream |
| `lgpd-retention-erasure` | Retenção × direito de eliminação (Arts. 15–16, 18 VI) + prazos BR (fiscal, trabalhista, Marco Civil). **Adaptada**: migration + Laravel scheduler | upstream → Laravel |
| `lgpd-ripd` | Relatório de Impacto (Art. 38) para tratamentos de alto risco | upstream |
| `lgpd-ropa` | Registro de Operações de Tratamento (Art. 37) no formato ANPD | upstream |
| `lgpd-vendor-audit` | Due diligence de operadores terceiros (Art. 39) + checklist | upstream |
| **`marco-civil-platform`** | **Nova (fork)**: Marco Civil (Lei 12.965/2014 + Dec. 8.771/2016) — logs de aplicação 6 meses, sigilo via ordem judicial, takedown UGC Art. 19/21, termos de uso | fork, informado por evo-nexus + brasil-legal-skills |
| **`lgpd-school-dpa`** | **Nova (fork)**: DPA escola(controladora)→plataforma(operadora) edtech + template com menores, ECA, SLA, ciclo letivo | fork |
| **`lbi-education-a11y`** | **Nova (fork)**: LBI (Lei 13.146/2015), Decreto 5.296/2004, eMAG p/ editais B2G, NBR 17225, PNLD — camada legal de acessibilidade | fork |

### Skills complementares (instaladas fora do bundle)

| Skill | Fonte | O que cobre |
|-------|-------|-------------|
| `scorm-xapi-interoperability` | [`hamzabellouch/agent-skills`](https://github.com/hamzabellouch/agent-skills) (MIT) | SCORM 1.2/2004, xAPI, CMI5, imsmanifest, API JS↔LMS, LRS — para export de aulas |

### Instalação (este fork)

```bash
# Claude Code
/plugin marketplace add pedropenha/lgpd-skills
/plugin install lgpd-skills@lgpd-skills

# Qualquer agente compatível com Agent Skills (via Vercel skills CLI)
npx skills add pedropenha/lgpd-skills

# Gemini CLI
gemini extensions install https://github.com/pedropenha/lgpd-skills
```

---

## 📑 Índice

- [O que é](#-o-que-é)
- [Quando usar](#-quando-usar)
- [Instalação](#-instalação)
- [Como usar](#-como-usar)
- [As 19 skills](#-as-19-skills)
- [Living artifacts em `.lgpd/`](#-living-artifacts-em-lgpd)
- [Gatilhos](#-gatilhos)
- [Cobertura normativa](#-cobertura-normativa)
- [Stack assumida](#-stack-assumida)
- [Disclaimer](#%EF%B8%8F-disclaimer)
- [Contribuindo](#-contribuindo)
- [Licença](#-licença)

---

## 🎯 O que é

Um pacote de **Agent Skills** — instalável em Claude Code, Codex, Gemini CLI, Cursor e OpenCode — que transforma uma conversa de "preciso adequar isso aqui à LGPD" em um pipeline executável, versionável e auditável.

Em vez de uma skill gigante e monolítica, aqui são **19 skills coordenadas**:

- **1 maestro** (`lgpd-audit`) que orquestra todo o fluxo, decide o pipeline, mantém estado e pausa em checkpoints críticos
- **18 sub-skills** especializadas — cada uma roda sozinha, ou em sequência sob comando do maestro

Cada sub-skill produz **artefatos vivos versionáveis em Git** sob `.lgpd/` (STATUS.md, ROPA.md, RIPD/, política, runbook de incidente, etc.) — combina com fluxo de ADR e spec-driven development.

## 🧭 Quando usar

| Cenário | Pipeline ativado | Tempo estimado |
|---|---|---|
| Projeto greenfield, privacy-by-design desde o início | Pipeline A | 1-2 semanas |
| Sistema legado em produção precisando de auditoria + retrofit | Pipeline B | 2-6 semanas |
| Codebase híbrida (legado + features novas) | Pipeline C | 3-8 semanas |
| Incidente de segurança em andamento — preciso comunicar a ANPD | Pipeline D | **3 dias úteis** (Res. 15/2024) |

Nenhum desses tempos é cravado — depende do tamanho do projeto, da equipe disponível, e do nível de maturidade atual.

## 📦 Instalação

As skills seguem o padrão aberto **Agent Skills** ([agentskills.io](https://agentskills.io))
e funcionam nativamente em 5 agentes. Escolha o seu:

### Claude Code (recomendado)

```text
/plugin marketplace add pedropenha/lgpd-skills
/plugin install lgpd-skills@lgpd-skills
```

Atualizar: `/plugin marketplace update lgpd-skills`.

### Codex (CLI / App)

```bash
codex plugin marketplace add pedropenha/lgpd-skills
codex plugin add lgpd-skills@lgpd-skills
```

Ou, dentro do `codex`, rode `/plugins` e instale pela vitrine. Não é preciso submeter
nada ao marketplace da OpenAI — o repo é a própria fonte.

### Gemini CLI

```bash
gemini extensions install https://github.com/pedropenha/lgpd-skills
```

Atualizar: `gemini extensions update lgpd-skills`. As 19 skills são auto-descobertas e
disparam via `activate_skill`.

### Cursor

No editor, rode `/add-plugin` e cole a URL do repo
(`https://github.com/pedropenha/lgpd-skills`), ou instale pela vitrine em
[cursor.com/marketplace](https://cursor.com/marketplace).

### OpenCode

Adicione ao array `plugin` do seu `opencode.json` e reinicie:

```json
{
  "plugin": ["lgpd-skills@git+https://github.com/pedropenha/lgpd-skills.git"]
}
```

Detalhes e ressalvas em [`.opencode/INSTALL.md`](./.opencode/INSTALL.md). **Atenção:** no
OpenCode as skills não disparam sozinhas — ative com a ferramenta `skill` (ex.: "use a
skill lgpd-audit").

### Manual (qualquer agente que leia `~/.claude/skills/` ou `.claude/skills/`)

```bash
git clone https://github.com/pedropenha/lgpd-skills.git /tmp/lgpd-skills
cp -r /tmp/lgpd-skills/skills/lgpd-* ~/.claude/skills/   # global
# ou, por projeto: cp -r /tmp/lgpd-skills/skills/lgpd-* .claude/skills/
rm -rf /tmp/lgpd-skills
```

## 🚀 Como usar

Após instalar, o agente detecta as skills automaticamente — não precisa configurar nada. (No OpenCode, as skills não disparam sozinhas: ative com a ferramenta `skill`, ex.: "use a skill lgpd-audit".)

### Fluxo típico: auditoria completa

```
Você: veja como estamos de LGPD

Agente: [ativa lgpd-audit]
  → Antes de começar, qual cenário descreve melhor o projeto?
    A) Greenfield  B) Legacy  C) Híbrido  D) Resposta a incidente

Você: B

Agente: [inicia Pipeline B — Legacy Retrofit]
  → Vou começar pelo lgpd-legacy-retrofit para gap analysis...
  → [lê código, schemas, package.json, env, ToS atual]
  → [produz .lgpd/discovery.md + .lgpd/gaps.md]
  → ⏸ CHECKPOINT — revise antes de seguir

Você: (revisa) Continue.

Agente: → lgpd-data-mapping...
  → lgpd-legal-basis...
  → (continua até a conclusão do pipeline)
```

### Uso individual

Pode invocar qualquer skill diretamente:

```
Você: implementa o endpoint de exclusão de conta

Agente: [ativa lgpd-dsar e gera código + schema + audit log]
```

```
Você: tivemos um vazamento de e-mails, preciso comunicar a ANPD

Agente: [ativa lgpd-incident-response em modo emergencial]
  → Vou guiar pelo runbook da Res. 15/2024. T+0...
```

## 🗂️ As 19 skills

```
lgpd-audit (maestro)
│
├── lgpd-legal-basis           Base legal por atividade (Art. 7/11) + LIA
├── lgpd-data-mapping          Inventário (reverse-eng em legado, schema annotations em greenfield)
├── lgpd-ropa                  Registro de Operações (Art. 37) — modelo ANPD ATPP
├── lgpd-ripd                  Relatório de Impacto (Art. 38) — metodologia ISO 31000
├── lgpd-consent-schema        Prisma ledger append-only + Better Auth + Guardian/MinorLink
├── lgpd-dsar                  Direitos do titular (Art. 18) — SLA 15 dias hard-coded
├── lgpd-privacy-policy        Política Art. 9 — 7 elementos + versionamento + CHECKPOINT
├── lgpd-incident-response     Res. 15/2024 — 3 dias úteis ANPD + titular, runbook + tabletop
├── lgpd-dpo-encarregado       Designação Res. 18/2024 — divulgação + conflito de interesse
├── lgpd-dpa                   12 cláusulas mínimas para contrato com operador
├── lgpd-international-transfer Res. 19/2024 — Cláusulas-Padrão BR
├── lgpd-vendor-audit          Due diligence em operadores + tiering por risco
├── lgpd-audit-logging         Log imutável encadeado (hash chain) — accountability
├── lgpd-encryption-keys       TLS, TDE, KMS, crypto-shredding, Argon2id
├── lgpd-anonymization         k-anonymity, pseudonimização, vault de tokens
├── lgpd-retention-erasure     Hierarquia retenção legal × eliminação (Art. 18, VI)
├── lgpd-eca-digital-minors    Lei 15.211/2025 — verificação de idade, vinculação ao responsável
└── lgpd-legacy-retrofit       Discovery + gap analysis + plano priorizado (entrada Pipeline B)
```

## 📂 Living artifacts em `.lgpd/`

Tudo que as skills produzem fica versionado:

```
.lgpd/
├── STATUS.md              ← estado da auditoria + próximos passos
├── data-map.md            ← inventário de tratamentos
├── legal-basis.md         ← base legal por atividade
├── ROPA.md                ← registro consolidado (Art. 37)
├── RIPD/                  ← relatórios de impacto (Art. 38)
│   └── ripd-{slug}.md
├── consent/               ← schema do ledger
├── dsar/                  ← workflow + endpoints
├── incidents/             ← runbook + log 5 anos (Art. 10 Res. 15/2024)
│   └── log.md
├── policies/              ← política versionada
│   └── privacy-policy-v{N}.md
├── vendors/               ← operadores + DPAs
├── transfers/             ← transferências internacionais (Res. 19/2024)
├── encarregado.md         ← designação + publicação
├── eca-digital.md         ← se aplicável
└── gaps.md                ← não-conformidades priorizadas
```

Versione em Git, revise em PR, audite em retrospectiva.

## 🔑 Gatilhos

O agente detecta automaticamente — você não precisa decorar nada. Exemplos que ativam:

| Frase | Skill |
|---|---|
| "veja como estamos de LGPD" | `lgpd-audit` |
| "nos deixe LGPD-seguros" | `lgpd-audit` |
| "audite LGPD do projeto" | `lgpd-audit` |
| "gap analysis LGPD" | `lgpd-legacy-retrofit` (via maestro) |
| "tivemos um incidente" | `lgpd-incident-response` |
| "precisamos comunicar a ANPD" | `lgpd-incident-response` |
| "preciso de uma RIPD" | `lgpd-ripd` |
| "consent ledger no Prisma" | `lgpd-consent-schema` |
| "endpoint de exclusão de conta" | `lgpd-dsar` |
| "política de privacidade" | `lgpd-privacy-policy` |
| "DPA com a AWS" | `lgpd-dpa` |
| "menores na plataforma" | `lgpd-eca-digital-minors` |

## 📚 Cobertura normativa

- **Lei nº 13.709/2018** (LGPD), com alterações da Lei nº 15.352/2026
- **Resolução CD/ANPD nº 2/2022** — Agentes de Tratamento de Pequeno Porte (ATPP)
- **Resolução CD/ANPD nº 4/2023** — Dosimetria de sanções
- **Resolução CD/ANPD nº 15/2024** — Comunicação de Incidentes de Segurança (3 dias úteis)
- **Resolução CD/ANPD nº 18/2024** — Encarregado pelo Tratamento
- **Resolução CD/ANPD nº 19/2024** — Cláusulas-Padrão Contratuais para Transferência Internacional
- **Resoluções CD/ANPD nº 30 e 31/2025** — Agendas regulatória e de fiscalização
- **Lei nº 15.211/2025** (ECA Digital) — em vigor desde 17/03/2026

Encode rígido das normas em [`skills/lgpd-audit/references/normative-reference.md`](./skills/lgpd-audit/references/normative-reference.md). As skills citam o artigo e a fonte sempre.

## 🛠️ Stack assumida

As skills assumem (sem exigir) uma stack moderna comum:

- **Next.js 14+** com App Router
- **Prisma + PostgreSQL**
- **Better Auth** (autenticação)
- **React Native + Expo** (mobile)
- **AWS / GCP** (cloud)
- **Datadog / Sentry** (observabilidade)
- **Stripe / Pagar.me** (pagamento)

Os exemplos de código vêm nessa stack, mas os princípios e templates funcionam em qualquer stack — só adapte a sintaxe.

## ⚠️ Disclaimer

**Este projeto não constitui aconselhamento jurídico.** As skills codificam boas práticas e o texto das normas conforme encode rígido na referência normativa, mas:

- Decisões sobre comunicação à ANPD devem envolver o Encarregado e jurídico
- Avaliação de fato de incidente notificável é caso a caso
- Cláusulas contratuais negociadas devem ser revisadas por advogado
- Resposta a fiscalização deve ser conduzida por especialista
- Texto da política de privacidade deve passar por revisão jurídica antes de publicar

**Consulte um(a) advogado(a) especializado em proteção de dados** para tomada de decisão jurídica concreta.

As próprias skills lembram disso nos pontos críticos — quando você pedir "isso é legal?" ou "vamos ser multados?", a resposta inclui essa recomendação.

## 🤝 Contribuindo

Issues e PRs muito bem-vindos! Especialmente:

- Correções de normas (ANPD publica resoluções com frequência)
- Templates adicionais (LIA por setor, RIPD para casos específicos)
- Stacks adicionais (Django, Rails, Spring Boot, Laravel)
- Traduções

Ver [CONTRIBUTING.md](./CONTRIBUTING.md) para detalhes.

## 📄 Licença

[MIT](./LICENSE). Use à vontade, inclusive comercialmente. Atribuição é apreciada mas não obrigatória.

---

Construído por [goul4rt](https://github.com/goul4rt).
Feedback, dúvidas e sugestões: abra uma issue.
