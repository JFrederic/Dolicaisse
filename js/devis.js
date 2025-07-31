$(function() {
  // Variables de gestion produits du devis
  var produitsDevis = [];

  // Sélection des éléments
  var $searchInput = $('#produitSearch');
  var $suggestions = $('#suggestionsProduit');
  var $addBtn = $('#addProduitBtn');
  var $tableBody = $('#tableProduitsDevis tbody');

  // Recherche produit à la volée
  $searchInput.on('input', function() {
    var query = $(this).val();
    if (query.length < 2) {
      $suggestions.hide();
      return;
    }
    searchProduit(query).then(function(produits) {
      $suggestions.empty();
      if (Array.isArray(produits) && produits.length > 0) {
        produits.forEach(function(prod) {
          var $li = $('<li class="list-group-item list-group-item-action"></li>');
          $li.html('<strong>'+prod.ref+'</strong> - '+prod.label);
          $li.attr('data-id', prod.id || prod.rowid);
          $li.attr('data-ref', prod.ref);
          $li.attr('data-label', prod.label);
          $li.attr('data-pu', prod.price || 0);
          $suggestions.append($li);
        });
        $suggestions.show();
      } else {
        $suggestions.hide();
      }
    });
  });

  // Sélection d'un produit
  $suggestions.on('mousedown', 'li', function() {
    var id = $(this).data('id');
    var ref = $(this).data('ref');
    var label = $(this).data('label');
    var pu = $(this).data('pu');
    // Préremplit l'input avec la réf sélectionnée
    $searchInput.val(ref + ' - ' + label);
    $searchInput.data('selected', {id: id, ref: ref, label: label, pu: pu});
    $suggestions.hide();
  });

  // Ajout du produit à la liste
  $addBtn.on('click', function() {
    var selected = $searchInput.data('selected');
    if (!selected) {
      alert('Sélectionnez un produit dans la liste.');
      return;
    }
    // Vérifie doublon
    if (produitsDevis.some(p => p.id == selected.id)) {
      alert('Ce produit a déjà été ajouté.');
      return;
    }
    // Ajoute avec quantité/prix par défaut
    produitsDevis.push({
      id: selected.id,
      ref: selected.ref,
      label: selected.label,
      qty: 1,
      pu: selected.pu
    });
    renderProduitsDevis();
    // Reset input
    $searchInput.val('').removeData('selected');
  });

  // Fonction d'affichage de la liste des produits dans le tableau
  function renderProduitsDevis() {
    $tableBody.empty();
    produitsDevis.forEach(function(prod, idx) {
      var $row = $('<tr></tr>');
      var prixttc = parseFloat(prod.pu)
      prixttc = isNaN(prixttc) ? 0 : prixttc.toFixed(2);
      $row.append('<td>'+prod.ref+'</td>');
      $row.append('<td>'+prod.label+'</td>');
      $row.append('<td><input type="number" min="1" class="form-control form-control-sm qty-produit" data-idx="'+idx+'" value="'+prod.qty+'"></td>');
      $row.append('<td><input type="number" min="0" step="0.01" class="form-control form-control-sm pu-produit" data-idx="'+idx+'" value="'+prixttc+'"></td>');
      $row.append('<td><button type="button" class="btn btn-danger btn-sm remove-produit" data-idx="'+idx+'">Supprimer</button></td>');
      $tableBody.append($row);
    });
  }

  // Modification quantité/prix dans le tableau
  $tableBody.on('input', '.qty-produit', function() {
    var idx = $(this).data('idx');
    produitsDevis[idx].qty = parseFloat($(this).val());
  });
  $tableBody.on('input', '.pu-produit', function() {
    var idx = $(this).data('idx');
    produitsDevis[idx].pu = parseFloat($(this).val());
  });

  // Suppression produit
  $tableBody.on('click', '.remove-produit', function() {
    var idx = $(this).data('idx');
    produitsDevis.splice(idx, 1);
    renderProduitsDevis();
  });

  // Submit du formulaire = envoi des produitsDevis
  $('#createDevisForm').on('submit', function(e) {
    e.preventDefault();
    // ... récupération client, etc. comme avant ...
    const tiers_id = $('#devisClientId').val();
    const date = $('#devisDate').val();
    if (!tiers_id || produitsDevis.length === 0) {
      $('#createDevisResult').html('<div class="alert alert-danger">Veuillez remplir tous les champs requis.</div>');
      return;
    }
    const data = {
      tiers_id: tiers_id,
      date: date,
      produits: produitsDevis.map(function(prod) {
        var ttc = parseFloat(prod.pu);
        ttc = isNaN(ttc) ? 0 : ttc.toFixed(2); // Assure que le prix est en format décimal
        return {
          id: prod.id,
          ref: prod.ref,
          designation: prod.label,
          qty: prod.qty,
          pu: ttc // Assure que le prix est en format décimal
        };
      })
    };
    createDevis(data).then(function(resp) {
      if (resp && resp.id) {
        $('#createDevisResult').html('<div class="alert alert-success">Devis créé avec succès (ID : '+resp.id+')</div>');
        produitsDevis = [];
        renderProduitsDevis();
        $('#createDevisForm')[0].reset();
        $('#devisClientId').val('');
      } else {
        $('#createDevisResult').html('<div class="alert alert-danger">Erreur création devis : '+(resp && resp.message ? resp.message : "Erreur inconnue")+'</div>');
      }
    }).catch(function() {
      $('#createDevisResult').html('<div class="alert alert-danger">Erreur serveur lors de la création du devis.</div>');
    });
  });

  // Cacher les suggestions si click ailleurs
  $(document).on('mousedown', function(e) {
    if (!$(e.target).closest('#produitSearch, #suggestionsProduit').length) {
      $suggestions.hide();
    }
  });

  // Entrée dans la search = sélectionner le premier résultat
  $searchInput.on('keydown', function(e) {
    if (e.key === 'Enter' && $suggestions.children('li').length) {
      var $first = $suggestions.children('li').first();
      $searchInput.val($first.data('ref') + ' - ' + $first.data('label'));
      $searchInput.data('selected', {
        id: $first.data('id'),
        ref: $first.data('ref'),
        label: $first.data('label'),
        pu: $first.data('pu')
      });
      $suggestions.hide();
      e.preventDefault();
    }
  });
});



// RECHERCHE CLIENTS DANS LE DEVIS
$(function() {
  var $inputClient = $('#devisClient');
  var $suggestionsList = $('#suggestionsClient');
  var $hiddenClientId = $('#devisClientId');

  // Suggestion client en tapant
  $inputClient.on('input', function() {
    var query = $(this).val();
    $hiddenClientId.val('');
    if (query.length < 2) {
      $suggestionsList.hide();
      return;
    }
    // searchTiers() DOIT retourner une promise, sinon remplace ici par ton appel AJAX natif
    searchTiers(query).then(function(clients) {
      $suggestionsList.empty();
      if (Array.isArray(clients) && clients.length > 0) {
        clients.forEach(function(tier) {
            console.log(tier.name)
          var $li = $('<li class="list-group-item list-group-item-action"></li>');
          $li.text(
            tier.name
          );
          $li.attr('data-id', tier.id || tier.rowid || tier.socid);
          $li.attr('data-nom', tier.name);
          $suggestionsList.append($li);
        });
        $suggestionsList.show();
      } else {
        $suggestionsList.hide();
      }
    });
  });

  // Sélection par clic
  $suggestionsList.on('mousedown', 'li', function(e) {
    $inputClient.val($(this).attr('data-nom'));
    $hiddenClientId.val($(this).attr('data-id'));
    $suggestionsList.hide();
  });

  // Masquer la liste si on clique ailleurs
  $(document).on('mousedown', function(e) {
    if (!$(e.target).closest('#devisClient, #suggestionsClient').length) {
      $suggestionsList.hide();
    }
  });

  // Touche entrée = sélection du premier résultat
  $inputClient.on('keydown', function(e) {
    if (e.key === 'Enter' && $suggestionsList.children('li').length) {
      var $first = $suggestionsList.children('li').first();
      $inputClient.val($first.attr('data-nom'));
      $hiddenClientId.val($first.attr('data-id'));
      $suggestionsList.hide();
      e.preventDefault();
    }
  });

  // Affiche suggestions si déjà chargées
  $inputClient.on('focus', function() {
    if ($suggestionsList.children().length) $suggestionsList.show();
  });
});



