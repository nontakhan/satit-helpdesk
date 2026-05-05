<?php
require_once '../../db_connect.php';

if (isset($_POST['action'])) {
    
    // --- จัดการการเพิ่มข้อมูล ---
    if ($_POST['action'] == 'add') {
        if (!empty($_POST['category_name'])) {
            $categoryName = $_POST['category_name'];
            $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $stmt->bind_param("s", $categoryName);
            $stmt->execute();
            $stmt->close();
        }
    }

    // --- จัดการการแก้ไขข้อมูล ---
    if ($_POST['action'] == 'update') {
        if (!empty($_POST['id']) && !empty($_POST['category_name'])) {
            $categoryId = $_POST['id'];
            $categoryName = $_POST['category_name'];
            $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE id = ?");
            $stmt->bind_param("si", $categoryName, $categoryId);
            $stmt->execute();
            $stmt->close();
        }
    }

    // --- จัดการการลบข้อมูล ---
    if ($_POST['action'] == 'delete') {
        if (!empty($_POST['id'])) {
            $categoryId = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->bind_param("i", $categoryId);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$conn->close();
header('Location: ../../admin/manage_categories.php');
exit();
?>
