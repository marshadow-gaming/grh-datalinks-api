<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employe;
use App\Models\DocumentEmploye;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
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

        $employe->update($request->only(
            'departement_id', 'poste', 'type_contrat', 'date_embauche',
            'date_fin_contrat', 'date_naissance', 'adresse', 'salaire',
            'jours_conge_annuels', 'notes'
        ));

        $userFields = $request->only('name', 'email', 'telephone', 'role', 'statut');
        if (!empty($userFields)) {
            $employe->user->update($userFields);
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