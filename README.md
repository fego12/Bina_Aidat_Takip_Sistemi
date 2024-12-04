# Aidat Takip Sistemi (Dues Tracking System)

Bu sistem, site/apartman yöneticilerinin aidat takibini kolaylaştırmak için geliştirilmiş bir web uygulamasıdır.

## 🚀 Özellikler

- 💰 Aidat takibi ve yönetimi
- 👥 Üye yönetimi
- 📊 Gelir-gider takibi
- 📈 Aylık raporlama
- 💳 Ödeme durumu takibi
- 📱 Mobil uyumlu tasarım

## 🛠️ Teknolojiler

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5
- HTML5
- CSS3
- JavaScript

## ⚙️ Kurulum

1. Repoyu klonlayın:
bash
git clone https://github.com/kullaniciadi/aidat-takip.git

2. Veritabanını oluşturun:

sql
CREATE DATABASE aidat_takip;

3. `config/database.php` dosyasını düzenleyin:

php
define('DB_HOST', 'localhost');
define('DB_USER', 'kullaniciadi');
define('DB_PASS', 'sifre');
define('DB_NAME', 'aidat_takip');

4. Veritabanı tablolarını oluşturun:

bash
php install/setup.php


## 📋 Gereksinimler

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Apache/Nginx web sunucusu
- mod_rewrite modülü (aktif)

## 🔒 Güvenlik

- XSS koruması
- SQL Injection koruması
- CSRF koruması
- Şifreleme (password_hash)
- Session güvenliği

## 📱 Ekran Görüntüleri

Pek yakında 

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'feat: Add amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📝 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 🙏 Teşekkürler

- Bootstrap ekibine
- PHP topluluğuna
- Tüm katkıda bulunanlara

---
⭐️ Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!
