<?php
  require_once 'session.php';
  require_once 'database.php';
  require_once 'functions.php';

  // Restriction au droit '2'
  if (!isset($_SESSION['droit']) || $_SESSION['droit'] != '2') {
    header('Location: listeSalaries.php');
    exit();
  }

  $leSalarie = null;
  $errors = [];

  if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo "ID du salarié non spécifié.";
    exit();
  }

  $id = (int)$_GET['id'];

  try {
    $sql = "SELECT * FROM salaries WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $leSalarie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$leSalarie) {
      http_response_code(404);
      echo "Salarié non trouvé.";
      exit();
    }
  } catch(PDOException $e) {
    http_response_code(500);
    echo "Erreur lors de la récupération des données du salarié : " . $e->getMessage();
    exit();
  }

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';
    $date_embauche = $_POST['date_embauche'] ?? '';
    $salaire = $_POST['salaire'] ?? '';
    $service = trim($_POST['service'] ?? '');

    if ($nom === '') { $errors['nom'] = "Nom requis."; }
    if ($prenom === '') { $errors['prenom'] = "Prénom requis."; }

    $dn = DateTime::createFromFormat('Y-m-d', $date_naissance) ?: null;
    $de = DateTime::createFromFormat('Y-m-d', $date_embauche) ?: null;
    if (!$dn) { $errors['date_naissance'] = "Date de naissance invalide."; }
    if (!$de) { $errors['date_embauche'] = "Date d'embauche invalide."; }
    if ($dn && $de && $de <= $dn) { $errors['date_embauche'] = "La date d'embauche doit être après la naissance."; }

    if (!is_numeric($salaire) || (float)$salaire < 0) { $errors['salaire'] = "Salaire invalide."; }
    if ($service === '') { $errors['service'] = "Service requis."; }

    if (empty($errors)) {
      try {
        $sql = "UPDATE salaries SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, date_embauche = :date_embauche, salaire = :salaire, service = :service WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':prenom', $prenom);
        $stmt->bindValue(':date_naissance', $dn->format('Y-m-d'));
        $stmt->bindValue(':date_embauche', $de->format('Y-m-d'));
        $stmt->bindValue(':salaire', number_format((float)$salaire, 2, '.', ''));
        $stmt->bindValue(':service', $service);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        logAction("Mise à jour complète salarié", "ID: $id, Nom: $nom, Prénom: $prenom");

        header('Location: listeSalaries.php?updated=1');
        exit();
      } catch(PDOException $e) {
        $errors['global'] = "Erreur lors de la mise à jour du salarié.";
      }
    } else {
      $leSalarie['nom'] = $nom;
      $leSalarie['prenom'] = $prenom;
      $leSalarie['date_naissance'] = $date_naissance;
      $leSalarie['date_embauche'] = $date_embauche;
      $leSalarie['salaire'] = $salaire;
      $leSalarie['service'] = $service;
    }
  }
?>
<?php include 'header.php'; ?>
    <div class="container my-5">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          <div class="card shadow">
            <div class="card-header bg-primary text-white">
              <h3 class="card-title mb-0">Modifier le salarié</h3>
            </div>
            <div class="card-body">
              <?php if (!empty($errors['global'])): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($errors['global'], ENT_QUOTES, 'UTF-8'); ?></div>
              <?php endif; ?>
              <form action="updateSalaries.php?id=<?= htmlspecialchars($leSalarie['id'], ENT_QUOTES, 'UTF-8'); ?>" method="POST" novalidate>
                <div class="mb-3">
                  <label for="nom" class="form-label">Nom</label>
                  <input type="text" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>" id="nom" name="nom" value="<?= htmlspecialchars($leSalarie['nom'], ENT_QUOTES, 'UTF-8'); ?>" required aria-invalid="<?= isset($errors['nom']) ? 'true' : 'false' ?>" aria-describedby="nomHelp" pattern="[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}" title="Lettres, espaces et apostrophes, 2 à 50 caractères">
                  <div id="nomHelp" class="invalid-feedback"><?= isset($errors['nom']) ? htmlspecialchars($errors['nom'], ENT_QUOTES, 'UTF-8') : 'Veuillez saisir un nom valide.' ?></div>
                </div>
                <div class="mb-3">
                  <label for="prenom" class="form-label">Prénom</label>
                  <input type="text" class="form-control<?= isset($errors['prenom']) ? ' is-invalid' : '' ?>" id="prenom" name="prenom" value="<?= htmlspecialchars($leSalarie['prenom'], ENT_QUOTES, 'UTF-8'); ?>" required aria-invalid="<?= isset($errors['prenom']) ? 'true' : 'false' ?>" aria-describedby="prenomHelp" pattern="[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}" title="Lettres, espaces et apostrophes, 2 à 50 caractères">
                  <div id="prenomHelp" class="invalid-feedback"><?= isset($errors['prenom']) ? htmlspecialchars($errors['prenom'], ENT_QUOTES, 'UTF-8') : 'Veuillez saisir un prénom valide.' ?></div>
                </div>
                <div class="mb-3">
                  <label for="date_naissance" class="form-label">Date de naissance</label>
                  <input type="date" class="form-control<?= isset($errors['date_naissance']) ? ' is-invalid' : '' ?>" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($leSalarie['date_naissance'], ENT_QUOTES, 'UTF-8'); ?>" required aria-invalid="<?= isset($errors['date_naissance']) ? 'true' : 'false' ?>" aria-describedby="dnHelp" title="Format AAAA-MM-JJ">
                  <div id="dnHelp" class="invalid-feedback"><?= isset($errors['date_naissance']) ? htmlspecialchars($errors['date_naissance'], ENT_QUOTES, 'UTF-8') : 'Veuillez saisir une date valide.' ?></div>
                </div>
                <div class="mb-3">
                  <label for="date_embauche" class="form-label">Date d'embauche</label>
                  <input type="date" class="form-control<?= isset($errors['date_embauche']) ? ' is-invalid' : '' ?>" id="date_embauche" name="date_embauche" value="<?= htmlspecialchars($leSalarie['date_embauche'], ENT_QUOTES, 'UTF-8'); ?>" required aria-invalid="<?= isset($errors['date_embauche']) ? 'true' : 'false' ?>" aria-describedby="deHelp" title="Format AAAA-MM-JJ">
                  <div id="deHelp" class="invalid-feedback"><?= isset($errors['date_embauche']) ? htmlspecialchars($errors['date_embauche'], ENT_QUOTES, 'UTF-8') : 'Veuillez saisir une date valide.' ?></div>
                </div>
                <div class="mb-3">
                  <label for="salaire" class="form-label">Salaire</label>
                  <input type="number" class="form-control<?= isset($errors['salaire']) ? ' is-invalid' : '' ?>" id="salaire" name="salaire" value="<?= htmlspecialchars($leSalarie['salaire'], ENT_QUOTES, 'UTF-8'); ?>" required aria-invalid="<?= isset($errors['salaire']) ? 'true' : 'false' ?>" aria-describedby="salaireHelp" min="0" step="0.01" inputmode="decimal" title="Nombre positif, 2 décimales maximum">
                  <div id="salaireHelp" class="invalid-feedback"><?= isset($errors['salaire']) ? htmlspecialchars($errors['salaire'], ENT_QUOTES, 'UTF-8') : 'Veuillez saisir un salaire valide.' ?></div>
                </div>
                <div class="mb-3">
                  <label for="service" class="form-label">Service</label>
                  <input type="text" class="form-control<?= isset($errors['service']) ? ' is-invalid' : '' ?>" id="service" name="service" value="<?= htmlspecialchars($leSalarie['service'], ENT_QUOTES, 'UTF-8'); ?>" required aria-invalid="<?= isset($errors['service']) ? 'true' : 'false' ?>" aria-describedby="serviceHelp" pattern="[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}" title="Nom de service, lettres et espaces">
                  <div id="serviceHelp" class="invalid-feedback"><?= isset($errors['service']) ? htmlspecialchars($errors['service'], ENT_QUOTES, 'UTF-8') : 'Veuillez saisir un service valide.' ?></div>
                </div>
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary">Mettre à jour</button>
                  <a href="listeSalaries.php" class="btn btn-outline-secondary">Annuler</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php include 'footer.html'; ?>
