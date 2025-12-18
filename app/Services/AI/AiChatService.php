<?php

namespace App\Services\AI;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\User;
use OpenAI\Laravel\Facades\OpenAI;

class AiChatService
{
    public function __construct(
        private readonly AiContextService $context,
        private readonly AiPromptBuilder $promptBuilder,
    ) {}

    public function reply(User $user, string $text): string
    {
        $conversation = AiConversation::firstOrCreate(
            ['user_id' => $user->id],
            ['title' => 'AI Assistant']
        );

        // сохраняем user message
        $conversation->messages()->create([
            'role' => 'user',
            'content' => $text,
        ]);

        // берём последние N сообщений истории
        $history = $conversation->messages()
            ->latest('id')
            ->take(14)
            ->get()
            ->reverse()
            ->values()
            ->all();

        $payload = $this->promptBuilder->build(
            $this->context->build($user),
            $history,
            $text
        );

        // Реальный запрос к OpenAI Responses API
        // OpenAI PHP Laravel: OpenAI::responses()->create([...]) :contentReference[oaicite:1]{index=1}
        // Responses endpoint: /v1/responses :contentReference[oaicite:2]{index=2}
        $response = OpenAI::responses()->create([
            'model' => 'gpt-5',
            'instructions' => $payload['instructions'],
            'input' => $payload['input'],
            'max_output_tokens' => 600,
        ]);

        $answer = trim($response->outputText ?? '');

        $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $answer !== '' ? $answer : 'Я не смог сформировать ответ. Попробуй переформулировать.',
            'meta' => [
                'model' => $response->model ?? null,
                'usage' => $response->usage?->toArray() ?? null,
                'response_id' => $response->id ?? null,
            ],
        ]);

        return $answer;
    }
}
