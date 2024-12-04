<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Oturum kontrolü
checkAuth();

// Aidat ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_due'])) {
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];
    $payment_date = !empty($_POST['payment_date']) ? $_POST['payment_date'] : null;
    $status = $_POST['status'];
    $notes = $_POST['notes'];
    
    $stmt = $db->prepare("INSERT INTO dues (member_id, amount, due_date, payment_date, status, notes) VALUES (?, ?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$member_id, $amount, $due_date, $payment_date, $status, $notes]);
        $_SESSION['success_message'] = "Aidat başarıyla eklendi.";
        header('Location: dues.php');
        exit();
    } catch(PDOException $e) {
        $error = "Aidat eklenirken bir hata oluştu.";
    }
}

// Aidat silme işlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM dues WHERE id = ?");
    try {
        $stmt->execute([$id]);
        $_SESSION['success_message'] = "Aidat başarıyla silindi.";
        header('Location: dues.php');
        exit();
    } catch(PDOException $e) {
        $error = "Aidat silinirken bir hata oluştu.";
    }
}

// Aidatları listele
$dues = $db->query("
    SELECT d.*, m.name as member_name 
    FROM dues d 
    JOIN members m ON d.member_id = m.id 
    ORDER BY d.due_date DESC
")->fetchAll();

// Üye listesini al (select için)
$members = $db->query("SELECT id, name FROM members WHERE status = 'active' ORDER BY name")->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Aidatlar</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDueModal">
            Yeni Aidat Ekle
        </button>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Üye</th>
                    <th>Miktar</th>
                    <th>Son Ödeme Tarihi</th>
                    <th>Ödeme Tarihi</th>
                    <th>Durum</th>
                    <th>Notlar</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dues as $due): ?>
                <tr>
                    <td><?php echo $due['id']; ?></td>
                    <td><?php echo htmlspecialchars($due['member_name']); ?></td>
                    <td><?php echo formatMoney($due['amount']); ?></td>
                    <td><?php echo date('d.m.Y', strtotime($due['due_date'])); ?></td>
                    <td><?php echo $due['payment_date'] ? date('d.m.Y', strtotime($due['payment_date'])) : '-'; ?></td>
                    <td>
                        <span class="badge bg-<?php echo $due['status'] == 'paid' ? 'success' : ($due['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                            <?php 
                            echo $due['status'] == 'paid' ? 'Ödendi' : 
                                ($due['status'] == 'pending' ? 'Beklemede' : 'Gecikmiş'); 
                            ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($due['notes']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editDue(<?php echo $due['id']; ?>)">Düzenle</button>
                        <a href="dues.php?delete=<?php echo $due['id']; ?>" class="btn btn-sm btn-danger" 
                           onclick="return confirm('Bu aidatı silmek istediğinizden emin misiniz?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Yeni Aidat Ekleme Modal -->
<div class="modal fade" id="addDueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Aidat Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="member_id" class="form-label">Üye</label>
                        <select class="form-select" id="member_id" name="member_id" required>
                            <option value="">Üye Seçin</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Miktar (₺)</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Son Ödeme Tarihi</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Ödeme Tarihi</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Durum</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending">Beklemede</option>
                            <option value="paid">Ödendi</option>
                            <option value="overdue">Gecikmiş</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notlar</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="add_due" value="1">
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editDue(id) {
    // Aidat düzenleme modalını aç
    alert('Aidat düzenleme özelliği eklenecek. ID: ' + id);
}
</script>

<?php include 'includes/footer.php'; ?>