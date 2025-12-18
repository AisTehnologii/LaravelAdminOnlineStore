<?php

namespace App\Services\AI;

use App\Models\AiMessage;

class AiPromptBuilder
{
    /**
     * @param array<int, AiMessage> $history
     */
    public function build(string $context, array $history, string $userText): array
    {
        // В Responses API можно слать просто input строкой + instructions,
        // но историю удобнее склеить текстом (простая, стабильная схема).
        $chat = [];
        foreach ($history as $m) {
            $chat[] = strtoupper($m->role) . ": " . $m->content;
        }
        $chat[] = "USER: " . $userText;

        return [
            'instructions' =>
                "You are an AI assistant inside a Laravel Filament admin panel.\n" .
                "Rules:\n" .
                "- Read-only guidance. No destructive actions.\n" .
                "- If user asks to delete/modify data, explain how to do it in Filament or via code, but don't claim you executed it.\n" .
                "- Be concise and practical.\n\n" .
                "Context:\n{$context}\n",
            'input' => implode("\n\n", $chat),
        ];
    }
}
