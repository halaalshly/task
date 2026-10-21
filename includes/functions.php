<?php
// =========================================
// دوال مساعدة (Functions + return + parameters)
// =========================================

// دالة لتنظيف المدخلات (تمرير بارامتر + return)
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// دالة ترجمة الأولوية باستخدام switch
function translate_priority($priority) {
    switch ($priority) {
        case 'high':
            return 'عالية';
        case 'medium':
            return 'متوسطة';
        case 'low':
            return 'منخفضة';
        default:
            return 'غير محددة';
    }
}

// دالة ترجمة الحالة باستخدام switch
function translate_status($status) {
    switch ($status) {
        case 'pending':
            return 'قيد الانتظار';
        case 'in_progress':
            return 'قيد التنفيذ';
        case 'completed':
            return 'مكتملة';
        default:
            return 'غير معروفة';
    }
}

// دالة لون الأولوية (if/elseif/else)
function priority_color($priority) {
    if ($priority === 'high') {
        return '#e74c3c';
    } elseif ($priority === 'medium') {
        return '#f39c12';
    } else {
        return '#27ae60';
    }
}

// دالة لون الحالة
function status_color($status) {
    if ($status === 'completed') {
        return '#27ae60';
    } elseif ($status === 'in_progress') {
        return '#3498db';
    } else {
        return '#e67e22';
    }
}

// دالة حساب نسبة الإنجاز (تمرير بارامترات + return)
function calc_progress($completed, $total) {
    if ($total == 0) return 0;
    $progress = ($completed / $total) * 100;
    return round($progress);
}

// دالة تنسيق التاريخ
function format_date($date) {
    if (empty($date)) return 'بدون تاريخ';
    return date('Y/m/d', strtotime($date));
}

// دالة فحص تسجيل الدخول
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// دالة فحص الأدمن
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// دالة عرض رسالة تنبيه
function show_alert($msg, $type = 'success') {
    $icon = '';
    switch ($type) {
        case 'success': $icon = 'fa-circle-check'; break;
        case 'error':   $icon = 'fa-circle-xmark'; break;
        case 'warning': $icon = 'fa-triangle-exclamation'; break;
        default:        $icon = 'fa-circle-info'; break;
    }
    return "<div class='alert alert-{$type}'><i class='fa-solid {$icon}'></i> {$msg}</div>";
}

// دالة حساب الأيام المتبقية (return + parameters)
function days_remaining($due_date) {
    if (empty($due_date)) return null;
    $today = new DateTime();
    $due = new DateTime($due_date);
    $diff = $today->diff($due);
    if ($due < $today) return -$diff->days;
    return $diff->days;
}
?>
