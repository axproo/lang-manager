<?php 

namespace Axproo\LangManager\Contracts;

interface TranslationStrategy
{
    /**
     * Translate a key/value into target language.
     * Return translated string or null if not available.
     *
     * @param string $key full key like 'module.sub.key'
     * @param string $value original value (usually same as key)
     * @param string $targetLang 'fr' or 'en' etc.
     * @return string|null
     */
    public function translate(string $key, string $value, string $targetLang) : ?string;
}