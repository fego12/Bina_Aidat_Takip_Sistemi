<?php
session_start();
require_once 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">Kullanım Koşulları</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">1. Genel Hükümler</h2>
            <p>Bu web sitesini kullanarak aşağıdaki kullanım koşullarını kabul etmiş sayılırsınız. Bu koşulları kabul etmiyorsanız, lütfen siteyi kullanmayınız.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">2. Hizmet Tanımı</h2>
            <p>Aidat Takip Sistemi, üyelerin aidat ödemelerini ve site yönetiminin giderlerini takip etmek amacıyla geliştirilmiş bir yazılım hizmetidir. Sistem aşağıdaki temel özellikleri içerir:</p>
            <ul>
                <li>Aidat takibi ve yönetimi</li>
                <li>Üye bilgilerinin yönetimi</li>
                <li>Gider takibi</li>
                <li>Raporlama araçları</li>
                <li>Ödeme takibi</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">3. Kullanım Şartları</h2>
            <p>Sistemi kullanırken aşağıdaki kurallara uymayı kabul etmiş sayılırsınız:</p>
            <ul>
                <li>Doğru ve güncel bilgi sağlamak</li>
                <li>Hesap güvenliğini korumak</li>
                <li>Sistemin güvenliğini tehlikeye atacak işlemlerden kaçınmak</li>
                <li>Başkalarının haklarına saygı göstermek</li>
                <li>Yasal düzenlemelere uymak</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">4. Hesap Güvenliği</h2>
            <p>Kullanıcılar aşağıdaki güvenlik önlemlerini almakla yükümlüdür:</p>
            <ul>
                <li>Güçlü şifre kullanımı</li>
                <li>Hesap bilgilerinin gizli tutulması</li>
                <li>Şüpheli aktivitelerin raporlanması</li>
                <li>Düzenli şifre değişimi</li>
                <li>Güvenli internet bağlantısı kullanımı</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">5. Ödeme ve İade Koşulları</h2>
            <p>Sistem üzerinden yapılan ödemeler için aşağıdaki koşullar geçerlidir:</p>
            <ul>
                <li>Ödemeler zamanında yapılmalıdır</li>
                <li>İade talepleri yönetim tarafından değerlendirilir</li>
                <li>Hatalı ödemeler 3 iş günü içinde düzeltilir</li>
                <li>Ödeme kayıtları sistem tarafından tutulur</li>
                <li>İtirazlar yazılı olarak yapılmalıdır</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">6. Sorumluluk Sınırları</h2>
            <p>Sistemin kullanımından doğabilecek sorumluluklar:</p>
            <ul>
                <li>Sistem kesintilerinden kaynaklanan gecikmeler</li>
                <li>Kullanıcı hatalarından kaynaklanan sorunlar</li>
                <li>Mücbir sebeplerden kaynaklanan aksaklıklar</li>
                <li>Üçüncü taraf hizmetlerden kaynaklanan sorunlar</li>
                <li>Veri kayıpları ve güvenlik ihlalleri</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">7. Fikri Mülkiyet Hakları</h2>
            <p>Sistem üzerindeki tüm içerik ve yazılım hakları saklıdır:</p>
            <ul>
                <li>Yazılım lisans hakları</li>
                <li>Logo ve marka hakları</li>
                <li>Tasarım hakları</li>
                <li>İçerik hakları</li>
                <li>Patent ve telif hakları</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">8. Sözleşme Değişiklikleri</h2>
            <p>Bu kullanım koşulları, önceden haber verilmeksizin güncellenebilir. Değişiklikler sitede yayınlandığı tarihte yürürlüğe girer.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">9. İletişim</h2>
            <p>Kullanım koşulları hakkında sorularınız için:</p>
            <ul>
                <li>E-posta: info@aidattakip.com</li>
                <li>Telefon: +90 (xxx) xxx xx xx</li>
                <li>Adres: [Şirket Adresi]</li>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">10. Son Güncelleme</h2>
            <p>Bu kullanım koşulları en son <?php echo date('d.m.Y'); ?> tarihinde güncellenmiştir.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 