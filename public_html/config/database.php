

<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=mulaogl1_aidat;charset=utf8mb4", "mulaogl1_aidat", "xt&k-UqU-n)C");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET NAMES utf8mb4");
    $db->exec("SET CHARACTER SET utf8mb4");
    $db->exec("SET CHARACTER_SET_CONNECTION=utf8mb4");
} catch(PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>