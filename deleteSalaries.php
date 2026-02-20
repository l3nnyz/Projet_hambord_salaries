<?php
  require_once 'session.php';
  require_once 'database.php';
  require_once 'functions.php';

  if (!isset($_SESSION['droit']) || $_SESSION['droit'] != '2') {
    http_response_code(403);
    echo "Accès refusé : vous n'avez pas les droits nécessaires pour supprimer un salarié.";
    exit();
  }

  if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM salaries WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    logAction("Suppression salarié", "ID: $id");
    
    header('Location: listeSalaries.php');
    exit();
  } else {
    echo "ID du salarié non spécifié.";
    exit();
  }
?>