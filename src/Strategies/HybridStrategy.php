<?php 

namespace Axproo\LangManager\Strategies;

use Axproo\LangManager\Contracts\TranslationStrategy;

class HybridStrategy implements TranslationStrategy
{
    public function __construct(
        protected LocalDictionaryStrategy $local,
        protected OnlineApiStrategy $online,
        protected string $dictionaryGeneratedFile
    ) {}

    public function translate(string $key, string $value, string $targetLang): ?string
    {
        // Dictionnaire local
        $local = $this->local->translate($key, $value, $targetLang);
        if ($local) return $local;

        // API online
        $online = $this->online->translate($key, $value, $targetLang);
        if ($online) {
            $this->saveToDictionary($key, $online);
            return $online;
        }

        // Fallback
        return null;
    }

    private function saveToDictionary(string $key, string $tranlated) {
        $file = $this->dictionaryGeneratedFile;

        $data = file_exists($file) ? require $file : [];
        $data[$key] = $translated;

        $export = "<?php\n\nreturn " .var_export($data, true). ";\n>";
        file_put_contents($file, $export);
    }
}