<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StakeholderDirectory extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'stakeholder_directories';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | PUBLIC URL
    |--------------------------------------------------------------------------
    |
    | Builds the public URL for this file based on the config value.
    | Falls back to asset('uploads/stakeholder-directories/...') when the
    | config key is missing (e.g. on local dev).
    |
    | The config value is defined in config/filesystems.php under
    | `filesystems.stakeholder_directory.url`, and can be overridden per
    | environment via the STAKEHOLDER_DIRECTORY_URL env variable.
    |
    */

    public function fileUrl(): string
    {
        $base = rtrim(
            config(
                'filesystems.stakeholder_directory.url',
                asset('uploads/stakeholder-directories')
            ),
            '/'
        );

        return $base . '/' . ltrim($this->file_path ?? '', '/');
    }

    /*
    |--------------------------------------------------------------------------
    | TYPE HELPERS
    |--------------------------------------------------------------------------
    */

    public function isImage(): bool
    {
        $type = strtolower($this->file_type ?? '');

        if (in_array($type, ['image', 'jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return true;
        }

        $ext = strtolower(pathinfo($this->file_path ?? '', PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }

    public function isPdf(): bool
    {
        $type = strtolower($this->file_type ?? '');

        if ($type === 'pdf') {
            return true;
        }

        return str_ends_with(strtolower($this->file_path ?? ''), '.pdf');
    }
}
