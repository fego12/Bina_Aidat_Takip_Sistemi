// Genel JavaScript fonksiyonları
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap tooltips'i aktifleştir
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Alert mesajlarını otomatik kapat
    var alertList = document.querySelectorAll('.alert')
    alertList.forEach(function (alert) {
        setTimeout(function() {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});