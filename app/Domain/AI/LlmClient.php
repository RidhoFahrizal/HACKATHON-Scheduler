<?php

namespace App\Domain\AI;

use Illuminate\Support\Facades\Http;
use Throwable;

class LlmClient
{
    public function generate(string $systemPrompt, string $userMessage): ?string
    {
        $baseUrl = config('services.ai_chat.base_url', 'https://api.openai.com/v1');
        $apiKey = config('services.ai_chat.api_key');
        $model = config('services.ai_chat.model', 'gpt-4o-mini');

        if (empty($apiKey)) {
            return null;
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(15)
                ->post(rtrim($baseUrl, '/') . '/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'temperature' => 0.3,
                ]);

            if (! $response->successful()) {
                return null;
            }

            return $response->json('choices.0.message.content');
        } catch (Throwable) {
            return null;
        }
    }
}
