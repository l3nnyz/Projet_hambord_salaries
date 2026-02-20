<?php
session_start();
require_once 'database.php';
require_once 'functions.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: listeSalaries.php');
    exit;
}

$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_input = $_POST['username'] ?? '';
    $password_input = $_POST['password'] ?? '';

    try {
        $sql = "SELECT id, nom, motdepasse, droit FROM users WHERE nom = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username_input, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password_input === $user['motdepasse']) {
            $_SESSION['loggedin'] = true;
            $_SESSION['userid'] = $user['id'];
            $_SESSION['username'] = $user['nom'];
            $_SESSION['droit'] = $user['droit'];

            logAction("Connexion", "Utilisateur connecté");

            header('Location: listeSalaries.php');
            exit;
        } else {
            $_SESSION['login_error'] = 'Nom d\'utilisateur ou mot de passe incorrect.';
            header('Location: login.php');
            exit;
        }
    } catch(PDOException $e) {
        $_SESSION['login_error'] = "Erreur de connexion à la base de données : " . $e->getMessage();
        header('Location: login.php');
        exit;
    }
}
?>
<?php include 'header.php'; ?>
    <div class="container my-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="text-center">Connexion</h3>
            </div>
            <div class="card-body">
              <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                  <?= htmlspecialchars($error); ?>
                </div>
              <?php endif; ?>
              <form action="login.php" method="POST">
                <div class="mb-3">
                  <label for="username" class="form-label">Nom d'utilisateur</label>
                  <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Mot de passe</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php include 'footer.html'; ?>