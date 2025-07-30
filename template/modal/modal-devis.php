<!-- Modal Bootstrap Devis -->
<div class="modal fade" id="modal-devis" tabindex="-1" aria-labelledby="devisModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="devisModalLabel">Gestion des Devis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <!-- Onglets recherche / création -->
        <ul class="nav nav-tabs mb-3" id="devisTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="search-devis-tab" data-bs-toggle="tab" data-bs-target="#search-devis" type="button" role="tab" aria-controls="search-devis" aria-selected="true">
              Recherche Devis
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="create-devis-tab" data-bs-toggle="tab" data-bs-target="#create-devis" type="button" role="tab" aria-controls="create-devis" aria-selected="false">
              Création Devis
            </button>
          </li>
        </ul>
        <div class="tab-content" id="devisTabContent">
          <!-- Onglet Recherche -->
          <div class="tab-pane fade show active" id="search-devis" role="tabpanel" aria-labelledby="search-devis-tab">
            <form id="searchDevisForm">
              <div class="mb-3">
                <div class="row">
                  <label for="devisSearch" class="form-label">Nom du client ou Référence du devis</label>
                  <div class="col-10">
                    <input type="text" class="form-control" id="devisSearch" placeholder="Exemple: Dupont ou DV22001">
                    <div id="devisResult" class="mt-2"></div>
                  </div>
                  <div class="col-2">
                    <button type="button" class="btn btn-primary" id="btnDevisSearch">Recherche</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <!-- Onglet Création -->
          <div class="tab-pane fade" id="create-devis" role="tabpanel" aria-labelledby="create-devis-tab">
            <form id="createDevisForm">
              <div class="mb-3">
                <label for="devisClient" class="form-label">Client (ID ou nom)</label>
                <input type="text" class="form-control" id="devisClient" placeholder="ID ou nom du client" required>
              </div>
              <div class="mb-3">
                <label for="devisDate" class="form-label">Date du devis</label>
                <input type="date" class="form-control" id="devisDate" value="<?php echo date('Y-m-d'); ?>" required>
              </div>
              <div id="devisProduitsContainer">
                <label class="form-label">Produits / Services</label>
                <div class="row mb-2 devisProduitRow">
                  <div class="col">
                    <input type="text" class="form-control" name="produit_ref[]" placeholder="Réf. produit/service" required>
                  </div>
                  <div class="col">
                    <input type="text" class="form-control" name="produit_libelle[]" placeholder="Libellé" required>
                  </div>
                  <div class="col">
                    <input type="number" class="form-control" name="produit_qty[]" placeholder="Quantité" min="1" value="1" required>
                  </div>
                  <div class="col">
                    <input type="number" class="form-control" name="produit_pu[]" placeholder="Prix Unitaire" min="0" step="0.01" required>
                  </div>
                </div>
              </div>
              <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addProduitRow">+ Ajouter une ligne</button>
              <div>
                <button type="submit" class="btn btn-success">Créer le devis</button>
              </div>
              <div id="createDevisResult" class="mt-2"></div>
            </form>
          </div>
        </div>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

