<?php

namespace App\Objects;

use Illuminate\Support\Carbon;

abstract class MessageObject
{
    public function __construct(
        public string $messageId,
        public Carbon $date,
        public string $subject,
        public string $from,
        public string $to,
        public string $body,
        public string $text,
        // public array $attachments
    ) {
        // ...
    }

    public static function fromString(string $jsonString): self
    {
        $data = json_decode($jsonString, true);

        return new static(
            $data['messageId'],
            Carbon::parse($data['date']),
            $data['subject'],
            $data['from'],
            $data['to'],
            $data['body'],
            $data['text'],
            // $data['attachments']
        );
    }

    public function toString(): string
    {
        return json_encode([
            'messageId' => $this->messageId,
            'date' => $this->date->toIso8601String(),
            'subject' => $this->subject,
            'from' => $this->from,
            'to' => $this->to,
            'body' => $this->body,
            'text' => $this->text,
            // 'attachments' => $this->attachments,
        ]);
    }
}
