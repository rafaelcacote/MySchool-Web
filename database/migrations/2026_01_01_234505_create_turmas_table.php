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
            Schema::connection('shared')->create('turmas', function ($table) {
                $table->uuid('id')->primary();
                $table->uuid('tenant_id');
                $table->uuid('professor_id')->nullable();
                $table->string('nome', 100);
                $table->string('serie', 50)->nullable();
                $table->string('turma_letra', 10)->nullable();
                $table->string('ano_letivo', 10)->nullable();
                $table->string('periodo', 20)->nullable();
                $table->string('turno', 20)->nullable();
                $table->integer('capacidade')->nullable();
                $table->text('observacoes')->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('tenant_id');
                $table->index('professor_id');
                $table->unique(['tenant_id', 'nome', 'ano_letivo']);
            });

            return;
        }

        // Postgres: criação no schema `escola`
        DB::connection('shared')->statement('CREATE SCHEMA IF NOT EXISTS escola');

        DB::connection('shared')->statement('
            CREATE TABLE IF NOT EXISTS escola.turmas (
                id UUID PRIMARY KEY,
                tenant_id UUID NOT NULL,
                professor_id UUID,
                nome VARCHAR(100) NOT NULL,
                serie VARCHAR(50),
                turma_letra VARCHAR(10),
                ano_letivo VARCHAR(10),
                periodo VARCHAR(20),
                turno VARCHAR(20),
                capacidade INTEGER,
                observacoes TEXT,
                ativo BOOLEAN DEFAULT true,
                created_at TIMESTAMP,
                updated_at TIMESTAMP,
                deleted_at TIMESTAMP
            )
        ');

        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_turmas_tenant_id ON escola.turmas(tenant_id)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_turmas_professor_id ON escola.turmas(professor_id)');
        DB::connection('shared')->statement('CREATE UNIQUE INDEX IF NOT EXISTS turmas_tenant_id_nome_ano_letivo_key ON escola.turmas(tenant_id, nome, ano_letivo)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection('shared')->getDriverName();

        if ($driver === 'sqlite') {
            Schema::connection('shared')->dropIfExists('turmas');
        } else {
            DB::connection('shared')->statement('DROP TABLE IF EXISTS escola.turmas');
        }
    }
};
