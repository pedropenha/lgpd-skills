# Template de DPA Escolar — Escola/Rede (controladora) → Plataforma (operadora)

> ⚠️ **Não é peça jurídica pronta.** Esqueleto para acelerar redação/revisão — submeta ao jurídico antes de assinar. Reutiliza as 12 cláusulas de `lgpd-dpa/assets/dpa-template.md` (papéis, objeto, instruções, confidencialidade, segurança, sub-operadores, direitos, incidentes, devolução, auditoria, responsabilidade, transferência) e **acrescenta as cláusulas 13–16 específicas de edtech** abaixo. Substitua todos os `{...}`.

---

**ACORDO DE TRATAMENTO DE DADOS PESSOAIS — EDUCAÇÃO**

Anexo ao Contrato nº `{NUMERO_CONTRATO}`, entre:

- **CONTROLADORA**: `{ESCOLA/REDE/SECRETARIA}`, CNPJ `{CNPJ}`, doravante "Controladora" (escola, rede de ensino ou secretaria de educação);
- **OPERADORA**: `{RAZAO_SOCIAL_PLATAFORMA}`, CNPJ `{CNPJ}`, doravante "Operadora" (plataforma edtech).

Regido pela Lei 13.709/2018 (LGPD), Lei 15.211/2025 (ECA Digital) e resoluções da ANPD.

*Cláusulas 1–12: conforme `lgpd-dpa/assets/dpa-template.md`, preenchidas para o cenário — objeto = "{hospedagem da plataforma educacional, gestão de turmas e conteúdo pedagógico}".*

---

## Cláusula 13 — Dados de crianças e adolescentes

13.1. As categorias de titulares incluem **alunos menores de 18 anos** (Art. 14 LGPD; Lei 15.211/2025).

13.2. É **vedada** à Operadora, sobre dados de alunos: (a) perfilamento para fins publicitários (Arts. 22 e 26, Lei 15.211); (b) publicidade direcionada de qualquer forma; (c) venda, cessão ou monetização; (d) uso para treinamento de modelos de IA sem autorização expressa e documentada da Controladora; (e) qualquer tratamento além das finalidades pedagógicas e de gestão escolar deste Acordo.

13.3. A obtenção de consentimento parental e a verificação de idade, quando exigidas, são de responsabilidade da Controladora; a Operadora obriga-se a prover os mecanismos técnicos (vinculação a responsável, controles parentais, verificação de idade) descritos no **Anexo IV**.

## Cláusula 14 — Limitação de finalidade e analytics

14.1. O tratamento destina-se **exclusivamente** a: `{gestão de turmas, publicação/consumo de conteúdo pedagógico, avaliações e relatórios de aprendizado, comunicação escola-aluno-responsável}`.

14.2. Analytics e estatísticas internas da Operadora somente sobre dados **anonimizados de forma irreversível** (Art. 12 LGPD), vedado re-identificar.

14.3. Logs e telemetria operacional limitados ao necessário para segurança e funcionamento, com retenção documentada.

## Cláusula 15 — Atendimento a titulares e incidentes (SLA edtech)

15.1. Requisições de titulares (alunos/responsáveis) recebidas pela Operadora serão repassadas à Controladora em até **2 dias úteis**, sem resposta direta.

15.2. A Operadora executará tecnicamente acesso, correção, portabilidade e eliminação em até **5 dias úteis** do pedido validado pela Controladora, para garantir o prazo legal de 15 dias (Art. 19, II).

15.3. Incidentes: notificação à Controladora em até **48 horas** da ciência (Cláusula 8), com informações para a comunicação da Controladora à ANPD em 3 dias úteis (Res. 15/2024). Incidentes envolvendo dados de menores são presumidamente de alto risco.

15.4. Canal único de privacidade: Controladora `{email}`; Operadora `{email}`.

## Cláusula 16 — Retenção, retorno e eliminação (ciclo letivo)

16.1. **Fim de contrato**: devolução integral dos dados em formato aberto e legível por máquina (CSV/JSON) em até `{30 dias}` + eliminação certificada em até `{90 dias}`, salvo retenção legal (Art. 16 LGPD; Art. 15 Marco Civil para logs de aplicação — 6 meses).

16.2. **Ciclo letivo**: dados de alunos desligados/transferidos ficam inacessíveis ao corpo docente anterior a partir do encerramento do ciclo, conforme regra de arquivamento `{ex.: 1 ano letivo + 1}` definida pela Controladora.

16.3. Backups: eliminação no ciclo normal de rotação, em até `{30 dias}` adicionais, com criptografia enquanto retidos.

---

## Anexo IV — Recursos de conformidade ECA Digital providos pela Operadora

`{Listar: verificação de idade, vinculação menor↔responsável até 16, painel de controles parentais, restrições por faixa etária, ausência de publicidade, relatórios de transparência se aplicável.}`

*Anexos I–III: conforme template base (medidas de segurança, sub-operadores, transferência internacional).*

---

**Assinaturas**

Controladora: ______________________  Data: ___/___/______
Operadora:    ______________________  Data: ___/___/______
