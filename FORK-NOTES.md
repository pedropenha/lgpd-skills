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
