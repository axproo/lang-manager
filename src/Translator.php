<?php

namespace Axproo\LangManager;

use Axproo\LangManager\Contracts\TranslationStrategy;

class Translator
{
    protected TranslationStrategy $strategy;

    public function __construct(TranslationStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function translate(string $key, string $value, string $targetLang): ?string
    {
        return $this->strategy->translate($key, $value, $targetLang);
    }
}
