<?php
// =========================================
// متغيرات وأنواع بيانات + مصفوفات
// Variables & Data Types (String, Int, Float, Boolean, Arrays)
// =========================================

// متغيرات (String, Int, Float, Boolean)
$site_name      = "مُنجز | Task Manager";   // String
$founded_year   = 2026;                      // Integer
$version        = 1.0;                       // Float
$is_active      = true;                      // Boolean

// مصفوفة فهرسية (Indexed Array) - عبارات تحفيزية
$motivations = [
    "كل مهمة تنجزها تقربك من هدفك 🎯",
    "ابدأ بالأهم ثم الأقل أهمية ✨",
    "النجاح يبدأ بخطوة واحدة 🚀",
    "خطط ليومك واستثمر وقتك 📋"
];

// مصفوفة ترابطية (Associative Array) - معلومات التطبيق
$app_info = [
    "developer" => "مشروع مقرر البرمجة",
    "email"     => "info@task-app.com",
    "version"   => "1.0",
    "year"      => "2026"
];

// مصفوفة ثنائية الأبعاد (Multi-dimensional Array) - أولويات المهام
$priorities = [
    ["key" => "high",   "name" => "عالية",   "color" => "#e74c3c", "icon" => "fa-circle-exclamation"],
    ["key" => "medium", "name" => "متوسطة",  "color" => "#f39c12", "icon" => "fa-circle-minus"],
    ["key" => "low",    "name" => "منخفضة",  "color" => "#27ae60", "icon" => "fa-circle-check"]
];

// مصفوفة حالات المهام
$statuses = [
    ["key" => "pending",     "name" => "قيد الانتظار",  "color" => "#e67e22", "icon" => "fa-clock"],
    ["key" => "in_progress", "name" => "قيد التنفيذ",   "color" => "#3498db", "icon" => "fa-spinner"],
    ["key" => "completed",   "name" => "مكتملة",        "color" => "#27ae60", "icon" => "fa-circle-check"]
];
?>
