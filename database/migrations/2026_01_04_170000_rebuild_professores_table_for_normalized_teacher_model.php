<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection('shared')->getDriverName();

        // Em SQLite (testes), não existe schema e alterar constraints/colunas é limitado.
        // Recriamos a tabela no formato esperado pelo modelo `Teacher`.
        if ($driver === 'sqlite') {
            Schema::connection('shared')->dropIfExists('professores');

            Schema::connection('shared')->create('professores', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('tenant_id');
                $table->uuid('usuario_id');
                $table->string('matricula', 50);
                $table->text('disciplinas')->nullable();
                $table->string('especializacao')->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('tenant_id');
                $table->index('usuario_id');
                $table->unique(['tenant_id', 'matricula']);
            });

            return;
        }

        // Postgres: garantir schema e ajustar tabela existente sem apagar dados.
        DB::connection('shared')->statement('CREATE SCHEMA IF NOT EXISTS escola');

        DB::connection('shared')->statement('
            CREATE TABLE IF NOT EXISTS escola.professores (
                id UUID PRIMARY KEY,
                tenant_id UUID NOT NULL,
                usuario_id UUID,
                matricula VARCHAR(50),
                disciplinas TEXT,
                especializacao VARCHAR(255),
                ativo BOOLEAN DEFAULT true,
                created_at TIMESTAMP,
                updated_at TIMESTAMP,
                deleted_at TIMESTAMP
            )
        ');

        DB::connection('shared')->statement('ALTER TABLE escola.professores ADD COLUMN IF NOT EXISTS usuario_id UUID');
        DB::connection('shared')->statement('ALTER TABLE escola.professores ADD COLUMN IF NOT EXISTS matricula VARCHAR(50)');
        DB::connection('shared')->statement('ALTER TABLE escola.professores ADD COLUMN IF NOT EXISTS disciplinas TEXT');

        // A coluna pode ter sido criada como NOT NULL em versões antigas.
        DB::connection('shared')->statement("
            DO $$
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM information_schema.columns
                    WHERE table_schema = 'escola'
                      AND table_name = 'professores'
                      AND column_name = 'nome_completo'
                ) THEN
                    ALTER TABLE escola.professores ALTER COLUMN nome_completo DROP NOT NULL;
                END IF;
            END $$;
        ");

        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_professores_tenant_id ON escola.professores(tenant_id)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_professores_usuario_id ON escola.professores(usuario_id)');
        DB::connection('shared')->statement('CREATE UNIQUE INDEX IF NOT EXISTS professores_tenant_id_matricula_key ON escola.professores(tenant_id, matricula) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        $driver = DB::connection('shared')->getDriverName();

        if ($driver === 'sqlite') {
            Schema::connection('shared')->dropIfExists('professores');

            return;
        }

        DB::connection('shared')->statement('DROP INDEX IF EXISTS professores_tenant_id_matricula_key');
        DB::connection('shared')->statement('DROP INDEX IF EXISTS idx_professores_usuario_id');
    }
};
