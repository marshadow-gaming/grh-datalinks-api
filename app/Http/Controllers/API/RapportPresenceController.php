<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\Employe;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RapportPresenceController extends Controller
{
    public function statistiques(Request $request)
    {
        $query = Presence::with('employe.user', 'employe.departement');

        if ($request->employe_id) {
            $query->where('employe_id', $request->employe_id);
        }
        if ($request->date_debut && $request->date_fin) {
            $query->whereBetween('date', [$request->date_debut, $request->date_fin]);
        }

        $presences = $query->orderBy('date', 'desc')->get();

        $totalJours = $presences->count();
        $heuresTotal = $presences->reduce(function($acc, $p) {
            if ($p->heure_arrivee && $p->heure_depart) {
                $debut = strtotime($p->heure_arrivee);
                $fin = strtotime($p->heure_depart);
                $acc += ($fin - $debut) / 3600;
            }
            return $acc;
        }, 0);

        return response()->json([
            'total_jours'   => $totalJours,
            'heures_total'  => round($heuresTotal, 1),
            'presences'     => $presences,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $query = Presence::with('employe.user', 'employe.departement');

        $employe = null;
        if ($request->employe_id) {
            $query->where('employe_id', $request->employe_id);
            $employe = Employe::with('user', 'departement')->find($request->employe_id);
        }
        if ($request->date_debut && $request->date_fin) {
            $query->whereBetween('date', [$request->date_debut, $request->date_fin]);
        }

        $presences = $query->orderBy('date', 'asc')->get();

        $heuresTotal = $presences->reduce(function($acc, $p) {
            if ($p->heure_arrivee && $p->heure_depart) {
                $debut = strtotime($p->heure_arrivee);
                $fin = strtotime($p->heure_depart);
                $acc += ($fin - $debut) / 3600;
            }
            return $acc;
        }, 0);

        $pdf = Pdf::loadView('rapports.presence', [
            'presences'   => $presences,
            'employe'     => $employe,
            'totalJours'  => $presences->count(),
            'heuresTotal' => round($heuresTotal, 1),
            'debut'       => $request->date_debut,
            'fin'         => $request->date_fin,
        ]);

        return $pdf->download('rapport-presence.pdf');
    }
}