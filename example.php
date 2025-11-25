<?php 

/**
 * Si vous lancer les test à partir de composer, vous pouvez faire ceci:
 * require __DIR__ '/vendor/autoload.php';
 * composer dump-autoload
 * en suite lancer dans votre CLI: php exampe.php
 */
require 'Axproo/LangManager/src/LangManager.php';

use Axproo\LangManager\LangManager;

// Projet principal + sortie Language/

$project    = ''; // Le projet à scanner
$target     = ''; // le répertoire cible
$locales    = []; // langues du projet à générer ['fr','en']

$manager = new LangManager($project, $target, $locales);
$manager->generate();

echo "Fichiers de langue générés avec succès !\n";