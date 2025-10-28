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

    public function popular()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->tmdb->getPopularMovies()
        ]);
    }

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

    public function details($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->tmdb->getMovieDetails($id)
        ]);
    }
    public function filtered(Request $request)
{
    $filters = [
        'genre' => $request->query('genre'),       // id du genre TMDb
        'year' => $request->query('year'),         // ex: 2023
        'min_rating' => $request->query('min_rating'), // ex: 7
        'sort_by' => $request->query('sort_by'),   // ex: popularity.desc
    ];

    return response()->json($this->tmdb->getFilteredMovies($filters));
}

}
