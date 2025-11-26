<?php 

namespace Axproo\LangManager;

class LangManager
{
    public function run(string $projectDir, string $outputDir, array $locales) : void {
        
        $scanner = new Scanner();
        $generator = new FileGenerator();
        $dictionary = new DictionaryLoader();

        $langData = $scanner->scan($projectDir);

        foreach ($locales as $locale) {
            foreach ($langData as $module => $keys) {
                
                $translated = [];

                foreach ($keys as $k => $v) {
                    $translated[$k] = ($locale === 'fr')
                        ? $dictionary->translate($v)
                        : $v;
                }

                $nested = Helpers::buildNestedArray($translated);
                $generator->save("$outputDir/$locale/$module", $nested);
            }
        }
    }
}