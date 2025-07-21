let paiements = [];

function ajouterPaiement(mode, montant) {
    montant = parseFloat(montant);
    if (isNaN(montant) || montant <= 0) {
        alert("Montant invalide !");
        return;
    }
    paiements.push({ mode, montant });
    renderPaiementsCourants();
    $('#inputMontant').focus().select();
}

function renderPaiementsCourants() {
    let html = '';
    let total = 0;
    for (let p of paiements) {
        html += `<div><b>${p.mode}</b> : ${p.montant.toFixed(2)} €</div>`;
        total += p.montant;
    }
    document.getElementById('paiementsCourants').innerHTML = html;
    let reste = (window.montantTotalCaisse - total);
    document.getElementById('resteAPayer').innerHTML = reste.toFixed(2) + ' €';
    document.getElementById('inputMontant').value = reste.toFixed(2);
}

function validerPaiement() {
    let total = paiements.reduce((sum, p) => sum + p.montant, 0);
    console.log("Total des paiements :", total, "Montant total caisse :", window.montantTotalCaisse);
    if (total.toFixed(2) < window.montantTotalCaisse.toFixed(2)) {
        alert('Il reste encore ' + (window.montantTotalCaisse - total).toFixed(2) + ' € à payer !');
        return;
    }
    // Passe les paiements à la fonction d'encaissement facture
    // console.log("Encaissement avec paiements :", paiements);
    // throw new Error("Arrêt forcé du script");
    encaisserFactureAvecPaiements(paiements);
    // Ferme le modal (adapte selon modal utilisé)
    var modal = bootstrap.Modal.getInstance(document.getElementById('modal-paiement'));
    if (modal) modal.hide();
    paiements = [];
    renderPaiementsCourants();
}

async function encaisserFactureAvecPaiements(paiements) {
    const clientId = document.getElementById('selectedClientId').value;
    // if (!clientId || panier.length === 0) {
    //     alert('Sélectionnez un client et ajoutez des articles !');
    //     return;
    // }
    let panier = $('#panierItems tr').toArray().map(tr => {
        let designation = $(tr).find('.designation').text();
        let qty = parseFloat($(tr).find('.qty').val());
        let pu = parseFloat($(tr).find('.pu').text());
        let mtotal = parseFloat($(tr).find('.mtotal').text());
        let remise = parseFloat($(tr).find('.remise').val()) || 0;
        let remiseEuro = parseFloat($(tr).find('.remise-euro').val()) || 0;
        let id = parseInt($(tr).find('.idproduit').val()) || 0;
        let refproduit = $(tr).find('.refproduit').val() || '';
        return { designation, qty, pu, mtotal, remise, remiseEuro, id };
    });

    let produits = panier.map(p => ({
        id: p.id || 0, // Assure que l'ID est toujours un nombre
        designation: String(p.designation),
        qty: Number(p.qty),
        pu: Number(p.pu),
        remise: Number(p.remise) || 0,
        remise_euro: Number(p.remise_euro) || 0,
        mtotal: Number(p.mtotal),
        ref: String(p.refproduit || ''),
        tva: 8.5
    }));
    console.log("Produits du panier:", produits);
    let warehouseId = 2; // Adapter si entrepôt choisi/paramétré
    const resp = await createFacture({
        tiers_id: clientId,
        produits: produits,
        paiements: paiements,
        warehouseId: warehouseId
    });
    console.log("Réponse de création de facture:", resp);
    if (resp && resp.id) {
        alert('Facture créée !');
        viderPanier();
        paiements = [];
        renderPaiementsCourants();
        // Tu peux aussi réinitialiser les montants/payments UI ici
    } else {
        alert(resp.message || 'Erreur lors de la création de la facture.');
    }
}

