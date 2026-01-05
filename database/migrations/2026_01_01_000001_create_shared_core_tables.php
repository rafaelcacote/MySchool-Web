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

        if ($driver === 'pgsql') {
            DB::connection('shared')->statement('CREATE SCHEMA IF NOT EXISTS shared');
        }

        if (! Schema::connection('shared')->hasTable('usuarios')) {
            Schema::connection('shared')->create('usuarios', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('email')->nullable()->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password_hash')->nullable();
                $table->string('nome_completo');
                $table->string('cpf', 11)->nullable()->unique();
                $table->string('telefone', 20)->nullable();
                $table->string('avatar_url', 2048)->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamp('last_login_at')->nullable();

                $table->rememberToken();
                $table->text('two_factor_secret')->nullable();
                $table->text('two_factor_recovery_codes')->nullable();
                $table->timestamp('two_factor_confirmed_at')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::connection('shared')->hasTable('tenants')) {
            Schema::connection('shared')->create('tenants', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nome');
                $table->string('subdominio')->nullable();
                $table->string('cnpj', 20)->nullable();
                $table->string('email')->nullable();
                $table->string('telefone', 20)->nullable();
                $table->string('endereco')->nullable();
                $table->string('endereco_numero', 20)->nullable();
                $table->string('endereco_complemento', 100)->nullable();
                $table->string('endereco_bairro', 100)->nullable();
                $table->string('endereco_cep', 10)->nullable();
                $table->string('endereco_cidade', 100)->nullable();
                $table->string('endereco_estado', 2)->nullable();
                $table->string('endereco_pais', 50)->nullable();
                $table->string('logo_url', 2048)->nullable();
                $table->uuid('plano_id')->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamp('trial_ate')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::connection('shared')->hasTable('usuario_tenants')) {
            Schema::connection('shared')->create('usuario_tenants', function (Blueprint $table) {
                $table->uuid('usuario_id');
                $table->uuid('tenant_id');
                $table->timestamp('created_at')->nullable();

                $table->primary(['usuario_id', 'tenant_id']);
                $table->index('usuario_id');
                $table->index('tenant_id');
            });
        }
    }

    public function down(): void
    {
        Schema::connection('shared')->dropIfExists('usuario_tenants');
        Schema::connection('shared')->dropIfExists('tenants');
        Schema::connection('shared')->dropIfExists('usuarios');
    }
};
