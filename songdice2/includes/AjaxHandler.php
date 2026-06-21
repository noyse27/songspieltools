<?php
/**
 * Ajax Handler Class
 * 
 * Verarbeitet Ajax-Anfragen für die dynamische Funktionalität der Songdice-Anwendung.
 */
class AjaxHandler {
    /**
     * Verarbeitet eingehende Ajax-Anfragen
     */
    public static function handleRequest() {
        // Sicherstellen, dass es sich um eine Ajax-Anfrage handelt
        if (!isset($_SERVER["HTTP_X_REQUESTED_WITH"]) || strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) !== "xmlhttprequest") {
            self::sendResponse(["error" => "Nur Ajax-Anfragen sind erlaubt"], 403);
            return;
        }
        
        // Sicherstellen, dass die Aktion angegeben wurde
        if (!isset($_POST["action"])) {
            self::sendResponse(["error" => "Keine Aktion angegeben"], 400);
            return;
        }
        
        // Erforderliche Dateien einbinden
        require_once "includes/Config.php";
        require_once "includes/Language.php";
        require_once "includes/Dice.php";
        
        // Konfiguration initialisieren (lädt aus Cookie)
        Config::init();
        
        // Aktion verarbeiten
        $action = $_POST["action"];
        
        switch ($action) {
            case "roll":
                self::handleRoll();
                break;
                
            case "updateConfig":
                // Diese Aktion wird jetzt über das normale Formular in index.php gehandhabt
                // und nicht mehr über AJAX, um das Neuladen der Seite für die Cookie-Aktualisierung zu erzwingen.
                // Falls doch benötigt, müsste es angepasst werden, um die Seite neu zu laden.
                self::sendResponse(["error" => "Konfigurationsaktualisierung erfolgt jetzt über Formular-Submit."], 400);
                break;
                
            case "getHint":
                self::handleGetHint();
                break;
                
            default:
                self::sendResponse(["error" => "Unbekannte Aktion"], 400);
                break;
        }
    }
    
    /**
     * Verarbeitet die Würfel-Aktion
     */
    private static function handleRoll() {
        // Prüfen, ob genügend Kategorien und Editionen ausgewählt sind
        $activeCategories = Config::getActiveCategories();
        $activeEditions = Config::getActiveEditions();
        
        if (empty($activeCategories) || empty($activeEditions)) {
            $errorMessage = Language::getUI("errorNoSelection");
            self::sendResponse(["error" => $errorMessage], 400);
            return;
        }
        
        $result = Dice::roll();
        self::sendResponse($result);
    }
    
    /**
     * Verarbeitet die Anfrage nach einem Hinweis für eine Kategorie
     */
    private static function handleGetHint() {
        if (!isset($_POST["category"])) {
            self::sendResponse(["error" => "Keine Kategorie angegeben"], 400);
            return;
        }

        // $_POST["category"] ist die interne Kategorie-ID (siehe Language::getCategories())
        $categoryId = $_POST["category"];
        $hint = Dice::getHint($categoryId);

        self::sendResponse(["hint" => $hint]);
    }
    
    /**
     * Sendet eine JSON-Antwort an den Client
     * 
     * @param array $data Die zu sendenden Daten
     * @param int $statusCode Der HTTP-Statuscode
     */
    private static function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header("Content-Type: application/json");
        echo json_encode($data);
        exit;
    }
}
