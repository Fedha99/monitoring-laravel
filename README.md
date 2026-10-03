# Learning Notes - web-monitor Setup

## 2026-10-03: SQLite Driver Missing Error

### Gejala
```
WARN could not find driver (Connection: sqlite, Database: database/database.sqlite, SQL: select exists ...)
```
Saat jalanin `php artisan migrate` di project Laravel baru.

### Penyebab
PHP 8.5 di Arch/Artix dikompilasi dengan `--with-pdo-sqlite=shared` dan `--with-sqlite3=shared` (lihat `php -i`), artinya extension dikompilasi sebagai file `.so` terpisah tapi **tidak diaktifkan otomatis** di `php.ini`. File `pdo_sqlite.so` dan `sqlite3.so` ada di `/usr/lib/php/modules/` tapi tidak diload.

### Diagnosis
1. `php -m | grep sqlite` → kosong (extension tidak terload)
2. `php -i | grep sqlite` → muncul di Configure Command sebagai `shared`
3. `find /usr/lib/php -name "*sqlite*"` → file `.so` ada
4. `grep pdo_sqlite /etc/php/php.ini` → tertulis `;extension=pdo_sqlite` (dikomentari)
5. `/etc/php/conf.d/` kosong — tidak ada file `.ini` tambahan

### Fix
Buat file `/etc/php/conf.d/sqlite.ini` berisi:
```
extension=pdo_sqlite
extension=sqlite3
```
Jalankan via `sudo sh -c 'echo "extension=pdo_sqlite" > /etc/php/conf.d/sqlite.ini && echo "extension=sqlite3" >> /etc/php/conf.d/sqlite.ini'`

### Verifikasi
`php -m | grep sqlite` → sekarang muncul `pdo_sqlite` dan `sqlite3`  
`php artisan migrate --graceful` → migration SQLite berhasil jalan

### Pencegahan
- Di Arch/Artix, extension `shared` butuh file `.ini` di `/etc/php/conf.d/` untuk diaktifkan
- Cek `php -m` setelah install package PHP baru
- Untuk project ini: SQLite cuma fallback, production pakai PostgreSQL via Docker
