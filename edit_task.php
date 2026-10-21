<?php
// =========================================
// تعديل مهمة (Form + MySQL UPDATE + Sessions)
// =========================================
require_once 'config/db.php';
session_start();
require_once 'includes/functions.php';

if (!is_logged_in()) { header("Location: login.php"); exit; }

$error = '';
$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// جلب بيانات المهمة
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

if (!$task) {
    header("Location: tasks.php");
    exit;
}

// معالجة التعديل
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title']);
    $description = clean_input($_POST['description']);
    $priority = clean_input($_POST['priority']);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $due_date = !empty($_POST['due_date']) ? clean_input($_POST['due_date']) : null;
    $status = clean_input($_POST['status']);
    
    if (empty($title)) {
        $error = "عنوان المهمة مطلوب";
    } else {
        $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, priority=?, status=?, category_id=?, due_date=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssssissi", $title, $description, $priority, $status, $category_id, $due_date, $task_id, $user_id);
        
        if ($stmt->execute()) {
            header("Location: tasks.php?msg=updated");
            exit;
        } else {
            $error = "حدث خطأ أثناء التعديل";
        }
    }
}

// جلب التصنيفات
$cats = $conn->query("SELECT * FROM categories ORDER BY name");

require_once 'includes/header.php';
?>

<h1 class="page-title"><i class="fa-solid fa-pen-to-square"></i> تعديل المهمة</h1>
<p class="page-subtitle">تعديل: <?php echo $task['title']; ?></p>

<div class="form-card fade-in">
    <?php if ($error) echo show_alert($error, "error"); ?>
    
    <form method="POST">
        <div class="form-group">
            <label><i class="fa-solid fa-heading"></i> عنوان المهمة *</label>
            <input type="text" name="title" class="form-control" value="<?php echo $task['title']; ?>" required>
        </div>
        
        <div class="form-group">
            <label><i class="fa-solid fa-align-right"></i> وصف المهمة</label>
            <textarea name="description" class="form-control"><?php echo $task['description']; ?></textarea>
        </div>
        
        <div class="form-group">
            <label><i class="fa-solid fa-folder"></i> التصنيف</label>
            <select name="category_id" class="form-control">
                <option value="">بدون تصنيف</option>
                <?php 
                while ($cat = $cats->fetch_assoc()) {
                    $selected = ($cat['id'] == $task['category_id']) ? 'selected' : '';
                    echo "<option value='{$cat['id']}' {$selected}>{$cat['name']}</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label><i class="fa-solid fa-flag"></i> الأولوية</label>
            <select name="priority" class="form-control">
                <?php
                foreach ($priorities as $p) {
                    $selected = ($p['key'] === $task['priority']) ? 'selected' : '';
                    echo "<option value='{$p['key']}' {$selected}>{$p['name']}</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label><i class="fa-solid fa-signal"></i> الحالة</label>
            <select name="status" class="form-control">
                <?php
                foreach ($statuses as $s) {
                    $selected = ($s['key'] === $task['status']) ? 'selected' : '';
                    echo "<option value='{$s['key']}' {$selected}>{$s['name']}</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label><i class="fa-solid fa-calendar"></i> تاريخ الاستحقاق</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo $task['due_date']; ?>">
        </div>
        
        <button type="submit" class="btn btn-primary btn-block btn-lg">
            <i class="fa-solid fa-save"></i> حفظ التعديلات
        </button>
        
        <a href="tasks.php" class="btn btn-outline btn-block" style="margin-top:10px;">
            <i class="fa-solid fa-arrow-right"></i> العودة للمهام
        </a>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
