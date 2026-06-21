/**
 * Bingo-Karten-Logik für den Host-Modus.
 *
 * Erzeugt eine 5x5-Bingokarte (5 Farben x 5 Felder), erkennt BINGO auf
 * Reihen/Spalten/Diagonalen und bietet einen Bestätigungsdialog zum
 * Erzeugen einer neuen Karte. Die Karte wird erst beim ersten Wechsel in
 * die Bingo-Ansicht erzeugt (siehe assets/dice.js, btn-toggle-view).
 */

const COLORS = ['green', 'yellow', 'red', 'purple', 'blue'];
const grid = document.getElementById('grid');
const newCardBtn = document.getElementById('newCardBtn');
const bingoOverlay = document.getElementById('bingoOverlay');
const confirmOverlay = document.getElementById('confirmOverlay');
const confirmYes = document.getElementById('confirmYes');
const confirmNo = document.getElementById('confirmNo');

let marked = new Array(25).fill(false);

function shuffle(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
}

function generateColors() {
    let colors = [];
    COLORS.forEach(c => {
        for (let i = 0; i < 5; i++) colors.push(c);
    });
    return shuffle(colors);
}

function buildCard() {
    marked = new Array(25).fill(false);
    bingoOverlay.classList.remove('show');
    grid.innerHTML = '';
    const colors = generateColors();

    colors.forEach((color, index) => {
        const cell = document.createElement('div');
        cell.className = `cell color-${color}`;
        cell.dataset.index = index;

        const mark = document.createElement('span');
        mark.className = 'mark';
        mark.textContent = '✕';
        cell.appendChild(mark);

        cell.addEventListener('click', () => toggleCell(index, cell));
        grid.appendChild(cell);
    });
}

function toggleCell(index, cell) {
    marked[index] = !marked[index];
    cell.classList.toggle('marked', marked[index]);
    checkBingo();
}

function checkBingo() {
    const lines = [];

    // Zeilen
    for (let r = 0; r < 5; r++) {
        lines.push([0,1,2,3,4].map(c => r * 5 + c));
    }
    // Spalten
    for (let c = 0; c < 5; c++) {
        lines.push([0,1,2,3,4].map(r => r * 5 + c));
    }
    // Diagonalen
    lines.push([0,6,12,18,24]);
    lines.push([4,8,12,16,20]);

    const hasBingo = lines.some(line => line.every(i => marked[i]));

    if (hasBingo) {
        bingoOverlay.classList.add('show');
    } else {
        bingoOverlay.classList.remove('show');
    }
}

newCardBtn.addEventListener('click', buildCard);

bingoOverlay.addEventListener('click', () => {
    confirmOverlay.classList.add('show');
});

confirmYes.addEventListener('click', () => {
    confirmOverlay.classList.remove('show');
    buildCard();
});

confirmNo.addEventListener('click', () => {
    confirmOverlay.classList.remove('show');
});

// Für den Lazy-Init beim ersten Wechsel in die Bingo-Ansicht (assets/dice.js)
window.buildCard = buildCard;
