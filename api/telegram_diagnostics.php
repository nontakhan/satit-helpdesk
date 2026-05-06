<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'กรุณาเข้าสู่ระบบผู้ดูแลระบบก่อน'
    ]);
    exit();
}

require_once '../config.php';
require_once 'telegram_sender.php';

$status = getTelegramConfigStatus();
$shouldSendTest = $_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['send']);

if ($shouldSendTest) {
    if (!$status['configured']) {
        echo json_encode([
            'success' => false,
            'test_sent' => false,
            'message' => 'ยังไม่ได้ตั้งค่า TELEGRAM_BOT_TOKEN หรือ TELEGRAM_CHAT_ID ในไฟล์ .env บน server',
            'status' => $status
        ]);
        exit();
    }

    if (!$status['curl_loaded']) {
        echo json_encode([
            'success' => false,
            'test_sent' => false,
            'message' => 'PHP cURL extension ยังไม่เปิดใช้งานบน server',
            'status' => $status
        ]);
        exit();
    }

    $message = "<b>satit-helpdesk Telegram test</b>\n";
    $message .= "ทดสอบการแจ้งเตือนจาก server เวลา " . date('d/m/Y H:i:s');
    $response = sendTelegramMessage(TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID, $message);
    $decodedResponse = json_decode((string)$response, true);
    $lastResult = getLastTelegramApiResult();

    echo json_encode([
        'success' => is_array($decodedResponse) && !empty($decodedResponse['ok']),
        'test_sent' => true,
        'message' => is_array($decodedResponse) && !empty($decodedResponse['ok'])
            ? 'ส่งข้อความทดสอบ Telegram สำเร็จ'
            : 'ส่งข้อความทดสอบ Telegram ไม่สำเร็จ',
        'status' => $status,
        'last_result' => $lastResult,
        'telegram_response' => $decodedResponse
    ]);
    exit();
}

echo json_encode([
    'success' => true,
    'test_sent' => false,
    'message' => 'ตรวจพบการตั้งค่า Telegram แล้ว หากต้องการส่งทดสอบให้เปิด URL นี้พร้อม ?send=1',
    'status' => $status
]);
