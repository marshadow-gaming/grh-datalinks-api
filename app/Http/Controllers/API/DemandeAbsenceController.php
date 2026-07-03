<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DemandeAbsence;
use Illuminate\Http\Request;
use App\Notifications\NouvelleDemandeNotification;
class DemandeAbsenceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin et DRH voient toutes les demandes
        if ($user->role === 'admin' || $user->role === 'drh') {
            return response()->json(
                DemandeAbsence::with('employe.user', 'employe.departement', 'traitePar')->get()
            );
        }

        // Tout le monde voit ses propres demandes
        $employe = $user->employe;
        return response()->json(
            DemandeAbsence::with('employe.user', 'traitePar')
                ->where('employe_id', $employe?->id)
                ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'employe_id'  => 'required|exists:employes,id',
            'type'        => 'required|in:conge,permission',
            'date_debut'  => 'required|date',
            'date_fin'    => 'nullable|date',
            'heure_debut' => 'nullable',
            'heure_fin'   => 'nullable',
            'motif'       => 'required|string',
        ]);

        $demande = DemandeAbsence::create([
            'employe_id'  => $request->employe_id,
            'type'        => $request->type,
            'date_debut'  => $request->date_debut,
            'date_fin'    => $request->date_fin,
            'heure_debut' => $request->heure_debut,
            'heure_fin'   => $request->heure_fin,
            'motif'       => $request->motif,
            'statut'      => 'en_attente',
        ]);

        $demande->load('employe.user');

        // Notifie le DRH
        \App\Models\User::where('role', 'drh')->each(function($user) use ($demande) {
            $user->notify(new NouvelleDemandeNotification($demande));
        });

        return response()->json(['message' => 'Demande envoyée', 'demande' => $demande], 201);
    }

    public function traiter(Request $request, $id)
    {
        $request->validate([
            'statut'      => 'required|in:approuvee,rejetee',
            'commentaire' => 'nullable|string',
        ]);

        $demande = DemandeAbsence::with('employe.user')->findOrFail($id);
        $demande->update([
            'statut'      => $request->statut,
            'commentaire' => $request->commentaire,
            'traite_par'  => auth()->id(),
        ]);
         // Notifie l'employé concerné
        $demande->employe->user->notify(new \App\Notifications\DemandeTraiteeNotification($demande));

        return response()->json(['message' => 'Demande traitée', 'demande' => $demande]);
    }
}