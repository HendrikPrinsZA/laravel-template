<?php

namespace App\Clients;

use App\Models\TokenUsageLog;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class OpenAiClient
{
    protected const BASE_URI = 'https://api.openai.com';

    protected const MAX_TOKENS = 100;

    // protected const ENGINE = 'davinci-002';
    // protected const ENGINE = 'babbage-002';
    protected const ENGINE = 'gpt-3.5-turbo';

    protected Client $client;

    public function __construct(protected string $apiKey)
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URI,
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $this->apiKey),
            ],
        ]);
    }

    public function getChatCompletion(
        string $subject,
        array $messages = []
    ): string {
        // return 'Test response. API is bypassed';

        array_unshift($messages, [
            'role' => 'system',
            'content' => implode(' ', [
                'Consider that you are having a conversation with a scammer. Try to keep the conversation going',
                'in order to waste as much of their time as possible. The longer you can keep them engaged, the',
                sprintf('less time they have to scam others. Keep the answers short but interesting. Add some humour. The subject is %s.', $subject),
            ]),
        ]);

        $endpoint = sprintf('/v1/chat/completions', self::ENGINE);
        $params = [
            'model' => self::ENGINE,
            'messages' => $messages,
        ];
        $response = $this->client->post($endpoint, [
            'json' => $params,
        ]);

        $text = $this->getDataFromResponse($response, 'choices.0.message.content');
        if (is_null($text)) {
            throw new Exception(sprintf('Invalid response from OpenAI: %s', $response->getBody()));
        }

        return $text;
    }

    protected function getDataFromResponse(Response $response, string $key): mixed
    {
        $data = json_decode($response->getBody(), true);
        $value = data_get($data, $key);

        $usage = data_get($data, 'usage');
        TokenUsageLog::create([
            'key' => 'open-ai',
            'usage' => $usage,
            'tokens_used' => $usage['total_tokens'],
        ]);

        return $value;
    }

    public function getCompletion(string $prompt): string
    {
        $endpoint = sprintf('/v1/engines/%s/completions', self::ENGINE);
        $response = $this->client->post($endpoint, [
            'json' => [
                'prompt' => $prompt,
                'max_tokens' => self::MAX_TOKENS,
            ],
        ]);

        // Decode the response JSON
        $data = json_decode($response->getBody(), true);

        $text = data_get($data, 'choices.0.text');
        if (is_null($text)) {
            throw new Exception(sprintf('Invalid response from OpenAI: %s', $response->getBody()));
        }

        return $text;
    }
}
