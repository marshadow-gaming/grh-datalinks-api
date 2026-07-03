<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TravailStagiaire;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\RapportJournalierNotification;

class TravailStagiaireController extends Controller
{
    // Vue globale — Admin/DRH/Directeur voient tous les stagiaires
    public function index(Request $request)
    {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'drh', 'directeur'])) {
            $query = TravailStagiaire::with('employe.user', 'employe.departement');

            if ($request->employe_id) {
                $query->where('employe_id', $request->employe_id);
            }

            return response()->json($query->orderBy('date', 'desc')->get());
        }

        // Stagiaire voit uniquement son propre historique
        $employe = $user->employe;
        if (!$employe) return response()->json([]);

        return response()->json(
            TravailStagiaire::where('employe_id', $employe->id)
                ->orderBy('date', 'desc')
                ->get()
        );
    }

    // Créer ou mettre à jour le rapport du jour (upsert)
    public function store(Request $request)
    {
        $request->validate([
            'titre'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $employe = $request->user()->employe;
        if (!$employe) {
            return response()->json(['message' => 'Aucun profil employé associé'], 404);
        }

        $today = Carbon::today()->toDateString();

        $travail = TravailStagiaire::updateOrCreate(
            ['employe_id' => $employe->id, 'date' => $today],
            ['titre' => $request->titre, 'description' => $request->description]
        );

        $travail->load('employe.user');

        \App\Models\User::whereIn('role', ['admin', 'drh'])->each(function($user) use ($travail) {
            $user->notify(new RapportJournalierNotification($travail));
        });
        
        return response()->json([
            'message' => 'Rapport enregistré',
            'travail' => $travail,
        ], 201);
    }

    // Le rapport du jour pour le stagiaire connecté (pré-remplir le formulaire si déjà saisi)
    public function monRapportDuJour(Request $request)
    {
        $employe = $request->user()->employe;
        if (!$employe) return response()->json(null);

        $today = Carbon::today()->toDateString();

        $travail = TravailStagiaire::where('employe_id', $employe->id)
            ->where('date', $today)
            ->first();

        return response()->json($travail);
    }
}