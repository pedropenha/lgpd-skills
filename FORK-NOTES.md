# Attribution — lgpd-skills

The `lgpd-*` skill family in this directory is a local fork of:

- **Upstream**: https://github.com/goul4rt/lgpd-skills
- **Upstream commit**: `d85d79abeeb37cb99fc0785e735a9ca790698a77` (v1.2.0, 2026-05-31)
- **License**: MIT — Copyright (c) 2026 goul4rt

MIT License text: see upstream repo `LICENSE`. Permission is hereby granted, free
of charge, to use, copy, modify, and distribute copies of the Software, subject
to inclusion of the copyright notice above.

## Local modifications

Adapted from the original **Prisma + Better Auth + Next.js + React Native**
stack to **PHP/Laravel + Flutter + Vue/Nuxt 2**:

- `lgpd-consent-schema` — Prisma schema → Laravel migration
  (`assets/consent-migration.php`), Better Auth hooks → Laravel auth flow,
  JS helpers → PHP service code
- `lgpd-dsar/assets/endpoints.md` — Next.js route handlers → Laravel
  controllers, React Native screen → Flutter widget + Nuxt notes
- `lgpd-audit-logging` — Prisma schema + TS middleware → Eloquent model +
  PHP service/middleware
- `lgpd-encryption-keys` — Node crypto + Prisma extension → PHP openssl +
  Laravel encrypted casts, RN SSL pinning → Flutter
- `lgpd-data-mapping` — Prisma annotations → PHP docblock annotations;
  `extract-prisma-annotations.sh` → `extract-lgpd-annotations.sh`
- `lgpd-retention-erasure` — Prisma schema → migration, cron → Laravel scheduler
- Minor stack references in `lgpd-audit`, `lgpd-legacy-retrofit`,
  `lgpd-vendor-audit`, `lgpd-dsar`, `lgpd-ripd` template

Legal content (articles, resolutions, deadlines, templates ROPA/RIPD/DPA) is
unchanged from upstream.

## New skills added in this fork (v1.4.0)

- `marco-civil-platform` — Marco Civil da Internet (Lei 12.965/2014 + Decreto
  8.771/2016) for application providers with UGC: 6-month access logs,
  judicial-order-only record disclosure, Art. 19/21 takedown workflows.
  Informed by `evolution-foundation/evo-nexus` `.claude/skills/legal-compliance-check`
  (Apache-2.0) and `AlissonSantos1/brasil-legal-skills` `digital` (MIT,
  Copyright (c) 2026 AlissonSantos1).
- `lgpd-school-dpa` + `assets/dpa-school-template.md` — school-as-controller /
  platform-as-operador DPA for edtech, extending `lgpd-dpa` with minors,
  purpose-limitation, edtech SLA, and school-year retention clauses.
- `lbi-education-a11y` — LBI (Lei 13.146/2015), Decreto 5.296/2004, eMAG,
  ABNT NBR 17225, PNLD accessibility requirements for educational platforms
  and content; delegates WCAG implementation to the `accessibility` skill.
