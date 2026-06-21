<?php
/**
 * Language Class
 *
 * Verwaltet die Übersetzungen und Spracheinstellungen für die Songdice-Anwendung.
 * Kategorien, Editionen und Farben werden über stabile interne IDs referenziert,
 * damit die Auswahl des Nutzers unabhängig von der Anzeigesprache bleibt.
 */
class Language {
    /**
     * Übersetzungsdaten für Deutsch
     */
    private static $translations_de = [
        'categories' => [
            'group_solo' => "Gruppe / Solo",
            'pm3' => "3 vor / 3 nach",
            'exact_year' => "genaues Jahr",
            'song_title' => "Songtitel",
            'artist' => "Interpret",
            'decade' => "Welche Dekade",
            'pm4' => "4 vor / 4 nach",
            'pm2' => "2 vor / 2 nach",
            'before2000' => "vor 2000?"
        ],
        'editions' => [
            'hitster_bingo' => "Hitster Bingo",
            'original' => "Original Ausgabe",
            'summer_hits' => "Summer Hits",
            'guilty_pleasures' => "Guilty Pleasures",
            'schlager' => "Schlager",
            'bayern1' => "Bayern 1 Edition",
            'soundtrack' => "Soundtrack"
        ],
        'colors' => [
            'yellow' => "Gelb",
            'green' => "Grün",
            'purple' => "Lila",
            'lightblue' => "Hellblau",
            'pink' => "Pink"
        ],
        'hints' => [
            'group_solo' => "Ist der Interpret eine Band oder ein Solokünstler? (Features gelten als Gruppe)",
            'pm3' => "In welchem Jahr wurde der Song veröffentlicht? (Bis 3 Jahre vor oder nach dem getippten Jahr gelten als richtig)",
            'exact_year' => "In welchem Jahr wurde der Song veröffentlicht? (Das genaue Jahr muss getippt werden)",
            'song_title' => "Wie heißt der Song? (Die genaue Schreibweise ist notwendig)",
            'artist' => "Wer ist der Interpret des Songs? (Bei mehreren Interpreten gilt der Hauptinterpret)",
            'decade' => "In welcher Dekade wurde der Song veröffentlicht?",
            'pm4' => "In welchem Jahr wurde der Song veröffentlicht? (Bis 4 Jahre vor oder nach dem getippten Jahr gelten als richtig)",
            'pm2' => "In welchem Jahr wurde der Song veröffentlicht? (Bis 2 Jahre vor oder nach dem getippten Jahr gelten als richtig)",
            'before2000' => "Entstand der Song vor 2000? (Es gilt ja oder nein)"
        ],
        'ui' => [
            'startButton' => "Start",
            'configButton' => "Konfiguration öffnen",
            'h4Conf' => "Konfiguration",
            'h5Lang' => "Sprache",
            'h5Colorpad' => "Farbfeld",
            'h5Playmode' => "Spielmodus",
            'h5Edition' => "Edition",
            'lblColorpad' => "Farbfeld aktivieren",
            'h1Dicehead' => "Würfel für Hitster Bingo",
            'errorNoSelection' => "Bitte wähle mindestens einen Begriff und eine Edition aus!",
            'screenActive' => "Bildschirm aktiv halten",
            'cookieNotice' => "Diese Anwendung verwendet Cookies, um deine Einstellungen zu speichern.",
            'noHint' => "Kein Hinweis verfügbar.",
            'bingoTitle' => "HITSTER BINGO",
            'newCardButton' => "Neue BingoCard erzeugen",
            'bingoOverlayText' => "BINGO!",
            'confirmNewCardText' => "Neue Karte erzeugen?",
            'confirmYes' => "Ja",
            'confirmNo' => "Abbrechen",
            'toggleToBingo' => "🎯 Bingo-Karte",
            'toggleToDice' => "🎲 Würfel"
        ]
    ];

    /**
     * Übersetzungsdaten für Englisch
     */
    private static $translations_en = [
        'categories' => [
            'group_solo' => "Group / Artist",
            'pm3' => "3 before / 3 after",
            'exact_year' => "Exact Year",
            'song_title' => "Song Title",
            'artist' => "Artist",
            'decade' => "Which Decade",
            'pm4' => "4 before / 4 after",
            'pm2' => "2 before / 2 after",
            'before2000' => "before 2000?"
        ],
        'editions' => [
            'hitster_bingo' => "Hitster Bingo",
            'original' => "Original Version",
            'summer_hits' => "Summer Hits",
            'guilty_pleasures' => "Guilty Pleasures",
            'schlager' => "Schlager",
            'bayern1' => "Bavaria 1 Edition",
            'soundtrack' => "Soundtrack"
        ],
        'colors' => [
            'yellow' => "Yellow",
            'green' => "Green",
            'purple' => "Purple",
            'lightblue' => "Light Blue",
            'pink' => "Pink"
        ],
        'hints' => [
            'group_solo' => "Is the artist a band or a solo artist? (Features count as a group)",
            'pm3' => "In which year was the song released? (Up to 3 years before or after the guessed year count as correct)",
            'exact_year' => "In which year was the song released? (The exact year must be guessed)",
            'song_title' => "What is the name of the song? (Exact spelling is required)",
            'artist' => "Who is the artist of the song? (The main artist counts in case of multiple artists)",
            'decade' => "In which decade was the song released?",
            'pm4' => "In which year was the song released? (Up to 4 years before or after the guessed year count as correct)",
            'pm2' => "In which year was the song released? (Up to 2 years before or after the guessed year count as correct)",
            'before2000' => "Was the song created before 2000? (The answer is yes or no)"
        ],
        'ui' => [
            'startButton' => "Start",
            'configButton' => "Open Configuration",
            'h4Conf' => "Configuration",
            'h5Lang' => "Language",
            'h5Colorpad' => "Colorpad",
            'h5Playmode' => "Playmode",
            'h5Edition' => "Edition",
            'lblColorpad' => "Enable Colorpad",
            'h1Dicehead' => "Dice for Hitster Bingo",
            'errorNoSelection' => "Please select at least one category and one edition!",
            'screenActive' => "Keep screen active",
            'cookieNotice' => "This application uses cookies to store your settings.",
            'noHint' => "No hint available.",
            'bingoTitle' => "HITSTER BINGO",
            'newCardButton' => "Generate new card",
            'bingoOverlayText' => "BINGO!",
            'confirmNewCardText' => "Generate a new card?",
            'confirmYes' => "Yes",
            'confirmNo' => "Cancel",
            'toggleToBingo' => "🎯 Bingo Card",
            'toggleToDice' => "🎲 Dice"
        ]
    ];

    /**
     * Gibt die Übersetzung für einen bestimmten Schlüssel zurück
     *
     * @param string $section Der Abschnitt der Übersetzung (categories, editions, colors, hints, ui)
     * @param string $key Der Schlüssel (interne ID) innerhalb des Abschnitts
     * @return string|array Die Übersetzung oder ein Array von Übersetzungen (ID => Text)
     */
    public static function get($section, $key = null) {
        $lang = Config::getLanguage();
        $translations = ($lang === 'de') ? self::$translations_de : self::$translations_en;

        if (!isset($translations[$section])) {
            return ($key === null) ? [] : '';
        }

        if ($key === null) {
            return $translations[$section];
        }

        return isset($translations[$section][$key]) ? $translations[$section][$key] : '';
    }

    /**
     * Gibt alle Kategorien für die aktuelle Sprache zurück
     *
     * @return array Assoziatives Array ID => Anzeigename
     */
    public static function getCategories() {
        return self::get('categories');
    }

    /**
     * Gibt alle Editionen für die aktuelle Sprache zurück
     *
     * @return array Assoziatives Array ID => Anzeigename
     */
    public static function getEditions() {
        return self::get('editions');
    }

    /**
     * Gibt alle Farben für die aktuelle Sprache zurück
     *
     * @return array Assoziatives Array ID => Anzeigename
     */
    public static function getColors() {
        return self::get('colors');
    }

    /**
     * Gibt den Hinweis für eine bestimmte Kategorie zurück
     *
     * @param string $categoryId Die interne ID der Kategorie
     * @return string Der Hinweis für die Kategorie
     */
    public static function getHint($categoryId) {
        $hint = self::get('hints', $categoryId);
        return $hint !== '' ? $hint : self::getUI('noHint');
    }

    /**
     * Gibt einen UI-Text zurück
     *
     * @param string $key Der Schlüssel des UI-Texts
     * @return string Der UI-Text
     */
    public static function getUI($key) {
        return self::get('ui', $key);
    }
}
