<?php require_once 'template/header.php';
require_once 'template/includes.php';
?>

<body class="min-h-screen bg-gray-100">

    <body>
        <div class="container-fluid">

            <div class="row  ">
                <div class="col-12 bgdefaut" style="height: 80px; ">
                    <div class="row text-white ">
                        <div class="col-10">
                            <h1 class="mt-2"><i class="fa-solid fa-cash-register"></i> Caisse</h1>
                        </div>
                    </div>
                </div>
                <div class="col-12 ">
                    <div class="row main-caisse" >
                        <div class="col-9 bg-light text-white ">
                        <div class="row">
                            <div class="col-12 col-md-3 sm bg-primary  bloc-dolibarr" data-bs-toggle="modal" data-bs-target="#clientModal">
                                <h3 class="text-center"><i class="fa-solid fa-user"></i> Client </h3>
                            </div>
                            <div class="col-12 col-md-3 bg-danger bloc-dolibarr" data-bs-toggle="modal" data-bs-target="#factureModal">
                                <h3 class="text-center"><i class="fa-solid fa-file-invoice"></i> Facture </h3>
                            </div>
                            <div class="col-12 col-md-3 bg-success bloc-dolibarr">
                                <h3 class="text-center"><i class="fa-solid fa-box"></i> Cloture Caisse </h3>
                            </div>
                            <div class="col-12 col-md-3 bg-dark bloc-dolibarr">
                                <h3 class="text-center"><i class="fa-solid fa-cash-register"></i> Total Caisse </h3>
                            </div>
                        </div>
                        <!-- Panier Composant -->
                        <div class="row">
                            <div class="col-12">
                                <div class="container mt-3">
                                    <?php require_once 'template/composant/panier.php'; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Fin Panier Composant -->
                    </div>
                    <!-- Colonne droite -->
                    <div class="col-3 bg-primary d-flex align-items-center justify-content-center">
                        <p>Colonne droite</p>
                    </div>
                    </div>
                </div>


            </div>

            <?php require_once 'template/footer.php'; ?>
    </body>

    </html>