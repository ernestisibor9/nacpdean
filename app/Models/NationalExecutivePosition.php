<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NationalExecutivePosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'position',
        'status',
    ];
}
