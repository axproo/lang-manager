<?php 

namespace Axproo\LangManager;

class DictionaryLoader
{
    protected array $dictionaries = [];

    public function __construct(string $dictionaryPath, string $source = 'en', string $target = 'fr') {
        $mainFile      = rtrim($dictionaryPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . "$source-$target.php";
        $generatedFile = rtrim($dictionaryPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . "$source-$target.generated.php";

        $mainData       = file_exists($mainFile) ? require $mainFile : [];
        $genenratedData = file_exists($generateFile) ? require $generateFile : [];

        // Fusion: priorité au dictionary généré
        $this->dictionaries = array_merge($mainData, $genenratedData);
    }

    public function getAll() : array {
        return $this->dictionaries;
    }
}