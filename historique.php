<?php
  require_once 'session.php';
  require_once 'database.php';
  require_once 'functions.php';

  // Récupération des listes uniques pour les filtres
  $uniqueActions = getUniqueActions();

  // Récupération des paramètres de filtre
  $username = $_SESSION['username'] ?? '';
  $filterAction = $_GET['filter_action'] ?? '';
  $filterStartDate = $_GET['filter_start_date'] ?? '';
  $filterEndDate = $_GET['filter_end_date'] ?? '';

  $logs = getLogs($username, $filterAction, $filterStartDate, $filterEndDate);
?>
<?php include 'header.php'; ?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clock-history"></i> Mon Historique d'actions</h2>
        <a href="listeSalaries.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Retour à la liste</a>
    </div>

    <div class="mb-3">
        <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
            <i class="bi bi-funnel"></i> Afficher/Masquer les filtres
        </button>
    </div>

    <div class="collapse" id="filterCollapse">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filtres</h5>
            </div>
            <div class="card-body">
                <form action="historique.php" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="filter_action" class="form-label">Action</label>
                        <select class="form-select" id="filter_action" name="filter_action">
                            <option value="">Toutes</option>
                            <?php foreach ($uniqueActions as $action): ?>
                                <option value="<?= htmlspecialchars($action); ?>" <?= ($filterAction === $action) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($action); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filter_start_date" class="form-label">Date de début</label>
                        <input type="text" class="form-control flatpickr-input" id="filter_start_date" name="filter_start_date" value="<?= htmlspecialchars($filterStartDate); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="filter_end_date" class="form-label">Date de fin</label>
                        <input type="text" class="form-control flatpickr-input" id="filter_end_date" name="filter_end_date" value="<?= htmlspecialchars($filterEndDate); ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Appliquer les filtres</button>
                        <a href="historique.php" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Vous n'avez pas encore d'historique d'actions.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="text-nowrap small text-muted">
                                        <?= htmlspecialchars($log['created_at']); ?>
                                    </td>
                                    <td>
                                        <span class="fw-bold"><?= htmlspecialchars($log['action']); ?></span>
                                    </td>
                                    <td class="small">
                                        <?= htmlspecialchars($log['details'] ?? '-'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    flatpickr(".flatpickr-input", {
      dateFormat: "Y-m-d",
      locale: "fr"
    });
  });
</script>
<?php include 'footer.html'; ?>