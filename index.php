<?php
session_start();
require_once 'template/header.php';
require_once 'template/includes.php';


if (empty($_SESSION['entrepot_id'])) {
    header('Location: /connexion.php');
    exit;
}
?>

<body class="min-h-screen bg-gray-100">

    <body>
        <div class="container-fluid d-flex flex-column min-vh-100">

            <div class="row  flex-grow-1">

                <div class="col-12 bgdefaut">
                    <!-- ENTETE CAISSE -->
                    <!-- <div class="row">
                        <div class="col-12 ">
                            <h1 class="text-white p-1"><i class="fa-solid fa-cash-register"></i> Caisse</h1>
                        </div>
                    </div> -->
                    <!-- FIN ENTETE CAISSE -->

                    <!-- CAISSE -->
                    <div class="row main-caisse">
                        <!-- Colonne gauche -->
                        <div class="col-9 text-white " style="background-color: rgb(242, 242, 242) !important;">
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
                                <div class="col-12 col-md-3 bg-info bloc-dolibarr">
                                    <h3 class="text-center"><i class="fa-solid fa-cash-register"></i> Correction Caisse </h3>
                                </div>
                            </div>
                            <!-- RECHERCHE PRODUIT -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="input-group">
                                        <input type="search" class="form-control form-control-lg input" id="searchArticle" placeholder="Scanner un article" autofocus="">
                                        <!-- <div id="livesearch"></div> -->
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-lg btn-default">
                                                <i class="fa fa-barcode"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Panier Composant -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="mt-3">
                                        <?php require_once 'template/composants/panier.php'; ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Panier Composant -->
                        </div>

                        <!-- Colonne droite -->
                        <div class="col-3  d-flex align-items-start justify-content-center paiement-caisse">
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h3 class="text-center" style="font-family: 'Tahoma';font-weight: 600;"> <i class="fa-solid fa-cash-register"></i> TOTAL A PAYER</h3>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="card-body" style="border:3px solid #ffffff ;border-radius:10px; background-color: rgb(0, 0, 139);color:#ffffff;
                    padding: 0 15px !important;">
                                        <p id="total" class="float-right montant-total">175.02 €</p>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="card-body border border-primary rounded-3 p-3">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-danger btn-lg w-100 btnCaisse" onclick="viderPanier('1','1')">
                                                    Vider
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-primary btn-lg w-100 btnCaisse" id="paiementCB" onclick="showModalPaiement('CB')" >
                                                    CB
                                                </button>
                                            </div>

                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-primary btn-lg w-100 btnCaisse text-white" id="paiementCheque" onclick="showModalPaiement('Chèque')">
                                                    Chèques
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-success btn-lg w-100 btnCaisse" id="paiementEspece" onclick="showModalPaiement('Espèces')">
                                                    Espèces
                                                </button>
                                            </div>

                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-dark btn-lg w-100 btnCaisse" onclick="totalCaisse('1',0)">
                                                    Total caisse
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-primary btn-lg w-100 btnCaisse" data-bs-toggle="modal" data-bs-target="#modal-retour" id="retourArticle">
                                                    Retour article
                                                </button>
                                            </div>

                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-primary btn-lg w-100 btnCaisse" data-bs-toggle="modal" data-bs-target="#modal-remise" id="modalRemise">
                                                    Remise
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-lg w-100 btnCaisse text-white" style="background-color: orange; border: 1px solid orange;" data-bs-toggle="modal" data-bs-target="#modal-divers" id="produitDivers">
                                                    Divers
                                                </button>
                                            </div>

                                            <div class="col-12">
                                                <button type="button" class="btn btn-info btn-lg w-100 btnCaisse text-white" onclick="ticketAvoir()">
                                                    Avoir
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- FIN CAISSE -->

                </div>


            </div>
            <!-- Footer en bas -->



        </div>
        <?php require_once 'template/footer.php'; ?>
    </body>

    </html>