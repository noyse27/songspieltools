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

<!-- Konfigurationsdialog -->
<div id="mode-dialog">
  <h4 id="h4-conf"><?php echo Language::getUI("h4Conf"); ?></h4>
  <form method="post" id="config-form">
    <div class="mode-grid">
      <!-- Spracheinstellung -->
      <div id="language-box">
        <h5 id="h5-lang"><?php echo Language::getUI("h5Lang"); ?></h5>
        <label>
          <input type="radio" name="language" value="de" <?php echo $language === "de" ? "checked" : ""; ?>> Deutsch
        </label>
        <label>
          <input type="radio" name="language" value="en" <?php echo $language === "en" ? "checked" : ""; ?>> English
        </label>
      </div>

      <!-- Spielmodi -->
      <div id="category-box">
        <h5 id="h5-playmode"><?php echo Language::getUI("h5Playmode"); ?></h5>
        <div id="categories">
          <?php foreach ($allCategories as $categoryId => $isActive): ?>
            <label>
              <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($categoryId); ?>" <?php echo $isActive ? "checked" : ""; ?>>
              <?php echo htmlspecialchars($categoryLabels[$categoryId] ?? $categoryId); ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Farben aktivieren/deaktivieren -->
      <div id="color-box">
        <h5 id="h5-colorpad"><?php echo Language::getUI("h5Colorpad"); ?></h5>
        <label id="colorpad">
          <input type="checkbox" id="toggle-colors" name="toggle_colors" <?php echo $showColors ? "checked" : ""; ?>>
          <?php echo Language::getUI("lblColorpad"); ?>
        </label>
      </div>

      <!-- Editionen -->
      <div id="editions-box">
        <h5 id="h5-edition"><?php echo Language::getUI("h5Edition"); ?></h5>
        <div id="editions">
          <?php foreach ($allEditions as $editionId => $isActive): ?>
            <label>
              <input type="checkbox" name="editions[]" value="<?php echo htmlspecialchars($editionId); ?>" <?php echo $isActive ? "checked" : ""; ?>>
              <?php echo htmlspecialchars($editionLabels[$editionId] ?? $editionId); ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <input type="hidden" name="update_config" value="1">
    <button type="submit"><?php echo Language::getUI("configButton"); ?></button>
  </form>
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
</body>
</html>
