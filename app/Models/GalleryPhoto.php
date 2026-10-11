<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryPhoto extends Model
{
    //
        protected $fillable = [
        'title',
        'caption',
        'file_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function imageUrl(): string
    {
        return asset('uploads/gallery/' . $this->file_path);
    }
}
