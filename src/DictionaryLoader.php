<?php 

namespace Axproo\LangManager;

class DictionaryLoader
{
    protected array $dict = [];
    protected string $base;
    
    public function __construct() {
        $this->base = __DIR__ . '/dictionaries';

        if (!is_dir($this->base)) {
            mkdir($this->base, 0777, true);
        }

        foreach (glob($this->base . "/*.php") as $file) {

            $name = pathinfo($file, PATHINFO_FILENAME);
            $this->dict[$name] = include $file;
        }
    }

    public function translate(string $key, string $locale = 'fr') : string {
        $map = "en-$locale";

        // Dictionnaire existant ?
        if (isset($this->dict[$map][$key])) {
            return $this->dict[$map][$key];
        }

        // Pas de traduction -> retourne la clé brute
        return $key;
    }

    /**
     * Vérifie si une entrée existe dans le dictionnaire
     * @param string $key
     * @param string $locale
     * @return bool
     */
    public function exists(string $key, string $locale) : bool {
        $map = "en-$locale";

        return isset($this->dict[$map]) && array_key_exists($key, $this->dict[$map]);
    }

    /**
     * Ajouter une nouvelle entrée au dictionnaire et sauvegarde automatique
     * @param string $key
     * @param string $locale
     * @param string $value
     * @return void
     */
    public function add(string $key, string $locale, string $value) : void {
        $map = "en-$locale";

        if (!isset($this->dict[$map])) {
            $this->dict[$map] = [];
        }

        // Ajout ou remplacement
        $this->dict[$map][$key] = $value;

        // Sauvegarde
        $this->save($locale);
    }

    /**
     * Nettoyage des clés non utilisées dans le projet
     * @param array $usedKeys
     * @param string $locale
     * @return void
     */
    public function clean(array $usedKeys, string $locale) : void {
        $map = "en-$locale";

        if (!isset($this->dict[$map])) {
            return;
        }
        $usedKeysAssoc = array_flip($usedKeys);
        $this->dict[$map] = array_intersect_key($this->dict[$map], $usedKeysAssoc);
        
        // Mettre à jour le fichier dictionnaire
        $this->save($locale);
    }

    public function save(string $locale) : void {
        $map = "en-$locale";

        $file = $this->base . "/$map.php";

        if (!isset($this->dict[$map])) {
            $this->dict[$map] = [];
        }
        $content = "<?php\n\nreturn " .var_export($this->dict[$map], true). ";\n";
        file_put_contents($file, $content);
    }
}