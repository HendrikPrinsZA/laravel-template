<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Thread extends Model
{
    use HasFactory;

    protected $fillable = [
        'uniqid',
        'subject',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (Thread $thread): void {
            $thread->uniqid = Str::random(32);
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
