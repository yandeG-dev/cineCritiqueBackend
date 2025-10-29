<?php

namespace App\Services;

use GuzzleHttp\Client;

class TMDbService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.themoviedb.org/3/']);
        $this->apiKey = env('TMDB_API_KEY');
    }

    private function formatMovies(array $movies)
    {
        return collect($movies)->map(function ($movie) {
            return [
                'id' => $movie['id'] ?? null,
                'title' => $movie['title'] ?? null,
                'poster' => !empty($movie['poster_path']) ? 'https://image.tmdb.org/t/p/w500'.$movie['poster_path'] : null,
                'release_date' => $movie['release_date'] ?? null,
                'rating' => $movie['vote_average'] ?? null,
                'overview' => $movie['overview'] ?? null,
            ];
        });
    }

    public function getPopularMovies($page = 1)
    {
        $response = $this->client->get('movie/popular', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
                'page' => $page
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $this->formatMovies($data['results'] ?? []);
    }

    public function searchMovies($query, $page = 1)
    {
        $response = $this->client->get('search/movie', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
                'query' => $query,
                'page' => $page
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $this->formatMovies($data['results'] ?? []);
    }

    public function getFiltratedMovies($filters = [], $page = 1)
    {
        $query = [
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
            'page' => $page,
            'sort_by' => $filters['sort_by'] ?? 'popularity.desc',
        ];

        if (!empty($filters['genre'])) {
            $query['with_genres'] = $filters['genre'];
        }
        if (!empty($filters['year'])) {
            $query['primary_release_year'] = $filters['year'];
        }
        if (!empty($filters['min_rating'])) {
            $query['vote_average.gte'] = $filters['min_rating'];
        }

        $response = $this->client->get('discover/movie', ['query' => $query]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $this->formatMovies($data['results'] ?? []);
    }
    public function getMovieDetails($id)
{
    $response = $this->client->get("movie/{$id}", [
        'query' => [
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
            'append_to_response' => 'credits,videos'
        ]
    ]);

    $data = json_decode($response->getBody()->getContents(), true);

    return [
        'id' => $data['id'] ?? null,
        'title' => $data['title'] ?? null,
        'poster' => !empty($data['poster_path']) ? 'https://image.tmdb.org/t/p/w500'.$data['poster_path'] : null,
        'backdrop' => !empty($data['backdrop_path']) ? 'https://image.tmdb.org/t/p/original'.$data['backdrop_path'] : null,
        'overview' => $data['overview'] ?? null,
        'release_date' => $data['release_date'] ?? null,
        'runtime' => isset($data['runtime']) ? $data['runtime'].' min' : null,
        'genres' => collect($data['genres'] ?? [])->pluck('name'),
        'rating' => $data['vote_average'] ?? null,
        'cast' => collect($data['credits']['cast'] ?? [])->take(8)->map(function ($actor) {
            return [
                'name' => $actor['name'] ?? null,
                'character' => $actor['character'] ?? null,
                'profile' => !empty($actor['profile_path']) ? 'https://image.tmdb.org/t/p/w300'.$actor['profile_path'] : null
            ];
        }),
        'trailer' => $this->getTrailerUrl($data['videos']['results'] ?? [])
    ];
}
private function getTrailerUrl(array $videos)
{
    $trailer = collect($videos)->firstWhere('type', 'Trailer');
    return $trailer ? 'https://www.youtube.com/watch?v=' . ($trailer['key'] ?? '') : null;
}

}

// namespace App\Services;

// use GuzzleHttp\Client;

// class TMDbService
// {
//     protected $client;
//     protected $apiKey;

//     public function __construct()
//     {
//         $this->client = new Client(['base_uri' => 'https://api.themoviedb.org/3/']);
//         $this->apiKey = env('TMDB_API_KEY');
//     }

//     /** 🔹 Films populaires */
//     public function getPopularMovies($page = 1)
//     {
//         $response = $this->client->get('movie/popular', [
//             'query' => [
//                 'api_key' => $this->apiKey,
//                 'language' => 'fr-FR',
//                 'page' => $page
//             ]
//         ]);

//         $data = json_decode($response->getBody()->getContents(), true);

//         // On ne garde que les infos utiles
//         return collect($data['results'])->map(function ($movie) {
//             return [
//                 'id' => $movie['id'],
//                 'title' => $movie['title'],
//                 'poster' => 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'],
//                 'release_date' => $movie['release_date'],
//                 'rating' => $movie['vote_average'],
//                 'overview' => $movie['overview'],
//             ];
//         });
//     }

//     /**  Recherche de films */
//     public function searchMovies($query, $page = 1)
//     {
//         $response = $this->client->get('search/movie', [
//             'query' => [
//                 'api_key' => $this->apiKey,
//                 'language' => 'fr-FR',
//                 'query' => $query,
//                 'page' => $page
//             ]
//         ]);

//         $data = json_decode($response->getBody()->getContents(), true);

//         return collect($data['results'])->map(function ($movie) {
//             return [
//                 'id' => $movie['id'],
//                 'title' => $movie['title'],
//                 'poster' => 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'],
//                 'release_date' => $movie['release_date'],
//                 'rating' => $movie['vote_average'],
//                 'overview' => $movie['overview'],
//             ];
//         });
//     }

//     /**  Détails d’un film */
//     public function getMovieDetails($id)
//     {
//         $response = $this->client->get("movie/{$id}", [
//             'query' => [
//                 'api_key' => $this->apiKey,
//                 'language' => 'fr-FR',
//                 'append_to_response' => 'credits,videos'
//             ]
//         ]);

//         $data = json_decode($response->getBody()->getContents(), true);

//         // Extraction propre des informations utiles
//         return [
//             'id' => $data['id'],
//             'title' => $data['title'],
//             'poster' => 'https://image.tmdb.org/t/p/w500' . $data['poster_path'],
//             'backdrop' => 'https://image.tmdb.org/t/p/original' . $data['backdrop_path'],
//             'overview' => $data['overview'],
//             'release_date' => $data['release_date'],
//             'runtime' => $data['runtime'] . ' min',
//             'genres' => collect($data['genres'])->pluck('name'),
//             'rating' => $data['vote_average'],
//             'cast' => collect($data['credits']['cast'])->take(8)->map(function ($actor) {
//                 return [
//                     'name' => $actor['name'],
//                     'character' => $actor['character'],
//                     'profile' => $actor['profile_path']
//                         ? 'https://image.tmdb.org/t/p/w300' . $actor['profile_path']
//                         : null,
//                 ];
//             }),
//             'trailer' => $this->getTrailerUrl($data['videos']['results'])
//         ];
//     }

//     // Récupération du lien YouTube de la bande-annonce */
//     private function getTrailerUrl($videos)
//     {
//         $trailer = collect($videos)->firstWhere('type', 'Trailer');
//         return $trailer ? 'https://www.youtube.com/watch?v=' . $trailer['key'] : null;
//     }
     
//        /**  Films filtrés */
//     public function getFiltratedMovies($filters = [], $page = 1)
//     {
//         $query = [
//             'api_key' => $this->apiKey,
//             'language' => 'fr-FR',
//             'page' => $page,
//             'sort_by' => $filters['sort_by'] ?? 'popularity.desc',
//         ];

//         if (!empty($filters['genre'])) {
//             $query['with_genres'] = $filters['genre'];
//         }

//         if (!empty($filters['year'])) {
//             $query['primary_release_year'] = $filters['year'];
//         }

//         if (!empty($filters['min_rating'])) {
//             $query['vote_average.gte'] = $filters['min_rating'];
//         }

//         $response = Http::get("{$this->baseUrl}/discover/movie", $query);

//         if ($response->failed()) {
//             throw new \Exception('Erreur API TMDb : ' . $response->body());
//         }

//         $data = $response->json();

//         if (!isset($data['results'])) {
//             throw new \Exception('Réponse API invalide : ' . json_encode($data));
//         }

//         return collect($data['results'])->map(function ($movie) {
//             return [
//                 'id' => $movie['id'] ?? null,
//                 'title' => $movie['title'] ?? null,
//                 'poster' => isset($movie['poster_path'])
//                     ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path']
//                     : null,
//                 'release_date' => $movie['release_date'] ?? null,
//                 'rating' => $movie['vote_average'] ?? null,
//                 'overview' => $movie['overview'] ?? null,
//             ];
//         });
//     }


// }
