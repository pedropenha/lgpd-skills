---
name: lbi-education-a11y
description: Brazilian accessibility law for educational platforms and content — LBI (Lei 13.146/2015, Art. 63), Decreto 5.296/2004, eMAG for public-sector contracts, ABNT NBR 17225, plus WCAG 2.2 AA as the technical baseline. Use when user mentions 'acessibilidade', 'LBI', 'eMAG', 'contrato escola pública acessibilidade', 'PNLD acessível', 'LIBRAS', 'audiodescrição', or building/auditing edtech content and platforms sold to Brazilian schools (public or private). For WCAG implementation details defer to the `accessibility`/`frontend-a11y` skills; this skill encodes the Brazilian legal layer.
---

# Acessibilidade Legal BR — Educação Digital

Camada jurídica brasileira sobre acessibilidade digital em plataformas e conteúdo educacional. A implementação técnica é WCAG — use `accessibility`/`frontend-a11y` para o "como"; esta skill diz **o que a lei exige** e em quais contratos isso é condição.

## Base legal

| Norma | O que exige |
|-------|-------------|
| **LBI — Lei 13.146/2015** (Estatuto da Pessoa com Deficiência) | Art. 63: acessibilidade obrigatória em sistemas/tecnologias da informação para pessoas com deficiência. Art. 18: educação inclusiva como dever do Estado e das instituições. Aplica-se a empresas privadas que ofertam ao público |
| **Decreto 5.296/2004** | Acessibilidade em portais e sítios de administração pública e de **empresas com contratos/convênios com o poder público** — escolas e secretarias públicas puxam isso para fornecedores |
| **eMAG** (Modelo de Acessibilidade em Governo Eletrônico) | Referência técnica exigida em editais/contratos B2G — mapeia para WCAG |
| **ABNT NBR 17225** | Norma brasileira de acessibilidade para conteúdo digital (referencia WCAG) |
| **Editais PNLD** | Materiais didáticos digitais exigem formatos acessíveis (EPUB3 acessível, audiodescrição, LIBRAS) |

## Onde pesa na prática (edtech)

1. **Vender pra escola/rede pública ou secretaria** → edital quase sempre exige WCAG AA + eMAG; sem conformidade, desclassificação.
2. **PNLD/material didático digital** → formatos acessíveis são critério de aprovação.
3. **Plataforma SaaS para qualquer escola** → LBI Art. 63 se aplica por si só (oferta ao público); discriminação por inacessibilidade é ilícito civil.
4. **Conteúdo gerado por professores (UGC)** → a plataforma deve dar os **meios** de acessibilidade (campos de alt text, captioning) e orientar os autores.

## Requisitos técnicos por camada

### Plataforma (web Nuxt + app Flutter)
- WCAG 2.2 **AA** como baseline (critérios eMAG mapeiam para AA).
- Navegação completa por teclado; foco visível; roles/labels semânticos.
- Contraste ≥ 4.5:1 texto, ≥ 3:1 componentes; alvo de toque ≥ 24px.
- Leitores de tela: TalkBack/VoiceOver (Flutter — `Semantics`), NVDA/JAWS (web).
- Sem flashes > 3/s (risco de convulsão); nada essencial só por cor.
- Forms: erro identificado, label associado, sugestão de correção.

### Conteúdo educacional
- **Vídeo**: closed captions obrigatórias; audiodescrição quando o visual carrega informação; janela/trilha LIBRAS para conteúdo PNLD/público.
- **Imagens/figuras**: alt text — incluir campo obrigatório no editor de aulas.
- **Documentos**: PDF/UA ou EPUB3 acessível (não PDF escaneado); HTML acessível preferível.
- **Interações/quizzes**: operáveis por teclado, sem dependência de arrastar-e-soltar exclusiva (oferecer alternativa).
- **Linguagem**: camada de linguagem simples ajuda deficiência intelectual (boa prática, não exigência formal).

### Exportações
- Pacotes SCORM: HTML interno deve manter acessibilidade fora da plataforma (o player do LMS não resgata acessibilidade do conteúdo).
- Relatórios/dashboards: tabelas com headers semânticos + alternativa textual pra gráficos.

## Workflow de implementação

1. Auditoria WCAG 2.2 AA na plataforma (ferramenta `accessibility` skill)
2. Campos de acessibilidade no editor de conteúdo (alt text obrigatório, upload de captions/VTT, flag de audiodescrição)
3. Checklist eMAG para propostas B2G
4. Documento de conformidade para editais (`.lgpd/` não — sugiro `docs/a11y/declaration-of-conformance.md`)
5. Suporte a formatos acessíveis nas exportações (EPUB3/PDF-UA/SCORM acessível)

## Casca de evidência para edital

Mantenha pronto: declaração de conformidade WCAG AA/eMAG, relatório de auditoria, política de acessibilidade publicada, canal de feedback de acessibilidade.

## Status update

```markdown
## A11y Legal ✓
- Auditoria WCAG 2.2 AA: {data} — {score}
- eMAG checklist (B2G): {conforme/pendências}
- Editor de conteúdo: alt text {obrigatório?}, captions {sim/não}
- Declaração de conformidade: {versão}
- Pendências: {lista}
```
