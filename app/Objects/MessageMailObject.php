<?php

namespace App\Objects;

use Illuminate\Support\Carbon;
use PhpImap\IncomingMail;

class MessageMailObject extends MessageObject
{
    public static function fromIncomingMail(IncomingMail $incomingMail): self
    {
        $date = Carbon::parse($incomingMail->date);

        $fromAddress = $incomingMail->fromAddress;
        $toaddress = $incomingMail->headers->toaddress;

        // Strip away the name part of the email address
        $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        preg_match($pattern, $toaddress, $matches);
        $toaddress = $matches[0];

        return new self(
            $incomingMail->id,
            $date,
            $incomingMail->subject,
            $fromAddress,
            $toaddress,
            $incomingMail->textHtml,
            $incomingMail->textPlain,
            // $incomingMail->hasAttachments() ? $incomingMail->getAttachments() : []
        );
    }
}
