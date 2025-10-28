<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TMDbService;

class FilmController extends Controller
{
    protected $tmdb;

    public function __construct(TMDbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }
// Méthode pour les films populaires
    public function popular()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->tmdb->getPopularMovies()
        ]);
    }
// Méthode pour la recherche de films
    public function search(Request $request)
    {
        $query = $request->query('q');
        if (!$query) {
            return response()->json(['error' => 'Veuillez entrer un titre de film'], 400);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->tmdb->searchMovies($query)
        ]);
    }
// Méthode pour les détails d’un film
    public function details($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->tmdb->getMovieDetails($id)
        ]);
    }
    // Méthode pour les films filtrés 
    public function filtrated(Request $request)
{
    // On récupère les paramètres de filtrage
    $filters = [
        'genre' => $request->query('genre'),       // id du genre TMDb
        'year' => $request->query('year'),         // ex: 2023
        'min_rating' => $request->query('min_rating'), // ex: 7
        'sort_by' => $request->query('sort_by'),   // ex: popularity.desc
    ];

    try {
        $movies = $this->tmdb->getFiltratedMovies($filters);
        return response()->json($movies);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erreur lors du filtrage des films',
            'message' => $e->getMessage()
        ], 500);
    }
}

  
}
