<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
        protected $fillable = [
            'user_id', 'film_id', 'note', 'commentaire'
        ];
}
