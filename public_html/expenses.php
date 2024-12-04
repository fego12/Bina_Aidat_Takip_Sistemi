<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Oturum kontrolü
checkAuth();

// Harcama ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_expense'])) {
    $description = trim($_POST['description']);
    $amount = trim($_POST['amount']);
    $expense_date = $_POST['expense_date'];
    $category = trim($_POST['category']);
    $status = $_POST['status'];
    $notes = trim($_POST['notes']);
    
    $stmt = $db->prepare("INSERT INTO expenses (description, amount, expense_date, category, status, notes) VALUES (?, ?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$description, $amount, $expense_date, $category, $status, $notes]);
        $_SESSION['success_message'] = "Harcama başarıyla eklendi.";
        header('Location: expenses.php');
        exit();
    } catch(PDOException $e) {
        $error = "Harcama eklenirken bir hata oluştu.";
    }
}

// Harcama silme işlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM expenses WHERE id = ?");
    try {
        $stmt->execute([$id]);
        $_SESSION['success_message'] = "Harcama başarıyla silindi.";
        header('Location: expenses.php');
        exit();
    } catch(PDOException $e) {
        $error = "Harcama silinirken bir hata oluştu.";
    }
}

// Filtreleme parametreleri
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// SQL sorgusu oluştur
$sql = "SELECT * FROM expenses WHERE 1=1";
$params = [];

if ($start_date) {
    $sql .= " AND expense_date >= ?";
    $params[] = $start_date;
}
if ($end_date) {
    $sql .= " AND expense_date <= ?";
    $params[] = $end_date;
}
if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}
if ($status) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY expense_date DESC";

// Harcamaları listele
$stmt = $db->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll();

// Kategorileri al
$categories = $db->query("SELECT DISTINCT category FROM expenses ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// Toplam harcamaları hesapla
$total_sql = "SELECT 
    SUM(amount) as total_amount,
    SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as paid_amount,
    SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_amount
FROM expenses 
WHERE expense_date BETWEEN ? AND ?";

$total_stmt = $db->prepare($total_sql);
$total_stmt->execute([$start_date, $end_date]);
$totals = $total_stmt->fetch();

include 'includes/header.php';
?>

<div class="container mt-4 animate-fade">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-money-bill-wave me-2"></i>Harcama Yönetimi
        </h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="fas fa-plus me-2"></i>Yeni Harcama Ekle
        </button>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <!-- Filtreler -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="far fa-calendar me-2"></i>Başlangıç Tarihi
                    </label>
                    <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="far fa-calendar me-2"></i>Bitiş Tarihi
                    </label>
                    <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-tag me-2"></i>Kategori
                    </label>
                    <select class="form-select select2" name="category">
                        <option value="">Tümü</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo $category == $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-check-circle me-2"></i>Durum
                    </label>
                    <select class="form-select select2" name="status">
                        <option value="">Tümü</option>
                        <option value="paid" <?php echo $status == 'paid' ? 'selected' : ''; ?>>Ödendi</option>
                        <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Beklemede</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">
                        <i class="fas fa-filter me-2"></i>Filtrele
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Özet Kartları -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Toplam Harcama</h5>
                    <h3><?php echo formatMoney($totals['total_amount'] ?? 0); ?></h3>
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Ödenen Harcamalar</h5>
                    <h3><?php echo formatMoney($totals['paid_amount'] ?? 0); ?></h3>
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Bekleyen Harcamalar</h5>
                    <h3><?php echo formatMoney($totals['pending_amount'] ?? 0); ?></h3>
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Harcama Tablosu -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Açıklama</th>
                            <th>Kategori</th>
                            <th>Miktar</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                            <th>Notlar</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($expenses)): ?>
                            <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?php echo $expense['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($expense['description']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-tag me-1"></i>
                                        <?php echo htmlspecialchars($expense['category']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-primary">
                                        <?php echo formatMoney($expense['amount']); ?>
                                    </strong>
                                </td>
                                <td>
                                    <i class="far fa-calendar me-1"></i>
                                    <?php echo date('d.m.Y', strtotime($expense['expense_date'])); ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $expense['status'] == 'paid' ? 'success' : 'warning'; ?>">
                                        <i class="fas <?php echo $expense['status'] == 'paid' ? 'fa-check-circle' : 'fa-clock'; ?> me-1"></i>
                                        <?php echo $expense['status'] == 'paid' ? 'Ödendi' : 'Beklemede'; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($expense['notes']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary me-1" 
                                            onclick="editExpense(<?php echo $expense['id']; ?>)"
                                            data-bs-toggle="tooltip"
                                            title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="expenses.php?delete=<?php echo $expense['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Bu harcamayı silmek istediğinizden emin misiniz?')"
                                       data-bs-toggle="tooltip"
                                       title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                    <p class="text-muted">Harcama kaydı bulunamadı.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Harcama Ekleme Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Yeni Harcama Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-file-alt me-2"></i>Açıklama
                        </label>
                        <input type="text" class="form-control" name="description" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-lira-sign me-2"></i>Miktar (₺)
                        </label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="far fa-calendar me-2"></i>Harcama Tarihi
                        </label>
                        <input type="date" class="form-control" name="expense_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-tag me-2"></i>Kategori
                        </label>
                        <input type="text" class="form-control" name="category" list="categoryList" required>
                        <datalist id="categoryList">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-check-circle me-2"></i>Durum
                        </label>
                        <select class="form-select" name="status" required>
                            <option value="paid">Ödendi</option>
                            <option value="pending">Beklemede</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-sticky-note me-2"></i>Notlar
                        </label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="add_expense" value="1">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editExpense(id) {
    // Harcama düzenleme modalını aç
    alert('Harcama düzenleme özelliği eklenecek. ID: ' + id);
}
</script>

<?php include 'includes/footer.php'; ?>