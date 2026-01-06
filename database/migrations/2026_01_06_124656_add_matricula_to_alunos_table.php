<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Esta migration não faz nada, pois a matrícula agora é o ID da tabela matriculas_turma.
     */
    public function up(): void
    {
        // Matrícula agora é o ID da tabela pivot matriculas_turma
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nada a fazer
    }
};
