document.getElementById('searchClientBtn').addEventListener('click', async function() {
    const query = document.getElementById('clientName').value.trim();
    const resultsDiv = document.getElementById('clients-result');
    const hiddenInput = document.getElementById('selectedClientId');
    hiddenInput.value = ""; // On réinitialise l'id sélectionné

    if (!query) {
        alert("Veuillez entrer un nom de client !");
        resultsDiv.innerHTML = "";
        return;
    }

    try {
        const result = await searchTiers(query);

        // Vérifier que l'API retourne bien un tableau
        if (!Array.isArray(result) || result.length === 0) {
            resultsDiv.innerHTML = "<em>Aucun client trouvé.</em>";
            return;
        }

        // Générer la liste de suggestions
        let list = document.createElement('ul');
        list.style.listStyle = "none";
        list.style.padding = 0;

        result.forEach(client => {
            let li = document.createElement('li');
            li.style.cursor = "pointer";
            li.style.padding = "5px 10px";
            li.style.borderBottom = "1px solid #eee";
            li.style.boxShadow = "0 2px 8px rgba(0,0,0,0.08)"; // Ajout du box-shadow
            li.textContent = `${client.name || ""} ${client.firstname || ""}`.trim();

            // Quand on clique sur un nom, on sélectionne ce client
            li.addEventListener('click', function() {
                const clientFullName = `${client.name || ""} ${client.firstname || ""}`.trim();
                document.getElementById('clientName').value = clientFullName;
                hiddenInput.value = client.id; // stocke l'id du client
                // Affiche le client sélectionné dans le panier
                const clientDisplay = document.getElementById('selectedClientDisplay');
                if (clientDisplay) {
                    clientDisplay.textContent = `👤 ${clientFullName}`;
                    
                }
                // Efface la liste après sélection
                resultsDiv.innerHTML = `<span style="color:green;">Client sélectionné : ${li.textContent}</span>`;
                
            });
            list.appendChild(li);
        });

        // Affiche la liste dans la div
        resultsDiv.innerHTML = "";
        resultsDiv.appendChild(list);
        

    } catch (err) {
        resultsDiv.textContent = "Erreur lors de la recherche : " + err;
    }
});


document.getElementById('formCreateClient').addEventListener('submit', async function(e){
    e.preventDefault();
    const name = document.getElementById('newClientName').value.trim();
    // const firstname = document.getElementById('newClientFirstname').value.trim();
    const phone = document.getElementById('newClientPhone').value.trim();
    const email = document.getElementById('newClientEmail').value.trim();
    const address = document.getElementById('newClientAddress').value.trim();
    const city = document.getElementById('newClientCity').value.trim();
    const zip = document.getElementById('newClientZip').value.trim();

    if(!name){
        document.getElementById('create-client-msg').innerHTML = '<span class="text-danger">Le nom est obligatoire.</span>';
        return;
    }
    if(!zip){
        document.getElementById('create-client-msg').innerHTML = '<span class="text-danger">Le code postal est obligatoire.</span>';
        return;
    }

    try {
        const resp = await createTiers({ name, firstname, phone, email, address, city, zip });
        
        if (resp>0) {
            document.getElementById('create-client-msg').innerHTML = '<span class="text-success">Client ajouté !</span>';
            document.getElementById('selectedClientId').value = resp;
            document.getElementById('clientName').value = name + (firstname ? ' ' + firstname : '');
            setTimeout(() => {
                document.getElementById('search-client-block').style.display = '';
                document.getElementById('create-client-form').style.display = 'none';
                document.getElementById('clients-result').innerHTML = `<span class="text-success">Client sélectionné : ${name} ${firstname}</span>`;
                const clientDisplay = document.getElementById('selectedClientDisplay');
                if (clientDisplay) {
                    clientDisplay.textContent = `👤 ${name} ${firstname}`;
                    
                }
                $('#search-client-block > form').css('display', 'block'); // cache le formulaire de recherche
            }, 1000);

        } else {
            
            document.getElementById('create-client-msg').innerHTML = '<span class="text-danger">Erreur lors de la création.</span>';
        }
    } catch (err) {
        console.error("Erreur lors de la création du client :", err);
        let errorMsg = 'Erreur lors de la création du client.';
        if (err && err.response && err.response.data) {
            // Si l'API retourne un message d'erreur détaillé
            errorMsg += ' ' + (err.response.data.message || JSON.stringify(err.response.data));
        } else if (err && err.message) {
            errorMsg += ' ' + err.message;
        } else if (typeof err === 'string') {
            errorMsg += ' ' + err;
        }
        document.getElementById('create-client-msg').innerHTML = '<span class="text-danger">' + errorMsg + '</span>';
    }
});


// Alerte avant fermeture/rechargement de la page si un client est sélectionné (seulement en PROD)
// window.addEventListener("beforeunload", function(e) {
//     if (document.getElementById('selectedClientId').value) {
//         // Si un client est sélectionné, alerte le user avant fermeture/rechargement
//         e.preventDefault();
//         // Certains navigateurs ont besoin que cette ligne soit définie
//         e.returnValue = '';
//         return '';
//     }
// });



async function handleFactureSearch() {
    const ref = document.getElementById('factureNumber').value.trim();
    const resultDiv = document.getElementById('factureResult');
    resultDiv.innerHTML = "Recherche...";
    const facture = await searchFacture(ref);
    console.log("Résultat de la recherche de facture :", facture);
    if (facture && facture.length > 0) {
        const f = facture[0];
        var ttc = f.total_ttc || 0;
        ttc = parseFloat(ttc).toFixed(2); // Assure que c
        resultDiv.innerHTML = `
            <div class="alert alert-success p-2">
                <b>Facture trouvée :</b><br>
                Ref : ${f.ref}<br>
                Client : ${f.socname || ''}<br>
                Total TTC : ${ttc || ''} €<br>
                <a href="http://localhost:8000/public/index.php?action=search_download_facture_pdf&invoiceId=${f.id}" target="_blank" class="btn btn-sm btn-secondary mt-2">
                    Télécharger le PDF
                </a>
            </div>
        `;
    } else {
        resultDiv.innerHTML = `<div class="alert alert-danger p-2">Aucune facture trouvée pour cette référence.</div>`;
    }
}

document.querySelector('#factureModal .btn.btn-primary').addEventListener('click', handleFactureSearch);

document.getElementById('factureNumber').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        handleFactureSearch();
    }
});


