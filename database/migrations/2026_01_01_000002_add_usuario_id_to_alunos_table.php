<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('shared')->table('alunos', function (Blueprint $table) {
            if (! Schema::connection('shared')->hasColumn('alunos', 'usuario_id')) {
                $table->uuid('usuario_id')->nullable()->after('tenant_id');
                $table->index('usuario_id');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('shared')->table('alunos', function (Blueprint $table) {
            if (Schema::connection('shared')->hasColumn('alunos', 'usuario_id')) {
                $table->dropIndex(['usuario_id']);
                $table->dropColumn('usuario_id');
            }
        });
    }
};
