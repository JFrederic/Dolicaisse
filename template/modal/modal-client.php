<!-- Modal Bootstrap Recherche et Ajout Client -->
<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clientModalLabel">Recherche / Ajout Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body mb-3">

        <!-- Bloc Recherche -->
        <div id="search-client-block">
          <form autocomplete="off" onsubmit="return false;">
            <div class="row align-items-end">
              <div class="col-8">
                <label for="clientName" class="form-label">Nom ou prénom du client</label>
                <input type="text" class="form-control" id="clientName" placeholder="Tapez un nom ou un prénom...">
                <!-- Champ caché pour stocker l'id du client sélectionné -->
              </div>
              <div class="col-4">
                <button type="button" class="btn btn-primary w-100" id="searchClientBtn">Rechercher</button>
              </div>
            </div>
            <div id="clients-result" class="mt-2"></div>
          </form>
          <button type="button" class="btn btn-outline-success w-100 mt-3" id="showCreateClient">
            <i class="fa-solid fa-plus"></i> Ajouter un client
          </button>
        </div>

        <!-- Bloc Formulaire Création Client (caché au départ) -->
        <!-- Bloc Formulaire Création Client (à mettre dans le modal) -->
<div id="create-client-form" style="display:none;">
  <h6 class="mt-2">Ajouter un nouveau client</h6>
  <form id="formCreateClient" autocomplete="off">
    <div class="row mb-2">
      <div class="col-md-6">
        <label for="newClientName" class="form-label">Nom</label>
        <input type="text" class="form-control" id="newClientName" required>
      </div>
      <div class="col-md-6">
        <label for="newClientFirstname" class="form-label">Prénom</label>
        <input type="text" class="form-control" id="newClientFirstname">
      </div>
    </div>
    <div class="row mb-2">
      <div class="col-md-6">
        <label for="newClientPhone" class="form-label">Téléphone</label>
        <input type="text" class="form-control" id="newClientPhone">
      </div>
      <div class="col-md-6">
        <label for="newClientEmail" class="form-label">Email</label>
        <input type="email" class="form-control" id="newClientEmail">
      </div>
    </div>
    <div class="mb-2">
      <label for="newClientAddress" class="form-label">Adresse</label>
      <input type="text" class="form-control" id="newClientAddress">
    </div>
    <div class="row mb-2">
      <div class="col-md-8">
        <label for="newClientCity" class="form-label">Ville</label>
        <input type="text" class="form-control" id="newClientCity">
      </div>
      <div class="col-md-4">
        <label for="newClientZip" class="form-label">Code postal</label>
        <input type="text" class="form-control" id="newClientZip">
      </div>
    </div>
    <div class="d-flex justify-content-between mt-3">
      <button type="button" class="btn btn-outline-secondary" id="cancelCreateClient">Annuler</button>
      <button type="submit" class="btn btn-success">Créer</button>
    </div>
    <div id="create-client-msg" class="mt-2"></div>
  </form>
</div>

      </div>
      <div class="modal-footer d-flex justify-content-between">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
