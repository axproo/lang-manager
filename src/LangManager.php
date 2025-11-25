<?php

namespace Axproo\LangManager;

use Axproo\LangManager\Strategies\LocalDictionaryStrategy;
use Axproo\LangManager\Strategies\OnlineApiStrategy;
use Axproo\LangManager\Strategies\HybridStrategy;

class LangManager
{
    protected array $scanDirs = [];
    protected string $outputDir;
    protected array $locales = ['fr','en'];
    protected FileGenerator $generator;
    protected string $dictionaryPath;

    public function __construct(array $scanDirs, string $outputDir, array $locales = ['fr','en'], string $dictionaryPath = __DIR__ . '/../dictionaries')
    {
        $this->scanDirs = $scanDirs;
        $this->outputDir = rtrim($outputDir, DIRECTORY_SEPARATOR);
        $this->locales = $locales;
        $this->generator = new FileGenerator();
        $this->dictionaryPath = $dictionaryPath;
    }

    public function generate(bool $autoTranslate = false): void
    {
        $langData = [];
        foreach ($this->scanDirs as $dir) {
            if (!is_dir($dir)) continue;
            $langData = $this->mergeTranslations($langData, $this->scanProject($dir));
        }

        // prepare translator strategy (hybrid) if required
        $dictLoader = new DictionaryLoader($this->dictionaryPath, 'en', 'fr');
        $localDict = new LocalDictionaryStrategy($dictLoader->getAll());
        $online = new OnlineApiStrategy('none', []);
        $hybrid = new HybridStrategy($localDict, $online, $this->dictionaryPath . '/en-fr.generated.php');

        foreach ($this->locales as $locale) {
            $localeDir = $this->outputDir . DIRECTORY_SEPARATOR . $locale;
            if (!is_dir($localeDir)) mkdir($localeDir, 0777, true);

            foreach ($langData as $module => $keys) {
                $nested = $this->buildNestedArray($keys);

                // For each locale, optionally translate values or keep key as placeholder
                $final = [];
                foreach ($nested as $k => $v) {
                    $final[$k] = $this->walkAndTranslate($v, $module, $hybrid, $locale, $autoTranslate);
                }

                $filepath = $localeDir . DIRECTORY_SEPARATOR . $module . '.php';
                $existing = file_exists($filepath) ? require $filepath : [];
                $merged = $this->mergeTranslations($existing, $final);

                $this->generator->generateFile($filepath, $merged);
            }
        }
    }

    protected function walkAndTranslate($node, $module, HybridStrategy $hybrid, $locale, $autoTranslate)
    {
        if (is_array($node)) {
            $out = [];
            foreach ($node as $k => $v) {
                $out[$k] = $this->walkAndTranslate($v, $module, $hybrid, $locale, $autoTranslate);
            }
            return $out;
        }

        // node is a string subkey path like 'failed.email.required'
        $fullKey = $module . '.' . $node;
        if ($autoTranslate) {
            $translated = $hybrid->translate($fullKey, $node, $locale);
            return $translated ?? $node;
        }

        // default placeholder: keep node (you can change to fullKey if desired)
        return $node;
    }

    protected function scanProject(string $path): array
    {
        $langData = [];
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

        foreach ($rii as $file) {
            if ($file->isDir()) continue;
            if (pathinfo($file->getPathname(), PATHINFO_EXTENSION) !== 'php') continue;
            if (strpos($file->getPathname(), 'Language') !== false) continue;

            $content = @file_get_contents($file->getPathname());
            if ($content === false) continue;

            if (preg_match_all("/lang\(['\"](.*?)['\"]\)/", $content, $matches)) {
                foreach ($matches[1] as $fullKey) {
                    $parts = explode('.', $fullKey);
                    if (count($parts) >= 2) {
                        $module = array_shift($parts);
                        $sub = implode('.', $parts);
                        $langData[$module][$sub] = $sub;
                    }
                }
            }
        }
        return $langData;
    }

    protected function buildNestedArray(array $flat): array
    {
        $res = [];
        foreach ($flat as $fullKey => $value) {
            $keys = explode('.', $fullKey);
            $temp = &$res;
            foreach ($keys as $k) {
                if (!isset($temp[$k])) $temp[$k] = [];
                $temp = &$temp[$k];
            }
            $temp = $value;
            unset($temp);
        }
        return $res;
    }

    protected function mergeTranslations(array $existing, array $new): array
    {
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
}