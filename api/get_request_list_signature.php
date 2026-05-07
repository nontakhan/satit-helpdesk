<?php
session_start();
header('Content-Type: application/json');

require_once '../db_connect.php';

$response = [
    'success' => false,
    'message' => '',
    'count' => 0,
    'latest_id' => 0
];

if (!isset($_SESSION['admin_id'])) {
    $response['message'] = 'กรุณา login ก่อนทำรายการ';
    echo json_encode($response);
    exit();
}

$status_id = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : 0;

try {
    $where = '';
    $params = [];
    $types = '';

    if ($status_id > 0) {
        $where = ' WHERE current_status_id = ?';
        $params[] = $status_id;
        $types .= 'i';
    }

    $sql = "SELECT COUNT(id) AS total_count, COALESCE(MAX(id), 0) AS latest_id FROM requests" . $where;
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    $response['success'] = true;
    $response['count'] = (int)($row['total_count'] ?? 0);
    $response['latest_id'] = (int)($row['latest_id'] ?? 0);
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>
