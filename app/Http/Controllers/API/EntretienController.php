<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Entretien;
use App\Models\Candidature;
use Illuminate\Http\Request;

class EntretienController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'candidature_id'  => 'required|exists:candidatures,id',
            'date_entretien'  => 'required|date',
            'lieu'            => 'nullable|string',
        ]);

        $entretien = Entretien::create([
            'candidature_id' => $request->candidature_id,
            'date_entretien' => $request->date_entretien,
            'lieu'           => $request->lieu,
            'interviewer_id' => auth()->id(),
        ]);

        // Passe automatiquement la candidature en "en_entretien"
        Candidature::find($request->candidature_id)->update(['statut' => 'en_entretien']);

        return response()->json([
            'message'   => 'Entretien programmé',
            'entretien' => $entretien->load('interviewer'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $entretien = Entretien::findOrFail($id);
        $entretien->update($request->only('date_entretien', 'lieu', 'compte_rendu'));
        return response()->json(['message' => 'Entretien mis à jour', 'entretien' => $entretien]);
    }
}