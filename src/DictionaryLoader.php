<?php 

namespace Axproo\LangManager;

class DictionaryLoader
{
    protected array $dict = [];
    
    public function __construct() {
        $main = __DIR__ . '/dictionaries/en-fr.php';
        $generated = __DIR__ . '/dictionaries/en-fr.generated.php';

        if (file_exists($main)) {
            $this->dict = include $main;
        }

        if (file_exists($generated)) {
            $this->dict = array_merge($this->dict, include $generated);
        }
    }

    public function translate(string $key) : string {
        return $this->dict[$key] ?? $key;
    }
}