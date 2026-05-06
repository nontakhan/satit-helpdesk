<?php
/**
 * telegram_sender.php
 * ฟังก์ชันสำหรับส่งข้อความและรูปภาพไปยัง Telegram Bot API
 */
function sendTelegramMessage($botToken, $chatId, $message)
{
    $apiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $postData = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    // SSL options for Windows compatibility
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        error_log('Telegram message cURL Error: ' . curl_error($ch));
    }

    curl_close($ch);

    logTelegramApiError('sendMessage', $httpCode, $response);

    return $response;
}

function isTelegramConfigured()
{
    return defined('TELEGRAM_BOT_TOKEN') &&
        defined('TELEGRAM_CHAT_ID') &&
        TELEGRAM_BOT_TOKEN !== '' &&
        TELEGRAM_CHAT_ID !== '' &&
        TELEGRAM_BOT_TOKEN !== 'YOUR_BOT_TOKEN_HERE' &&
        TELEGRAM_CHAT_ID !== 'YOUR_CHAT_ID_HERE';
}

function getTelegramConfigStatus()
{
    $token = defined('TELEGRAM_BOT_TOKEN') ? TELEGRAM_BOT_TOKEN : '';
    $chatId = defined('TELEGRAM_CHAT_ID') ? TELEGRAM_CHAT_ID : '';

    return [
        'configured' => isTelegramConfigured(),
        'token_set' => $token !== '' && $token !== 'YOUR_BOT_TOKEN_HERE',
        'chat_id_set' => $chatId !== '' && $chatId !== 'YOUR_CHAT_ID_HERE',
        'curl_loaded' => function_exists('curl_init'),
        'env_file_exists' => file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env'),
        'token_preview' => maskTelegramToken($token),
        'chat_id' => $chatId !== 'YOUR_CHAT_ID_HERE' ? $chatId : ''
    ];
}

function maskTelegramToken($token)
{
    if ($token === '' || $token === 'YOUR_BOT_TOKEN_HERE') {
        return '';
    }

    return substr($token, 0, 8) . '...' . substr($token, -4);
}

function sendTelegramPhoto($botToken, $chatId, $photoPath, $caption = '')
{
    if (!is_file($photoPath)) {
        error_log('Telegram photo file not found: ' . $photoPath);
        return false;
    }

    $apiUrl = "https://api.telegram.org/bot{$botToken}/sendPhoto";
    $postData = [
        'chat_id' => $chatId,
        'photo' => new CURLFile($photoPath),
        'caption' => mb_substr($caption, 0, 1024)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    // SSL options for Windows compatibility
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        error_log('Telegram photo cURL Error: ' . curl_error($ch));
    }

    curl_close($ch);

    logTelegramApiError('sendPhoto', $httpCode, $response);

    return $response;
}

function logTelegramApiError($method, $httpCode, $response)
{
    if ($response === false) {
        error_log('Telegram API ' . $method . ' failed: empty cURL response');
        return;
    }

    $decodedResponse = json_decode($response, true);
    if ($httpCode < 200 || $httpCode >= 300 || !is_array($decodedResponse) || empty($decodedResponse['ok'])) {
        error_log('Telegram API ' . $method . ' error. HTTP ' . $httpCode . ' Response: ' . $response);
    }
}
?>
