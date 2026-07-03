<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Departement;
use App\Models\Employe;
use App\Models\TypeConge;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Départements
        $departements = [
            'Datalinks',
            'Alphacom',
            'Micro Finance',
            'Département Administratif et RH',
            'Département de Formation',
            'Services',
            'Stagiaires',
        ];

        $deptModels = [];
        foreach ($departements as $nom) {
            $deptModels[$nom] = Departement::create(['nom' => $nom]);
        }

        // Types de congés
        TypeConge::create(['nom' => 'Congé annuel', 'jours_par_an' => 30]);
        TypeConge::create(['nom' => 'Congé maladie', 'jours_par_an' => 15]);
        TypeConge::create(['nom' => 'Congé maternité', 'jours_par_an' => 98]);
        TypeConge::create(['nom' => 'Permission', 'jours_par_an' => null]);

        // Admin
        $admin = User::create([
            'name' => 'Admin Système',
            'email' => 'admin@datalinks.bj',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // DRH
        $drh = User::create([
            'name' => 'Responsable RH',
            'email' => 'drh@datalinks.bj',
            'password' => Hash::make('password'),
            'role' => 'drh',
        ]);
        Employe::create([
            'user_id' => $drh->id,
            'departement_id' => $deptModels['Département Administratif et RH']->id,
            'matricule' => 'DL-DRH-001',
            'poste' => 'Directeur des Ressources Humaines',
            'type_contrat' => 'CDI',
            'date_embauche' => '2020-01-15',
        ]);

        // Directeur Général
        $directeur = User::create([
            'name' => 'Directeur Général',
            'email' => 'directeur@datalinks.bj',
            'password' => Hash::make('password'),
            'role' => 'directeur',
        ]);
        Employe::create([
            'user_id' => $directeur->id,
            'departement_id' => $deptModels['Datalinks']->id,
            'matricule' => 'DL-DG-001',
            'poste' => 'Directeur Général',
            'type_contrat' => 'CDI',
            'date_embauche' => '2018-06-01',
        ]);

        // Employé
        $employeUser = User::create([
            'name' => 'Jean Kossou',
            'email' => 'employe@datalinks.bj',
            'password' => Hash::make('password'),
            'role' => 'employe',
        ]);
        Employe::create([
            'user_id' => $employeUser->id,
            'departement_id' => $deptModels['Alphacom']->id,
            'matricule' => 'DL-EMP-001',
            'poste' => 'Développeur Web',
            'type_contrat' => 'CDI',
            'date_embauche' => '2022-03-10',
        ]);

        // Stagiaire
        $stagiaireUser = User::create([
            'name' => 'Aïcha Lawani',
            'email' => 'stagiaire@datalinks.bj',
            'password' => Hash::make('password'),
            'role' => 'stagiaire',
        ]);
        Employe::create([
            'user_id' => $stagiaireUser->id,
            'departement_id' => $deptModels['Stagiaires']->id,
            'matricule' => 'DL-STG-001',
            'poste' => 'Stagiaire Développement',
            'type_contrat' => 'Stage',
            'date_embauche' => '2026-01-05',
            'date_fin_contrat' => '2026-07-05',
        ]);
    }
}