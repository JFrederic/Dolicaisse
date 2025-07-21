function loginMagasin() {
    let code = document.getElementById('inputCodeMagasin').value.trim();
    console.log('Code entré:', code);
    var url = 'api/login.php';
    fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ code: code })
    })
    .then(r => r.json())
    .then(res => {
        // Affiche la réponse brute dans la console pour debug
        console.log('Réponse fetch:', res);
        
        if(res.success) {
            // Stocker éventuellement en localStorage pour rechargement si besoin
            localStorage.setItem('entrepot_id', res.entrepot_id);
            document.getElementById('loginMsg').innerHTML = '<span class="text-success">Connexion réussie (entrepôt ' + res.entrepot_id + ')</span>';
            // Rediriger ou afficher la caisse
            window.location.href = window.origin + '/Dolicaisse/index.php';
        } else {
            document.getElementById('loginMsg').innerHTML = '<span class="text-danger">Code invalide</span>';
        }
        });
    }