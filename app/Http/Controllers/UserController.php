<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Avis;

class ProfilController extends Controller
{
    // Affichage du profil d'un utilisateur par son ID
    public function show($id)
    {
        $user = User::with('avis')->findOrFail($id); // Récupère l'utilisateur et ses avis

        return response()->json([
            'status' => 'success',
            'data' => [
                'pseudo' => $user->pseudo,
                'bio' => $user->bio,
                'avatar' => $user->avatar,
                'avis' => $user->avis
            ]
        ]);
    }

    // Mise à jour du profil (bio et avatar)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048', // vérifie que c'est une image max 2Mo
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        if ($request->filled('bio')) {
            $user->bio = $request->bio;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil mis à jour',
            'data' => $user
        ]);
    }
}
