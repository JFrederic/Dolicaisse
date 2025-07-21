// dolibarr-api.js

const API_BASE = 'http://localhost:8000/public/index.php';

function apiCall(action, params = {}, method = "GET", body = null) {
    let url = API_BASE + '?action=' + encodeURIComponent(action);
    
    if (method === "GET" && params && Object.keys(params).length > 0) {
        url += '&' + new URLSearchParams(params);
    }
    console.log("API Call:", url, params, method, body);
    let fetchOptions = {
        method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    if (body && method !== "GET") {
        fetchOptions.body = JSON.stringify(body);
    }
    return fetch(url, fetchOptions)
        .then(res => res.json());
}

/** 1. Recherche de client (tiers) par nom/prénom */
function searchTiers(nom) {
    return apiCall('search_tiers', {q: nom});
}

/** 2. Création de client (tiers) */
function createTiers(data) {
    // data = {nom, prenom, email, ...}
    return apiCall('create_tiers', {}, "POST", data);
}

/** 3. Recherche de produit par code-barres ou nom */
function searchProduit(query) {
    return apiCall('search_product', {q: query});
}

/** 4. Recherche de facture par nom/prénom ou numéro de facture */
function searchFacture(query) {
    return apiCall('search_invoice', {q: query});
}

/** 5. Création de facture avec multipaiement */
function createFacture(data) {
    // data = {tiers_id, produits: [...], paiements: [...]}
    return apiCall('create_invoice', {}, "POST", data);
}

/** 6. Authentification (connexion) pour le multicompany */
function dolibarrLogin(username, password, entity) {
    // entity = numéro d'entité multicompany
    return apiCall('login', {}, "POST", {username, password, entity});
}

/** 7. Modifier une facture (montant et paiements) */
function updateFacture(factureId, data) {
    // data = {nouveau_montant, nouveaux_paiements: [...]}
    return apiCall('update_invoice', {id: factureId}, "PUT", data);
}

/** 8. Obtenir le total des montants de toutes les factures payées (clôture de caisse) */
function getCaisseCloture() {
    return apiCall('cloture_caisse');
}

/** --- EXEMPLE D’UTILISATION (async/await) --- */

// Exemple : Recherche de client
// searchTiers("Durand").then(console.log);

// Exemple : Création de client
// createTiers({ nom: "Martin", prenom: "Julie", email: "julie.martin@mail.com" }).then(console.log);

// Exemple : Recherche produit
// searchProduit("1234567890123").then(console.log);

// Exemple : Création de facture
/*
createFacture({
    tiers_id: 7,
    produits: [
        {id: 21, qty: 2},
        {id: 33, qty: 1}
    ],
    paiements: [
        {mode: "CB", montant: 10.5},
        {mode: "Espèces", montant: 5.0}
    ]
}).then(console.log);
*/

// Exemple : Clôture de caisse
// getCaisseCloture().then(console.log);

