<?php
require_once 'session.php';
require_once 'database.php';
require_once 'functions.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['droit']) || $_SESSION['droit'] != '2') {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Accès refusé']);
  exit;
}

$allowed = ['nom','prenom','date_naissance','date_embauche','salaire','service'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
  exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$field = $_POST['field'] ?? '';
$value = $_POST['value'] ?? '';

if ($id <= 0 || !in_array($field, $allowed, true)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
  exit;
}

$errors = [];

try {
  switch ($field) {
    case 'nom':
    case 'prenom':
    case 'service':
      $value = trim($value);
      if ($value === '' || mb_strlen($value) < 2 || mb_strlen($value) > 50) {
        $errors[] = 'Texte invalide';
      }
      break;
    case 'salaire':
      if (!is_numeric($value) || (float)$value < 0) {
        $errors[] = 'Salaire invalide';
      } else {
        $value = intval($value);
      }
      break;
    case 'date_naissance':
    case 'date_embauche':
      $formats = ['Y-m-d','d/m/Y','d-m-Y','Y/m/d'];
      $dt = null;
      foreach ($formats as $fmt) {
        $tmp = DateTime::createFromFormat($fmt, $value);
        if ($tmp) { $dt = $tmp; break; }
      }
      if (!$dt) {
        $ts = strtotime($value);
        if ($ts !== false) { $dt = (new DateTime())->setTimestamp($ts); }
      }
      if (!$dt) {
        $errors[] = 'Date invalide';
      } else {
        $value = $dt->format('Y-m-d');
      }
      break;
  }

  if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => $errors[0]]);
    exit;
  }

  $sql = "UPDATE salaries SET $field = :val WHERE id = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':val', $value);
  $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  $stmt->execute();

  logAction("Mise à jour champ", "ID: $id, Champ: $field, Valeur: $value");

  echo json_encode(['success' => true, 'message' => 'Mise à jour effectuée', 'value' => $value]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}