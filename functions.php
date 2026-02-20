<?php
require_once("database.php");

function getLesSalaries($sort = 'id', $order = 'ASC', $search = '', $fields = ['nom', 'prenom', 'service']){
  try {
    global $conn ;
    $allowedSort = ['id', 'nom', 'prenom', 'date_naissance', 'date_embauche', 'salaire', 'service'];
    $allowedOrder = ['ASC', 'DESC'];
    $allowedFields = ['nom', 'prenom', 'service', 'salaire', 'id'];
    
    $sort = in_array($sort, $allowedSort) ? $sort : 'id';
    $order = in_array(strtoupper($order), $allowedOrder) ? strtoupper($order) : 'ASC';

    $sql = "SELECT * FROM salaries";
    $params = [];
    
    if ($search !== '' && !empty($fields)) {
      $whereClauses = [];
      foreach ($fields as $field) {
        if (in_array($field, $allowedFields)) {
          $whereClauses[] = "$field LIKE :search";
        }
      }
      if (!empty($whereClauses)) {
        $sql .= " WHERE (" . implode(" OR ", $whereClauses) . ")";
        $params[':search'] = '%' . $search . '%';
      }
    }
    
    $sql .= " ORDER BY $sort $order";
    
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $val) {
      $stmt->bindValue($key, $val);
    }
    $stmt->execute();
    $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultat ;
  }
  catch(PDOException $e){
    echo "Erreur : " . $e->getMessage();
    return []; 
  }
}

function getSalariesParService(){
  try {
    global $conn;
    $sql = "SELECT service, COUNT(*) AS nombre_salaries FROM salaries GROUP BY service";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultat;
  } catch(PDOException $e){
    echo "Erreur lors de la récupération du nombre de salariés par service : " . $e->getMessage();
    return [];
  }
}

function getSalaireMinMax(){
  try {
    global $conn;
    $sql = "SELECT MIN(salaire) AS salaire_min, MAX(salaire) AS salaire_max FROM salaries";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultat;
  } catch(PDOException $e){
    echo "Erreur lors de la récupération des salaires min/max : " . $e->getMessage();
    return ['salaire_min' => 0, 'salaire_max' => 0];
  }
}

function getSalaireMoyen(){
  try {
    global $conn;
    $sql = "SELECT AVG(salaire) AS salaire_moyen FROM salaries";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
    return round($resultat['salaire_moyen'], 2);
  } catch(PDOException $e){
    echo "Erreur lors de la récupération du salaire moyen : " . $e->getMessage();
    return 0;
  }
}

function getNombreSalaries(){
  try {
    global $conn;
    $sql = "SELECT COUNT(*) AS total_salaries FROM salaries";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultat['total_salaries'];
  } catch(PDOException $e){
    echo "Erreur lors de la récupération du nombre de salariés : " . $e->getMessage();
    return 0;
  }
}

function logAction($action, $details = null) {
  try {
    global $conn;
    $userId = $_SESSION['userid'] ?? 0;
    $username = $_SESSION['username'] ?? 'Système';
    
    $sql = "INSERT INTO logs (user_id, username, action, details) VALUES (:uid, :uname, :action, :details)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':uname', $username);
    $stmt->bindValue(':action', $action);
    $stmt->bindValue(':details', $details);
    $stmt->execute();
  } catch(PDOException $e) {
    // RAF
  }
}

function getUniqueUsernames() {
  try {
    global $conn;
    $sql = "SELECT DISTINCT username FROM logs ORDER BY username ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  } catch(PDOException $e) {
    return [];
  }
}

function getUniqueActions() {
  try {
    global $conn;
    $sql = "SELECT DISTINCT action FROM logs ORDER BY action ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  } catch(PDOException $e) {
    return [];
  }
}

function getLogs($username = null, $action = null, $startDate = null, $endDate = null) {
  try {
    global $conn;
    $sql = "SELECT * FROM logs WHERE 1=1";
    $params = [];

    if ($username !== null && $username !== '') {
      $sql .= " AND username LIKE :username";
      $params[':username'] = '%' . $username . '%';
    }
    if ($action !== null && $action !== '') {
      $sql .= " AND action LIKE :action";
      $params[':action'] = '%' . $action . '%';
    }
    if ($startDate !== null && $startDate !== '') {
      $sql .= " AND created_at >= :start_date";
      $params[':start_date'] = $startDate . ' 00:00:00';
    }
    if ($endDate !== null && $endDate !== '') {
      $sql .= " AND created_at <= :end_date";
      $params[':end_date'] = $endDate . ' 23:59:59';
    }

    $sql .= " ORDER BY created_at DESC LIMIT 100";
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $val) {
      $stmt->bindValue($key, $val);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch(PDOException $e) {
    return [];
  }
}
?>