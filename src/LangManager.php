<?php 

namespace Axproo\LangManager;

class LangManager
{
    public function run(string $projectDir, string $outputDir, array $locales) : void {
        
        $scanner = new Scanner();
        $generator = new FileGenerator();
        $dictionary = new DictionaryLoader();
        $reader = new LangReader();

        // Scan toutes les clés détectés dans le projet
        $langData = $scanner->scan($projectDir);

        foreach ($locales as $locale) {

            foreach ($langData as $module => $keys) {

                $translated = [];

                // On charge le fichier source en (anglais / langue par défaut)
                $sourceData = $reader->loadBaseMessages($module);

                foreach ($keys as $k => $v) {

                    // Si une valeur existe dans les fichiers source, on l'utiliser
                    $sourceText = $sourceData[$k] ?? $v;

                    // Traduction réelle basée sur la *valeur*, pas la *clé*
                    $translated[$k] = $dictionary->translate($sourceText, $locale);
                }

                $nested = Helpers::buildNestedArray($translated);
                $generator->save("$outputDir/$locale/$module", $nested);
            }
        }
    }
}