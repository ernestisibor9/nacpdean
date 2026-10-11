<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeVideo extends Model
{
    protected $fillable = [
        'title',
        'description',
        'source_type',
        'source_value',
        'thumbnail',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | IS YOUTUBE?
    |--------------------------------------------------------------------------
    */

    public function isYoutube(): bool
    {
        return $this->source_type === 'youtube';
    }

    /*
    |--------------------------------------------------------------------------
    | YOUTUBE EMBED URL
    |--------------------------------------------------------------------------
    |
    | Accepts multiple URL formats:
    |   https://www.youtube.com/watch?v=XXXXXXXXXXX
    |   https://youtu.be/XXXXXXXXXXX
    |   https://www.youtube.com/embed/XXXXXXXXXXX
    |
    */

    public function youtubeEmbedUrl(): ?string
    {
        if (!$this->isYoutube()) {
            return null;
        }

        $id = $this->extractYoutubeId($this->source_value);

        return $id
            ? 'https://www.youtube.com/embed/' . $id
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | LOCAL FILE URL
    |--------------------------------------------------------------------------
    */

    public function fileUrl(): ?string
    {
        if ($this->isYoutube()) {
            return null;
        }

        return asset('uploads/videos/' . $this->source_value);
    }

    /*
    |--------------------------------------------------------------------------
    | THUMBNAIL URL
    |--------------------------------------------------------------------------
    |
    | For YouTube videos, uses the auto-generated YouTube thumbnail.
    | For local videos, uses the uploaded thumbnail if present.
    |
    */

    public function thumbnailUrl(): ?string
    {
        if ($this->isYoutube()) {
            $id = $this->extractYoutubeId($this->source_value);

            return $id
                ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg"
                : null;
        }

        return $this->thumbnail
            ? asset('uploads/videos/thumbs/' . $this->thumbnail)
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | EXTRACT YOUTUBE VIDEO ID
    |--------------------------------------------------------------------------
    */

    private function extractYoutubeId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        // Already just an ID (11 chars)
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        // /watch?v=ID or /embed/ID or youtu.be/ID
        if (preg_match(
            '/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            $url,
            $m
        )) {
            return $m[1];
        }

        return null;
    }
}
