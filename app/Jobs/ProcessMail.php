<?php

namespace App\Jobs;

use App\Clients\GmailClient;
use App\Clients\OpenAiClient;
use App\Enums\MessageType;
use App\Enums\PersonType;
use App\Models\Message;
use App\Models\Person;
use App\Models\Thread;
use App\Objects\MessageMailObject;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMail implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected MessageMailObject $mailObject)
    {
        // ...
    }

    public function handle(): void
    {
        $gmailClient = new GmailClient(
            username: config('spambot.username'),
            passcode: config('spambot.passcode')
        );

        // We should do something with this mail, for now just log the contents to a debug file
        \Illuminate\Support\Facades\Log::debug('process mail', [
            'from' => $this->mailObject->from,
            'subject' => $this->mailObject->subject,
            'text' => $this->mailObject->text,
        ]);

        $parentId = null;
        $thread = Thread::firstWhere('uniqid', $this->mailObject->messageId);
        if (is_null($thread)) {
            $thread = Thread::create([
                'uniqid' => $this->mailObject->messageId,
                'subject' => $this->mailObject->subject,
            ]);

            $parentId = $thread->messages()->select('id')->orderByDesc('id')->first()?->id;
        }

        $fromPerson = Person::query()->firstOrCreate([
            'email' => $this->mailObject->from,
        ], [
            'name' => 'Unknown Person',
            'type' => PersonType::UNKNOWN,
        ]);

        $toPerson = Person::query()->firstOrCreate([
            'email' => $this->mailObject->to,
        ], [
            'name' => 'Unknown Person',
            'type' => PersonType::UNKNOWN,
        ]);

        $message = $thread->messages()->create([
            'type' => MessageType::EMAIL,
            'from_person_id' => $fromPerson->id,
            'to_person_id' => $toPerson->id,
            'data' => $this->mailObject,
            'parent_id' => $parentId,
            'sent_at' => $this->mailObject->date,
        ]);

        if ($toPerson->type === PersonType::ROBOT) {
            $response = $this->respondAsRobot($message);

            $response->send();
        }

        if (config('spambot.options.delete')) {
            $gmailClient->deleteEmails([$this->mailObject->messageId]);
        }

        $gmailClient->flagMailsAsProcessed([$this->mailObject->messageId]);
    }

    protected function respondAsRobot(Message $message): Message
    {
        $openAiClient = new OpenAiClient(apiKey: config('open-ai.api_key'));

        $messages = $message->thread->messages()->where('type', MessageType::EMAIL)->get()->map(function ($message) {
            return [
                'role' => $message->fromPerson->type === PersonType::ROBOT ? 'system' : 'user',
                'content' => $message->data->text,
            ];
        })->toArray();

        $response = $openAiClient->getChatCompletion(
            subject: $this->mailObject->subject,
            messages: $messages,
        );

        $mailObject = new MessageMailObject(
            messageId: $this->mailObject->messageId,
            date: now(),
            subject: 'Re: '.$this->mailObject->subject,
            from: $this->mailObject->to,
            to: $this->mailObject->from,
            body: $response,
            text: $response,
            // attachments: [],
        );

        return $message->thread->messages()->create([
            'type' => MessageType::EMAIL,
            'from_person_id' => $message->to_person_id,
            'to_person_id' => $message->from_person_id,
            'data' => $mailObject,
            'parent_id' => $message->id,
        ]);
    }
}
