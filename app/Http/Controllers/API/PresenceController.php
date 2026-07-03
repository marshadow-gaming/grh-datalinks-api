<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\Employe;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresenceController extends Controller
{
    // Scan du QR — pointe arrivée ou départ selon l'état du jour
    public function scanner(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $employe = Employe::with('user')->where('qr_token', $request->qr_token)->first();

        if (!$employe) {
            return response()->json(['message' => 'QR Code invalide'], 404);
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        $presence = Presence::where('employe_id', $employe->id)
            ->where('date', $today)
            ->first();

        if (!$presence) {
            // Premier scan du jour → Arrivée
            $presence = Presence::create([
                'employe_id'    => $employe->id,
                'date'          => $today,
                'heure_arrivee' => $now,
                'scanne_par'    => auth()->id(),
            ]);

            $employe->user->notify(new \App\Notifications\PresenceScanneeNotification('arrivee', $now));

            return response()->json([
                'message' => 'Arrivée enregistrée',
                'type'    => 'arrivee',
                'employe' => $employe->user->name,
                'heure'   => $now,
                'presence' => $presence,
            ]);
        }

        if (!$presence->heure_depart) {
            // Deuxième scan du jour → Départ
            $presence->update([
                'heure_depart' => $now,
                'scanne_par'   => auth()->id(),
            ]);

            $employe->user->notify(new \App\Notifications\PresenceScanneeNotification('depart', $now));

            return response()->json([
                'message' => 'Départ enregistré',
                'type'    => 'depart',
                'employe' => $employe->user->name,
                'heure'   => $now,
                'presence' => $presence,
            ]);
        }

        // Déjà pointé arrivée + départ aujourd'hui
        return response()->json([
            'message' => 'Cet employé a déjà pointé son arrivée et son départ aujourd\'hui',
            'presence' => $presence,
        ], 409);
    }

    // Liste des présences (filtrable par employé/période)
    public function index(Request $request)
    {
        $query = Presence::with('employe.user', 'employe.departement', 'scannePar');

        if ($request->employe_id) {
            $query->where('employe_id', $request->employe_id);
        }
        if ($request->date_debut && $request->date_fin) {
            $query->whereBetween('date', [$request->date_debut, $request->date_fin]);
        }

        return response()->json($query->orderBy('date', 'desc')->get());
    }

    // QR Token de l'employé connecté (pour affichage dans son espace)
    public function monQrCode(Request $request)
    {
        $employe = $request->user()->employe;

        if (!$employe) {
            return response()->json(['message' => 'Aucun profil employé associé'], 404);
        }

        return response()->json(['qr_token' => $employe->qr_token]);
    }
    public function historique(Request $request)
    {
        $query = Presence::with('employe.user', 'employe.departement', 'scannePar');

        if ($request->date) {
            $query->where('date', $request->date);
        } elseif ($request->mois && $request->annee) {
            $query->whereYear('date', $request->annee)
                ->whereMonth('date', $request->mois);
        } elseif ($request->annee) {
        // Nouveau : toute l'année, sans filtre de mois
            $query->whereYear('date', $request->annee);
        }

        if ($request->employe_id) {
            $query->where('employe_id', $request->employe_id);
        }

        return response()->json(
            $query->orderBy('date', 'desc')->orderBy('heure_arrivee', 'desc')->get()
        );
    }
    public function presentsAujourdhui()
    {
        $today = \Carbon\Carbon::today()->toDateString();

        $presents = Presence::with('employe.user', 'employe.departement')
            ->where('date', $today)
            ->whereNotNull('heure_arrivee')
            ->get();

        return response()->json($presents);
    }
}