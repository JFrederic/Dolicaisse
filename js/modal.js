// Modal creation client

const clientModal = document.getElementById('clientModal')
const clientName = document.getElementById('clientName')
clientModal.addEventListener('shown.bs.modal', () => {
    clientName.focus()
})

// Affiche le formulaire de création, cache la recherche
document.getElementById('showCreateClient').addEventListener('click', function () {
    document.querySelector('form').style.display = 'none'; // cache le formulaire de recherche
    document.getElementById('create-client-form').style.display = '';
});

// Annuler création → on revient à la recherche
document.getElementById('cancelCreateClient').addEventListener('click', function () {
    document.querySelector('form').style.display = '';
    document.getElementById('create-client-form').style.display = 'none';
    document.getElementById('create-client-msg').textContent = '';
});


// Quand on ferme la modale, on vide les résultats et le champ de recherche
// (pour éviter de garder les résultats affichés si on rouvre la modale)
$('#clientModal').on('hidden.bs.modal', function () {
    $('#clients-result').empty();
    $('#clientName').val('');
    // $('#create-client-form').hide();
});


// Reset des champs et paiements à la fermeture

function resetModalPaiementFields() {
    document.getElementById('inputMontant').value = '';
    paiements = [];
    renderPaiementsCourants();
    document.getElementById('resteAPayer').textContent = (window.montantTotalCaisse || '0.00') + ' €';
    // Correction ARIA/Focus : remet le focus sur le body
    document.activeElement && document.activeElement.blur();
    setTimeout(() => { document.body.focus(); }, 100);
}

document.getElementById('modal-paiement').addEventListener('hidden.bs.modal', resetModalPaiementFields);

function showModalPaiement(modeDefaut) {
    // Ouvre le modal correspondant au mode de paiement
    $('#modal-paiement').modal('show');
    var total = document.getElementById('total').textContent;
    window.montantTotalCaisse = parseFloat(total);
    paiements = [];
    renderPaiementsCourants();
    if (total) {
        total = parseFloat(total.replace(' €', '').replace(',', '.'));
    }
    document.getElementById('resteAPayer').innerHTML = total.toFixed(2) + ' €';
    document.getElementById('totalModalPaiement').innerHTML = total.toFixed(2);
    // Focus sur le champ du mode choisi
    var paiementType = ""
    console.log("Ouverture modal paiement, mode par défaut:", modeDefaut, total);
    setTimeout(() => {
        document.getElementById('inputMontant').value = total.toFixed(2);
        $('#inputMontant').focus().select();
    }, 350);
}




