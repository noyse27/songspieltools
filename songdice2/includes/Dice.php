<?php
/**
 * Dice Class
 *
 * Verwaltet die Würfel-Funktionalität für die Songdice-Anwendung.
 * Generiert zufällige Werte für Kategorien, Farben und Editionen.
 */
class Dice {
    /**
     * Farbcodes je interner Farb-ID (sprachunabhängig)
     */
    private static $colorCodes = [
        'yellow' => "#FFFF00",
        'green' => "#008000",
        'purple' => "#800080",
        'lightblue' => "#ADD8E6",
        'pink' => "#FFC0CB"
    ];

    /**
     * Icons je interner Kategorie-ID (sprachunabhängig), angelehnt an die
     * Symbole auf den Original-Hitster-Spielplänen.
     */
    private static $categoryIcons = [
        'group_solo' => "👥",
        'before2000' => "2000?",
        'pm4' => "4️⃣🎵",
        'decade' => "0's",
        'pm2' => "2️⃣🎵",
        'song_title' => "🎵",
        'exact_year' => "🎯",
        'artist' => "🎤",
        'pm3' => "3️⃣🎵"
    ];

    /**
     * Gibt die ID einer zufälligen aktiven Kategorie zurück
     *
     * @return string Die ID einer zufälligen aktiven Kategorie oder "" wenn keine aktiv ist.
     */
    public static function getRandomCategoryId() {
        $activeCategories = Config::getActiveCategories();

        if (empty($activeCategories)) {
            return "";
        }

        $randomIndex = array_rand($activeCategories);
        return $activeCategories[$randomIndex];
    }

    /**
     * Gibt die ID einer zufälligen Farbe zurück
     *
     * @return string Die ID einer zufälligen Farbe
     */
    public static function getRandomColorId() {
        $colorIds = array_keys(self::$colorCodes);
        $randomIndex = array_rand($colorIds);
        return $colorIds[$randomIndex];
    }

    /**
     * Gibt die ID einer zufälligen aktiven Edition zurück
     *
     * @return string Die ID einer zufälligen aktiven Edition oder "" wenn keine aktiv ist.
     */
    public static function getRandomEditionId() {
        $activeEditions = Config::getActiveEditions();

        if (empty($activeEditions)) {
            return "";
        }

        $randomIndex = array_rand($activeEditions);
        return $activeEditions[$randomIndex];
    }

    /**
     * Gibt den Hinweis für eine Kategorie zurück
     *
     * @param string $categoryId Die interne ID der Kategorie
     * @return string Der Hinweis für die Kategorie
     */
    public static function getHint($categoryId) {
        if ($categoryId === "") {
            return "";
        }
        return Language::getHint($categoryId);
    }

    /**
     * Generiert einen neuen Würfelwurf
     *
     * @return array Assoziatives Array mit den übersetzten Würfelergebnissen
     */
    public static function roll() {
        $categoryId = self::getRandomCategoryId();
        $colorId = self::getRandomColorId();
        $editionId = self::getRandomEditionId();

        $categories = Language::getCategories();
        $colors = Language::getColors();
        $editions = Language::getEditions();

        return [
            'category' => $categoryId !== "" ? ($categories[$categoryId] ?? "---") : "---",
            'categoryIcon' => $categoryId !== "" ? (self::$categoryIcons[$categoryId] ?? "❓") : "❓",
            'color' => [
                'name' => $colors[$colorId] ?? "",
                'code' => self::$colorCodes[$colorId] ?? "#fdfdfd"
            ],
            'edition' => $editionId !== "" ? ($editions[$editionId] ?? "---") : "---",
            'hint' => self::getHint($categoryId)
        ];
    }

    /**
     * Gibt die Icons aller Kategorien zurück, in derselben Reihenfolge wie
     * Language::getCategories() (für die clientseitige Rotationsanimation)
     *
     * @return array Liste von Icon-Strings
     */
    public static function getAllCategoryIcons() {
        $icons = [];

        foreach (Language::getCategories() as $id => $name) {
            $icons[] = self::$categoryIcons[$id] ?? "❓";
        }

        return $icons;
    }

    /**
     * Gibt alle verfügbaren Farben für die aktuelle Sprache zurück
     *
     * @return array Liste mit Name und Farbcode je Farbe
     */
    public static function getAllColors() {
        $colors = [];

        foreach (Language::getColors() as $id => $name) {
            $colors[] = [
                'name' => $name,
                'code' => self::$colorCodes[$id] ?? "#fdfdfd"
            ];
        }

        return $colors;
    }
}
