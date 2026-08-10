<?php
/**
 * Hauptdatei der Songdice-Anwendung
 *
 * Initialisiert die Anwendung und stellt die Benutzeroberfläche dar.
 */

// Fehlerberichterstattung für die Entwicklung (kann in Produktion deaktiviert werden)
// ini_set("display_errors", 1);
// ini_set("display_startup_errors", 1);
// error_reporting(E_ALL);

// Erforderliche Dateien einbinden
require_once "includes/Config.php";
require_once "includes/Language.php";
require_once "includes/Dice.php";

// Konfiguration initialisieren (lädt aus Cookie)
Config::init();

// POST-Anfragen verarbeiten (für Formular-Übermittlungen zur Konfigurationsänderung)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_config"])) {
    Config::updateFromPost();
    // Umleiten, um Formular-Neuübermittlung zu verhindern und Cookie zu setzen
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;
}

// Aktuelle Sprache und Konfiguration abrufen
$language = Config::getLanguage();
$showColors = Config::showColors();
$categoryLabels = Language::getCategories();
$editionLabels = Language::getEditions();
$allCategories = Config::getAllCategories();
$allEditions = Config::getAllEditions();

// Daten für die clientseitige Rotationsanimation (kein zusätzlicher AJAX-Request nötig)
$animationData = [
    'categories' => array_values($categoryLabels),
    'categoryIcons' => Dice::getAllCategoryIcons(),
    'editions' => array_values($editionLabels),
    'colors' => Dice::getAllColors()
];
?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Condiment&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Caveat&family=Modak&display=swap" rel="stylesheet">
  <title>Hitster Dice</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- Cookie-Hinweis -->
<div id="cookie-notice"><?php echo Language::getUI("cookieNotice"); ?></div>

<!-- Host-Modus: Umschalten zwischen Würfel- und Bingo-Ansicht -->
<button id="btn-toggle-view"
        data-label-dice="<?php echo htmlspecialchars(Language::getUI("toggleToBingo")); ?>"
        data-label-bingo="<?php echo htmlspecialchars(Language::getUI("toggleToDice")); ?>"><?php echo Language::getUI("toggleToBingo"); ?></button>

<!-- Konfigurationsbutton -->
<input type="checkbox" id="wakeLockCheckbox">
<label for="wakeLockCheckbox"><?php echo Language::getUI("screenActive"); ?></label>

<div id="dice-view">
<h1 id="h1-dicehead"><?php echo Language::getUI("h1Dicehead"); ?></h1>

<button id="btn-mode"><?php echo Language::getUI("configButton"); ?></button>

<!-- Konfigurationsmodal -->
<div id="config-modal-overlay">
  <div id="config-modal">
    <div id="config-modal-header">
      <h4 id="h4-conf"><?php echo Language::getUI("h4Conf"); ?></h4>
      <button type="button" id="config-modal-close" aria-label="<?php echo htmlspecialchars(Language::getUI("closeConfig")); ?>">&times;</button>
    </div>
    <div id="config-modal-body">
      <div id="config-tabs" role="tablist">
        <button type="button" class="config-tab active" data-tab="playmode"><?php echo Language::getUI("tabPlaymode"); ?></button>
        <button type="button" class="config-tab" data-tab="editions"><?php echo Language::getUI("tabEditions"); ?></button>
        <button type="button" class="config-tab" data-tab="misc"><?php echo Language::getUI("tabMisc"); ?></button>
        <button type="button" class="config-tab" data-tab="language"><?php echo Language::getUI("tabLanguage"); ?></button>
      </div>
      <form method="post" id="config-form">
        <div id="config-panels">
          <!-- Spielmodi -->
          <div class="config-panel active" data-panel="playmode" id="category-box">
            <?php foreach ($allCategories as $categoryId => $isActive): ?>
              <label>
                <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($categoryId); ?>" <?php echo $isActive ? "checked" : ""; ?>>
                <?php echo htmlspecialchars($categoryLabels[$categoryId] ?? $categoryId); ?>
              </label>
            <?php endforeach; ?>
          </div>

          <!-- Editionen -->
          <div class="config-panel" data-panel="editions" id="editions-box">
            <?php foreach ($allEditions as $editionId => $isActive): ?>
              <label>
                <input type="checkbox" name="editions[]" value="<?php echo htmlspecialchars($editionId); ?>" <?php echo $isActive ? "checked" : ""; ?>>
                <?php echo htmlspecialchars($editionLabels[$editionId] ?? $editionId); ?>
              </label>
            <?php endforeach; ?>
          </div>

          <!-- Sonstiges -->
          <div class="config-panel" data-panel="misc" id="misc-box">
            <label id="colorpad">
              <input type="checkbox" id="toggle-colors" name="toggle_colors" <?php echo $showColors ? "checked" : ""; ?>>
              <?php echo Language::getUI("lblColorpad"); ?>
            </label>
          </div>

          <!-- Sprache -->
          <div class="config-panel" data-panel="language" id="language-box">
            <label>
              <input type="radio" name="language" value="de" <?php echo $language === "de" ? "checked" : ""; ?>> Deutsch
            </label>
            <label>
              <input type="radio" name="language" value="en" <?php echo $language === "en" ? "checked" : ""; ?>> English
            </label>
          </div>
        </div>
        <input type="hidden" name="update_config" value="1">
        <div id="config-modal-footer">
          <button type="submit"><?php echo Language::getUI("saveButton"); ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Boxen -->
<div class="container" <?php echo $showColors ? "" : "style=\"grid-template-columns: repeat(2, 1fr);\""; ?>>
  <div class="box" id="left">---</div>
  <?php if ($showColors): ?>
  <div class="box" id="middle">---</div>
  <?php endif; ?>
  <div class="box" id="right">---</div>
</div>

<!-- Start-Button -->
<button id="btn-start"><?php echo Language::getUI("startButton"); ?></button>

<!-- Hinweisbox -->
<div id="hint-box"></div>

<!-- GO-Kreis -->
<div id="go-circle">GO!</div>
</div>

<!-- Bingo-Ansicht (Host-Modus) -->
<div id="bingo-view" style="display: none;">
  <h1><?php echo Language::getUI("bingoTitle"); ?></h1>
  <button id="newCardBtn"><?php echo Language::getUI("newCardButton"); ?></button>

  <div id="card">
    <div id="grid"></div>
    <div id="logo">HITSTER</div>
  </div>

  <div id="bingoOverlay">
    <div id="bingoText"><?php echo Language::getUI("bingoOverlayText"); ?></div>
  </div>

  <div id="confirmOverlay">
    <div id="confirmModal">
      <h2><?php echo Language::getUI("bingoOverlayText"); ?></h2>
      <p><?php echo Language::getUI("confirmNewCardText"); ?></p>
      <div id="confirmButtons">
        <button id="confirmNo"><?php echo Language::getUI("confirmNo"); ?></button>
        <button id="confirmYes"><?php echo Language::getUI("confirmYes"); ?></button>
      </div>
    </div>
  </div>
</div>

<!-- Daten für die clientseitige Würfel-Animation -->
<script>
  const DICE_ANIMATION_DATA = <?php echo json_encode($animationData, JSON_UNESCAPED_UNICODE); ?>;
</script>
<script src="assets/dice.js"></script>
<script src="assets/bingo.js"></script>

<footer>
  &copy; 2016 <a href="https://polze.net" target="_blank">PolzeSoft</a> &nbsp;|&nbsp;
  Kontakt: <a href="mailto:songspieltools@polze.net">songspieltools@polze.net</a><br>
  <?php echo Language::getUI("trademarkDisclaimer"); ?>
</footer>
</body>
</html>
