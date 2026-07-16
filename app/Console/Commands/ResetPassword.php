<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Command
{
    protected $signature = 'admin:reset-password {email} {password}';
    protected $description = 'Réinitialise le mot de passe d\'un utilisateur par son email';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Aucun utilisateur trouvé avec l'email : {$email}");
            return Command::FAILURE;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Mot de passe réinitialisé avec succès pour {$user->name} ({$email}).");
        return Command::SUCCESS;
    }
}