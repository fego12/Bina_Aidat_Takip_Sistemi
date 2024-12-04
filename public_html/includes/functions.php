<?php
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Bu sayfayı görüntülemek için giriş yapmanız gerekmektedir.";
        header('Location: login.php');
        exit();
    }
}
function turkce_karakter($str) {
    $str = trim($str);
    $str = str_replace(
        ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'],
        ['i', 'g', 'u', 's', 'o', 'c', 'I', 'G', 'U', 'S', 'O', 'C'],
        $str
    );
    return $str;
}


function formatMoney($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}