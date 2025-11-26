<?php 

namespace Axproo\LangManager;

class LangReader
{
    protected string $baseLocale = 'en';

    public function loadBaseMessages(string $module) : array {
        $path = "./src/Language/{$this->baseLocale}/{$module}.php";

        if (!file_exists($path)) {
            return [];
        }

        $data = include $path;
        return Helpers::flattenArray($data);
    }
}