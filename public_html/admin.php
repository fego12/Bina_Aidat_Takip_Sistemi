<?php
require_once 'config/database.php';

// Yeni admin bilgileri
$username = 'admin';
$password = 'admin123';
$email = 'admin@example.com';

// Şifreyi hashle
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Önce eski admin kaydını silelim
    $stmt = $db->prepare("DELETE FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    // Yeni admin kaydı oluşturalım
    $stmt = $db->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hashed_password, $email]);
    
    echo "Admin kullanıcısı başarıyla oluşturuldu!<br>";
    echo "Kullanıcı adı: admin<br>";
    echo "Şifre: admin123";
} catch(PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?>