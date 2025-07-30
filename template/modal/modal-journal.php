<div class="modal fade" id="journalFacturesModal" tabindex="-1" aria-labelledby="journalFacturesLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="journalFacturesLabel">Journal des Factures</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3 mb-3" id="filtreJournalFacturesForm">
          <div class="col-md-3">
            <label for="dateDebut" class="form-label">Du</label>
            <input type="date" class="form-control" id="dateDebut">
          </div>
          <div class="col-md-3">
            <label for="dateFin" class="form-label">Au</label>
            <input type="date" class="form-control" id="dateFin">
          </div>
          <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Afficher</button>
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle" id="journalFacturesTable">
            <thead class="table-dark">
              <tr>
                <th>N° Ticket</th>
                <th>Date</th>
                <th>Heure</th>
                <th>TTC</th>
                <th>Client</th>
                <th>Paiement</th>
              </tr>
            </thead>
            <tbody>
              <!-- Rempli dynamiquement -->
            </tbody>
          </table>
        </div>
        <div id="journalFacturesInfo" class="mt-2 text-center"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
