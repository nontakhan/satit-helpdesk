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

    $upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'requests';
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
        throw new Exception('ไม่สามารถสร้างโฟลเดอร์สำหรับเก็บรูปได้');
    }

    $filename = date('YmdHis') . '-' . bin2hex(random_bytes(8)) . '.' . $allowed_types[$mime_type];
    $target_path = $upload_dir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        throw new Exception('ไม่สามารถบันทึกรูปภาพได้');
    }

    return 'uploads/requests/' . $filename;
}

function getUploadErrorMessage($error_code)
{
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return 'ไฟล์รูปภาพมีขนาดใหญ่เกินกว่าที่ระบบรองรับ กรุณาเลือกไฟล์ไม่เกิน 5MB';
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
