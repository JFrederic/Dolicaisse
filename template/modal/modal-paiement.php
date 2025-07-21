<!-- MODAL Bootstrap paiement multi-moyens (auto-reset + validation clavier + focus) -->
<div class="modal" id="modal-paiement" tabindex="-1" aria-labelledby="modalCBLabel" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modalPaiementLabel">Paiement - Multi moyens</h4>
        <button type="button" class="btn-close"  aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <p class="text-center" style="font-size: 18px">
          Choisissez le montant à payer pour chaque moyen&nbsp;:<br>
          <span style="font-weight:600;">Total à payer&nbsp;: <span id="totalModalPaiement"></span> €</span>
        </p>
        <div class="row justify-content-center">
          <div class="col-12 col-md-4">
            <input type="number" min="0" step="0.01" id="inputMontant" class="form-control mb-2" placeholder="Montant" 
             onkeydown="if(event.key==='Enter'){ajouterPaiement('CB', this.value);}">
          </div>
        </div>
        <div class="row g-2 mb-3 justify-content-center">
          <div class="col-12 col-md-3 text-center">
            <!-- <input type="number" min="0" step="0.01" id="inputMontantCB" class="form-control mb-2" placeholder="Montant CB" 
                   onkeydown="if(event.key==='Enter'){ajouterPaiement('CB', this.value);}"> -->
            <button class="btn btn-outline-primary w-100" type="button" onclick="ajouterPaiement('CB', document.getElementById('inputMontant').value)">Ajouter CB</button>
          </div>
          <div class="col-12 col-md-3 text-center">
            
            <button class="btn btn-outline-success w-100" type="button" onclick="ajouterPaiement('Espèces', document.getElementById('inputMontant').value)">Ajouter Espèces</button>
          </div>
          <div class="col-12 col-md-3 text-center">
            <!-- <input type="number" min="0" step="0.01" id="inputMontantCheque" class="form-control mb-2" placeholder="Montant Chèque" 
                   onkeydown="if(event.key==='Enter'){ajouterPaiement('Chèque', this.value);}"> -->
            <button class="btn btn-outline-info w-100" type="button" onclick="ajouterPaiement('Chèque', document.getElementById('inputMontant').value)">Ajouter Chèque</button>
          </div>
        </div>
        <hr>
        <div class="mb-2">
          <div><b>Paiements ajoutés :</b></div>
          <div id="paiementsCourants"></div>
          <div class="mt-2"><b>Reste à payer :</b> <span id="resteAPayer">0.00 €</span></div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"  >Fermer</button>
        <button type="button" class="btn btn-primary" onclick="validerPaiement()">Valider le paiement</button>
      </div>
    </div>
  </div>
</div>