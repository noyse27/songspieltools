<?php
/**
 * Ajax-Endpunkt für die Songdice-Anwendung
 * 
 * Verarbeitet alle Ajax-Anfragen und leitet sie an den AjaxHandler weiter.
 */

// Fehlerberichterstattung für die Entwicklung (kann in Produktion deaktiviert werden)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Erforderliche Dateien einbinden
require_once 'includes/Config.php';
require_once 'includes/Language.php';
require_once 'includes/Dice.php';
require_once 'includes/AjaxHandler.php';

// Ajax-Anfrage verarbeiten
AjaxHandler::handleRequest();
