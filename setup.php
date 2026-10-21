<?php
// سكربت إعداد قاعدة البيانات - يُشغل مرة واحدة فقط
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';

$conn = new mysqli($db_host, $db_user, $db_pass);
if ($conn->connect_error) die("فشل الاتصال: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

// قراءة ملف SQL
$sql = file_get_contents(__DIR__ . '/database.sql');

// حذف الجداول القديمة
$conn->query("USE lamasat_db");
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$tables = ['orders','messages','products','tasks','categories','users'];
foreach ($tables as $t) {
    $conn->query("DROP TABLE IF EXISTS $t");
}
$conn->query("SET FOREIGN_KEY_CHECKS = 1");
$conn->query("DROP DATABASE IF EXISTS lamasat_db");

// تنفيذ SQL
if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) $result->free();
    } while ($conn->next_result());
}

if ($conn->error) {
    echo "خطأ: " . $conn->error;
} else {
    echo "<h1 style='text-align:center; margin-top:50px; font-family:Cairo;'>✅ تم إعداد قاعدة البيانات بنجاح!</h1>";
    echo "<p style='text-align:center; font-family:Cairo;'><a href='login.php'>اذهب لتسجيل الدخول</a></p>";
    echo "<p style='text-align:center; font-family:Cairo; color:gray;'>حساب تجريبي: admin / admin123</p>";
}
$conn->close();
?>
