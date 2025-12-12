<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    protected $fillable = [
        'section',
        'key',
        'locale',
        'value',
        // 'group' можно оставить, но больше не использовать
    ];

    // Человеческое имя секции
    public function getSectionLabelAttribute(): string
    {
        return match ($this->section) {
            'home.about_card' => 'About card (блок слева от слайдера)',
            'home.blog_intro' => 'Blog & Updates — заголовок и текст',
            default           => $this->section,
        };
    }

    // Человеческое имя поля внутри секции
    public function getKeyLabelAttribute(): string
    {
        $map = [
            'home.about_card.title'   => 'Заголовок',
            'home.about_card.text_1'  => 'Текст, абзац 1',
            'home.about_card.text_2'  => 'Текст, абзац 2',
            'home.about_card.name'    => 'Имя',
            'home.about_card.role'    => 'Должность',

            'home.blog_intro.title'   => 'Заголовок блока',
            'home.blog_intro.text'    => 'Описание блока',
        ];

        return $map[$this->key] ?? $this->key;
    }
}
