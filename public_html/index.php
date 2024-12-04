<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get current month and year
$currentMonth = date('m');
$currentYear = date('Y');

// Toplu aidat ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bulk_due'])) {
    $amount = $_POST['amount'] ?? 0;
    $due_date = $_POST['due_date'] ?? date('Y-m-d');
    
    if ($amount > 0) {
        try {
            $stmt = $db->prepare("INSERT INTO dues (member_id, amount, status, due_date) SELECT id, ?, 'pending', ? FROM members");
            $stmt->execute([$amount, $due_date]);
            $_SESSION['success_message'] = 'Yeni aidat tüm üyelere başarıyla eklendi.';
        } catch (PDOException $e) {
            $_SESSION['error_message'] = 'Aidat eklenirken bir hata oluştu: ' . $e->getMessage();
        }
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Lütfen geçerli bir miktar girin.';
    }
}

// İstatistikleri al (Aylık)
$stats = $db->query("
    SELECT 
        SUM(CASE WHEN d.status = 'paid' AND MONTH(d.due_date) = $currentMonth AND YEAR(d.due_date) = $currentYear THEN d.amount ELSE 0 END) as total_paid,
        SUM(CASE WHEN (d.status = 'pending' OR d.status = 'overdue') AND MONTH(d.due_date) = $currentMonth AND YEAR(d.due_date) = $currentYear THEN d.amount ELSE 0 END) as total_pending,
        (SELECT SUM(amount) FROM expenses WHERE status = 'paid' AND MONTH(expense_date) = $currentMonth AND YEAR(expense_date) = $currentYear) as total_expenses
    FROM dues d
")->fetch();

// Kalan bakiyeyi hesapla (Güncel)
$total_income = $db->query("SELECT SUM(amount) FROM dues WHERE status = 'paid'")->fetchColumn() ?? 0;
$total_expenses = $db->query("SELECT SUM(amount) FROM expenses WHERE status = 'paid'")->fetchColumn() ?? 0;
$remaining_balance = $total_income - $total_expenses;

// Son aidatları al
$recent_dues = $db->query("
    SELECT d.*, m.name as member_name 
    FROM dues d 
    JOIN members m ON d.member_id = m.id 
    ORDER BY d.due_date DESC 
    LIMIT 5
")->fetchAll();

// Son harcamaları al
$recent_expenses = $db->query("
    SELECT * FROM expenses 
    ORDER BY expense_date DESC 
    LIMIT 5
")->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-4">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h1 class="text-center mb-4">Aidat Takip Sistemi</h1>

    <!-- Toplu Aidat Ekleme Formu -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Toplu Aidat Ekle</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="" class="row g-3">
                <div class="col-md-6">
                    <label for="amount" class="form-label">Aidat Miktarı</label>
                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label for="due_date" class="form-label">Son Ödeme Tarihi</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" 
                           value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-12">
                    <button type="submit" name="add_bulk_due" class="btn btn-primary">
                        Tüm Üyelere Aidat Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- İstatistik Kartları -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Aylık Gelir</h5>
                    <h3><?php echo formatMoney($stats['total_paid'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Aylık Gider</h5>
                    <h3><?php echo formatMoney($stats['total_expenses'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card <?php echo $remaining_balance >= 0 ? 'bg-success' : 'bg-danger'; ?> text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Güncel Kalan Bakiye</h5>
                    <h3><?php echo formatMoney($remaining_balance); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Detay İstatistikleri -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Ödenen Aidat</h5>
                    <h3><?php echo formatMoney($stats['total_paid'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Bekleyen Aidat</h5>
                    <h3><?php echo formatMoney($stats['total_pending'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Aidatlar -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Son Aidat Ödemeleri</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Üye</th>
                            <th>Miktar</th>
                            <th>Son Ödeme Tarihi</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_dues)): ?>
                            <?php foreach ($recent_dues as $due): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($due['member_name']); ?></td>
                                <td><?php echo formatMoney($due['amount']); ?></td>
                                <td><?php echo date('d.m.Y', strtotime($due['due_date'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $due['status'] == 'paid' ? 'success' : ($due['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                        <?php 
                                        echo $due['status'] == 'paid' ? 'Ödendi' : 
                                            ($due['status'] == 'pending' ? 'Beklemede' : 'Gecikmiş'); 
                                        ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Henüz aidat kaydı bulunmamaktadır.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Son Harcamalar -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Son Harcamalar</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Açıklama</th>
                            <th>Kategori</th>
                            <th>Miktar</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_expenses)): ?>
                            <?php foreach ($recent_expenses as $expense): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php echo htmlspecialchars($expense['category']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatMoney($expense['amount']); ?></td>
                                <td><?php echo date('d.m.Y', strtotime($expense['expense_date'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $expense['status'] == 'paid' ? 'success' : 'warning'; ?>">
                                        <?php echo $expense['status'] == 'paid' ? 'Ödendi' : 'Beklemede'; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Henüz harcama kaydı bulunmamaktadır.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="text-center mb-4">
        <a href="expenses.php" class="btn btn-primary">Tüm Harcamaları Görüntüle</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>