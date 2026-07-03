<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\PermissionModifieeNotification;

class PermissionController extends Controller
{
    // Liste les permissions d'un utilisateur précis
    public function index($userId)
    {
        $permissions = Permission::where('user_id', $userId)->pluck('module');
        return response()->json($permissions);
    }

    // Accorder un module à un utilisateur
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'module'  => 'required|in:absences,scanner,historique_presence,travaux_stagiaire,recrutement',
        ]);

        $permission = Permission::firstOrCreate(
            ['user_id' => $request->user_id, 'module' => $request->module],
            ['accorde_par' => auth()->id()]
        );

        $userConcerne = \App\Models\User::find($request->user_id);
        $userConcerne->notify(new PermissionModifieeNotification($request->module, 'accordee'));

        return response()->json(['message' => 'Permission accordée', 'permission' => $permission], 201);
    }

    // Retirer un module à un utilisateur
    public function destroy(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'module'  => 'required|string',
        ]);
        $userConcerne = \App\Models\User::find($request->user_id);
        $userConcerne->notify(new PermissionModifieeNotification($request->module, 'retiree'));

        Permission::where('user_id', $request->user_id)
            ->where('module', $request->module)
            ->delete();

        return response()->json(['message' => 'Permission retirée']);
    }

    // Mes propres permissions (pour que chaque utilisateur connecté sache ce qu'il a en plus)
    public function mesPermissions(Request $request)
    {
        $permissions = $request->user()->permissions()->pluck('module');
        return response()->json($permissions);
    }
}