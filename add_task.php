<?php
// =========================================
// إضافة مهمة جديدة (Form + MySQL INSERT + Sessions)
// =========================================
require_once 'config/db.php';
session_start();
require_once 'includes/functions.php';

if (!is_logged_in()) { header("Location: login.php"); exit; }

$error = '';
$success = '';

// معالجة الفورم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title']);
    $description = clean_input($_POST['description']);
    $priority = clean_input($_POST['priority']);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $due_date = !empty($_POST['due_date']) ? clean_input($_POST['due_date']) : null;
    $status = clean_input($_POST['status']);
    
    // التحقق (if/else)
    if (empty($title)) {
        $error = "عنوان المهمة مطلوب";
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, category_id, title, description, priority, status, due_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $_SESSION['user_id'], $category_id, $title, $description, $priority, $status, $due_date);
        
        if ($stmt->execute()) {
            header("Location: tasks.php?msg=added");
            exit;
        } else {
            $error = "حدث خطأ أثناء إضافة المهمة";
        }
    }
}

// جلب التصنيفات
$cats = $conn->query("SELECT * FROM categories ORDER BY name");

require_once 'includes/header.php';
?>

<h1 class="page-title"><i class="fa-solid fa-plus-circle"></i> إضافة مهمة</h1>
<p class="page-subtitle">أضف مهمة جديدة لقائمتك</p>

<div class="form-card fade-in">
    <?php 
    if ($error) echo show_alert($error, "error");
    if ($success) echo show_alert($success, "success");
    ?>
    
    <form method="POST" id="addTaskForm">
        <div class="form-group">
            <label><i class="fa-solid fa-heading"></i> عنوان المهمة *</label>
            <input type="text" name="title" class="form-control" placeholder="مثال: إنهاء تقرير المشروع" required>
        </div>
        
        <div class="form-group">
            <label><i class="fa-solid fa-align-right"></i> وصف المهمة</label>
            <textarea name="description" class="form-control" placeholder="اكتب تفاصيل المهمة هنا..."></textarea>
        </div>
        
        <div class="form-group">
            <label><i class="fa-solid fa-folder"></i> التصنيف</label>
            <select name="category_id" class="form-control">
                <option value="">بدون تصنيف</option>
                <?php 
                // حلقة while لعرض التصنيفات في القائمة
                while ($cat = $cats->fetch_assoc()) {
                    echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label><i class="fa-solid fa-flag"></i> الأولوية</label>
            <select name="priority" class="form-control">
                <?php
                // حلقة foreach لعرض الأولويات
                foreach ($priorities as $p) {
                    $selected = ($p['key'] === 'medium') ? 'selected' : '';
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
                    echo "<option value='{$s['key']}'>{$s['name']}</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label><i class="fa-solid fa-calendar"></i> تاريخ الاستحقاق</label>
            <input type="date" name="due_date" class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary btn-block btn-lg">
            <i class="fa-solid fa-plus"></i> إضافة المهمة
        </button>
        
        <a href="tasks.php" class="btn btn-outline btn-block" style="margin-top:10px;">
            <i class="fa-solid fa-arrow-right"></i> العودة للمهام
        </a>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
