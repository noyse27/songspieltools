<?php
/**
 * Config Class
 *
 * Verwaltet die Konfigurationseinstellungen für die Songdice-Anwendung.
 * Speichert und lädt Benutzereinstellungen über einen einzigen Cookie.
 *
 * Kategorien und Editionen werden über sprachunabhängige interne IDs
 * gespeichert (siehe Language::getCategories() / getEditions()), damit
 * die Auswahl bei einem Sprachwechsel erhalten bleibt.
 */
class Config {
    // Cookie-Lebensdauer: 30 Tage
    const COOKIE_LIFETIME = 2592000; // 30 Tage in Sekunden
    const COOKIE_NAME = 'songdice_config';

    // Version des Konfigurationsformats. Bei Erhöhung wird ein
    // gespeichertes Cookie in einem alten Format verworfen und auf
    // die Standardwerte zurückgesetzt.
    const CONFIG_VERSION = 2;

    private static $config = [];

    /**
     * Initialisiert die Konfiguration und lädt Cookies
     */
    public static function init() {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            $decodedConfig = json_decode($_COOKIE[self::COOKIE_NAME], true);
            if (is_array($decodedConfig)) {
                self::$config = $decodedConfig;
            }
        }

        // Wenn keine Konfiguration geladen wurde, sie ungültig ist oder aus
        // einem alten Format stammt, Standardwerte setzen
        if (
            empty(self::$config)
            || !isset(self::$config['initialized'])
            || !isset(self::$config['version'])
            || self::$config['version'] !== self::CONFIG_VERSION
        ) {
            self::resetToDefaults();
        }
    }

    /**
     * Setzt die Konfiguration auf Standardwerte zurück
     */
    public static function resetToDefaults() {
        self::$config = [
            'initialized' => true,
            'version' => self::CONFIG_VERSION,
            'language' => 'de',
            'show_colors' => true,
            'categories' => array_fill_keys(array_keys(Language::get('categories')), true),
            'editions' => array_fill_keys(array_keys(Language::get('editions')), true)
        ];
        self::saveConfig();
    }

    /**
     * Speichert die aktuelle Konfiguration in einem Cookie
     */
    private static function saveConfig() {
        $jsonConfig = json_encode(self::$config);
        $options = [
            'expires' => time() + self::COOKIE_LIFETIME,
            'path' => '/',
            'samesite' => 'Lax' // SameSite Attribut für Sicherheit
        ];

        // Verwende die moderne Syntax für setcookie, wenn verfügbar (PHP >= 7.3)
        if (PHP_VERSION_ID >= 70300) {
            setcookie(self::COOKIE_NAME, $jsonConfig, $options);
        } else {
            // Fallback für ältere PHP-Versionen
            setcookie(
                self::COOKIE_NAME,
                $jsonConfig,
                $options['expires'],
                $options['path'],
                '',      // domain
                false,   // secure
                true     // httponly
            );
        }
    }

    /**
     * Gibt die aktuelle Sprache zurück
     *
     * @return string Die aktuelle Sprache (de oder en)
     */
    public static function getLanguage() {
        return isset(self::$config['language']) ? self::$config['language'] : 'de';
    }

    /**
     * Setzt die aktuelle Sprache
     *
     * @param string $language Die zu setzende Sprache (de oder en)
     */
    public static function setLanguage($language) {
        if ($language === 'de' || $language === 'en') {
            self::$config['language'] = $language;
            self::saveConfig();
        }
    }

    /**
     * Prüft, ob Farben angezeigt werden sollen
     *
     * @return bool True, wenn Farben angezeigt werden sollen
     */
    public static function showColors() {
        return isset(self::$config['show_colors']) ? (bool)self::$config['show_colors'] : true;
    }

    /**
     * Setzt, ob Farben angezeigt werden sollen
     *
     * @param bool $show True, wenn Farben angezeigt werden sollen
     */
    public static function setShowColors($show) {
        self::$config['show_colors'] = (bool)$show;
        self::saveConfig();
    }

    /**
     * Gibt die IDs aller aktiven Kategorien zurück
     *
     * @return array Liste der aktiven Kategorie-IDs
     */
    public static function getActiveCategories() {
        return array_keys(array_filter(self::$config['categories'] ?? []));
    }

    /**
     * Gibt alle Kategorien zurück (ID => aktiv?)
     *
     * @return array Assoziatives Array mit Kategorie-IDs und ihrem Status
     */
    public static function getAllCategories() {
        return self::$config['categories'] ?? [];
    }

    /**
     * Gibt die IDs aller aktiven Editionen zurück
     *
     * @return array Liste der aktiven Edition-IDs
     */
    public static function getActiveEditions() {
        return array_keys(array_filter(self::$config['editions'] ?? []));
    }

    /**
     * Gibt alle Editionen zurück (ID => aktiv?)
     *
     * @return array Assoziatives Array mit Edition-IDs und ihrem Status
     */
    public static function getAllEditions() {
        return self::$config['editions'] ?? [];
    }

    /**
     * Aktualisiert die Konfiguration basierend auf POST-Daten
     */
    public static function updateFromPost() {
        // Sprache aktualisieren (nur wenn geändert)
        if (isset($_POST['language']) && ($_POST['language'] === 'de' || $_POST['language'] === 'en')) {
            self::$config['language'] = $_POST['language'];
        }

        // Farbanzeige aktualisieren
        self::$config['show_colors'] = isset($_POST['toggle_colors']);

        // Kategorien zurücksetzen und aktualisieren
        foreach (array_keys(self::$config['categories']) as $categoryId) {
            self::$config['categories'][$categoryId] = false; // Erst alle deaktivieren
        }
        if (isset($_POST['categories']) && is_array($_POST['categories'])) {
            foreach ($_POST['categories'] as $categoryId) {
                if (isset(self::$config['categories'][$categoryId])) {
                    self::$config['categories'][$categoryId] = true;
                }
            }
        }

        // Editionen zurücksetzen und aktualisieren
        foreach (array_keys(self::$config['editions']) as $editionId) {
            self::$config['editions'][$editionId] = false; // Erst alle deaktivieren
        }
        if (isset($_POST['editions']) && is_array($_POST['editions'])) {
            foreach ($_POST['editions'] as $editionId) {
                if (isset(self::$config['editions'][$editionId])) {
                    self::$config['editions'][$editionId] = true;
                }
            }
        }

        // Änderungen speichern
        self::saveConfig();
    }
}
