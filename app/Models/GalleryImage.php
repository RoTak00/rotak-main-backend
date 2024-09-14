<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImage extends Model
{

    use HasFactory;

    public function gallery_post(): BelongsTo 
    {
        return $this->belongsTo(GalleryPost::class);
    }
}
