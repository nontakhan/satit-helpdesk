<?php

function handleRequestImageUpload($field_name = 'request_image')
{
    if (
        !isset($_FILES[$field_name]) ||
        !is_array($_FILES[$field_name]) ||
        $_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    $file = $_FILES[$field_name];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception(getUploadErrorMessage($file['error']));
    }

    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        throw new Exception('รูปภาพต้องมีขนาดไม่เกิน 5MB');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);
    $allowed_types = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    if (!isset($allowed_types[$mime_type])) {
        throw new Exception('รองรับเฉพาะไฟล์รูปภาพ JPG, PNG, WEBP หรือ GIF');
    }

    $upload_dir = ensureRequestUploadDirectory();

    $filename = date('YmdHis') . '-' . bin2hex(random_bytes(8)) . '.' . $allowed_types[$mime_type];
    $target_path = $upload_dir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        $last_error = error_get_last();
        $error_message = is_array($last_error) && isset($last_error['message']) ? $last_error['message'] : 'unknown error';
        error_log('Request image move failed. Target: ' . $target_path . ' Error: ' . $error_message);
        throw new Exception('ไม่สามารถบันทึกรูปภาพได้');
    }

    return 'uploads/requests/' . $filename;
}

function ensureRequestUploadDirectory()
{
    $upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'requests';

    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
        error_log('Request image upload directory creation failed: ' . $upload_dir);
        throw new Exception('ไม่สามารถสร้างโฟลเดอร์สำหรับเก็บรูปได้');
    }

    if (!is_writable($upload_dir)) {
        @chmod($upload_dir, 0775);
    }

    if (!is_writable($upload_dir)) {
        error_log('Request image upload directory is not writable: ' . $upload_dir);
        throw new Exception('โฟลเดอร์เก็บรูปภาพไม่สามารถเขียนไฟล์ได้ กรุณาแจ้งผู้ดูแลระบบ');
    }

    return $upload_dir;
}

function getUploadErrorMessage($error_code)
{
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return 'ไฟล์รูปภาพใหญ่เกินค่าที่ PHP อนุญาตในขณะนี้ (' . getPhpUploadLimitLabel() . ') กรุณาเลือกไฟล์ให้เล็กกว่านี้ หรือตรวจค่า upload_max_filesize/post_max_size ของ server';
        case UPLOAD_ERR_PARTIAL:
            return 'อัปโหลดรูปภาพไม่สมบูรณ์ กรุณาเลือกไฟล์แล้วส่งใหม่อีกครั้ง';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'เซิร์ฟเวอร์ไม่มีโฟลเดอร์ชั่วคราวสำหรับอัปโหลดไฟล์ กรุณาแจ้งผู้ดูแลระบบ';
        case UPLOAD_ERR_CANT_WRITE:
            return 'เซิร์ฟเวอร์ไม่สามารถเขียนไฟล์รูปภาพได้ กรุณาแจ้งผู้ดูแลระบบ';
        case UPLOAD_ERR_EXTENSION:
            return 'ส่วนขยายของ PHP ปฏิเสธการอัปโหลดไฟล์ กรุณาแจ้งผู้ดูแลระบบ';
        default:
            return 'ไม่สามารถอัปโหลดรูปได้ กรุณาลองใหม่อีกครั้ง';
    }
}

function getPhpUploadLimitLabel()
{
    $upload_limit = parsePhpSize(ini_get('upload_max_filesize'));
    $post_limit = parsePhpSize(ini_get('post_max_size'));
    $limits = array_filter([$upload_limit, $post_limit]);

    if (empty($limits)) {
        return 'ไม่ทราบค่า limit';
    }

    return formatBytes(min($limits));
}

function parsePhpSize($value)
{
    $value = trim((string)$value);
    if ($value === '') {
        return 0;
    }

    $unit = strtolower(substr($value, -1));
    $number = (float)$value;

    switch ($unit) {
        case 'g':
            return (int)($number * 1024 * 1024 * 1024);
        case 'm':
            return (int)($number * 1024 * 1024);
        case 'k':
            return (int)($number * 1024);
        default:
            return (int)$number;
    }
}

function formatBytes($bytes)
{
    if ($bytes >= 1024 * 1024) {
        return rtrim(rtrim(number_format($bytes / 1024 / 1024, 2, '.', ''), '0'), '.') . 'MB';
    }

    if ($bytes >= 1024) {
        return rtrim(rtrim(number_format($bytes / 1024, 2, '.', ''), '0'), '.') . 'KB';
    }

    return $bytes . ' bytes';
}
