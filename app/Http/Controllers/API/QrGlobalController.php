<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\QrGlobal;
use Illuminate\Http\Request;

class QrGlobalController extends Controller
{
    // Récupère le QR global actuel (le génère s'il n'existe pas encore)
    public function show(Request $request)
    {
        $qr = QrGlobal::actuel();

        if (!$qr) {
            $qr = QrGlobal::create([
                'code' => 'GLOBAL-' . strtoupper(bin2hex(random_bytes(8))),
                'genere_par' => $request->user()->id,
            ]);
        }

        return response()->json($qr);
    }

    // Régénère un nouveau QR global (invalide l'ancien pour le scan futur)
    public function regenerer(Request $request)
    {
        $qr = QrGlobal::create([
            'code' => 'GLOBAL-' . strtoupper(bin2hex(random_bytes(8))),
            'genere_par' => $request->user()->id,
        ]);

        return response()->json(['message' => 'QR Code global régénéré', 'qr' => $qr], 201);
    }
}