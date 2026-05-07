<?php
session_start();
require_once '../db_connect.php';

// ฟังก์ชันสำหรับ Redirect พร้อมข้อความ
function redirect_with_message($url, $message, $type = 'success') {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'text' => $message
    ];
    header("Location: " . $url);
    exit();
}

function get_request_image_file_path($image_path) {
    if (empty($image_path)) {
        return null;
    }

    $normalized_path = str_replace('\\', '/', $image_path);
    $expected_prefix = 'uploads/requests/';

    if (strpos($normalized_path, $expected_prefix) !== 0) {
        return null;
    }

    $filename = basename($normalized_path);
    if ($filename === '' || $filename !== substr($normalized_path, strlen($expected_prefix))) {
        return null;
    }

    $upload_dir = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'requests');
    if ($upload_dir === false) {
        return null;
    }

    $file_path = $upload_dir . DIRECTORY_SEPARATOR . $filename;
    $real_file_path = realpath($file_path);

    if ($real_file_path === false || strpos($real_file_path, $upload_dir . DIRECTORY_SEPARATOR) !== 0) {
        return null;
    }

    return $real_file_path;
}

function delete_request_image_file($image_path) {
    $file_path = get_request_image_file_path($image_path);

    if ($file_path === null || !is_file($file_path)) {
        return;
    }

    if (!unlink($file_path)) {
        error_log('Request image delete failed: ' . $file_path);
    }
}

if (!isset($_SESSION['admin_id'])) {
    redirect_with_message('../admin/login.php', 'กรุณา login ก่อนทำรายการ', 'error');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_message('../admin/requests_list.php', 'Invalid request.', 'error');
}

if (empty($_POST['request_id'])) {
    redirect_with_message('../admin/requests_list.php', 'ไม่พบรหัสรายการที่ต้องการลบ', 'error');
}

$request_id = (int)$_POST['request_id'];

try {
    $image_path = null;
    $select_stmt = $conn->prepare("SELECT image_path FROM requests WHERE id = ?");
    $select_stmt->bind_param("i", $request_id);
    $select_stmt->execute();
    $select_result = $select_stmt->get_result();

    if ($select_result->num_rows > 0) {
        $request = $select_result->fetch_assoc();
        $image_path = $request['image_path'];
    }
    $select_stmt->close();

    $stmt = $conn->prepare("DELETE FROM requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            delete_request_image_file($image_path);
            redirect_with_message('../admin/requests_list.php', 'ลบรายการแจ้งซ่อมสำเร็จแล้ว', 'success');
        } else {
            throw new Exception('ไม่พบรายการที่ต้องการลบ (อาจถูกลบไปแล้ว)');
        }
    } else {
        throw new Exception('ไม่สามารถลบข้อมูลได้: ' . $stmt->error);
    }
    $stmt->close();

} catch (Exception $e) {
    redirect_with_message('../admin/requests_list.php', $e->getMessage(), 'error');
}

$conn->close();
?>
