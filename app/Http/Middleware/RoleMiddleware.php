<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // On vérifie si l'utilisateur est connecté ET si son rôle est dans la liste autorisée
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            // Si non, on bloque avec une erreur 403 (Interdit)
            abort(403, "Action non autorisée pour votre profil (" . (auth()->user()->role ?? 'invité') . ").");
        }

        return $next($request);
    }
}