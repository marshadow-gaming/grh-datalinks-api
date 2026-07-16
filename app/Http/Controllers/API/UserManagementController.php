<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employe;
use App\Models\DocumentEmploye;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\BienvenuNotification;

class UserManagementController extends Controller
{
    // Modules accordés par défaut selon le rôle (référence : commande permissions:seed-defaults)
    private $permissionsParDefaut = [
        'drh'       => ['absences', 'scanner', 'historique_presence', 'recrutement'],
        'directeur' => ['absences', 'recrutement'],
    ];

    // Attribue les permissions par défaut correspondant au rôle, sans dupliquer
    private function seedPermissionsParDefaut(User $user)
    {
        $modules = $this->permissionsParDefaut[$user->role] ?? [];
        foreach ($modules as $module) {
            Permission::firstOrCreate(
                ['user_id' => $user->id, 'module' => $module],
                ['accorde_par' => auth()->id()]
            );
        }
    }

    // Liste complète tous rôles (vue Admin)
    public function index()
    {
        return response()->json(
            Employe::with('user', 'departement', 'documents')->get()
        );
    }

    // Création d'un compte (employé, stagiaire, drh, directeur, admin)
    public function store(Request $request)
    {
        $request->validate([
            'prenom'           => 'required|string',
            'nom'              => 'required|string',
            'email'            => 'required|email|unique:users',
            'password'         => 'required|min:6',
            'role'             => 'required|in:admin,drh,directeur,employe,stagiaire',
            'telephone'        => 'nullable|string',
            'date_naissance'   => 'nullable|date',
            'adresse'          => 'nullable|string',
            'departement_id'   => 'required|exists:departements,id',
            'poste'            => 'nullable|string',
            'type_contrat'     => 'nullable|in:CDI,CDD,Stage,Consultant',
            'date_embauche'    => 'nullable|date',
            'date_fin_contrat' => 'nullable|date',
            'salaire'          => 'nullable|numeric',
            'jours_conge_annuels' => 'nullable|integer',
            'notes'            => 'nullable|string',
            'cv'               => 'nullable|file|mimes:pdf|max:5120',
            'contrat'          => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $user = User::create([
            'name'     => $request->prenom . ' ' . $request->nom,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'telephone'=> $request->telephone,
        ]);

        $matricule = 'DL-' . strtoupper(substr($request->role, 0, 3)) . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT);

        $employe = Employe::create([
            'user_id'             => $user->id,
            'departement_id'      => $request->departement_id,
            'matricule'           => $matricule,
            'poste'               => $request->poste,
            'type_contrat'        => $request->type_contrat,
            'date_embauche'       => $request->date_embauche,
            'date_fin_contrat'    => $request->date_fin_contrat,
            'date_naissance'      => $request->date_naissance,
            'adresse'             => $request->adresse,
            'salaire'             => $request->salaire,
            'jours_conge_annuels' => $request->jours_conge_annuels,
            'notes'               => $request->notes,
        ]);

        $user->notify(new BienvenuNotification());

        // Attribution automatique des permissions par défaut si DRH ou Directeur
        $this->seedPermissionsParDefaut($user);

        // Rappel à tous les admins : ne pas oublier d'accorder l'accès scanner si nécessaire
        User::where('role', 'admin')->each(function ($admin) use ($user) {
            $admin->notify(new \App\Notifications\RappelAccorderScannerNotification($user->name));
        });

        if ($request->hasFile('cv')) {
            $path = $request->file('cv')->store('documents/cv', 'public');
            DocumentEmploye::create([
                'employe_id' => $employe->id, 'type' => 'cv',
                'nom_fichier' => $request->file('cv')->getClientOriginalName(),
                'chemin_fichier' => $path,
            ]);
        }

        if ($request->hasFile('contrat')) {
            $path = $request->file('contrat')->store('documents/contrats', 'public');
            DocumentEmploye::create([
                'employe_id' => $employe->id, 'type' => 'contrat',
                'nom_fichier' => $request->file('contrat')->getClientOriginalName(),
                'chemin_fichier' => $path,
            ]);
        }

        return response()->json([
            'message' => 'Compte créé avec succès',
            'employe' => $employe->load('user', 'departement', 'documents')
        ], 201);
    }

    // Modification (rôle, département, infos de base)
    public function update(Request $request, $id)
    {
        $employe = Employe::findOrFail($id);
        $ancienRole = $employe->user->role;
        $demandeur = $request->user();

        // Une DRH ou un Directeur ne peut modifier que des Employés ou des Stagiaires,
        // jamais un compte Admin, Directeur ou DRH (y compris le sien)
        if (in_array($demandeur->role, ['drh', 'directeur']) && !in_array($ancienRole, ['employe', 'stagiaire'])) {
            return response()->json(['message' => "Vous ne pouvez modifier que des employés ou des stagiaires"], 403);
        }

        $employe->update($request->only(
            'departement_id', 'poste', 'type_contrat', 'date_embauche',
            'date_fin_contrat', 'date_naissance', 'adresse', 'salaire',
            'jours_conge_annuels', 'notes'
        ));

        $userFields = $request->only('name', 'email', 'telephone', 'role', 'statut');

        // Le mot de passe doit être traité à part : ne l'inclure que s'il est fourni,
        // et toujours le hasher (jamais stocké en clair)
        if ($request->filled('password')) {
            $userFields['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        // Une DRH ou un Directeur ne peut jamais changer le rôle vers autre chose qu'employe/stagiaire
        if (in_array($demandeur->role, ['drh', 'directeur']) && isset($userFields['role'])
            && !in_array($userFields['role'], ['employe', 'stagiaire'])) {
            return response()->json(['message' => "Vous ne pouvez pas attribuer ce rôle"], 403);
        }

        if (!empty($userFields)) {
            $employe->user->update($userFields);
        }

        // Si le rôle a changé vers drh/directeur (promotion), on attribue les permissions par défaut
        $nouveauRole = $employe->user->fresh()->role;
        if ($nouveauRole !== $ancienRole && in_array($nouveauRole, ['drh', 'directeur'])) {
            $this->seedPermissionsParDefaut($employe->user->fresh());
        }

        return response()->json([
            'message' => 'Compte modifié',
            'employe' => $employe->load('user', 'departement', 'documents')
        ]);
    }

    // Suppression définitive du compte
    public function destroy($id)
    {
        $employe = Employe::findOrFail($id);
        $employe->user->delete(); // cascade supprime aussi l'employe
        return response()->json(['message' => 'Compte supprimé']);
    }
}