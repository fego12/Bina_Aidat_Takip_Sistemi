<?php
session_start();
require_once 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">Gizlilik Politikası</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">1. Veri Sorumlusu</h2>
            <p>Bu gizlilik politikası, Aidat Takip Sistemi ("biz", "bizim" veya "Sistem") tarafından yönetilmektedir.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">2. Toplanan Veriler</h2>
            <p>Sistemimiz aşağıdaki kişisel verileri toplamaktadır:</p>
            <ul>
                <li>Ad ve soyad</li>
                <li>İletişim bilgileri (telefon, e-posta)</li>
                <li>Adres bilgileri</li>
                <li>Aidat ödeme bilgileri</li>
                <li>Sistem kullanım kayıtları</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">3. Veri İşleme Amacı</h2>
            <p>Kişisel verileriniz aşağıdaki amaçlarla işlenmektedir:</p>
            <ul>
                <li>Aidat takip ve yönetimi</li>
                <li>Üyelik işlemlerinin yürütülmesi</li>
                <li>İletişim faaliyetlerinin yürütülmesi</li>
                <li>Yasal yükümlülüklerin yerine getirilmesi</li>
                <li>Sistem güvenliğinin sağlanması</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">4. Veri Güvenliği</h2>
            <p>Kişisel verilerinizin güvenliği için aşağıdaki önlemler alınmaktadır:</p>
            <ul>
                <li>Güvenli sunucu altyapısı</li>
                <li>Şifreleme teknolojileri</li>
                <li>Düzenli güvenlik güncellemeleri</li>
                <li>Erişim kontrolleri</li>
                <li>Veri yedekleme sistemleri</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">5. Veri Saklama Süresi</h2>
            <p>Kişisel verileriniz, işlenme amaçlarının gerektirdiği süreler boyunca ve yasal saklama yükümlülüklerimiz kapsamında saklanmaktadır.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">6. Veri Sahiplerinin Hakları</h2>
            <p>6698 sayılı KVKK kapsamında aşağıdaki haklara sahipsiniz:</p>
            <ul>
                <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                <li>Kişisel verileriniz işlenmişse buna ilişkin bilgi talep etme</li>
                <li>Kişisel verilerinizin işlenme amacını ve bunların amacına uygun kullanılıp kullanılmadığını öğrenme</li>
                <li>Yurt içinde veya yurt dışında kişisel verilerinizin aktarıldığı üçüncü kişileri bilme</li>
                <li>Kişisel verilerinizin eksik veya yanlış işlenmiş olması hâlinde bunların düzeltilmesini isteme</li>
                <li>KVKK'nın 7. maddesinde öngörülen şartlar çerçevesinde kişisel verilerinizin silinmesini veya yok edilmesini isteme</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">7. İletişim</h2>
            <p>Gizlilik politikamız hakkında sorularınız için aşağıdaki kanallardan bize ulaşabilirsiniz:</p>
            <ul>
                <li>E-posta: info@aidattakip.com</li>
                <li>Telefon: +90 (xxx) xxx xx xx</li>
                <li>Adres: [Şirket Adresi]</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">8. Güncellemeler</h2>
            <p>Bu gizlilik politikası en son <?php echo date('d.m.Y'); ?> tarihinde güncellenmiştir. Politika üzerinde değişiklik yapma hakkımız saklıdır.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 