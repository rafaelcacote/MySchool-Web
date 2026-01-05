<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AssignDefaultRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-default-roles {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign default roles to users without roles';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('Running in dry-run mode. No changes will be made.');
        }

        $this->info('Checking users without roles...');

        $usersWithoutRoles = User::with('roles', 'tenants')
            ->get()
            ->filter(fn ($user) => $user->roles->isEmpty());

        if ($usersWithoutRoles->isEmpty()) {
            $this->info('✅ All users already have roles assigned.');

            return self::SUCCESS;
        }

        $this->warn("Found {$usersWithoutRoles->count()} user(s) without roles:");

        foreach ($usersWithoutRoles as $user) {
            $role = $user->tenants->isEmpty() ? 'Administrador Geral' : 'Administrador Escola';

            $this->line("  - {$user->name} ({$user->email}) → {$role}");

            if (! $dryRun) {
                $user->assignRole($role);
                $this->info("    ✅ Role '{$role}' assigned");
            }
        }

        if ($dryRun) {
            $this->info('Dry-run completed. Run without --dry-run to apply changes.');
        } else {
            $this->info('✅ All roles assigned successfully!');
        }

        return self::SUCCESS;
    }
}
