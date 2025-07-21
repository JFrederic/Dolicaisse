<?php require_once 'template/header.php'; ?>
<body style="background-color: #435165;">
<div class="container mt-5">
    <div class="card col-12 col-md-4 mx-auto p-4">
        <h4>Connexion magasin</h4>
        
        <input type="password" class="form-control my-2" maxlength="6" id="inputCodeMagasin" placeholder="Code magasin (6 caractères)">
        <button class="btn btn-primary w-100" onclick="loginMagasin()">Se connecter</button>
        <div id="loginMsg" class="mt-2"></div>
    </div>
</div>
<?php require_once 'template/footer.php'; ?>
</body>