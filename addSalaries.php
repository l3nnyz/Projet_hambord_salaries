<?php
  require_once 'session.php';
  require_once 'database.php';

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $date_embauche = $_POST['date_embauche'];
    $salaire = intval($_POST['salaire']);
    $service = $_POST['service'];

    try {
      $sql = "INSERT INTO salaries (nom, prenom, date_naissance, date_embauche, salaire, service) VALUES (:nom, :prenom, :date_naissance, :date_embauche, :salaire, :service)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':nom', $nom);
      $stmt->bindParam(':prenom', $prenom);
      $stmt->bindParam(':date_naissance', $date_naissance);
      $stmt->bindParam(':date_embauche', $date_embauche);
      $stmt->bindParam(':salaire', $salaire, PDO::PARAM_INT);
      $stmt->bindParam(':service', $service);
      $stmt->execute();

      header('Location: listeSalaries.php');
      exit();
    } catch(PDOException $e) {
      echo "Erreur lors de l'ajout du salarié : " . $e->getMessage();
    }
  }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un salarié</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  </head>
  <body>
    <div class="container my-5">
      <h1>Ajouter un nouveau salarié</h1>
      <form action="addSalaries.php" method="POST">
        <div class="mb-3">
          <label for="nom" class="form-label">Nom</label>
          <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="mb-3">
          <label for="prenom" class="form-label">Prénom</label>
          <input type="text" class="form-control" id="prenom" name="prenom" required>
        </div>
        <div class="mb-3">
          <label for="date_naissance" class="form-label">Date de naissance</label>
          <input type="text" class="form-control flatpickr-input" id="date_naissance" name="date_naissance" required>
        </div>
        <div class="mb-3">
          <label for="date_embauche" class="form-label">Date d'embauche</label>
          <input type="text" class="form-control flatpickr-input" id="date_embauche" name="date_embauche" required>
          <div class="invalid-feedback">Veuillez entrer une date d'embauche.</div>
        </div>
        <div class="mb-3">
          <label for="salaire" class="form-label">Salaire</label>
          <input type="number" class="form-control" id="salaire" name="salaire" required>
        </div>
        <div class="mb-3">
          <label for="service" class="form-label">Service</label>
          <input type="text" class="form-control" id="service" name="service" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="listeSalaries.php" class="btn btn-secondary">Annuler</a>
      </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        flatpickr(".flatpickr-input", {
          dateFormat: "Y-m-d",
          locale: "fr"
        });

        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);

        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
          input.addEventListener('input', function() {
            if (input.checkValidity()) {
              input.classList.remove('is-invalid');
              input.classList.add('is-valid');
            } else {
              input.classList.remove('is-valid');
              input.classList.add('is-invalid');
            }
          });
        });
      });
    </script>
  </body>
</html>