<?php

namespace App\Livewire;

use Livewire\Component;
use Throwable;
use App\Models\AiConversation;
use App\Services\AI\AiChatService;

class AiAssistantDrawer extends Component
{
    public bool $open = false;
    public string $message = '';

    // 👉 сообщение об ошибке для UI
    public ?string $errorMessage = null;

    protected $listeners = ['ai-scroll-bottom' => '$refresh'];

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function getConversationProperty(): ?AiConversation
    {
        if (!auth()->check()) {
            return null;
        }

        return AiConversation::firstOrCreate(
            ['user_id' => auth()->id()],
            ['title' => 'AI Assistant']
        );
    }

    public function getMessagesProperty()
    {
        if (! $this->conversation) {
            return collect();
        }

        return $this->conversation
            ->messages()
            ->latest('id')
            ->take(50)
            ->get()
            ->reverse()
            ->values();
    }

    public function send(AiChatService $ai): void
    {
        if (!auth()->check()) {
            return;
        }

        // сбрасываем предыдущую ошибку
        $this->errorMessage = null;

        $text = trim($this->message);
        if ($text === '') {
            return;
        }

        // очищаем поле сразу (UX)
        $this->message = '';

        try {
            // основной вызов AI
            $ai->reply(auth()->user(), $text);

            $this->dispatch('ai-scroll-bottom');

        } catch (Throwable $e) {

            // 🔴 лог для разработчика
            logger()->error('AI Assistant error', [
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
            ]);

            // 🔴 сообщение для пользователя
            $this->errorMessage = $this->humanizeError($e);

            return;
        }
    }

    protected function humanizeError(Throwable $e): string
    {
        $msg = strtolower($e->getMessage());

        if (str_contains($msg, 'rate limit')) {
            return '⚠️ AI временно недоступен (превышен лимит). Попробуйте позже.';
        }

        if (str_contains($msg, 'billing')) {
            return '⚠️ AI недоступен: не подключена оплата.';
        }

        if (str_contains($msg, 'timeout')) {
            return '⚠️ AI не ответил вовремя. Попробуйте ещё раз.';
        }

        if (str_contains($msg, 'api key')) {
            return '⚠️ Ошибка ключа доступа AI.';
        }

        return '⚠️ Произошла ошибка AI-сервиса. Попробуйте позже.';
    }

    public function render()
    {
        return view('livewire.ai-assistant-drawer');
    }
}
