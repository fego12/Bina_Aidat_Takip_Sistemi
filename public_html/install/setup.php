 <?php
try {
    require_once '../config/database.php';

    echo "Veritabanı kurulumu başlıyor...\n\n";

    // Set UTF8MB4
    $db->exec("SET NAMES utf8mb4");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Users Table
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ Users tablosu oluşturuldu\n";

    // Create Members Table
    $db->exec("CREATE TABLE IF NOT EXISTS members (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        address TEXT,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ Members tablosu oluşturuldu\n";

    // Create Dues Table
    $db->exec("CREATE TABLE IF NOT EXISTS dues (
        id INT PRIMARY KEY AUTO_INCREMENT,
        member_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        due_date DATE NOT NULL,
        status ENUM('pending', 'paid', 'overdue') DEFAULT 'pending',
        payment_date TIMESTAMP NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ Dues tablosu oluşturuldu\n";

    // Create Expenses Table
    $db->exec("CREATE TABLE IF NOT EXISTS expenses (
        id INT PRIMARY KEY AUTO_INCREMENT,
        description TEXT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        expense_date DATE NOT NULL,
        category VARCHAR(50) NOT NULL,
        status ENUM('pending', 'paid') DEFAULT 'pending',
        payment_date TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ Expenses tablosu oluşturuldu\n";

    // Create Indexes
    $db->exec("CREATE INDEX IF NOT EXISTS idx_dues_member ON dues(member_id)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_dues_status ON dues(status)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_expenses_date ON expenses(expense_date)");
    echo "✓ İndeksler oluşturuldu\n";

    // Create Default Admin User
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT IGNORE INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
    $stmt->execute(['admin', $admin_password, 'admin@example.com']);
    echo "✓ Varsayılan admin kullanıcısı oluşturuldu\n";

    echo "\nKurulum başarıyla tamamlandı!\n";
    echo "\nVarsayılan Admin Bilgileri:\n";
    echo "Kullanıcı Adı: admin\n";
    echo "Şifre: admin123\n";
    echo "\nÖNEMLİ: Güvenliğiniz için lütfen admin şifresini değiştirin!\n";

} catch (PDOException $e) {
    die("Hata: " . $e->getMessage() . "\n");
}
?>
