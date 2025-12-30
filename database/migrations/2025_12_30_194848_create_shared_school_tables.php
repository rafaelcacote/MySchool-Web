<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Criar esquema shared se não existir
        DB::connection('shared')->statement('CREATE SCHEMA IF NOT EXISTS shared');

        // Criar tabela de alunos
        DB::connection('shared')->statement('
            CREATE TABLE IF NOT EXISTS shared.alunos (
                id UUID PRIMARY KEY,
                tenant_id UUID NOT NULL,
                nome_completo VARCHAR(255) NOT NULL,
                cpf VARCHAR(11),
                data_nascimento DATE,
                telefone VARCHAR(20),
                email VARCHAR(255),
                endereco VARCHAR(255),
                endereco_numero VARCHAR(20),
                endereco_complemento VARCHAR(100),
                endereco_bairro VARCHAR(100),
                endereco_cep VARCHAR(10),
                endereco_cidade VARCHAR(100),
                endereco_estado VARCHAR(2),
                endereco_pais VARCHAR(50),
                matricula VARCHAR(100),
                ativo BOOLEAN DEFAULT true,
                observacoes TEXT,
                created_at TIMESTAMP,
                updated_at TIMESTAMP,
                deleted_at TIMESTAMP
            )
        ');

        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_alunos_tenant_id ON shared.alunos(tenant_id)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_alunos_cpf ON shared.alunos(cpf)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_alunos_email ON shared.alunos(email)');

        // Criar tabela de responsáveis
        DB::connection('shared')->statement('
            CREATE TABLE IF NOT EXISTS shared.responsaveis (
                id UUID PRIMARY KEY,
                tenant_id UUID NOT NULL,
                nome_completo VARCHAR(255) NOT NULL,
                cpf VARCHAR(11),
                data_nascimento DATE,
                telefone VARCHAR(20),
                email VARCHAR(255),
                endereco VARCHAR(255),
                endereco_numero VARCHAR(20),
                endereco_complemento VARCHAR(100),
                endereco_bairro VARCHAR(100),
                endereco_cep VARCHAR(10),
                endereco_cidade VARCHAR(100),
                endereco_estado VARCHAR(2),
                endereco_pais VARCHAR(50),
                parentesco VARCHAR(50),
                ativo BOOLEAN DEFAULT true,
                observacoes TEXT,
                created_at TIMESTAMP,
                updated_at TIMESTAMP,
                deleted_at TIMESTAMP
            )
        ');

        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_responsaveis_tenant_id ON shared.responsaveis(tenant_id)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_responsaveis_cpf ON shared.responsaveis(cpf)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_responsaveis_email ON shared.responsaveis(email)');

        // Criar tabela de professores
        DB::connection('shared')->statement('
            CREATE TABLE IF NOT EXISTS shared.professores (
                id UUID PRIMARY KEY,
                tenant_id UUID NOT NULL,
                nome_completo VARCHAR(255) NOT NULL,
                cpf VARCHAR(11),
                data_nascimento DATE,
                telefone VARCHAR(20),
                email VARCHAR(255),
                endereco VARCHAR(255),
                endereco_numero VARCHAR(20),
                endereco_complemento VARCHAR(100),
                endereco_bairro VARCHAR(100),
                endereco_cep VARCHAR(10),
                endereco_cidade VARCHAR(100),
                endereco_estado VARCHAR(2),
                endereco_pais VARCHAR(50),
                formacao VARCHAR(255),
                especializacao VARCHAR(255),
                ativo BOOLEAN DEFAULT true,
                observacoes TEXT,
                created_at TIMESTAMP,
                updated_at TIMESTAMP,
                deleted_at TIMESTAMP
            )
        ');

        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_professores_tenant_id ON shared.professores(tenant_id)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_professores_cpf ON shared.professores(cpf)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_professores_email ON shared.professores(email)');

        // Criar tabela pivot aluno_responsavel
        DB::connection('shared')->statement('
            CREATE TABLE IF NOT EXISTS shared.aluno_responsavel (
                aluno_id UUID NOT NULL,
                responsavel_id UUID NOT NULL,
                created_at TIMESTAMP,
                updated_at TIMESTAMP,
                PRIMARY KEY (aluno_id, responsavel_id)
            )
        ');

        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_aluno_responsavel_aluno_id ON shared.aluno_responsavel(aluno_id)');
        DB::connection('shared')->statement('CREATE INDEX IF NOT EXISTS idx_aluno_responsavel_responsavel_id ON shared.aluno_responsavel(responsavel_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('shared')->statement('DROP TABLE IF EXISTS shared.aluno_responsavel');
        DB::connection('shared')->statement('DROP TABLE IF EXISTS shared.professores');
        DB::connection('shared')->statement('DROP TABLE IF EXISTS shared.responsaveis');
        DB::connection('shared')->statement('DROP TABLE IF EXISTS shared.alunos');
    }
};
