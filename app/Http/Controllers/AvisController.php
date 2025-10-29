<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Avis;
use Illuminate\Support\Facades\Auth;

class AvisController extends Controller
{
    // Ajouter une critique
    public function store(Request $request) {
        $request->validate([
            'film_id' => 'required|integer',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string',
        ]);

        $avis = Avis::create([
            'user_id' => Auth::id(),
            'film_id' => $request->film_id,
            'note' => $request->note,
            'commentaire' => $request->commentaire
        ]);

        return response()->json($avis, 201);
    }

    // Modifier sa propre critique
    public function update(Request $request, $id) {
        $avis = Avis::findOrFail($id);

        if ($avis->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'note' => 'integer|min:1|max:5',
            'commentaire' => 'string'
        ]);

        $avis->update($request->only(['note', 'commentaire']));

        return response()->json($avis);
    }

    // Supprimer sa propre critique
    public function destroy($id) {
        $avis = Avis::findOrFail($id);

        if ($avis->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $avis->delete();

        return response()->json(['message' => 'Deleted']);
    }

    // Voir les critiques d’un film
     public function indexByFilm($filmId)
    {
        $avis = Avis::with('user:id,pseudo') // charge le pseudo de l’auteur
            ->where('film_id', $filmId)
            ->get();

        $moyenne = $avis->avg('note');

        $avisFormates = $avis->map(function ($a) {
            return [
                'id' => $a->id,
                'film_id' => $a->film_id,
                'note' => $a->note,
                'commentaire' => $a->commentaire,
                'pseudo' => $a->user ? $a->user->pseudo : 'Utilisateur inconnu',
                'created_at' => $a->created_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $avisFormates,
            'moyenne' => round($moyenne, 2)
        ]);
    }


}
