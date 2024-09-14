<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogTag extends Model
{
    use HasFactory;

    public function blog_post(): BelongsTo 
    {
        return $this->belongsTo(BlogPost::class);
    }
}
