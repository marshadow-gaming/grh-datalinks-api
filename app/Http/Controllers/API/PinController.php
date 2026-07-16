<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PinController extends Controller
{
    // Indique si l'utilisateur connecté a déjà défini un PIN
    public function statut(Request $request)
    {
        return response()->json([
            'pin_defini' => !is_null($request->user()->pin),
        ]);
    }

    // Définit ou remplace son propre PIN (4 à 6 chiffres)
    public function definir(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits_between:4,6',
        ]);

        $request->user()->update([
            'pin' => Hash::make($request->pin),
        ]);

        return response()->json(['message' => 'PIN défini avec succès']);
    }

    // Vérifie le PIN saisi avant d'autoriser l'accès au QR personnel ou au scanner
    public function verifier(Request $request)
    {
        $request->validate([
            'pin' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->pin || !Hash::check($request->pin, $user->pin)) {
            return response()->json(['message' => 'PIN incorrect'], 422);
        }

        return response()->json(['message' => 'PIN vérifié']);
    }
}