# Hitster Bingo Tools

Eine Sammlung von Web-Tools als Ergänzung zum Brettspiel **Hitster**.

## Tools

### 🎴 Hitster BingoCard (`histercard/`)

Eine interaktive 5×5-Bingokarte im Browser – keine Installation nötig, kein Server erforderlich (reines HTML/JS).

- Zufällig generierte Karte mit 5 Farben (Grün, Gelb, Rot, Lila, Blau) – je 5 Felder pro Farbe
- Felder per Klick/Tap markieren
- Automatische Bingo-Erkennung (Reihen, Spalten, Diagonalen)
- Bestätigungsdialog zum Erzeugen einer neuen Karte

### 🎲 Hitster SongDice (`songdice2/`)

Ein digitaler Würfel-Ersatz für Hitster Bingo. Würfelt zufällig:

- **Spielkategorie** (z. B. „Songtitel", „Interpret", „3 vor / 3 nach", …)
- **Kartenfarbe** (Gelb, Grün, Lila, Hellblau, Pink)
- **Edition** (Original, Summer Hits, Guilty Pleasures, Schlager, …)

Nach dem Würfeln erscheint ein GO!-Kreis mit 35-Sekunden-Countdown für die Ratephase.

**Host-Modus:** Über den Button oben kann zwischen Würfel-Ansicht und einer eingebetteten Bingokarte umgeschaltet werden – ideal für den Spielleiter.

**Konfiguration:**
- Sprache: Deutsch / Englisch
- Aktive Kategorien auswählbar
- Farbfeld ein-/ausschaltbar
- Aktive Editionen auswählbar
- Einstellungen werden per Cookie gespeichert

> Benötigt einen PHP-Server (≥ PHP 7.4).

### 🏠 Startseite (`index.html`)

Einfache Übersichtsseite mit direkten Links zu BingoCard und SongDice.

## Technologie

| Bereich | Technologie |
|---|---|
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Backend (SongDice) | PHP ≥ 7.4 |
| Persistenz | Browser-Cookie |
| Fonts | Google Fonts (Caveat, Condiment, Modak) |

## Installation

1. Repository klonen oder herunterladen
2. Verzeichnis auf einem Webserver mit PHP-Unterstützung bereitstellen
3. `index.html` im Browser öffnen

```bash
git clone https://github.com/noyse27/songspieltools.git
```

Die BingoCard (`histercard/index.html`) funktioniert auch ohne Server direkt im Browser.

## Lizenz / Kontakt

&copy; 2016 PolzeSoft · [polze.net](https://polze.net)  
Kontakt: [songspieltools@polze.net](mailto:songspieltools@polze.net)

> *Hitster ist ein eingetragenes Markenzeichen seiner jeweiligen Inhaber. Dieses Projekt steht in keiner offiziellen Verbindung zum Spielehersteller.*
