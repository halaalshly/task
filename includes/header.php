<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/data.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="مُنجز - تطبيق إدارة المهام الذكي | نظّم يومك وحقق أهدافك">
    <title><?php echo $site_name; ?></title>
    <?php
    $depth = substr_count(str_replace('\\','/',$_SERVER['SCRIPT_NAME']), '/') - 2;
    $base  = $depth > 0 ? str_repeat('../', $depth) : '';
    ?>
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
</head>
<body>

<!-- شريط التنقل السفلي للجوال (Mobile Bottom Nav) -->
<nav class="bottom-nav" id="bottomNav">
    <a href="<?php echo $base; ?>index.php" class="bottom-nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-house"></i>
        <span>الرئيسية</span>
    </a>
    <a href="<?php echo $base; ?>tasks.php" class="bottom-nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'tasks.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-list-check"></i>
        <span>المهام</span>
    </a>
    <?php if (is_logged_in()): ?>
    <a href="<?php echo $base; ?>add_task.php" class="bottom-nav-item bottom-nav-add">
        <i class="fa-solid fa-plus"></i>
    </a>
    <?php endif; ?>
    <a href="<?php echo $base; ?>categories.php" class="bottom-nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-folder"></i>
        <span>التصنيفات</span>
    </a>
    <?php if (is_logged_in()): ?>
    <a href="<?php echo $base; ?>profile.php" class="bottom-nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-user"></i>
        <span>حسابي</span>
    </a>
    <?php else: ?>
    <a href="<?php echo $base; ?>login.php" class="bottom-nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'login.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-right-to-bracket"></i>
        <span>دخول</span>
    </a>
    <?php endif; ?>
</nav>

<!-- الهيدر العلوي -->
<header class="top-header" id="topHeader">
    <div class="header-inner">
        <a href="<?php echo $base; ?>index.php" class="logo">
            <div class="logo-icon"><i class="fa-solid fa-check-double"></i></div>
            <span class="logo-text">مُنجز</span>
        </a>
        <div class="header-actions">
            <?php if (is_logged_in()): ?>
                <span class="user-greeting">أهلاً، <?php echo $_SESSION['username']; ?></span>
                <a href="<?php echo $base; ?>logout.php" class="header-btn" title="خروج"><i class="fa-solid fa-right-from-bracket"></i></a>
            <?php else: ?>
                <a href="<?php echo $base; ?>login.php" class="header-btn"><i class="fa-solid fa-right-to-bracket"></i> دخول</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="app-content">
