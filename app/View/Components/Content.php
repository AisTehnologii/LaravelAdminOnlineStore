<?php

namespace App\View\Components;

use App\Models\ContentBlock;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Content extends Component
{
    public string $key;
    public ?string $default;

    public function __construct(string $key, string $default = null)
    {
        $this->key = $key;
        $this->default = $default;
    }

    public function render(): View|Closure|string
    {
        $locale = app()->getLocale() ?: 'ru';

        $value = ContentBlock::where('key', $this->key)
            ->where('locale', $locale)
            ->value('value');

        $text = $value ?? ($this->default ?? '');

        return function () use ($text) {
            return $text;
        };
    }
}
