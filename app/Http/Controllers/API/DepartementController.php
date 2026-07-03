<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Departement;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    public function index()
    {
        return response()->json(Departement::with('employes.user')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|unique:departements',
            'description' => 'nullable|string',
        ]);

        $departement = Departement::create($request->all());
        return response()->json(['message' => 'Département créé', 'departement' => $departement], 201);
    }

    public function show($id)
    {
        return response()->json(Departement::with('employes.user', 'offresEmploi')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $departement = Departement::findOrFail($id);
        $departement->update($request->all());
        return response()->json(['message' => 'Département modifié', 'departement' => $departement]);
    }

    public function destroy($id)
    {
        Departement::findOrFail($id)->delete();
        return response()->json(['message' => 'Département supprimé']);
    }
}