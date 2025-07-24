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
        viderPanier();
        // Ouvre le modal de confirmation
        let modal = new bootstrap.Modal(document.getElementById('modalFactureCreee'));
        modal.show();

        // Mise à jour des boutons
        document.getElementById('btnImprimerTicket').onclick = function() {
            window.open('http://localhost:8000/public/index.php?action=download_ticket&invoiceId=' + resp.id, '_blank');
        };
        document.getElementById('btnImprimerA4').onclick = function() {
            window.open('http://localhost:8000/public/index.php?action=download_facture_pdf&invoiceId=' + resp.id, '_blank');
        };
    } else {
        alert(resp.message || 'Erreur lors de la création de la facture.');
    }
}
