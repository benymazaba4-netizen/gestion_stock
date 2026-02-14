<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        // On liste tout le monde sauf soi-même
        $users = User::where('id', '!=', Auth::id())->get();
        return view('users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,manager,observer',
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Rôle de {$user->name} mis à jour.");
    }

    public function destroy(User $user)
    {
        // Sécurité réseau/système : ne pas se supprimer soi-même
        if ($user->id === Auth::id()) {
            return back()->with('error', "Action impossible.");
        }

        $user->delete();
        return back()->with('success', "Utilisateur supprimé avec succès.");
    }
}