<?php
  require_once 'session.php';
  include 'header.php';
  require_once 'database.php';
  require_once 'functions.php';

  try {
    $sort = $_GET['sort'] ?? 'id';
    $order = $_GET['order'] ?? 'ASC';
    $nextOrder = ($order === 'ASC') ? 'DESC' : 'ASC';
    
    $lesSalaries = getLesSalaries($sort, $order);
    $nombreSalaries = getNombreSalaries();
    $salaireMoyen = getSalaireMoyen();
    $salairesMinMax = getSalaireMinMax();
    $salariesParService = getSalariesParService();
  } catch(PDOException $e){
    echo "Erreur : " . $e->getMessage();
  }
?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100">
  <div id="updateSuccessToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
    <div class="d-flex">
      <div class="toast-body">Mise à jour réussie.</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
    </div>
  </div>
  </div>
<div class="container my-5">
  <h2>Statistiques des Salariés</h2>
  <div class="row  row cols-md-2 row-cols-4">
    <div class="col-md-3">
      <div class="card text-white bg-info mb-3">
        <div class="card-header">Nombre total de salariés</div>
        <div class="card-body">
          <h5 class="card-title"><?= htmlspecialchars($nombreSalaries); ?></h5>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-success mb-3">
        <div class="card-header">Salaire moyen</div>
        <div class="card-body">
          <h5 class="card-title"><?= htmlspecialchars($salaireMoyen); ?> €</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning mb-3">
        <div class="card-header">Salaire Min/Max</div>
        <div class="card-body">
          <h5 class="card-title">Min: <?= htmlspecialchars($salairesMinMax['salaire_min']); ?> €</h5>
          <h5 class="card-title">Max: <?= htmlspecialchars($salairesMinMax['salaire_max']); ?> €</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-primary mb-3">
        <div class="card-header">Salariés par service</div>
        <div class="card-body">
          <?php foreach ($salariesParService as $service): ?>
            <p class="card-text"><?= htmlspecialchars($service['service']); ?>: <?= htmlspecialchars($service['nombre_salaries']); ?></p>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
      <?php if (isset($_SESSION['droit']) && $_SESSION['droit'] == '2'): ?>
        <a href="addSalaries.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Ajouter un salarié</a>
      <?php endif; ?>
    </div>
    
    <div class="d-flex gap-2 align-items-center">
      <div class="input-group" style="max-width: 300px;">
        <span class="input-group-text bg-primary text-white"><i class="bi bi-search"></i></span>
        <input type="text" id="searchInput" class="form-control" placeholder="Rechercher..." aria-label="Recherche">
      </div>
      
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="searchOptions" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
          <i class="bi bi-gear"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="searchOptions" style="min-width: 200px;">
          <h6 class="dropdown-header px-0">Rechercher dans :</h6>
          <div class="form-check mb-2">
            <input class="form-check-input search-field" type="checkbox" id="checkNom" value="nom" checked>
            <label class="form-check-label" for="checkNom">Nom</label>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input search-field" type="checkbox" id="checkPrenom" value="prenom" checked>
            <label class="form-check-label" for="checkPrenom">Prénom</label>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input search-field" type="checkbox" id="checkService" value="service" checked>
            <label class="form-check-label" for="checkService">Service</label>
          </div>
          <div class="form-check">
            <input class="form-check-input search-field" type="checkbox" id="checkSalaire" value="salaire">
            <label class="form-check-label" for="checkSalaire">Salaire</label>
          </div>
        </div>
      </div>
    </div>
  </div>
   
  <div class="table-responsive">
    <table class="table table-hover align-middle" id="salariesTable">
      <thead class="table-dark">
        <tr id="tableHeader">
          <?php
          $columns = [
            'nom' => 'Nom',
            'prenom' => 'Prénom',
            'date_naissance' => 'Date de naissance',
            'date_embauche' => 'Date d\'embauche',
            'salaire' => 'Salaire',
            'service' => 'Service'
          ];
          // Add ID column if admin
          if (isset($_SESSION['droit']) && $_SESSION['droit'] == 2) {
              $columns = ['id' => 'ID'] + $columns; // Prepend 'id'
          }
          foreach ($columns as $colKey => $colLabel):
            $icon = 'bi-arrow-down-up';
            $opacity = 'opacity-50';
            if ($sort === $colKey) {
              $icon = ($order === 'ASC') ? 'bi-sort-up' : 'bi-sort-down';
              $opacity = 'opacity-100';
            }
          ?>
            <th class="sortable" data-sort="<?= $colKey ?>" data-order="<?= ($sort === $colKey) ? $nextOrder : 'ASC' ?>" style="cursor: pointer;">
              <div class="d-flex align-items-center justify-content-between">
                <span><?= htmlspecialchars($colLabel) ?></span>
                <i class="bi <?= $icon ?> ms-1 <?= $opacity ?>"></i>
              </div>
            </th>
          <?php endforeach; ?>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="salariesBody" data-user-droit="<?= htmlspecialchars($_SESSION['droit'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
      <?php if (empty($lesSalaries)): ?>
          <tr>
            <?php
            $colCount = count($columns) + 1; // +1 for Actions column
            ?>
            <td colspan="<?= $colCount ?>" class="text-center py-4 text-muted">Aucun salarié trouvé.</td>
          </tr>
        <?php else: ?>
      <?php foreach ($lesSalaries as $leSalarie): ?>    
          <tr>      
            <?php if (isset($_SESSION['droit']) && $_SESSION['droit'] == 2): ?>
              <td><?= htmlspecialchars( $leSalarie['id']); ?></td>
            <?php endif; ?>
            <td class="editable" data-id="<?= htmlspecialchars($leSalarie['id']); ?>" data-field="nom" data-type="text"><?= htmlspecialchars( $leSalarie['nom']); ?></td>  
            <td class="editable" data-id="<?= htmlspecialchars($leSalarie['id']); ?>" data-field="prenom" data-type="text"><?= htmlspecialchars($leSalarie['prenom']); ?></td>
            <td class="editable" data-id="<?= htmlspecialchars($leSalarie['id']); ?>" data-field="date_naissance" data-type="text"><?= htmlspecialchars( $leSalarie['date_naissance']); ?></td> 
            <td class="editable" data-id="<?= htmlspecialchars($leSalarie['id']); ?>" data-field="date_embauche" data-type="text"><?= htmlspecialchars( $leSalarie['date_embauche']); ?></td>
            <td class="editable" data-id="<?= htmlspecialchars($leSalarie['id']); ?>" data-field="salaire" data-type="number"><?= htmlspecialchars( $leSalarie['salaire']); ?></td>
            <td class="editable" data-id="<?= htmlspecialchars($leSalarie['id']); ?>" data-field="service" data-type="text"><?= htmlspecialchars( $leSalarie['service']); ?></td>
            <td>
              <?php if (isset($_SESSION['droit']) && $_SESSION['droit'] == '2'): ?>
                <a href="deleteSalaries.php?id=<?= htmlspecialchars($leSalarie['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer <?= htmlspecialchars($leSalarie['nom'] . ' ' . $leSalarie['prenom']); ?> ?');"><i class="bi bi-trash"></i> Supprimer</a>
              <?php else: ?>
                <span class="text-muted small">Action restreinte</span>
              <?php endif; ?>
            </td>
          </tr> 
      <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'footer.html'; ?>