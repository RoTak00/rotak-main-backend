<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryPost extends Model
{
    use HasFactory;

    public function gallery_image(): HasMany 
    {
        return $this->hasMany(GalleryImage::class);
    }
}
