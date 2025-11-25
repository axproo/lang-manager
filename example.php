<?php 

require 'Axproo/LangManager/src/LangManager.php';

use Axproo\LangManager\LangManager;

// Projet principal + sortie Language/

$project    = ''; // Le projet à scanner
$target     = ''; // le répertoire cible
$locales    = []; // langues du projet à générer

$manager = new LangManager($project, $target, $locales);
$manager->generate();

echo "Fichiers de langue générés avec succès !\n";