// Panier
let panier = [];

// Création de la box de suggestions (attachée à la barre recherche produit)
const searchArticleInput = document.getElementById('searchArticle');
const suggestionsBox = document.createElement('div');
suggestionsBox.id = 'suggestionsBox';
suggestionsBox.style.position = 'absolute';
suggestionsBox.style.background = '#fff';
suggestionsBox.style.zIndex = 1000;
suggestionsBox.style.width = searchArticleInput.offsetWidth + 'px';
suggestionsBox.style.maxHeight = '220px';
suggestionsBox.style.overflowY = 'auto';
suggestionsBox.style.boxShadow = '0 2px 8px rgba(0,0,0,0.12)';
suggestionsBox.style.display = 'none';
searchArticleInput.parentNode.appendChild(suggestionsBox);

let lastSuggestions = [];
let selectedSuggestionIdx = -1;

// Affiche suggestions au fur et à mesure qu'on tape
searchArticleInput.addEventListener('input', async function () {
    const q = this.value.trim();
    if (q.length < 2 || !isNaN(q)) {
        suggestionsBox.style.display = 'none';
        return;
    }
    const result = await searchProduit(q);
    console.log("Suggestions:", result);
    if (Array.isArray(result) && result.length > 0) {
        lastSuggestions = result;
        let html = '';
        result.slice(0, 8).forEach((p, idx) => {
            html += `<div class='suggestion-item' style='padding:7px;cursor:pointer;border-bottom:1px solid #eee;' data-idx="${idx}"><span style="font-weight:600;" class="text-danger">${p.label}</span> <span class="text-muted">${p.ref ? ' (' + p.ref + ')' : ''}</span></div>`;
        });
        suggestionsBox.innerHTML = html;
        suggestionsBox.style.display = '';
        positionSuggestionsBox();
    } else {
        suggestionsBox.innerHTML = "<div style='padding:7px;color:#aaa;'>Aucun produit</div>";
        suggestionsBox.style.display = '';
        positionSuggestionsBox();
    }
});

// Clique sur une suggestion
suggestionsBox.addEventListener('click', function (e) {
    let item = e.target.closest('.suggestion-item');
    if (!item) return;
    let idx = parseInt(item.getAttribute('data-idx'));
    if (!isNaN(idx) && lastSuggestions[idx]) {
        addProduitAuPanier(lastSuggestions[idx]);
        suggestionsBox.style.display = 'none';
        searchArticleInput.value = '';
        selectedSuggestionIdx = -1;
    }
});

// Navigation clavier (flèches/entrée)
searchArticleInput.addEventListener('keydown', function (e) {
    const items = suggestionsBox.querySelectorAll('.suggestion-item');
    if (suggestionsBox.style.display === '' && items.length) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedSuggestionIdx = (selectedSuggestionIdx + 1) % items.length;
            highlightSuggestion(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedSuggestionIdx = (selectedSuggestionIdx - 1 + items.length) % items.length;
            highlightSuggestion(items);
        } else if (e.key === 'Enter') {
            if (selectedSuggestionIdx >= 0 && lastSuggestions[selectedSuggestionIdx]) {
                addProduitAuPanier(lastSuggestions[selectedSuggestionIdx]);
                suggestionsBox.style.display = 'none';
                searchArticleInput.value = '';
                selectedSuggestionIdx = -1;
                e.preventDefault();
            } else if (items.length === 1) {
                addProduitAuPanier(lastSuggestions[0]);
                suggestionsBox.style.display = 'none';
                searchArticleInput.value = '';
                selectedSuggestionIdx = -1;
                e.preventDefault();
            }
        }
    }
});

function highlightSuggestion(items) {
    items.forEach((el, idx) => {
        el.style.background = (idx === selectedSuggestionIdx) ? '#e7f1ff' : '#fff';
    });
}

// Si on tape un code-barres (entier) → ajout direct sur Entrée
searchArticleInput.addEventListener('keypress', async function (e) {
    if (e.key === 'Enter' && (!isNaN(this.value.trim()) && this.value.trim().length > 0)) {
        e.preventDefault();
        const result = await searchProduit(this.value.trim());
        if (Array.isArray(result) && result.length > 0) {
            addProduitAuPanier(result[0]);
        } else {
            alert("Produit introuvable");
        }
        this.value = '';
        suggestionsBox.style.display = 'none';
    }
});

// Masquer suggestions si on clique ailleurs
document.addEventListener('click', function (e) {
    if (!suggestionsBox.contains(e.target) && e.target !== searchArticleInput) {
        suggestionsBox.style.display = 'none';
        selectedSuggestionIdx = -1;
    }
});

// Recalcule la position de la box si resize (responsive)
function positionSuggestionsBox() {
    let rect = searchArticleInput.getBoundingClientRect();
    suggestionsBox.style.top = (searchArticleInput.offsetTop + searchArticleInput.offsetHeight) + 'px';
    suggestionsBox.style.left = searchArticleInput.offsetLeft + 'px';
    suggestionsBox.style.width = searchArticleInput.offsetWidth + 'px';
}
window.addEventListener('resize', positionSuggestionsBox);

// ====================
// Fonctions panier identiques à avant
// ====================
function addProduitAuPanier(produit) {
    let item = panier.find(p => p.id === produit.id);
    if (item) {
        item.qty += 1;
    } else {
        panier.push({
            id: produit.id,
            label: produit.label,
            ref: produit.ref,
            pu: parseFloat((produit.price * (1 + produit.tva_tx / 100)).toFixed(2)),
            tva: produit.tva_tx,
            qty: 1,
            remise: 0,
            remise_euro: 0
        });
    }
    renderPanier();
}

function renderPanier() {
    let html = '';
    let total = 0;
    panier.forEach((item, idx) => {
        const montant = (item.pu * item.qty * (1 - item.remise / 100)) - item.remise_euro;
        total += montant;
        html += `<tr>
            <td ><span class="designation">${item.label}</span></td>
            <td><input type="number" class="qty" min="1" value="${item.qty}" style="width:60px" onchange="majQtePanier(${idx}, this.value)"></td>
            <td><span class="pu"><b>${item.pu.toFixed(2)}</b></span></td>
            <td><span class="mtotal"><b>${montant.toFixed(2)}</b></span></td>
            <td><input type="number" class="remise" min="0" max="100" value="${item.remise}" style="width:60px" onchange="majRemisePanier(${idx}, this.value)"></td>
            <td><input type="number" class="remise-euro" min="0" step="0.01" value="${item.remise_euro}" style="width:60px" onchange="majRemiseEuroPanier(${idx}, this.value)"></td>
            <td><input type="hidden" class="tva" value="${item.tva}">
            <input type="hidden" class="idproduit" value="${item.id}">
            <input type="hidden" class="refproduit" value="${item.ref}">
            <button class="btn btn-sm btn-danger" onclick="supprimeProduitPanier(${idx})">&times;</button></td>

            </tr>`;
    });
    document.getElementById('panierItems').innerHTML = html;
    document.getElementById('total').textContent = total.toFixed(2) + ' €';
}

function majQtePanier(idx, val) {
    let v = parseInt(val);
    if (isNaN(v) || v < 1) v = 1;
    panier[idx].qty = v;
    renderPanier();
}
function majRemisePanier(idx, val) {
    let v = parseFloat(val);
    if (isNaN(v) || v < 0) v = 0;
    if (v > 100) v = 100;
    panier[idx].remise = v;
    renderPanier();
}
function majRemiseEuroPanier(idx, val) {
    let v = parseFloat(val);
    if (isNaN(v) || v < 0) v = 0;
    panier[idx].remise_euro = v;
    renderPanier();
}
function viderPanier() {
    panier = [];
    renderPanier();
}
function supprimeProduitPanier(idx) {
    panier.splice(idx, 1);
    renderPanier();
}

