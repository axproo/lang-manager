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

        // Liste global des clés utilisés (ex: Auth.login.success)
        $usedKeys = [];
        foreach ($langData as $module => $keys) {
            foreach ($keys as $k => $v) {
                $usedKeys[$k] = $v;
            }
        }

        // Mise à jour du dictionnaires internes
        foreach ($locales as $locale) {
            foreach ($usedKeys as $key) {
                // Ajout automatique au dictionnaire si la clé n'existe pas
                if (!$dictionary->exists($key, $locale)) {
                    $dictionary->add($key, $locale, "__TRANSLATE__");
                }
            }

            // Nettoyage du dictionnaire: supprimer les clés non utilisés
            $dictionary->clean($usedKeys, $locale);

            // Sauvegarder modifications dictionnaire
            $dictionary->save($locale);
        }

        // Gnération des fichiers de langue
        foreach ($locales as $locale) {

            $localDir = rtrim($outputDir, '/') . '/' . $locale;

            // Création automatique du dossier si nécessaire
            if (!is_dir($localDir)) {
                mkdir($localDir, 0755, true);
                echo "Dossier {$localDir} créer avec succès";
            }

            foreach ($langData as $module => $keys) {

                $filePath = "$localDir/$module.php";

                // Charger le fichier existant
                $existing = [];
                if (file_exists($filePath)) {
                    $existing = include $filePath;
                    $existing = Helpers::flattenArray($existing);
                }

                $translated = $existing; // On part des valeurs existantes

                foreach ($keys as $k => $v) {
                    $existingValue = $existing[$k] ?? null;

                    // Traduction réelle basée sur la *valeur*, pas la *clé*
                    $dictTranslation = $dictionary->translate($k, $locale);

                    if ($dictTranslation !== $k) {
                        // La traduction existe dans le dictionnaire → l'utiliser
                        $translated[$k] = $dictTranslation;
                    } elseif ($existingValue && strpos($existingValue, '__TRANSLATE__') === false) {
                        // Valeur manuelle existante → conserver
                        $translated[$k] = $existingValue;
                    } else {
                        // Sinon → mettre placeholder
                        $translated[$k] = "__TRANSLATE__$k";
                    }
                }

                // S'assurer qu'on ne garde que les clés utilisées
                $translated = array_intersect_key($translated, array_flip($keys));

                // Générer le tableau hiérarchique et sauvegarder
                $nested = Helpers::buildNestedArray($translated);
                $generator->save("$localDir/$module", $nested);
            }
        }

        // Afficher le rapport des clés à traduire
        LangReporter::shwoPendingTranslation($outputDir, $locales);
    }
}