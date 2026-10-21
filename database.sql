-- =========================================
-- تطبيق إدارة المهام - قاعدة البيانات
-- Task Manager Application Database
-- =========================================
 

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول تصنيفات المهام
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(20) DEFAULT '#6c5ce7',
    icon VARCHAR(50) DEFAULT 'fa-folder'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول المهام
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT DEFAULT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    priority ENUM('low','medium','high') DEFAULT 'medium',
    status ENUM('pending','in_progress','completed') DEFAULT 'pending',
    due_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- بيانات تجريبية
-- =========================================

-- حساب أدمن (admin / admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@taskapp.com', '$2y$10$PIjm2ik9F8/k57kQ/WPHSelvFbF4VK6JEj75z/TMFLj/XZ3C4ynPO', 'admin');

-- تصنيفات المهام
INSERT INTO categories (name, color, icon) VALUES
('عمل', '#6c5ce7', 'fa-briefcase'),
('دراسة', '#00b894', 'fa-graduation-cap'),
('شخصي', '#e17055', 'fa-user'),
('مشتريات', '#fdcb6e', 'fa-cart-shopping'),
('صحة', '#e84393', 'fa-heart-pulse');

-- مهام تجريبية
INSERT INTO tasks (user_id, category_id, title, description, priority, status, due_date) VALUES
(1, 1, 'إعداد تقرير المشروع', 'كتابة التقرير النهائي وتسليمه للمدير', 'high', 'in_progress', '2026-05-20'),
(1, 2, 'مراجعة مادة البرمجة', 'مراجعة الفصل الخامس والسادس', 'high', 'pending', '2026-05-15'),
(1, 3, 'تنظيم الملفات الشخصية', 'ترتيب ملفات الكمبيوتر والنسخ الاحتياطي', 'low', 'completed', '2026-05-10'),
(1, 4, 'شراء مستلزمات المكتب', 'أقلام وأوراق ومجلدات', 'medium', 'pending', '2026-05-18'),
(1, 5, 'حجز موعد طبي', 'فحص دوري عند طبيب الأسنان', 'medium', 'pending', '2026-05-22'),
(1, 1, 'اجتماع فريق العمل', 'مناقشة خطة الأسبوع القادم', 'high', 'in_progress', '2026-05-14'),
(1, 2, 'تسليم الواجب', 'واجب مادة قواعد البيانات', 'high', 'pending', '2026-05-16');
