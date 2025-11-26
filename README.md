# Axproo LangManager

LangManager est une librairie PHP pour gérer la traduction et la génération des fichiers de langue dans vos projets.  
Elle scanne automatiquement votre code à la recherche des clés **`lang('module.key')`**, met à jour les fichiers de langue existants, ajoute les nouvelles clés avec un placeholder, et supprime les clés non utilisées.

## Fonctionnalités

- 📂 **Scan automatique** : Parcourt tous les fichiers PHP de votre projet pour détecter les clés `lang('module.key')`.
- 🌐 **Gestion multilingue** : Génère et met à jour les fichiers pour plusieurs langues (`fr`, `en`, etc.).
- 🆕 **Ajout automatique des nouvelles clés** avec placeholder `__TRANSLATE__`.
- 🧹 **Nettoyage des clés obsolètes** : Supprime les clés non utilisées dans le projet.
- 📝 **Rapport CLI** : Affiche les clés en attente de traduction.
- 🔄 **Réutilisable** : Peut être utilisé dans n’importe quel projet PHP ou librairie.

## 📦 Installation

```bash
composer require axproo/lang-manager
```

## Structure du projet

```css
Axproo/LangManager
├── src/
│   ├── LangManager.php
│   ├── Scanner.php
│   ├── FileGenerator.php
│   ├── DictionaryLoader.php
│   ├── Helpers.php
│   └── LangReporter.php
├── dictionaries/
│   ├── en-fr.php
│   └── en-en.php
└── vendor/
```

## Exemple d’utilisation

Dans votre projet :

Si vous lancer les test à partir de composer, vous pouvez faire ceci:

```bash
composer dump-autoload
```

Créer un fichier nommé example.php à la racine de votre projet,
et entré le code ci-dessous

```php
require __DIR__ . '/vendor/autoload.php';

use LangManager\LangManager;

$projectDir = __DIR__ . '/src';
$outputDir = __DIR__ . '/src/Language';
$locales = ['fr', 'en', 'es'];

$langManager = new LangManager();
$langManager->run($projectDir, $outputDir, $locales);
```

en suite lancer dans votre CLI:

```bash
php exampe.php
```

## Explication

- Les clés nouvelles sont ajoutées automatiquement dans les fichiers de langue avec le placeholder __TRANSLATE__.
- Les anciennes clés non utilisées sont supprimées du dictionnaire et des fichiers de langue.
- Les traductions existantes sont conservées si elles ne contiennent pas le placeholder.
- Le rapport CLI affiche toutes les clés encore à traduire.

## Fichiers de dictionnaire

Exemple dictionaries/en-fr.php :

```php
<?php
return [
    'login.success' => 'Connexion réussie',
    'login.unauthorized' => 'Accès non autorisé',
];
```

Exemple dictionaries/en-en.php :

```php
<?php
return [
    'login.success' => 'Login successful',
    'login.unauthorized' => 'Unauthorized access',
];
```

## Exemple de fichiers générés

```css
src/
└── Language/
    ├── fr/
    │   ├── Auth.php
    │   ├── Users.php
    │   └── Token.php
    └── en/
        ├── Auth.php
        ├── Users.php
        └── Token.php
```

Chaque clé trouvée est automatiquement placée dans le bon module.

## Contribution

Les contributions sont les bienvenues !
Pour ajouter une nouvelle langue, créez simplement un fichier en-xx.php dans le dossier dictionaries et exécutez LangManager.

Vous pouvez aussi générer un fichier de langue en-xx.php en le spécifiant dans locales lors de la création de langues :

```php
$locales = ['fr', 'en', 'es', 'de'];
```

La librairie générera automatiquement les fichiers nécessaires.

## 📄 Licence

MIT License – Vous pouvez utiliser cette librairie librement dans vos projets.

## 👨‍💻 Auteur

Développé par **Christian Djomou**.
Pour toute contribution, suggestion ou amélioration, ouvrez une issue ou un pull request.
