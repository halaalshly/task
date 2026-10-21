<?php
// =========================================
// الصفحة الرئيسية - Dashboard
// =========================================
require_once 'config/db.php';
require_once 'includes/header.php';

// فحص تسجيل الدخول
if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ============= إحصائيات المهام (استعلامات MySQL) =============
// إجمالي المهام
$total_q = $conn->query("SELECT COUNT(*) as c FROM tasks WHERE user_id = $user_id");
$total_tasks = $total_q->fetch_assoc()['c'];

// المهام المكتملة
$done_q = $conn->query("SELECT COUNT(*) as c FROM tasks WHERE user_id = $user_id AND status = 'completed'");
$done_tasks = $done_q->fetch_assoc()['c'];

// قيد التنفيذ
$prog_q = $conn->query("SELECT COUNT(*) as c FROM tasks WHERE user_id = $user_id AND status = 'in_progress'");
$prog_tasks = $prog_q->fetch_assoc()['c'];

// قيد الانتظار
$pend_q = $conn->query("SELECT COUNT(*) as c FROM tasks WHERE user_id = $user_id AND status = 'pending'");
$pend_tasks = $pend_q->fetch_assoc()['c'];

// نسبة الإنجاز (استخدام الدالة)
$progress = calc_progress($done_tasks, $total_tasks);

// جلب آخر 5 مهام
$recent = $conn->query("SELECT t.*, c.name as cat_name, c.color as cat_color, c.icon as cat_icon 
                         FROM tasks t 
                         LEFT JOIN categories c ON t.category_id = c.id 
                         WHERE t.user_id = $user_id 
                         ORDER BY t.created_at DESC LIMIT 5");

// جلب التصنيفات مع عدد المهام
$cats = $conn->query("SELECT c.*, COUNT(t.id) as task_count 
                       FROM categories c 
                       LEFT JOIN tasks t ON c.id = t.category_id AND t.user_id = $user_id 
                       GROUP BY c.id");
?>

<!-- قسم الترحيب -->
<div class="welcome-section fade-in">
    <div class="welcome-text">
        <p class="welcome-greeting">
        <?php
        // استخدام if/elseif/else لتحديد التحية حسب الوقت
        $hour = (int)date('H');
        if ($hour < 12) {
            echo "☀️ صباح الخير";
        } elseif ($hour < 17) {
            echo "🌤️ مساء النور";
        } else {
            echo "🌙 مساء الخير";
        }
        ?>
        </p>
        <h1 class="welcome-name"><?php echo $_SESSION['username']; ?></h1>
        <p class="welcome-motivation"><?php echo $motivations[array_rand($motivations)]; ?></p>
    </div>
</div>

<!-- الإحصائيات -->
<div class="stats-grid fade-in">
    <div class="stat-card stat-total">
        <div class="stat-number"><?php echo $total_tasks; ?></div>
        <div class="stat-label">إجمالي المهام</div>
        <i class="fa-solid fa-list-check stat-icon"></i>
    </div>
    <div class="stat-card stat-done">
        <div class="stat-number" style="color: var(--success);"><?php echo $done_tasks; ?></div>
        <div class="stat-label">مكتملة</div>
        <i class="fa-solid fa-circle-check stat-icon"></i>
    </div>
    <div class="stat-card stat-progress">
        <div class="stat-number" style="color: var(--info);"><?php echo $prog_tasks; ?></div>
        <div class="stat-label">قيد التنفيذ</div>
        <i class="fa-solid fa-spinner stat-icon"></i>
    </div>
    <div class="stat-card stat-pending">
        <div class="stat-number" style="color: var(--warning);"><?php echo $pend_tasks; ?></div>
        <div class="stat-label">قيد الانتظار</div>
        <i class="fa-solid fa-clock stat-icon"></i>
    </div>
</div>

<!-- شريط الإنجاز -->
<div class="progress-section card fade-in">
    <div class="progress-header">
        <span class="progress-label"><i class="fa-solid fa-chart-simple"></i> نسبة الإنجاز</span>
        <span class="progress-value"><?php echo $progress; ?>%</span>
    </div>
    <div class="progress-bar">
        <div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
    </div>
</div>

<!-- التصنيفات -->
<div class="section-header">
    <h2 class="section-title"><i class="fa-solid fa-folder"></i> التصنيفات</h2>
    <a href="categories.php" class="section-link">عرض الكل <i class="fa-solid fa-chevron-left"></i></a>
</div>
<div class="cat-grid fade-in" style="margin-bottom: 24px;">
<?php
// حلقة while لعرض التصنيفات
while ($cat = $cats->fetch_assoc()) {
    echo "<a href='tasks.php?cat={$cat['id']}' class='cat-card'>";
    echo "<div class='cat-card-icon' style='background:{$cat['color']};'><i class='fa-solid {$cat['icon']}'></i></div>";
    echo "<div class='cat-card-name'>{$cat['name']}</div>";
    echo "<div class='cat-card-count'>{$cat['task_count']} مهمة</div>";
    echo "</a>";
}
?>
</div>

<!-- آخر المهام -->
<div class="section-header">
    <h2 class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> آخر المهام</h2>
    <a href="tasks.php" class="section-link">عرض الكل <i class="fa-solid fa-chevron-left"></i></a>
</div>

<?php
if ($recent->num_rows == 0) {
    echo "<div class='empty-state'>";
    echo "<div class='empty-icon'><i class='fa-solid fa-clipboard-list'></i></div>";
    echo "<div class='empty-title'>لا توجد مهام بعد</div>";
    echo "<div class='empty-desc'>ابدأ بإضافة مهمتك الأولى!</div>";
    echo "<a href='add_task.php' class='btn btn-primary'><i class='fa-solid fa-plus'></i> إضافة مهمة</a>";
    echo "</div>";
} else {
    // حلقة while لعرض المهام
    while ($task = $recent->fetch_assoc()) {
        $days = days_remaining($task['due_date']);
        $is_overdue = ($days !== null && $days < 0 && $task['status'] !== 'completed');
        $is_done = ($task['status'] === 'completed');
?>
    <div class="task-card priority-<?php echo $task['priority']; ?> fade-in">
        <a href="toggle_task.php?id=<?php echo $task['id']; ?>&redirect=index.php" 
           class="task-checkbox <?php echo $is_done ? 'checked' : ''; ?>">
            <?php if ($is_done): ?><i class="fa-solid fa-check"></i><?php endif; ?>
        </a>
        <div class="task-info">
            <div class="task-title <?php echo $is_done ? 'completed-text' : ''; ?>">
                <?php echo $task['title']; ?>
            </div>
            <div class="task-meta">
                <?php if ($task['cat_name']): ?>
                <span class="task-badge" style="background:<?php echo $task['cat_color']; ?>20; color:<?php echo $task['cat_color']; ?>;">
                    <i class="fa-solid <?php echo $task['cat_icon']; ?>"></i> <?php echo $task['cat_name']; ?>
                </span>
                <?php endif; ?>
                <span class="task-badge" style="background:<?php echo priority_color($task['priority']); ?>20; color:<?php echo priority_color($task['priority']); ?>;">
                    <?php echo translate_priority($task['priority']); ?>
                </span>
                <?php if ($task['due_date']): ?>
                <span class="task-date <?php echo $is_overdue ? 'overdue' : ''; ?>">
                    <i class="fa-solid fa-calendar"></i>
                    <?php echo format_date($task['due_date']); ?>
                    <?php if ($is_overdue): ?> (متأخرة)<?php endif; ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
    }
}
?>

<?php require_once 'includes/footer.php'; ?>
