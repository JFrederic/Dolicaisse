<!-- ...dans .modal-body juste après le form de recherche... -->
<div id="create-client-form" style="display:none;">
  <h6>Ajouter un nouveau client</h6>
  <form id="formCreateClient">
    <div class="mb-2">
      <label for="newClientName" class="form-label">Nom</label>
      <input type="text" class="form-control" id="newClientName" required>
    </div>
    <div class="mb-2">
      <label for="newClientFirstname" class="form-label">Prénom</label>
      <input type="text" class="form-control" id="newClientFirstname">
    </div>
    <div class="mb-2">
      <label for="newClientEmail" class="form-label">Email</label>
      <input type="email" class="form-control" id="newClientEmail">
    </div>
    <!-- Ajoute d'autres champs Dolibarr si besoin -->
    <div class="d-flex justify-content-between mt-3">
      <button type="button" class="btn btn-outline-secondary" id="cancelCreateClient">Annuler</button>
      <button type="submit" class="btn btn-success">Créer</button>
    </div>
    <div id="create-client-msg" class="mt-2"></div>
  </form>
</div>
