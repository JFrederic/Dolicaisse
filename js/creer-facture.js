// Appel quand on valide la caisse
async function encaisserFacture() {
    const clientId = document.getElementById('selectedClientId').value;
    if (!clientId || panier.length === 0) {
        alert('Sélectionnez un client et ajoutez des articles !');
        return;
    }
    let paiements = await demanderPaiements(); // [{mode:1, montant:50},{mode:2, montant:20}]
    let produits = panier.map(p => ({
        id: p.id,
        qty: p.qty,
        pu: p.pu,
        remise: p.remise || 0,
        tva: 20
    }));
    let warehouseId = 1; // ou récupère via select, config, etc.
    const resp = await createFacture({
        tiers_id: clientId,
        produits: produits,
        paiements: paiements,
        warehouseId: warehouseId
    });
    if (resp && resp.id) {
        alert('Facture créée !');
        viderPanier();
    } else {
        alert(resp.message || 'Erreur lors de la création de la facture.');
    }
}

