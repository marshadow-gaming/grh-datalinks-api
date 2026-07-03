<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::with('employe.departement')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:6',
            'role'      => 'required|in:admin,drh,directeur,employe,stagiaire',
            'telephone' => 'nullable|string',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'telephone' => $request->telephone,
        ]);

        return response()->json(['message' => 'Utilisateur créé', 'user' => $user], 201);
    }

    public function show($id)
    {
        return response()->json(User::with('employe.departement')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'      => 'sometimes|string',
            'email'     => 'sometimes|email|unique:users,email,' . $id,
            'role'      => 'sometimes|in:admin,drh,directeur,employe,stagiaire',
            'statut'    => 'sometimes|in:actif,inactif',
            'telephone' => 'nullable|string',
        ]);

        $user->update($request->except('password'));

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json(['message' => 'Utilisateur modifié', 'user' => $user]);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }
}