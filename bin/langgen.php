#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Axproo\LangManager\LangManager;

// Adjust paths as needed
$scanDirs = [
    __DIR__ . '/../src',
    __DIR__ . '/../vendor'
];

$outputDir = __DIR__ . '/../src/Language';
$locales = ['fr','en'];

$manager = new LangManager($scanDirs, $outputDir, $locales);
echo "Scanning...\n";
$manager->generate();
echo "Language files generated.\n";
