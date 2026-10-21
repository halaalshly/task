<?php
// =========================================
// صفحة التصنيفات (Categories)
// =========================================
require_once 'config/db.php';
require_once 'includes/header.php';

if (!is_logged_in()) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];

// جلب التصنيفات مع عدد المهام لكل تصنيف
$cats = $conn->query("SELECT c.*, 
                       COUNT(t.id) as total_tasks,
                       SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as done_tasks
                       FROM categories c 
                       LEFT JOIN tasks t ON c.id = t.category_id AND t.user_id = $user_id
                       GROUP BY c.id
                       ORDER BY c.name");
?>

<h1 class="page-title"><i class="fa-solid fa-folder-open"></i> التصنيفات</h1>
<p class="page-subtitle">تصفح مهامك حسب التصنيف</p>

<div class="cat-grid fade-in">
<?php
// حلقة while لعرض التصنيفات
while ($cat = $cats->fetch_assoc()) {
    $cat_progress = calc_progress($cat['done_tasks'], $cat['total_tasks']);
?>
    <a href="tasks.php?cat=<?php echo $cat['id']; ?>" class="cat-card">
        <div class="cat-card-icon" style="background:<?php echo $cat['color']; ?>;">
            <i class="fa-solid <?php echo $cat['icon']; ?>"></i>
        </div>
        <div class="cat-card-name"><?php echo $cat['name']; ?></div>
        <div class="cat-card-count"><?php echo $cat['total_tasks']; ?> مهمة</div>
        <?php if ($cat['total_tasks'] > 0): ?>
        <div style="margin-top:10px;">
            <div class="progress-bar" style="height:6px;">
                <div class="progress-fill" style="width:<?php echo $cat_progress; ?>%;"></div>
            </div>
            <div style="font-size:0.65rem; color:var(--text-dim); margin-top:4px;"><?php echo $cat_progress; ?>% مكتمل</div>
        </div>
        <?php endif; ?>
    </a>
<?php } ?>
</div>

<?php require_once 'includes/footer.php'; ?>
