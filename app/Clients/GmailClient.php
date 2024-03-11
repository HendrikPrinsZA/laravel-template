<?php

namespace App\Clients;

use App\Objects\MessageMailObject;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;

class GmailClient
{
    public function __construct(
        protected string $username,
        protected string $passcode
    ) {
        // ...
    }

    protected function getMailBox(): Mailbox
    {
        $mailBox = new Mailbox(
            imapPath: '{imap.gmail.com:993/imap/ssl}INBOX',
            login: $this->username,
            password: $this->passcode,
            attachmentsDir: __DIR__, // Directory, where attachments will be saved (optional)
            serverEncoding: 'UTF-8',
            trimImapPath: true,
            attachmentFilenameMode: false // Attachment filename mode (optional; false = random filename; true = original filename)
        );

        // set some connection arguments (if appropriate)
        $mailBox->setConnectionArgs(
            CL_EXPUNGE // expunge deleted mails upon mailbox close
            // | OP_SECURE // don't do non-secure authentication
        );

        return $mailBox;
    }

    public function getEmails(int $max = 5): Collection
    {
        $mails = collect();
        $mailBox = $this->getMailBox();

        // Get all emails (messages)
        // PHP.net imap_search criteria: http://php.net/manual/en/function.imap-search.php
        $mailIds = $mailBox->searchMailbox('UNFLAGGED');

        // If $mailIds is empty, no emails could be found
        if (! $mailIds) {
            return $mails;
        }

        foreach ($mailIds as $mailId) {
            $mail = $mailBox->getMail($mailId);
            if (Str::endsWith($mail->fromAddress, config('spambot.ignore'))) {
                continue;
            }

            $text = $mail->textPlain;
            if (empty($text)) {
                $text = strip_tags($mail->textHtml);
            }

            $mails->push(MessageMailObject::fromIncomingMail($mail));
            if ($mails->count() >= $max) {
                break;
            }
        }

        return $mails;
    }

    public function deleteEmails(array $mailIds): void
    {
        $mailBox = $this->getMailBox();
        foreach ($mailIds as $mailId) {
            $mailBox->deleteMail($mailId);
        }
    }

    public function flagMailsAsProcessed(array $mailIds): void
    {
        $mailBox = $this->getMailBox();

        foreach ($mailIds as $mailId) {
            $mailBox->moveMail($mailId, 'PROCESSED');
        }
    }

    public function getMailById(string $mailId): IncomingMail
    {
        $mailBox = $this->getMailBox();

        return $mailBox->getMail($mailId);
    }

    public function replyToEmail(string $mailId, string $body): ?SentMessage
    {
        $mail = $this->getMailById($mailId);

        // Prepare reply headers and body
        $subject = 'Re: '.$mail->subject;
        $to = $mail->fromAddress;

        // Use your preferred method to send the email, Laravel's Mail, for example
        return Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }
}
