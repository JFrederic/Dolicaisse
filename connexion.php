<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Caisse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css" >
    <link href="css/fontawesome-free-6.7.2-web/css/fontawesome.css" rel="stylesheet" />
    <link href="css/fontawesome-free-6.7.2-web/css/solid.css" rel="stylesheet" />
    <link href="css/fontawesome-free-6.7.2-web/css/brands.css" rel="stylesheet" />
</head>

<body style="background-color: #435165;">
<div class="container mt-5">
    <div class="card col-12 col-md-4 mx-auto p-4">
        <h4>Connexion magasin</h4>
        
        <input type="password" class="form-control my-2" maxlength="6" id="inputCodeMagasin" placeholder="Code magasin (6 caractères)">
        <button class="btn btn-primary w-100" onclick="loginMagasin()">Se connecter</button>
        <div id="loginMsg" class="mt-2"></div>
    </div>
</div>
<footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous"></script>
    <script src="js/login.js"></script>
</footer>
</body>