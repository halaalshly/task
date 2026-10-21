<?php
// =========================================
// صفحة المهام (عرض + فلترة + بحث)
// =========================================
require_once 'config/db.php';
require_once 'includes/header.php';

if (!is_logged_in()) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];

// فلترة حسب الحالة (GET parameters)
$filter_status = isset($_GET['status']) ? clean_input($_GET['status']) : '';
$filter_cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$filter_priority = isset($_GET['priority']) ? clean_input($_GET['priority']) : '';
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

// بناء الاستعلام ديناميكياً
$sql = "SELECT t.*, c.name as cat_name, c.color as cat_color, c.icon as cat_icon 
        FROM tasks t 
        LEFT JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = ?";
$params = [$user_id];
$types = "i";

if ($filter_status) {
    $sql .= " AND t.status = ?";
    $params[] = $filter_status;
    $types .= "s";
}
if ($filter_cat > 0) {
    $sql .= " AND t.category_id = ?";
    $params[] = $filter_cat;
    $types .= "i";
}
if ($filter_priority) {
    $sql .= " AND t.priority = ?";
    $params[] = $filter_priority;
    $types .= "s";
}
if ($search) {
    $sql .= " AND (t.title LIKE ? OR t.description LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

$sql .= " ORDER BY FIELD(t.status, 'in_progress','pending','completed'), t.due_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$tasks = $stmt->get_result();

// جلب التصنيفات للفلتر
$cats = $conn->query("SELECT * FROM categories");
?>

<h1 class="page-title"><i class="fa-solid fa-list-check"></i> مهامي</h1>
<p class="page-subtitle">إدارة وتنظيم جميع مهامك</p>

<!-- بحث -->
<form method="GET" style="margin-bottom:16px;">
    <div style="position:relative;">
        <input type="text" name="search" class="form-control" placeholder="ابحث في المهام..." 
               value="<?php echo $search; ?>" style="padding-right:40px;">
        <button type="submit" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); 
                background:none; border:none; color:var(--text-dim); cursor:pointer;">
            <i class="fa-solid fa-search"></i>
        </button>
    </div>
    <?php if ($filter_cat): ?><input type="hidden" name="cat" value="<?php echo $filter_cat; ?>"><?php endif; ?>
</form>

<!-- فلاتر الحالة -->
<div class="filters">
    <a href="tasks.php<?php echo $filter_cat ? "?cat=$filter_cat" : ''; ?>" 
       class="filter-btn <?php echo !$filter_status ? 'active' : ''; ?>">الكل</a>
    <?php
    // حلقة foreach لعرض فلاتر الحالات
    foreach ($statuses as $s) {
        $active = ($filter_status === $s['key']) ? 'active' : '';
        $cat_param = $filter_cat ? "&cat=$filter_cat" : '';
        echo "<a href='tasks.php?status={$s['key']}{$cat_param}' class='filter-btn {$active}'>";
        echo "<i class='fa-solid {$s['icon']}'></i> {$s['name']}</a>";
    }
    ?>
</div>

<!-- فلاتر الأولوية -->
<div class="filters">
    <?php
    // حلقة for لعرض فلاتر الأولوية
    for ($i = 0; $i < count($priorities); $i++) {
        $p = $priorities[$i];
        $active = ($filter_priority === $p['key']) ? 'active' : '';
        echo "<a href='tasks.php?priority={$p['key']}' class='filter-btn {$active}' style='border-color:{$p['color']}30;'>";
        echo "<i class='fa-solid {$p['icon']}' style='color:{$p['color']};'></i> {$p['name']}</a>";
    }
    ?>
</div>

<!-- قائمة المهام -->
<?php
if ($tasks->num_rows == 0) {
    echo "<div class='empty-state'>";
    echo "<div class='empty-icon'><i class='fa-solid fa-clipboard-list'></i></div>";
    echo "<div class='empty-title'>لا توجد مهام</div>";
    echo "<div class='empty-desc'>لم يتم العثور على مهام بالفلتر المحدد</div>";
    echo "<a href='add_task.php' class='btn btn-primary'><i class='fa-solid fa-plus'></i> إضافة مهمة</a>";
    echo "</div>";
} else {
    $counter = 0;
    // حلقة do-while لعرض المهام (تطبيق do-while)
    $task = $tasks->fetch_assoc();
    do {
        $days = days_remaining($task['due_date']);
        $is_overdue = ($days !== null && $days < 0 && $task['status'] !== 'completed');
        $is_done = ($task['status'] === 'completed');
        $counter++;
?>
    <div class="task-card priority-<?php echo $task['priority']; ?> fade-in" style="animation-delay:<?php echo $counter * 0.05; ?>s;">
        <a href="toggle_task.php?id=<?php echo $task['id']; ?>&redirect=tasks.php" 
           class="task-checkbox <?php echo $is_done ? 'checked' : ''; ?>">
            <?php if ($is_done): ?><i class="fa-solid fa-check"></i><?php endif; ?>
        </a>
        <div class="task-info">
            <div class="task-title <?php echo $is_done ? 'completed-text' : ''; ?>">
                <?php echo $task['title']; ?>
            </div>
            <?php if ($task['description']): ?>
            <p style="font-size:0.78rem; color:var(--text-dim); margin:4px 0;">
                <?php echo mb_substr($task['description'], 0, 60) . (mb_strlen($task['description']) > 60 ? '...' : ''); ?>
            </p>
            <?php endif; ?>
            <div class="task-meta">
                <?php if ($task['cat_name']): ?>
                <span class="task-badge" style="background:<?php echo $task['cat_color']; ?>20; color:<?php echo $task['cat_color']; ?>;">
                    <i class="fa-solid <?php echo $task['cat_icon']; ?>"></i> <?php echo $task['cat_name']; ?>
                </span>
                <?php endif; ?>
                <span class="task-badge" style="background:<?php echo priority_color($task['priority']); ?>20; color:<?php echo priority_color($task['priority']); ?>;">
                    <?php echo translate_priority($task['priority']); ?>
                </span>
                <span class="task-badge" style="background:<?php echo status_color($task['status']); ?>20; color:<?php echo status_color($task['status']); ?>;">
                    <?php echo translate_status($task['status']); ?>
                </span>
                <?php if ($task['due_date']): ?>
                <span class="task-date <?php echo $is_overdue ? 'overdue' : ''; ?>">
                    <i class="fa-solid fa-calendar"></i>
                    <?php echo format_date($task['due_date']); ?>
                    <?php 
                    if ($is_overdue) echo " (متأخرة)";
                    elseif ($days !== null && $days == 0) echo " (اليوم!)";
                    elseif ($days !== null && $days == 1) echo " (غداً)";
                    ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="task-actions">
            <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="task-action-btn" title="تعديل">
                <i class="fa-solid fa-pen"></i>
            </a>
            <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="task-action-btn delete" title="حذف"
               onclick="return confirm('هل أنت متأكد من حذف هذه المهمة؟');">
                <i class="fa-solid fa-trash"></i>
            </a>
        </div>
    </div>
<?php
        $task = $tasks->fetch_assoc();
    } while ($task);
}
?>

<?php require_once 'includes/footer.php'; ?>
