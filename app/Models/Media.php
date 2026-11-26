<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    protected $fillable = ['title', 'type', 'creator', 'release_year', 'duration_minutes', 'platform'];

    // Rubrik relasi Eloquent: satu media dapat masuk koleksi banyak pengguna.
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_user')->withTimestamps();
    }
}
