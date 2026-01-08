<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection('shared')->getDriverName();

        // Em SQLite (testes), não existe schema. Criamos tabelas "planas".
        if ($driver === 'sqlite') {
            Schema::connection('shared')->create('provas', function ($table) {
                $table->uuid('id')->primary();
                $table->uuid('tenant_id');
                $table->uuid('professor_id');
                $table->uuid('turma_id');
                $table->string('disciplina', 100);
                $table->string('titulo', 255);
                $table->text('descricao')->nullable();
                $table->date('data_prova');
                $table->time('horario')->nullable();
                $table->string('sala', 50)->nullable();
                $table->integer('duracao_minutos')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('tenant_id');
                $table->index('professor_id');
                $table->index('turma_id');
            });

            return;
        }

        // Postgres: criação no schema `escola`
        DB::connection('shared')->statement('CREATE SCHEMA IF NOT EXISTS escola');

        DB::connection('shared')->statement('
            CREATE TABLE IF NOT EXISTS escola.provas (
                id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                tenant_id UUID NOT NULL,
                professor_id UUID NOT NULL,
                turma_id UUID NOT NULL,
                disciplina VARCHAR(100) NOT NULL,
                titulo VARCHAR(255) NOT NULL,
                descricao TEXT,
                data_prova DATE NOT NULL,
                horario TIME,
                sala VARCHAR(50),
                duracao_minutos INTEGER,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP,
                CONSTRAINT provas_professor_id_fkey FOREIGN KEY (professor_id) REFERENCES escola.professores(id) ON DELETE CASCADE,
                CONSTRAINT provas_tenant_id_fkey FOREIGN KEY (tenant_id) REFERENCES shared.tenants(id) ON DELETE CASCADE,
                CONSTRAINT provas_turma_id_fkey FOREIGN KEY (turma_id) REFERENCES escola.turmas(id) ON DELETE CASCADE
            )
        ');

        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_provas_tenant_id ON escola.provas(tenant_id)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_provas_professor_id ON escola.provas(professor_id)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_provas_turma_id ON escola.provas(turma_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection('shared')->getDriverName();

        if ($driver === 'sqlite') {
            Schema::connection('shared')->dropIfExists('provas');
        } else {
            DB::connection('shared')->statement('DROP TABLE IF EXISTS escola.provas');
        }
    }
};
