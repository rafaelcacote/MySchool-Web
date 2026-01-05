<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user with Administrador Geral role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Verificar se já existe usuário com esse CPF
        $existingUser = User::where('cpf', '74527436287')->first();

        if ($existingUser) {
            $this->warn("Usuário com CPF 74527436287 já existe: {$existingUser->nome_completo}");
            $this->info('Atribuindo role Administrador Geral...');
            $existingUser->assignRole('Administrador Geral');
            $this->info('✅ Role atribuída com sucesso!');

            return self::SUCCESS;
        }

        // Criar novo usuário
        $user = User::create([
            'nome_completo' => 'administrador',
            'cpf' => '74527436287',
            'email' => 'admin@myschool.local',
            'password_hash' => Hash::make('12031986'),
            'ativo' => true,
        ]);

        // Atribuir role Administrador Geral
        $user->assignRole('Administrador Geral');

        $this->info('✅ Usuário administrador criado com sucesso!');
        $this->info("   Nome: {$user->nome_completo}");
        $this->info("   CPF: {$user->cpf}");
        $this->info('   Role: Administrador Geral');

        return self::SUCCESS;
    }
}
