<?php 

namespace Axproo\LangManager\Strategies;

use Axproo\LangManager\Contracts\TranslationStrategy;

class OnlineApiStrategy implements TranslationStrategy
{
    protected $provider;
    protected $keys;

    public function __construct(string $provider = 'none', array $keys = []) {
        $this->provider = $provider;
        $this->keys = $keys;
    }

    public function translate(string $key, string $value, string $targetLang): ?string
    {
        return match ($this->provider) {
            'deepl'     => $this->translateWithDeepL($value, $targetLang),
            'google'    => $this->translateWithGoole($value, $targetLang),
            'openai'    => $this->translateWithOpenAI($value, $targetLang),

            default => null
        };
    }

    private function translateWithDeepL($text, $target) {
        return null; // A intégrer
    }

    private function translateWithGoole($text, $target) {
        return null; // A intégrer
    }

    private function translateWithOpenAI($text, $target) {
        return null; // A intégrer
    }
}