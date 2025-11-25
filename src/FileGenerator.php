<?php 

namespace Axproo\LangManager;

class FileGenerator
{
    public function generateFile(string $outputPath, array $data) : void {
        $formattedArray = $this->formatArray($data);
        $content = "<?php\n\nreturn " . $formattedArray .";\n";

        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0777, true);
        }

        file_put_contents($outputPath, $content);
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
            $output .= ",\n";
        }

        $output .= $indent . "]";

        return $output;
    }
}