<?php
require_once 'session.php';
require_once 'database.php';
require_once 'functions.php';

header('Content-Type: application/json; charset=UTF-8');

$sort = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'ASC';
$search = $_GET['search'] ?? '';
$fields = isset($_GET['fields']) ? explode(',', $_GET['fields']) : ['nom', 'prenom', 'service'];

try {
    $salaries = getLesSalaries($sort, $order, $search, $fields);
    echo json_encode(['success' => true, 'data' => $salaries]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
