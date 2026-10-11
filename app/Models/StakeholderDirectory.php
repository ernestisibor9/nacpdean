<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StakeholderDirectory extends Model
{
    //
        protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function fileUrl(): string
    {
        return asset('uploads/stakeholder-files/' . $this->file_path);
    }

    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    public function isPdf(): bool
    {
        return $this->file_type === 'pdf';
    }
}
