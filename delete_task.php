<?php
// =========================================
// حذف مهمة (MySQL DELETE + Sessions)
// =========================================
require_once 'config/db.php';
session_start();
require_once 'includes/functions.php';

if (!is_logged_in()) { header("Location: login.php"); exit; }

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

if ($task_id > 0) {
    // حذف المهمة مع فحص الملكية
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
}

header("Location: tasks.php?msg=deleted");
exit;
?>
