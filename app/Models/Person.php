<?php

namespace App\Models;

use App\Enums\PersonType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Person extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'type',
        'email',
    ];

    protected $casts = [
        'type' => PersonType::class,
    ];
}
