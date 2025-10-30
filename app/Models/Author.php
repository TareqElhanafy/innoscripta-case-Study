<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    protected $fillable = [
        'id',
        'name',
        'metadata',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
