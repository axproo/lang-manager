<?php 

namespace Axproo\LangManager;

class LangManager
{
    public function run(string $projectDir, string $outputDir, array $locales) : void {
        
        $scanner = new Scanner();
        $generator = new FileGenerator();
        $dictionary = new DictionaryLoader();

        // Scan toutes les clés détectés dans le projet
        $langData = $scanner->scan($projectDir);

        foreach ($locales as $locale) {

            foreach ($langData as $module => $keys) {

                $translated = [];

                foreach ($keys as $k => $v) {

                    // Traduction réelle basée sur la *valeur*, pas la *clé*
                    $translated[$k] = $dictionary->translate($k, $locale);
                }

                $nested = Helpers::buildNestedArray($translated);
                $generator->save("$outputDir/$locale/$module", $nested);
            }
        }
    }
}