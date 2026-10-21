<?php
// =========================================
// صفحة التسجيل (Forms + MySQL + Sessions + password_hash)
// =========================================
require_once 'config/db.php';
session_start();
$error = '';
$success = '';

// معالجة فورم التسجيل
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/functions.php';
    
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    // التحقق من المدخلات (if/elseif/else)
    if (empty($username) || empty($email) || empty($password)) {
        $error = "الرجاء تعبئة جميع الحقول";
    } elseif (strlen($password) < 6) {
        $error = "كلمة المرور يجب أن تكون 6 أحرف على الأقل";
    } elseif ($password !== $confirm) {
        $error = "كلمة المرور غير متطابقة";
    } else {
        // فحص تكرار اسم المستخدم أو البريد
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            $error = "اسم المستخدم أو البريد مسجل مسبقاً";
        } else {
            // تشفير كلمة المرور وإدخال المستخدم
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed);
            
            if ($stmt->execute()) {
                // تسجيل دخول تلقائي
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';
                $_SESSION['email'] = $email;
                header("Location: index.php");
                exit;
            } else {
                $error = "حدث خطأ، حاول مجدداً";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card form-card">
        <div class="auth-icon">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <h2>إنشاء حساب جديد</h2>
        <p style="text-align:center; color:var(--text-dim); margin-bottom:20px; font-size:0.85rem;">
            سجّل حساب جديد وابدأ بتنظيم مهامك
        </p>
        
        <?php 
        if ($error) echo show_alert($error, "error");
        if ($success) echo show_alert($success, "success");
        ?>
        
        <form method="POST" id="registerForm">
            <div class="form-group">
                <label>اسم المستخدم</label>
                <input type="text" name="username" class="form-control" placeholder="أدخل اسم المستخدم" required>
            </div>
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
            </div>
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" class="form-control" placeholder="6 أحرف على الأقل" required>
            </div>
            <div class="form-group">
                <label>تأكيد كلمة المرور</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="أعد كتابة كلمة المرور" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fa-solid fa-user-plus"></i> تسجيل
            </button>
        </form>
        
        <div class="auth-footer">
            لديك حساب؟ <a href="login.php">سجّل الدخول</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
