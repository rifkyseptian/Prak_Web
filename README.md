# FlipPlay API Demo

Prototype REST API FlipPlay berbasis Laravel 12 untuk mengelola media (film/game) dan koleksi pengguna. Kode simulasi OOP dari Modul 3 tetap disimpan di `legacy/` sebagai referensi.

## Menjalankan project

Persyaratan: PHP 8.2+, Composer, dan ekstensi PDO SQLite atau MySQL.

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Di PowerShell, `copy .env.example .env` bisa dijalankan sebagai `Copy-Item .env.example .env`. Aplikasi memakai SQLite secara default.

Data seed: pengguna `demo@flipplay.test` (ID 1) dan tiga contoh media. Seed dapat dijalankan berulang kali tanpa menggandakan data.

## Endpoint

Semua endpoint menggunakan awalan `/api` dan menerima/mengembalikan JSON.

| Method | Endpoint | Fungsi |
| --- | --- | --- |
| GET | `/api/media?search=judul` | Daftar media, pencarian judul, pagination |
| GET | `/api/media/{id}` | Detail media dan pengguna pemilik di koleksi |
| POST | `/api/media` | Tambah media |
| PATCH | `/api/media/{id}` | Ubah media |
| DELETE | `/api/media/{id}` | Hapus media (204) |
| GET | `/api/users/{user}/collection` | Lihat koleksi seorang pengguna |
| POST | `/api/users/{user}/collection/{media}` | Tambahkan media ke koleksi (201; duplikat 409) |
| DELETE | `/api/users/{user}/collection/{media}` | Hapus media dari koleksi (media tidak ada: 404) |

Contoh body tambah film:

```json
{
  "title": "Laskar Pelangi",
  "type": "film",
  "creator": "Riri Riza",
  "release_year": 2008,
  "duration_minutes": 124
}
```

Untuk game, isi `platform` (contoh `PC / Switch`). `title` wajib unik. Request dengan input yang tidak valid mendapat status `422` beserta rincian `errors`.

## Pengujian manual di Postman

Impor `postman/FlipPlay API.postman_collection.json`, jalankan server lokal, dan gunakan collection variable `base_url` (default `http://127.0.0.1:8000/api`). Jalankan request sesuai urutan yang tersedia: request tambah media akan mengisi `media_id` otomatis, lalu skenario koleksi menguji attach, duplikat, dan detach. Request hapus media berada paling akhir agar tidak menghapus data sebelum skenario koleksi selesai.

Collection mencakup happy path dan failure path: field wajib kosong, judul duplikat, resource tidak ditemukan, tambah media dua kali ke koleksi, dan hapus media yang bukan anggota koleksi. Seluruh request memakai header `Accept: application/json` agar error validasi tampil sebagai JSON `422`.

## Pemetaan rubrik penilaian

- **CRUD + status code:** `app/Http/Controllers/Api/MediaController.php` dan route resource di `routes/api.php`.
- **Relasi dan eager loading:** `app/Models/Media.php`, `app/Models/User.php`, migration tabel media/pivot, serta `with('users')` pada index/detail.
- **Validasi + 422:** `app/Http/Requests/StoreMediaRequest.php` dan `UpdateMediaRequest.php`.
- **API Resource + whenLoaded:** `app/Http/Resources/MediaResource.php` dan `UserResource.php`.
- **Custom action attach/detach:** `app/Http/Controllers/Api/UserCollectionController.php`.
- **Postman happy/failure path:** collection di `postman/FlipPlay API.postman_collection.json`.

Untuk melihat seluruh route: `php artisan route:list --path=api`.
