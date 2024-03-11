<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'usage',
        'tokens_used',
    ];

    protected $casts = [
        'usage' => 'json',
    ];
}
