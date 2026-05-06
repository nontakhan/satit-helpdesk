<?php
/**
 * config.php
 * ไฟล์สำหรับตั้งค่าต่างๆ ของระบบ satit-helpdesk
 */

// โหลด environment variables จาก .env (ถ้ายังไม่ได้โหลด)
if (!function_exists('env')) {
    require_once __DIR__ . '/env_loader.php';
}

// --- ชื่อระบบ ---
define('APP_NAME', 'satit-helpdesk');

// --- Timezone ---
define('APP_TIMEZONE', env('APP_TIMEZONE', 'Asia/Bangkok'));
date_default_timezone_set(APP_TIMEZONE);

// --- Telegram Bot Settings ---
// ค่าจะถูกอ่านจากไฟล์ .env โดยอัตโนมัติ
define('TELEGRAM_BOT_TOKEN', env('TELEGRAM_BOT_TOKEN', 'YOUR_BOT_TOKEN_HERE'));
define('TELEGRAM_CHAT_ID', env('TELEGRAM_CHAT_ID', 'YOUR_CHAT_ID_HERE'));
define('TELEGRAM_FORCE_IPV4', env('TELEGRAM_FORCE_IPV4', 'true'));
define('TELEGRAM_PROXY_URL', env('TELEGRAM_PROXY_URL', ''));
define('TELEGRAM_SSL_VERIFY', env('TELEGRAM_SSL_VERIFY', 'false'));
