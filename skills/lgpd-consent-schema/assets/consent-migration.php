<?php
// Consent ledger — append-only, versionado, evidência forte.
// LGPD Art. 8 (consentimento), Art. 9 (informação), Art. 18, IX (revogação).
// ECA Digital Lei 15.211/2025 — Guardian + MinorLink para vinculação até 16 anos.
// Fork de goul4rt/lgpd-skills — adaptado de Prisma para Laravel/Eloquent.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Versionamento do texto consentido — toda mudança material = nova versão
        Schema::create('policy_versions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('version')->unique();        // "v3.2"
            $table->timestamp('effective_at');
            $table->text('text');
            $table->string('text_hash');                 // SHA-256 do texto canônico
            $table->string('language', 10)->default('pt-BR');
            $table->text('changelog')->nullable();

            $table->index('effective_at');
        });

        // Ledger append-only — revogar = inserir novo registro com status REVOKED
        Schema::create('consent_records', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Quem
            $table->foreignId('subject_id')->constrained('users');
            // O quê — "marketing_email", "personalization", "analytics", "biometric_auth"
            $table->string('purpose');
            $table->foreignUlid('policy_version_id')->constrained('policy_versions');
            // CONSENT | CONTRACT | LEGAL_OBLIGATION | PUBLIC_POLICY | RESEARCH
            // REGULAR_RIGHTS | VITAL_PROTECTION | HEALTH_TUTELAGE
            // LEGITIMATE_INTEREST | CREDIT_PROTECTION
            $table->string('legal_basis')->default('CONSENT');

            // Status — GRANTED | REVOKED | EXPIRED | SUPERSEDED
            $table->string('status');
            $table->timestamp('granted_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('expires_at')->nullable();  // se houver TTL (raro)

            // Evidência (Art. 8º, § 2º — controlador tem ônus de prova)
            $table->string('evidence_hash');              // SHA-256 do payload (IP+UA+timestamp+ui_snapshot)
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('locale', 10)->default('pt-BR');
            // WEB | MOBILE_FLUTTER | API | SIGNUP | SETTINGS_PAGE | EMAIL_OPTIN | COOKIE_BANNER
            $table->string('channel');
            $table->text('ui_snapshot')->nullable();       // HTML/screenshot hash do que foi mostrado

            // ECA Digital / Art. 14 LGPD
            $table->boolean('is_minor')->default(false);
            $table->boolean('parental_consent')->nullable();
            $table->foreignUlid('guardian_id')->nullable()->constrained('guardians');

            // Propagação a operadores
            $table->json('propagated_to')->nullable();     // ["datadog", "mixpanel"]

            $table->timestamp('created_at')->useCurrent();

            $table->index(['subject_id', 'purpose', 'status']);
            $table->index('granted_at');
        });

        // ECA Digital — responsável legal
        Schema::create('guardians', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('full_name');
            $table->string('cpf');                       // criptografado em aplicação (cast 'encrypted')
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('relationship');              // "mother", "father", "legal_guardian"
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_method')->nullable(); // "id_doc", "video_call", "official_db"
            $table->timestamp('created_at')->useCurrent();
        });

        // ECA Digital — vinculação menor ↔ responsável até 16 anos (Art. 24)
        Schema::create('minor_links', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('minor_user_id')->constrained('users');
            $table->foreignUlid('guardian_id')->constrained('guardians');
            $table->boolean('active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();

            $table->unique(['minor_user_id', 'guardian_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minor_links');
        Schema::dropIfExists('consent_records');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('policy_versions');
    }
};
