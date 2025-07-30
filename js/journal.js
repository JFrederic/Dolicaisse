// Preremplir les dates du jour par défaut au chargement du DOM
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateDebut').value = today;
    document.getElementById('dateFin').value = today;
});

// Fonction pour charger les factures avec gestion des couleurs
function chargerFacturesJournal() {
    const dateDebut = document.getElementById('dateDebut').value;
    const dateFin = document.getElementById('dateFin').value;

    document.getElementById('journalFacturesInfo').innerHTML = "Chargement...";

    getJournalFactures(dateDebut, dateFin).then(data => {
        let tbody = '';
        if (data && Array.isArray(data) && data.length > 0) {
            data.forEach(function(facture) {
                let couleur = facture.color ? `style="background:${facture.color};"` : '';
                tbody += `
                  <tr ${couleur}>
                    <td>${facture.ref || facture.numero || facture.id || ''}</td>
                    <td>${facture.date || ''}</td>
                    <td>${facture.heure || ''}</td>
                    <td>${facture.ttc || ''}</td>
                    <td>${facture.client || ''}</td>
                    <td>${facture.paiement || ''}</td>
                  </tr>`;
            });
            document.getElementById('journalFacturesInfo').innerHTML = `${data.length} facture(s) trouvée(s)`;
        } else {
            document.getElementById('journalFacturesInfo').innerHTML = "Aucune facture pour cette période.";
        }
        document.querySelector('#journalFacturesTable tbody').innerHTML = tbody;
    }).catch(err => {
        document.getElementById('journalFacturesInfo').innerHTML = "Erreur lors du chargement.";
        document.querySelector('#journalFacturesTable tbody').innerHTML = '';
    });
}

// Recharge au submit
document.getElementById('filtreJournalFacturesForm').addEventListener('submit', function(e) {
    e.preventDefault();
    chargerFacturesJournal();
});

// Recharge aussi dès ouverture du modal
document.getElementById('journalFacturesModal').addEventListener('shown.bs.modal', function () {
    chargerFacturesJournal();
});
