<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
     protected $fillable = [
        'tmdb_id', 'titre', 'resume', 'duree', 'genre', 'casting'
    ];

}
