<?php
// =========================================
// صفحة الملف الشخصي (Profile + Sessions + Cookies)
// =========================================
require_once 'config/db.php';
session_start();
require_once 'includes/functions.php';

if (!is_logged_in()) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// حفظ إعداد الثيم في الكوكيز (Cookies)
if (isset($_POST['save_settings'])) {
    $theme = clean_input($_POST['theme']);
    // استخدام Cookies لحفظ الإعدادات
    setcookie('user_theme', $theme, time() + (365 * 24 * 60 * 60), '/');
    $success = "تم حفظ الإعدادات";
}

// تحديث الملف الشخصي
if (isset($_POST['update_profile'])) {
    $new_email = clean_input($_POST['email']);
    $new_pass = $_POST['new_password'];
    
    if (!empty($new_pass) && strlen($new_pass) < 6) {
        $error = "كلمة المرور يجب أن تكون 6 أحرف على الأقل";
    } else {
        if (!empty($new_pass)) {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_email, $hashed, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->bind_param("si", $new_email, $user_id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['email'] = $new_email;
            $success = "تم تحديث الملف الشخصي";
        } else {
            $error = "حدث خطأ أثناء التحديث";
        }
    }
}

// جلب بيانات المستخدم
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// إحصائيات المستخدم
$stats = $conn->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as done,
    SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) as progress
    FROM tasks WHERE user_id = $user_id")->fetch_assoc();

// قراءة الكوكي
$current_theme = isset($_COOKIE['user_theme']) ? $_COOKIE['user_theme'] : 'dark';

require_once 'includes/header.php';
?>

<div class="profile-header fade-in">
    <div class="profile-avatar">
        <i class="fa-solid fa-user"></i>
    </div>
    <div class="profile-name"><?php echo $user['username']; ?></div>
    <div class="profile-email"><?php echo $user['email']; ?></div>
    <div style="margin-top:8px; font-size:0.75rem; color:var(--text-dim);">
        عضو منذ: <?php echo format_date($user['created_at']); ?>
    </div>
</div>

<?php 
if ($success) echo show_alert($success, "success");
if ($error) echo show_alert($error, "error");
?>

<!-- إحصائيات سريعة -->
<div class="stats-grid fade-in" style="grid-template-columns: 1fr 1fr 1fr; margin-bottom:24px;">
    <div class="stat-card stat-total">
        <div class="stat-number"><?php echo $stats['total']; ?></div>
        <div class="stat-label">الكل</div>
    </div>
    <div class="stat-card stat-done">
        <div class="stat-number" style="color:var(--success);"><?php echo $stats['done']; ?></div>
        <div class="stat-label">مكتملة</div>
    </div>
    <div class="stat-card stat-progress">
        <div class="stat-number" style="color:var(--info);"><?php echo $stats['progress']; ?></div>
        <div class="stat-label">قيد التنفيذ</div>
    </div>
</div>

<!-- القائمة -->
<div class="fade-in">
    <a href="tasks.php" class="profile-menu-item">
        <div class="profile-menu-icon" style="background:rgba(108,92,231,0.15); color:var(--primary-light);">
            <i class="fa-solid fa-list-check"></i>
        </div>
        <span class="profile-menu-text">مهامي</span>
        <i class="fa-solid fa-chevron-left profile-menu-arrow"></i>
    </a>
    
    <a href="add_task.php" class="profile-menu-item">
        <div class="profile-menu-icon" style="background:rgba(0,184,148,0.15); color:var(--success);">
            <i class="fa-solid fa-plus"></i>
        </div>
        <span class="profile-menu-text">إضافة مهمة جديدة</span>
        <i class="fa-solid fa-chevron-left profile-menu-arrow"></i>
    </a>
    
    <a href="categories.php" class="profile-menu-item">
        <div class="profile-menu-icon" style="background:rgba(253,203,110,0.15); color:var(--warning);">
            <i class="fa-solid fa-folder"></i>
        </div>
        <span class="profile-menu-text">التصنيفات</span>
        <i class="fa-solid fa-chevron-left profile-menu-arrow"></i>
    </a>
</div>

<!-- تعديل الملف الشخصي -->
<div class="form-card fade-in" style="margin-top:20px;">
    <h2 style="font-size:1.1rem; margin-bottom:16px;">
        <i class="fa-solid fa-user-pen"></i> تعديل الملف الشخصي
    </h2>
    <form method="POST">
        <div class="form-group">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>">
        </div>
        <div class="form-group">
            <label>كلمة مرور جديدة (اتركها فارغة إذا لا تريد التغيير)</label>
            <input type="password" name="new_password" class="form-control" placeholder="كلمة مرور جديدة">
        </div>
        <button type="submit" name="update_profile" class="btn btn-primary btn-block">
            <i class="fa-solid fa-save"></i> حفظ التعديلات
        </button>
    </form>
</div>

<!-- تسجيل الخروج -->
<div style="margin-top:20px; margin-bottom:20px;">
    <a href="logout.php" class="btn btn-danger btn-block btn-lg">
        <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
    </a>
</div>

<?php require_once 'includes/footer.php'; ?>
