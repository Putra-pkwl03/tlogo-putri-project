Telogo Putri Project - Bakend (Laravel 12)

Spesifikasi Proyek
- Framework: Laravel 12.0.1
- PHP Version: 8.4.1
- Database: MySQL
- Composer: 2.8.4
- phpMyAdmin: 6.0+

- downlaod laragon v.6.0: [https://github.com/leokhoa/laragon/discussions/737](https://github.com/leokhoa/laragon/releases/download/6.0.0/laragon-wamp.exe)

Cara Menjalankan Proyek

Clone dan atau Fork Repository
```sh
git clone https://github.com/putrapkwl114117/telogo-putri-project.git
cd telogo-putri-project
```

Instal Dependensi
```sh
composer install
```

Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database:
```sh
cp .env.example .env
```

Ubah file `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=telogo_putri_db
DB_USERNAME=root
DB_PASSWORD= isikan jika ada/biarkan kosong jika tidak
```

 Generate Application Key
```sh
php artisan key:generate
```

Jalankan Migrasi Database
```sh
php artisan migrate
```

Jalankan Server
```sh
php artisan serve
```
Aplikasi akan berjalan di `http://127.0.0.1:8000`

Perintah Tambahan

Menjalankan Seeder (Opsional)
```sh
php artisan db:seed
```

Menjalankan Storage Link (Jika Ada Upload File)
```sh
php artisan storage:link
```


