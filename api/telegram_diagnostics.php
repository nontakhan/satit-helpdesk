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
$networkStatus = getTelegramNetworkStatus();
$shouldSendTest = $_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['send']);

if ($shouldSendTest) {
    if (!$status['configured']) {
        echo json_encode([
            'success' => false,
            'test_sent' => false,
            'message' => 'ยังไม่ได้ตั้งค่า TELEGRAM_BOT_TOKEN หรือ TELEGRAM_CHAT_ID ในไฟล์ .env บน server',
            'status' => $status,
            'network' => $networkStatus
        ]);
        exit();
    }

    if (!$status['curl_loaded']) {
        echo json_encode([
            'success' => false,
            'test_sent' => false,
            'message' => 'PHP cURL extension ยังไม่เปิดใช้งานบน server',
            'status' => $status,
            'network' => $networkStatus
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
        'network' => $networkStatus,
        'last_result' => $lastResult,
        'telegram_response' => $decodedResponse
    ]);
    exit();
}

echo json_encode([
    'success' => true,
    'test_sent' => false,
    'message' => 'ตรวจพบการตั้งค่า Telegram แล้ว หากต้องการส่งทดสอบให้เปิด URL นี้พร้อม ?send=1',
    'status' => $status,
    'network' => $networkStatus
]);

function getTelegramNetworkStatus()
{
    $host = 'api.telegram.org';
    $addresses = gethostbynamel($host);
    $tcp = [
        'host' => $host,
        'port' => 443,
        'dns_resolved' => is_array($addresses) && count($addresses) > 0,
        'resolved_ipv4' => is_array($addresses) ? $addresses : [],
        'tcp_connect' => false,
        'connect_error' => null,
        'connect_errno' => null
    ];

    $errno = 0;
    $errstr = '';
    $start = microtime(true);
    $socket = @fsockopen('ssl://' . $host, 443, $errno, $errstr, 5);
    $tcp['elapsed_seconds'] = round(microtime(true) - $start, 3);

    if (is_resource($socket)) {
        $tcp['tcp_connect'] = true;
        fclose($socket);
    } else {
        $tcp['connect_error'] = $errstr;
        $tcp['connect_errno'] = $errno;
    }

    return $tcp;
}
