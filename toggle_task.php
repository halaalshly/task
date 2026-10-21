<?php
// =========================================
// تبديل حالة المهمة (Toggle Status)
// =========================================
require_once 'config/db.php';
session_start();
require_once 'includes/functions.php';

if (!is_logged_in()) { header("Location: login.php"); exit; }

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$redirect = isset($_GET['redirect']) ? clean_input($_GET['redirect']) : 'tasks.php';
$user_id = $_SESSION['user_id'];

if ($task_id > 0) {
    // جلب الحالة الحالية
    $stmt = $conn->prepare("SELECT status FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $task = $stmt->get_result()->fetch_assoc();
    
    if ($task) {
        // تبديل الحالة (استخدام switch)
        switch ($task['status']) {
            case 'pending':
                $new_status = 'in_progress';
                break;
            case 'in_progress':
                $new_status = 'completed';
                break;
            case 'completed':
                $new_status = 'pending';
                break;
            default:
                $new_status = 'pending';
                break;
        }
        
        $update = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("sii", $new_status, $task_id, $user_id);
        $update->execute();
    }
}

header("Location: $redirect");
exit;
?>
