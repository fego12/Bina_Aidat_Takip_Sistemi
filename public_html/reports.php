<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Oturum kontrolü
checkAuth();

// Tarih filtresi için varsayılan değerler
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Genel İstatistikler
$stats = $db->query("
    SELECT 
        COUNT(DISTINCT m.id) as total_members,
        COUNT(d.id) as total_dues,
        SUM(CASE WHEN d.status = 'paid' THEN d.amount ELSE 0 END) as total_paid,
        SUM(CASE WHEN d.status = 'pending' OR d.status = 'overdue' THEN d.amount ELSE 0 END) as total_pending
    FROM members m
    LEFT JOIN dues d ON m.id = d.member_id
    WHERE (d.due_date BETWEEN '$start_date' AND '$end_date' OR d.due_date IS NULL)
")->fetch();

// Üye Bazlı Aidat Durumu
$member_dues = $db->query("
    SELECT 
        m.name,
        COUNT(d.id) as total_dues,
        SUM(CASE WHEN d.status = 'paid' THEN d.amount ELSE 0 END) as paid_amount,
        SUM(CASE WHEN d.status = 'pending' OR d.status = 'overdue' THEN d.amount ELSE 0 END) as pending_amount
    FROM members m
    LEFT JOIN dues d ON m.id = d.member_id AND d.due_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY m.id, m.name
    ORDER BY m.name
")->fetchAll();

// Aylık Aidat Özeti
$monthly_summary = $db->query("
    SELECT 
        DATE_FORMAT(due_date, '%Y-%m') as month,
        COUNT(*) as total_dues,
        SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as paid_amount,
        SUM(CASE WHEN status = 'pending' OR status = 'overdue' THEN amount ELSE 0 END) as pending_amount
    FROM dues
    WHERE due_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY DATE_FORMAT(due_date, '%Y-%m')
    ORDER BY month DESC
")->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-4">
    <h2>Aidat Raporları</h2>
    
    <!-- Tarih Filtresi -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Bitiş Tarihi</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block">Filtrele</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Genel İstatistikler -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Toplam Üye</h5>
                    <h3><?php echo $stats['total_members']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Toplam Aidat</h5>
                    <h3><?php echo $stats['total_dues']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Ödenen Toplam</h5>
                    <h3><?php echo formatMoney($stats['total_paid']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Bekleyen Toplam</h5>
                    <h3><?php echo formatMoney($stats['total_pending']); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Üye Bazlı Aidat Durumu -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Üye Bazlı Aidat Durumu</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Üye</th>
                            <th>Toplam Aidat</th>
                            <th>Ödenen</th>
                            <th>Bekleyen</th>
                            <th>Ödeme Oranı</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($member_dues as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['name']); ?></td>
                            <td><?php echo $member['total_dues']; ?></td>
                            <td><?php echo formatMoney($member['paid_amount']); ?></td>
                            <td><?php echo formatMoney($member['pending_amount']); ?></td>
                            <td>
                                <?php 
                                $total = $member['paid_amount'] + $member['pending_amount'];
                                $percentage = $total > 0 ? ($member['paid_amount'] / $total) * 100 : 0;
                                ?>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?php echo $percentage; ?>%">
                                        <?php echo round($percentage); ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Yazdırma ve Dışa Aktarma Butonları -->
    <div class="mb-4">
        <button class="btn btn-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> Yazdır
        </button>
        <button class="btn btn-success" onclick="exportToExcel()">
            <i class="fas fa-file-excel"></i> Excel'e Aktar
        </button>
    </div>
</div>

<script>
function exportToExcel() {
    // Excel'e aktarma fonksiyonu eklenecek
    alert('Excel\'e aktarma özelliği eklenecek.');
}
</script>

<?php include 'includes/footer.php'; ?>