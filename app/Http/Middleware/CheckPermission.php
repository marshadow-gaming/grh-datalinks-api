<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $module, ...$rolesStandards)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // Accès via le rôle standard (comportement habituel)
        if (in_array($user->role, $rolesStandards)) {
            return $next($request);
        }

        // Accès via une permission individuelle accordée
        if ($user->hasPermission($module)) {
            return $next($request);
        }

        return response()->json(['message' => 'Accès non autorisé'], 403);
    }
}