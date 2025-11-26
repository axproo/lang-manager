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

                $filePath = "$outputDir/$locale/$module.php";

                // Charger le fichier existant
                $existing = [];
                if (file_exists($filePath)) {
                    $existing = include $filePath;
                    $existing = Helpers::flattenArray($existing);
                }

                $translated = [];

                foreach ($keys as $k => $v) {

                    if (isset($existing[$k])) {
                        $translated[$k] = $existing[$k];
                    } else {
                        // Vérifier le dictionnaire
                        $dictTranslation = $dictionary->translate($k, $locale);

                        if ($dictTranslation === $k) {
                            // Pas de traduction -> placeholder
                            $translated[$k] = $dictTranslation;
                        }
                    }
                }

                $nested = Helpers::buildNestedArray($translated);
                $generator->save("$outputDir/$locale/$module", $nested);
            }
        }
    }
}