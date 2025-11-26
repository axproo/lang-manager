<?php 

namespace Axproo\LangManager;

class LangManager000
{
    private string $projectDir;
    private string $outputDir;
    private array $locales;

    public function __construct(string $projectDir, string $outputDir, array $locales = ['fr','en']) {
        $this->projectDir = rtrim($projectDir, '/');
        $this->outputDir = rtrim($outputDir, '/');
        $this->locales = $locales;
    }

    /**
     * Génère le fichier de langue pour tout le projet et les librairies
     *
     * @return void
     */
    public function generate() : void {
        $langData = $this->scanProject($this->projectDir);

        foreach ($this->locales as $locale) {
            $localeDir = $this->outputDir . '/' . $locale;
            if (!is_dir($localeDir)) mkdir($localeDir, 0777, true);

            foreach ($langData as $module => $keys) {
                $nestedArray = $this->buildNestedArray($keys);

                // Fusionner avec les traductions existantes
                $filepath = $localeDir . '/' . $module . '.php';
                $existing = file_exists($filepath) ? include $filepath : [];
                $finalArray = $this->mergeTranslations($existing, $nestedArray);

                // Formater et écrire
                $formattedArray = $this->formatArray($finalArray);
                $content = "<?php\n\nreturn " . $formattedArray .";\n>";
                file_put_contents($filepath, $content);
            }
        }
    }

    /**
     * Scan récursif du projet pour récupérer toutes les clés lang()
     *
     * @param string $dir
     * @return array
     */
    private function scanProject(string $dir) : array {
        $langData = [];
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));

        foreach ($rii as $file) {
            if (!$file->isDir() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                if (strpos($file->getPathname(), 'Language') !== false) continue;

                $content = file_get_contents($file);
                preg_match_all("/lang\(['\"](.*?)['\"]\)/", $content, $matches);

                foreach ($matches[1] as $fullKey) {
                    $parts = explode('.', $fullKey);

                    if (count($parts) >= 2) {
                        $module = array_shift($parts);
                        $subkeys = implode('.', $parts);
                        $langData[$module][$subkeys] = $subkeys;
                    }
                }
            }
        }
        return $langData;
    }

    /**
     * Transforme un tableau plat en tableau imbriqué
     *
     * @param array $flatArray
     * @return array
     */
    private function buildNestedArray(array $flatArray) : array {
        $result = [];

        foreach ($flatArray as $fullKey => $value) {
            $keys = explode('.', $fullKey);
            $temp = &$result;

            foreach ($keys as $key) {
                if (!isset($temp[$key])) $temp[$key] = [];
                $temp = &$temp[$key];
            }
            $temp = $value;
            unset($temp);
        }
        return $result;
    }

    /**
     * Fusionne deux tableaux imbriqués, en gardant les traductions existantes
     *
     * @param array $existing
     * @param array $new
     * @return array
     */
    private function mergeTranslations(array $existing, array $new) : array {
        foreach ($new as $key => $value) {
            if (is_array($value)) {
                if (!isset($existing[$key]) || !is_array($existing[$key])) $existing[$key] = [];
                $existing[$key] = $this->mergeTranslations($existing[$key], $value);
            } else {
                if (!isset($existing[$key])) $existing[$key] = $value;
            }
        }
        return $existing;
    }

    /**
     * Formatte un tableau PHP en texte avec syntaxe []
     * @param array $array
     * @param int $level
     * @return string
     */
    private function formatArray(array $array, int $level = 0) : string {
        $indent = str_repeat('    ', $level);
        $output = "[\n";

        foreach ($array as $key => $value) {
            $output .= $indent . '    ' . "'" . addslashes($key) . "' => ";

            if (is_array($value)) {
                $output .= $this->formatArray($value, $level + 1);
            } else {
                $output .= "'" . addslashes($value) . "'";
            }

            // Ajout de la virgule systématique (PHP accepte la virgule finale)
            $output .= ",";
            $output .= "\n";
        }

        $output .= $indent . "]";

        return $output;
    }
}