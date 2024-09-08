
# RestAPI Laravel - Midtrans

**Server:** Laravel, PHP

**Database**: PostgreSQL
## Run Locally

- Pastikan pull/update seluruh pembaruan commit
- Pastikan database menggunakan __PostgreSQL__, jika belum, silakan tonton referensi dibawah
- Sesuaikan .env (database dan midtrans) pada direktori lokal Laravel

```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=[PORT_AWAL_INISIASI_SAAT_INSTAL]
DB_DATABASE=[DISESUAIKAN]
DB_USERNAME=[DISESUAIKAN]
DB_PASSWORD=[DISESUAIKAN]

...

MIDTRANS_SERVER_KEY=[SERVER_KEY_MIDTRANS]
MIDTRANS_CLIENT_KEY=[CLIENT_KEY_MIDTRANS]
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
```

- Di terminal VSCode, lakukan migrasi seluruh pembuatan tabel dan entitas termasuk mengisi tabel dengan seeder

```bash
  php artisan migrate:fresh
  php artisan db:seed --class=RolesTableSeeder
  php artisan db:seed --class=ServicesTableSeeder
  php artisan db:seed --class=ServicePricesTableSeeder
```

- Jalanin servernya

```bash
  php artisan serve
```


## Referensi

 - [Instalasi PostgreSQL](https://www.youtube.com/watch?v=uN0AfifH1TA)



## Feedback

Jika ditemukan *error*, waduh, coba hubungi pemilik repositori ini.

