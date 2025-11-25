<?php 

namespace Axproo\LangManager\Strategies;

use Axproo\LangManager\Contracts\TranslationStrategy;

class LocalDictionaryStrategy implements TranslationStrategy
{
    protected array $dictionary;

    public function __construct(array $dictionary) {
        $this->dictionary = $dictionary;
    }

    public function translate(string $key, string $value, string $targetLang): ?string
    {
        return $this->dictionary[$key] ?? null;
    }
}