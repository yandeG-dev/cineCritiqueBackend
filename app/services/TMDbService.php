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

    private function safeMovieData(array $movie)
    {
        return [
            'id' => $movie['id'] ?? null,
            'title' => $movie['title'] ?? null,
            'poster' => !empty($movie['poster_path']) ? 'https://image.tmdb.org/t/p/w500'.$movie['poster_path'] : null,
            'release_date' => $movie['release_date'] ?? null,
            'rating' => $movie['vote_average'] ?? null,
            'overview' => $movie['overview'] ?? null,
        ];
    }

    /** Films populaires */
    public function getPopularMovies($page = 1)
    {
        try {
            $response = $this->client->get('movie/popular', [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'fr-FR',
                    'page' => $page
                ]
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return collect($data['results'] ?? [])->map([$this, 'safeMovieData']);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /** Recherche de films */
    public function searchMovies($query, $page = 1)
    {
        try {
            $response = $this->client->get('search/movie', [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'fr-FR',
                    'query' => $query,
                    'page' => $page
                ]
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return collect($data['results'] ?? [])->map([$this, 'safeMovieData']);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /** Détails d’un film */
    public function getMovieDetails($id)
    {
        try {
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
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getTrailerUrl($videos)
    {
        $trailer = collect($videos)->firstWhere('type', 'Trailer');
        return $trailer['key'] ?? null ? 'https://www.youtube.com/watch?v='.$trailer['key'] : null;
    }

    /** Films filtrés */
    public function getFiltratedMovies($filters = [], $page = 1)
    {
        try {
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

            return collect($data['results'] ?? [])->map([$this, 'safeMovieData']);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
