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
            Schema::connection('shared')->create('exercicios', function ($table) {
                $table->uuid('id')->primary();
                $table->uuid('tenant_id');
                $table->uuid('professor_id');
                $table->uuid('turma_id');
                $table->string('disciplina', 100);
                $table->string('titulo', 255);
                $table->text('descricao')->nullable();
                $table->date('data_entrega');
                $table->text('anexo_url')->nullable();
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
            CREATE TABLE IF NOT EXISTS escola.exercicios (
                id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                tenant_id UUID NOT NULL,
                professor_id UUID NOT NULL,
                turma_id UUID NOT NULL,
                disciplina VARCHAR(100) NOT NULL,
                titulo VARCHAR(255) NOT NULL,
                descricao TEXT,
                data_entrega DATE NOT NULL,
                anexo_url TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP,
                CONSTRAINT exercicios_professor_id_fkey FOREIGN KEY (professor_id) REFERENCES escola.professores(id) ON DELETE CASCADE,
                CONSTRAINT exercicios_tenant_id_fkey FOREIGN KEY (tenant_id) REFERENCES shared.tenants(id) ON DELETE CASCADE,
                CONSTRAINT exercicios_turma_id_fkey FOREIGN KEY (turma_id) REFERENCES escola.turmas(id) ON DELETE CASCADE
            )
        ');

        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_exercicios_tenant_id ON escola.exercicios(tenant_id)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_exercicios_professor_id ON escola.exercicios(professor_id)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_exercicios_turma_id ON escola.exercicios(turma_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection('shared')->getDriverName();

        if ($driver === 'sqlite') {
            Schema::connection('shared')->dropIfExists('exercicios');
        } else {
            DB::connection('shared')->statement('DROP TABLE IF EXISTS escola.exercicios');
        }
    }
};
