<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Permission;

class SeedDefaultPermissions extends Command
{
    protected $signature = 'permissions:seed-defaults';
    protected $description = 'Attribue rétroactivement les permissions par défaut aux comptes DRH et Directeur existants (absences, recrutement, + scanner/historique pour la DRH)';

    // Modules accordés par défaut, par rôle
    protected $defaults = [
        'drh'       => ['absences', 'scanner', 'historique_presence', 'recrutement'],
        'directeur' => ['absences', 'recrutement'],
    ];

    public function handle()
    {
        $total = 0;

        // La colonne accorde_par est NOT NULL en base : on utilise le premier Admin
        // trouvé comme "attributeur" par défaut pour ces permissions automatiques
        $adminParDefaut = User::where('role', 'admin')->first();

        if (!$adminParDefaut) {
            $this->error("Aucun compte Admin trouvé — impossible d'attribuer les permissions (accorde_par requis).");
            return Command::FAILURE;
        }

        foreach ($this->defaults as $role => $modules) {
            $users = User::where('role', $role)->get();

            $this->info("Rôle: {$role} — {$users->count()} compte(s) trouvé(s)");

            foreach ($users as $user) {
                foreach ($modules as $module) {
                    $permission = Permission::firstOrCreate(
                        ['user_id' => $user->id, 'module' => $module],
                        ['accorde_par' => $adminParDefaut->id]
                    );

                    if ($permission->wasRecentlyCreated) {
                        $total++;
                        $this->line("  + {$user->name} → {$module}");
                    }
                }
            }
        }

        $this->info("Terminé. {$total} permission(s) créée(s).");
        return Command::SUCCESS;
    }
}