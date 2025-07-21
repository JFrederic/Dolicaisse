<div class="card caddie">
  <div class="card-header">
    <h5 class="card-title">
      <i class="fa-solid fa-cart-shopping"></i> Caddie 1
      <span id="selectedClientDisplay" style="margin-left:15px; color:#ffffff; font-weight:normal;"></span>
      <input type="hidden" id="selectedClientId" value="1393">
    </h5>
    <button type="button" class="btn btn-danger btnMultiplePanier">Client Suivant</button>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-12">
        <table class="table" style="padding: 0 10px;">
          <thead>
            <tr>
              <th scope="col" class="col-designation entete">Désignation</th>
              <th scope="col" class="entete">QTE</th>
              <th scope="col" class="entete">PU €</th>
              <th scope="col" class="entete">Montant €</th>
              <th scope="col" class="entete">Remise %</th>
              <th scope="col" class="entete">Remise €</th>
            </tr>
          </thead>

          <tbody id="panierItems">
           <tr>
            <td><span class="designation">SAC A DOS 28X35.5X43CM</span></td>
            <td><input type="number" class="qty" min="1" value="1" style="width:60px" onchange="majQtePanier(0, this.value)"></td>
            <td><span class="pu"><b>175.02</b></span></td>
            <td><span class="mtotal"><b>175.02</b></span></td>
            <td><input type="number" class="remise" min="0" max="100" value="0" style="width:60px" onchange="majRemisePanier(0, this.value)"></td>
            <td><input type="number" class="remise-euro" min="0" step="0.01" value="0" style="width:60px" onchange="majRemiseEuroPanier(0, this.value)"></td>
            <td>  
              <input type="hidden" class="idproduit" value="15830">
              <input type="hidden" class="tva" value="0">
              <button class="btn btn-sm btn-danger" onclick="supprimeProduitPanier(0)">×</button></td>

            </tr>
            <!-- Les lignes du panier seront ajoutées ici dynamiquement -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>