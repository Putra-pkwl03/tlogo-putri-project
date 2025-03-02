# Telogo Putri - Bakend (Laravel 12)

## ?? Spesifikasi Proyek
- **Framework**: Laravel 12.0.1
- **PHP Version**: 8.4.1
- **Database**: MySQL
- **Composer**: 2.8.4
- **phpMyAdmin**: 6.0+

## ?? Cara Menjalankan Proyek

### 1?? Clone Repository
```sh
git clone https://github.com/putrapkwl114117/telogo-putri-project.git
cd telogo-putri-project
```

### 2?? Instal Dependensi
```sh
composer install
```

### 3?? Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database:
```sh
cp .env.example .env
```

Ubah file `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

### 4?? Generate Application Key
```sh
php artisan key:generate
```

### 5?? Jalankan Migrasi Database
```sh
php artisan migrate
```

### 6?? Jalankan Server
```sh
php artisan serve
```
Aplikasi akan berjalan di `http://127.0.0.1:8000`

## ?? Perintah Tambahan

### Menjalankan Seeder (Opsional)
```sh
php artisan db:seed
```

### Menjalankan Queue Worker (Jika Ada)
```sh
php artisan queue:work
```

### Menjalankan Storage Link (Jika Ada Upload File)
```sh
php artisan storage:link
```


# Telogo Putri - Backend (Laravel 12)

## ?? Spesifikasi Proyek
- **Framework**: Laravel 12.0.1
- **PHP Version**: 8.4.1
- **Database**: MySQL
- **Composer**: 2.8.4
- **phpMyAdmin**: 6.0+

## ?? Cara Menjalankan Proyek

### 1?? Clone Repository
```sh
git clone https://github.com/putrapkwl114117/telogo-putri-project.git
cd telogo-putri-project
```

### 2?? Instal Dependensi
```sh
composer install
```

### 3?? Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database:
```sh
cp .env.example .env
```

Ubah file `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

### 4?? Generate Application Key
```sh
php artisan key:generate
```

### 5?? Jalankan Migrasi Database
```sh
php artisan migrate
```

### 6?? Jalankan Server
```sh
php artisan serve
```
Aplikasi akan berjalan di `http://127.0.0.1:8000`

## ?? Perintah Tambahan

### Menjalankan Seeder (Opsional)
```sh
php artisan db:seed
```

### Menjalankan Queue Worker (Jika Ada)
```sh
php artisan queue:work
```

### Menjalankan Storage Link (Jika Ada Upload File)
```sh
php artisan storage:link
```


