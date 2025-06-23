<!-- Modal Bootstrap -->
<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clientModalLabel">Recherche Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body mb-3">
        <form>
          <div class="mb-3">
            <div class="row">
              <label for="clientName" class="form-label">Nom du client</label>
              <div class="col-10">
                <input type="text" class="form-control" id="clientName" placeholder="Entrez le nom du client">
              </div>
              <div class="col-2">
                <button type="button" class="btn btn-primary">Recherche</button>
              </div>
            </div>
            <div class="row">
              <div class="col-12 ">
                <button class="btn btn-primary w-100 mt-3"> <i class="fa-solid fa-plus"></i> Ajouter un client</button>
              </div>
            </div>

          </div>
          
        </form>
      </div>
      <div class=" modal-footer d-flex justify-content-between">

            <button type="button" class="btn btn-dark">Fermer</button>
          </div>
    </div>
  </div>
</div>