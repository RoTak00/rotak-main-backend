<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectIdea extends Model
{
    use HasFactory;

    public function tag(): HasMany
    {
        return $this->hasMany(ProjectIdeaTag::class);
    }
}
