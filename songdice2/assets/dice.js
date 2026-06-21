/**
 * Frontend-Logik für die Songdice-Anwendung.
 *
 * Erwartet eine globale Konstante DICE_ANIMATION_DATA mit den Feldern
 * categories, editions und colors (Array von {name, code}), die für die
 * rein clientseitige Rotationsanimation verwendet werden. Das eigentliche
 * Würfelergebnis kommt über einen einzigen AJAX-Aufruf an ajax.php.
 */

// Sprachumschaltung sofort übernehmen (Formular absenden), damit ein
// Wechsel nicht versehentlich verloren geht, falls statt des "Speichern"-Buttons
// der (gleich benannte) Button zum Ein-/Ausblenden des Dialogs geklickt wird.
document.querySelectorAll('#config-form input[name="language"]').forEach(function(radio) {
    radio.addEventListener("change", function() {
        document.getElementById("config-form").submit();
    });
});

// Host-Modus: Umschalten zwischen Würfel- und Bingo-Ansicht
(function() {
    const toggleBtn = document.getElementById("btn-toggle-view");
    const diceView = document.getElementById("dice-view");
    const bingoView = document.getElementById("bingo-view");
    const STORAGE_KEY = "songdice2_view";
    let cardBuilt = false;

    function showView(view) {
        if (view === "bingo") {
            diceView.style.display = "none";
            bingoView.style.display = "flex";
            toggleBtn.textContent = toggleBtn.dataset.labelBingo;
            if (!cardBuilt && typeof window.buildCard === "function") {
                window.buildCard();
                cardBuilt = true;
            }
        } else {
            diceView.style.display = "";
            bingoView.style.display = "none";
            toggleBtn.textContent = toggleBtn.dataset.labelDice;
        }
        localStorage.setItem(STORAGE_KEY, view);
    }

    toggleBtn.addEventListener("click", function() {
        const currentView = bingoView.style.display === "flex" ? "bingo" : "dice";
        showView(currentView === "bingo" ? "dice" : "bingo");
    });

    const savedView = localStorage.getItem(STORAGE_KEY);
    if (savedView === "bingo") {
        showView("bingo");
    }
})();

// Konfigurationsdialog ein- und ausblenden
document.getElementById("btn-mode").addEventListener("click", function() {
    const modeDialog = document.getElementById("mode-dialog");
    modeDialog.style.display = modeDialog.style.display === "block" ? "none" : "block";
});

// Wake Lock Funktionalität
const wakeLockCheckbox = document.getElementById("wakeLockCheckbox");
let wakeLock = null;

wakeLockCheckbox.addEventListener("change", async () => {
    if (wakeLockCheckbox.checked && "wakeLock" in navigator) {
        try {
            wakeLock = await navigator.wakeLock.request("screen");
            console.log("Wake Lock aktiviert");
            wakeLock.addEventListener("release", () => {
                console.log("Wake Lock wurde freigegeben");
                // Checkbox nicht automatisch zurücksetzen, wenn manuell freigegeben
                wakeLock = null;
            });
        } catch (err) {
            console.error(`Fehler beim Aktivieren des Wake Locks: ${err.name}, ${err.message}`);
            wakeLockCheckbox.checked = false; // Checkbox zurücksetzen bei Fehler
        }
    } else if (!wakeLockCheckbox.checked && wakeLock) {
        try {
            await wakeLock.release();
            console.log("Wake Lock erfolgreich freigegeben");
        } catch (err) {
            console.error(`Fehler beim Freigeben des Wake Locks: ${err.name}, ${err.message}`);
        }
    }
});

// Spielt eine kräftige Pressluft-Hupe ab (wie im Fußballstadion), ohne externe Audiodatei
function playHornSound() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const now = ctx.currentTime;
        const duration = 1.8;

        // Gesamtlautstärke mit kurzem Attack und Release, dazwischen volle Lautstärke
        const masterGain = ctx.createGain();
        masterGain.gain.setValueAtTime(0, now);
        masterGain.gain.linearRampToValueAtTime(0.9, now + 0.05);
        masterGain.gain.setValueAtTime(0.9, now + duration - 0.2);
        masterGain.gain.linearRampToValueAtTime(0, now + duration);

        // Leichter Hochpass-Filter für einen "blechernen" Hupen-Klang
        const filter = ctx.createBiquadFilter();
        filter.type = "lowpass";
        filter.frequency.value = 1800;
        filter.connect(masterGain);
        masterGain.connect(ctx.destination);

        // Grundton + Oktave + Quinte für einen kräftigen, mehrstimmigen Hupenklang
        [
            { freq: 116, gain: 1.0 },
            { freq: 233, gain: 0.6 },
            { freq: 349, gain: 0.35 }
        ].forEach(({ freq, gain }) => {
            const osc = ctx.createOscillator();
            osc.type = "sawtooth";
            osc.frequency.value = freq;

            const oscGain = ctx.createGain();
            oscGain.gain.value = gain;

            osc.connect(oscGain);
            oscGain.connect(filter);
            osc.start(now);
            osc.stop(now + duration);
        });
    } catch (err) {
        console.error("Signalton konnte nicht abgespielt werden:", err);
    }
}

// Funktion für das Starten des Kreises
let goCircleInterval = null;
function startGoCircleCountdown() {
    if (goCircleInterval) clearInterval(goCircleInterval); // Bestehenden Intervall löschen
    let counter = 0;
    const goCircle = document.getElementById("go-circle");
    goCircle.textContent = "GO!";
    goCircle.style.display = "flex";
    goCircle.style.opacity = "1";
    goCircle.style.background = "conic-gradient(green 0%, green 100%)";

    // Startet den Countdown erst, wenn der Benutzer auf den Kreis klickt
    goCircle.onclick = () => {
        goCircle.onclick = null; // Deaktiviere weiteren Klick nach Start
        const stepMs = 50; // Feinere Schritte für eine flüssige Füllung
        const totalSeconds = 25;
        goCircleInterval = setInterval(() => {
            counter += stepMs / 1000;
            const percentage = Math.min((counter / totalSeconds) * 100, 100);
            goCircle.style.background = `conic-gradient(
                red ${percentage}%,
                green ${percentage}% 100%
            )`;

            // In den letzten 5 Sekunden: Kreis beginnt zu blinken
            if (counter > totalSeconds - 5) {
                goCircle.style.opacity = (Math.floor(counter * 2) % 2 === 0) ? "0.5" : "1";
            }

            // Nach 25 Sekunden: Kreis wird "STOP" + Signalhorn
            if (counter >= totalSeconds) {
                clearInterval(goCircleInterval);
                goCircleInterval = null;
                goCircle.textContent = "STOP";
                goCircle.style.opacity = "1";
                goCircle.style.background = "red"; // Vollständig rot
                playHornSound();

                // Kreis nach 10 Sekunden wieder ausblenden
                setTimeout(() => {
                    goCircle.style.display = "none";
                }, 10000);
            }
        }, stepMs);
    };
}

// Hilfsfunktion: zufälliges Element aus einem Array
function randomItem(items) {
    return items[Math.floor(Math.random() * items.length)];
}

// Start-Button Logik
let leftInterval, middleInterval, rightInterval;
document.getElementById("btn-start").addEventListener("click", function() {
    // Laufende Intervalle stoppen
    if (leftInterval) clearInterval(leftInterval);
    if (middleInterval) clearInterval(middleInterval);
    if (rightInterval) clearInterval(rightInterval);
    if (goCircleInterval) clearInterval(goCircleInterval);

    // GO-Circle zurücksetzen
    const goCircle = document.getElementById("go-circle");
    goCircle.style.display = "none";
    goCircle.style.background = "green";
    goCircle.textContent = "GO!";
    goCircle.style.animation = "none";
    goCircle.onclick = null; // Klick-Handler entfernen

    const leftBox = document.getElementById("left");
    const middleBox = document.getElementById("middle");
    const rightBox = document.getElementById("right");
    const hintBox = document.getElementById("hint-box");

    // Boxen auf den Ausgangszustand setzen
    leftBox.textContent = "---";
    if (middleBox) {
        middleBox.textContent = "---";
        middleBox.style.backgroundColor = ""; // Standardfarbe aus CSS (#middle) wiederherstellen
    }
    rightBox.textContent = "---";
    hintBox.style.display = "none";

    // Einmaliger AJAX-Aufruf liefert das endgültige Würfelergebnis.
    // Die Rotationsanimation läuft anschließend rein clientseitig.
    fetch("ajax.php", {
        method: "POST",
        body: new URLSearchParams({
            "action": "roll"
        }),
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        }
    })
    .then(response => {
        if (!response.ok) {
            // Fehler aus JSON extrahieren und werfen
            return response.json().then(data => {
                throw new Error(data.error || "Ein unbekannter Fehler ist aufgetreten.");
            });
        }
        return response.json(); // Erfolgreiche Antwort als JSON parsen
    })
    .then(data => {
        // Starten der Würfelanimation mit dem endgültigen Ergebnis
        startDiceAnimation(data);
    })
    .catch(error => {
        // Fehlermeldung anzeigen
        alert(error.message);
    });
});

// Würfelanimation
function startDiceAnimation(finalData) {
    const leftBox = document.getElementById("left");
    const middleBox = document.getElementById("middle");
    const rightBox = document.getElementById("right");
    const hintBox = document.getElementById("hint-box");

    const categoryIcons = DICE_ANIMATION_DATA.categoryIcons;
    const editions = DICE_ANIMATION_DATA.editions;
    const colors = DICE_ANIMATION_DATA.colors;

    // Erstes Bild der Animation gleich anzeigen
    leftBox.textContent = randomItem(categoryIcons);
    if (middleBox) {
        const initialColor = randomItem(colors);
        middleBox.style.backgroundColor = initialColor.code;
        middleBox.textContent = initialColor.name;
    }
    rightBox.textContent = randomItem(editions);

    // Linke Box: Kategorie-Icons rotieren
    leftInterval = setInterval(() => {
        leftBox.textContent = randomItem(categoryIcons);
    }, 75);

    // Mittlere Box: Farben rotieren (falls aktiv)
    if (middleBox) {
        middleInterval = setInterval(() => {
            const color = randomItem(colors);
            middleBox.style.backgroundColor = color.code;
            middleBox.textContent = color.name;
        }, 75);
    }

    // Rechte Box: Editionen rotieren
    rightInterval = setInterval(() => {
        rightBox.textContent = randomItem(editions);
    }, 75);

    // Nach 3 Sekunden: Linke Box stoppen
    setTimeout(() => {
        clearInterval(leftInterval);
        leftInterval = null;
        // Finales Kategorie-Icon setzen
        leftBox.textContent = finalData.categoryIcon;
        // Hinweis für die Kategorie anzeigen
        hintBox.textContent = finalData.hint;
        hintBox.style.display = finalData.hint ? "block" : "none";
    }, 3000);

    // Nach 4 Sekunden: Mittlere Box stoppen
    setTimeout(() => {
        if (middleBox) {
            clearInterval(middleInterval);
            middleInterval = null;
            // Finale Farbe setzen
            middleBox.style.backgroundColor = finalData.color.code;
            middleBox.textContent = finalData.color.name;
        }
    }, 4000);

    // Nach 5 Sekunden: Rechte Box stoppen
    setTimeout(() => {
        clearInterval(rightInterval);
        rightInterval = null;
        // Finale Edition setzen
        rightBox.textContent = finalData.edition;

        // GO-Knopf erzeugen
        startGoCircleCountdown();
    }, 5000);
}
