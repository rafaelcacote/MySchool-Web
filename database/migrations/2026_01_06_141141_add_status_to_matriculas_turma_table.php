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

        if ($driver === 'sqlite') {
            Schema::connection('shared')->table('matriculas_turma', function ($table) {
                $table->string('status', 20)->default('ativo')->after('ativo');
            });

            // Atualizar registros existentes
            DB::connection('shared')
                ->table('matriculas_turma')
                ->whereNull('status')
                ->update(['status' => 'ativo']);

            return;
        }

        // Postgres: adicionar coluna no schema escola
        DB::connection('shared')->statement('
            ALTER TABLE escola.matriculas_turma
            ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT \'ativo\'
        ');

        // Atualizar registros existentes
        DB::connection('shared')->statement('
            UPDATE escola.matriculas_turma
            SET status = CASE
                WHEN ativo = true THEN \'ativo\'
                ELSE \'inativo\'
            END
            WHERE status IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection('shared')->getDriverName();

        if ($driver === 'sqlite') {
            Schema::connection('shared')->table('matriculas_turma', function ($table) {
                $table->dropColumn('status');
            });

            return;
        }

        DB::connection('shared')->statement('
            ALTER TABLE escola.matriculas_turma
            DROP COLUMN IF EXISTS status
        ');
    }
};
