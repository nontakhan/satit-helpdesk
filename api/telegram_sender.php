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
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    applyTelegramCurlOptions($ch);

    $response = curl_exec($ch);
    $curlInfo = curl_getinfo($ch);
    $httpCode = isset($curlInfo['http_code']) ? $curlInfo['http_code'] : 0;
    $curlError = curl_errno($ch) ? curl_error($ch) : '';

    if ($curlError !== '') {
        error_log('Telegram message cURL Error: ' . $curlError);
    }

    curl_close($ch);

    recordTelegramApiResult('sendMessage', $httpCode, $response, $curlError, $curlInfo);

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

function sendConfiguredTelegramMessage($message, $context = 'notification')
{
    if (!isTelegramConfigured()) {
        error_log('Telegram ' . $context . ' skipped: bot token or chat id is not configured. Check .env on this server.');
        return false;
    }

    $response = sendTelegramMessage(TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID, $message);
    $lastResult = getLastTelegramApiResult();

    if (!is_array($lastResult) || empty($lastResult['ok'])) {
        error_log('Telegram ' . $context . ' failed: ' . json_encode($lastResult, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    return $response;
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
        'force_ipv4' => telegramEnvEnabled('TELEGRAM_FORCE_IPV4', true),
        'proxy_set' => telegramEnvValue('TELEGRAM_PROXY_URL') !== '',
        'ssl_verify' => telegramEnvEnabled('TELEGRAM_SSL_VERIFY', true),
        'timezone' => date_default_timezone_get(),
        'server_time' => date('Y-m-d H:i:s'),
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
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    applyTelegramCurlOptions($ch);

    $response = curl_exec($ch);
    $curlInfo = curl_getinfo($ch);
    $httpCode = isset($curlInfo['http_code']) ? $curlInfo['http_code'] : 0;
    $curlError = curl_errno($ch) ? curl_error($ch) : '';

    if ($curlError !== '') {
        error_log('Telegram photo cURL Error: ' . $curlError);
    }

    curl_close($ch);

    recordTelegramApiResult('sendPhoto', $httpCode, $response, $curlError, $curlInfo);

    return $response;
}

function applyTelegramCurlOptions($ch)
{
    curl_setopt($ch, CURLOPT_USERAGENT, 'satit-helpdesk/telegram-notifier');

    if (telegramEnvEnabled('TELEGRAM_FORCE_IPV4', true) && defined('CURL_IPRESOLVE_V4')) {
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    }

    $proxyUrl = telegramEnvValue('TELEGRAM_PROXY_URL');
    if ($proxyUrl !== '') {
        curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
    }

    if (telegramEnvEnabled('TELEGRAM_SSL_VERIFY', true)) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    } else {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
}

function telegramEnvValue($key, $default = '')
{
    if (function_exists('env')) {
        return trim((string)env($key, $default));
    }

    $value = getenv($key);
    return $value === false ? $default : trim((string)$value);
}

function telegramEnvEnabled($key, $default = false)
{
    $value = strtolower(telegramEnvValue($key, $default ? 'true' : 'false'));
    return in_array($value, ['1', 'true', 'yes', 'on'], true);
}

function recordTelegramApiResult($method, $httpCode, $response, $curlError = '', $curlInfo = [])
{
    $decodedResponse = json_decode((string)$response, true);

    $GLOBALS['telegram_last_result'] = [
        'method' => $method,
        'http_code' => $httpCode,
        'curl_error' => $curlError,
        'ok' => is_array($decodedResponse) ? ($decodedResponse['ok'] ?? null) : null,
        'error_code' => is_array($decodedResponse) ? ($decodedResponse['error_code'] ?? null) : null,
        'description' => is_array($decodedResponse) ? ($decodedResponse['description'] ?? null) : null,
        'primary_ip' => isset($curlInfo['primary_ip']) ? $curlInfo['primary_ip'] : null,
        'primary_port' => isset($curlInfo['primary_port']) ? $curlInfo['primary_port'] : null,
        'total_time' => isset($curlInfo['total_time']) ? $curlInfo['total_time'] : null,
        'connect_time' => isset($curlInfo['connect_time']) ? $curlInfo['connect_time'] : null,
    ];

    logTelegramApiError($method, $httpCode, $response);
}

function getLastTelegramApiResult()
{
    return $GLOBALS['telegram_last_result'] ?? null;
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
