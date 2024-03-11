<?php

namespace App\Models;

use App\Casts\MessageObjectCast;
use App\Clients\GmailClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'type',
        'from_person_id',
        'to_person_id',
        'data',
        'parent_id',
        'sent_at',
    ];

    protected $casts = [
        'data' => MessageObjectCast::class,
        'sent_at' => 'datetime',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function fromPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function toPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function original(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function send(): bool
    {
        if (! is_null($this->sent_at)) {
            return true;
        }

        // To-do: This should be queued!
        $gmailClient = new GmailClient(
            username: config('spambot.username'),
            passcode: config('spambot.passcode')
        );

        $gmailClient->replyToEmail($this->data->messageId, $this->data->body);
        $this->update([
            'sent_at' => now(),
        ]);

        return true;
    }
}
