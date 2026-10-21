<?php
// =========================================
// صفحة تسجيل الدخول (Sessions + Forms + MySQL)
// =========================================
require_once 'config/db.php';
session_start();
$error = '';

// معالجة الفورم (Form Processing)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/functions.php';
    
    // تنظيف المدخلات (استخدام الدالة)
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    
    // التحقق من الحقول (if/else)
    if (empty($username) || empty($password)) {
        $error = "الرجاء تعبئة جميع الحقول";
    } else {
        // استعلام MySQL مع Prepared Statement
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if ($user && password_verify($password, $user['password'])) {
            // حفظ في الجلسة (Sessions)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            header("Location: index.php");
            exit;
        } else {
            $error = "اسم المستخدم أو كلمة المرور غير صحيحة";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card form-card">
        <div class="auth-icon">
            <i class="fa-solid fa-check-double"></i>
        </div>
        <h2>تسجيل الدخول</h2>
        <p style="text-align:center; color:var(--text-dim); margin-bottom:20px; font-size:0.85rem;">
            مرحباً بعودتك! سجّل الدخول لإدارة مهامك
        </p>
        
        <?php if ($error) echo show_alert($error, "error"); ?>
        
        <form method="POST" id="loginForm">
            <div class="form-group">
                <label>اسم المستخدم أو البريد</label>
                <input type="text" name="username" class="form-control" placeholder="أدخل اسم المستخدم" required>
            </div>
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" class="form-control" placeholder="أدخل كلمة المرور" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fa-solid fa-right-to-bracket"></i> دخول
            </button>
        </form>
        
        <div class="auth-footer">
            ليس لديك حساب؟ <a href="register.php">سجّل هنا</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
