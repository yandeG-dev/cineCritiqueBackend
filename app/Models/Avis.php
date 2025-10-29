<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
        protected $fillable = [
            'user_id', 'film_id', 'note', 'commentaire'
        ];
        // Relation avec l'utilisateur
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relation avec le film
    public function film() {
        return $this->belongsTo(Film::class); // Si tu as un modèle Film
    }
}
