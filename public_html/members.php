<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Oturum kontrolü
checkAuth();

// Üye ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_member'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    $stmt = $db->prepare("INSERT INTO members (name, email, phone, address) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$name, $email, $phone, $address]);
        $_SESSION['success_message'] = "Üye başarıyla eklendi.";
        header('Location: members.php');
        exit();
    } catch(PDOException $e) {
        $error = "Üye eklenirken bir hata oluştu.";
    }
}

// Üye silme işlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM members WHERE id = ?");
    try {
        $stmt->execute([$id]);
        $_SESSION['success_message'] = "Üye başarıyla silindi.";
        header('Location: members.php');
        exit();
    } catch(PDOException $e) {
        $error = "Üye silinirken bir hata oluştu.";
    }
}

// Üyeleri listele
$members = $db->query("SELECT * FROM members ORDER BY name")->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Üyeler</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
            Yeni Üye Ekle
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
                    <th>Ad Soyad</th>
                    <th>E-posta</th>
                    <th>Telefon</th>
                    <th>Adres</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member): ?>
                <tr>
                    <td><?php echo $member['id']; ?></td>
                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                    <td><?php echo htmlspecialchars($member['phone']); ?></td>
                    <td><?php echo htmlspecialchars($member['address']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $member['status'] == 'active' ? 'success' : 'danger'; ?>">
                            <?php echo $member['status'] == 'active' ? 'Aktif' : 'Pasif'; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editMember(<?php echo $member['id']; ?>)">Düzenle</button>
                        <a href="members.php?delete=<?php echo $member['id']; ?>" class="btn btn-sm btn-danger" 
                           onclick="return confirm('Bu üyeyi silmek istediğinizden emin misiniz?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Yeni Üye Ekleme Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Üye Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Adres</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="add_member" value="1">
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editMember(id) {
    // Üye düzenleme modalını aç
    alert('Üye düzenleme özelliği eklenecek. ID: ' + id);
}
</script>

<?php include 'includes/footer.php'; ?>