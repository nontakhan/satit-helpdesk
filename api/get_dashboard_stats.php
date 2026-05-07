<?php
session_start();
header('Content-Type: application/json');

require_once '../db_connect.php';

$response = [
    'success' => false,
    'message' => '',
    'stats' => [
        'pending' => 0,
        'processing' => 0,
        'completed_month' => 0,
        'total_month' => 0,
        'latest_id' => 0
    ]
];

if (!isset($_SESSION['admin_id'])) {
    $response['message'] = 'กรุณา login ก่อนทำรายการ';
    echo json_encode($response);
    exit();
}

try {
    $current_month = date('Y-m');

    $stmt = $conn->prepare("
        SELECT
            SUM(CASE WHEN current_status_id = 1 THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN current_status_id = 2 THEN 1 ELSE 0 END) AS processing,
            SUM(CASE WHEN final_status_id = 3 AND DATE_FORMAT(repair_date, '%Y-%m') = ? THEN 1 ELSE 0 END) AS completed_month,
            SUM(CASE WHEN DATE_FORMAT(request_date, '%Y-%m') = ? THEN 1 ELSE 0 END) AS total_month,
            COALESCE(MAX(id), 0) AS latest_id
        FROM requests
    ");

    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param('ss', $current_month, $current_month);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $response['success'] = true;
    $response['stats'] = [
        'pending' => (int)($row['pending'] ?? 0),
        'processing' => (int)($row['processing'] ?? 0),
        'completed_month' => (int)($row['completed_month'] ?? 0),
        'total_month' => (int)($row['total_month'] ?? 0),
        'latest_id' => (int)($row['latest_id'] ?? 0)
    ];
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>
