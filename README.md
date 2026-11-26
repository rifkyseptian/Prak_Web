# FlipPlay API Demo

Prototype REST API FlipPlay menggunakan Laravel 12 untuk mengelola media film/game dan koleksi media pengguna. Entitas `Media` dan `User` berasal dari simulasi OOP Modul 3; versi terminal awal tetap disimpan di folder `legacy/` sebagai referensi.

## Cara menjalankan project

### Persyaratan

- PHP 8.2 atau lebih baru dan Composer 2.
- Ekstensi `pdo_sqlite` aktif (sudah aktif pada instalasi Laravel lokal). Postman dipakai untuk mencoba endpoint.

### Langkah singkat di Windows

Buka PowerShell, lalu jalankan perintah berikut satu per satu. Nama perintah tetap mengikuti Composer dan Laravel; keterangan di bawah ini menjelaskan fungsinya dalam Bahasa Indonesia.

```powershell
git clone https://github.com/rifkyseptian/Prak_Web.git
cd Prak_Web
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

- `git clone` mengunduh project dari GitHub dan `cd` masuk ke foldernya.
- `composer install` memasang library Laravel yang diperlukan.
- `Copy-Item` menyalin pengaturan contoh menjadi pengaturan lokal. Jalankan hanya jika file `.env` belum ada.
- `key:generate` membuat kunci aplikasi lokal.
- `migrate --seed` membuat tabel sekaligus mengisi data contoh.
- `serve` menyalakan server. Biarkan jendela PowerShell ini tetap terbuka.

Server berjalan di `http://127.0.0.1:8000`; alamat dasar API adalah `http://127.0.0.1:8000/api`. Project memakai SQLite bawaan, jadi tidak perlu membuat database secara manual. Tidak perlu menjalankan `npm install` untuk demo API ini.

Seeder menyiapkan pengguna `demo@flipplay.test` (ID awal `1`) dan tiga media contoh. Jika project sudah ada di komputer, lewati `git clone`, `cd`, dan `composer install`; jalankan dari folder project:

```powershell
php artisan migrate --seed
php artisan serve
```

Untuk melihat daftar endpoint API, buka jendela PowerShell kedua di folder project dan jalankan `php artisan route:list --path=api`.

### Mencoba lewat Postman

1. Impor file `postman/FlipPlay API.postman_collection.json` ke Postman.
2. Pastikan server Laravel masih berjalan. Nilai `base_url` bawaan collection adalah `http://127.0.0.1:8000/api`.
3. Jalankan request berurutan. Request tambah media membuat judul unik dan menyimpan ID-nya ke `media_id` secara otomatis.
4. Jalankan folder **Koleksi Pengguna** untuk mencoba melihat koleksi, attach, attach duplikat, dan detach.
5. Request hapus media diletakkan paling akhir. Collection memeriksa status yang diharapkan untuk setiap skenario.

Untuk mengetes validasi gagal, request harus mengirim header `Accept: application/json`; contoh failure path pada collection sudah memakai header tersebut.

## Endpoint API

Semua endpoint menerima dan mengembalikan JSON. Laravel menambahkan prefix `/api` secara otomatis.

| Method | Endpoint | Fungsi dan status berhasil |
| --- | --- | --- |
| GET | `/api/media?search=judul` | Daftar media, pencarian judul, dan pagination (`200`) |
| GET | `/api/media/{id}` | Detail media beserta pengguna yang menyimpannya (`200`) |
| POST | `/api/media` | Tambah media (`201`) |
| PUT/PATCH | `/api/media/{id}` | Perbarui media (`200`) |
| DELETE | `/api/media/{id}` | Hapus media (`204`) |
| GET | `/api/users/{user}/collection` | Lihat koleksi pengguna (`200`) |
| POST | `/api/users/{user}/collection/{media}` | Tambah media ke koleksi (`201`; jika duplikat `409`) |
| DELETE | `/api/users/{user}/collection/{media}` | Hapus media dari koleksi (`200`; jika bukan anggota `404`) |

### Contoh request tambah film

```json
{
  "title": "Laskar Pelangi",
  "type": "film",
  "creator": "Riri Riza",
  "release_year": 2008,
  "duration_minutes": 124
}
```

Untuk game, gunakan `"type": "game"` dan isi `platform`, misalnya `"PC / Switch"`. Judul wajib diisi dan tidak boleh duplikat. Field film wajib memiliki `duration_minutes`, sedangkan field game wajib memiliki `platform`. Input yang gagal validasi menghasilkan HTTP `422` dengan daftar error.

Contoh pencarian: `GET http://127.0.0.1:8000/api/media?search=Pelangi`.

## Catatan persiapan tanya-jawab aslab

**Apa fungsi project ini?**
Mengubah simulasi FlipPlay berbasis terminal dari Modul 3 menjadi REST API. Client seperti Postman dapat melakukan CRUD media serta mengatur koleksi milik pengguna.

**Apa itu REST API dan mengapa endpoint memakai method berbeda?**
REST API menyediakan akses resource melalui HTTP. `GET` membaca data, `POST` membuat atau menjalankan aksi tambah, `PATCH/PUT` memperbarui, dan `DELETE` menghapus. Status HTTP menunjukkan hasil operasi.

**Mengapa ada tabel pivot `media_user`?**
Relasinya many-to-many: seorang pengguna dapat menyimpan banyak media, dan satu media dapat disimpan banyak pengguna. Tabel pivot menyimpan pasangan `user_id` dan `media_id`; foreign key memakai cascade delete dan pasangan tersebut dibuat unik agar tidak rangkap.

**Bagaimana relasi Eloquent dibuat?**
`User::mediaCollection()` dan `Media::users()` sama-sama memakai `belongsToMany()` dengan tabel pivot `media_user`.

**Apa kegunaan eager loading?**
`with('users')` memuat pengguna terkait bersama data media, sehingga API tidak menjalankan query berulang untuk setiap media (masalah N+1). Relasi baru ditampilkan oleh Resource jika sudah dimuat.

**Mengapa menggunakan Form Request dan API Resource?**
Form Request memisahkan aturan validasi dari controller dan Laravel mengembalikan `422` otomatis untuk input JSON yang gagal. API Resource menjaga bentuk response JSON konsisten dan memakai `whenLoaded` untuk relasi.

**Bagaimana validasi judul duplikat bekerja saat update?**
Saat membuat media, judul harus unik. Saat update, validasi mengabaikan ID media yang sedang diperbarui sehingga judulnya boleh tetap sama, tetapi tidak boleh memakai judul milik media lain.

**Apa custom action di luar CRUD?**
`POST /users/{user}/collection/{media}` melakukan attach media ke koleksi. `DELETE` pada endpoint yang sama melakukan detach tanpa menghapus data media. Attach duplikat menghasilkan `409`.

**Bagaimana skenario berhasil dan gagal diuji?**
Collection Postman memuat request CRUD dan koleksi, termasuk judul kosong/duplikat (`422`), ID yang tidak ditemukan (`404`), attach berhasil (`201`), attach duplikat (`409`), detach berhasil, dan delete media (`204`). Setiap request memiliki pemeriksaan status yang diharapkan.

**Apakah endpoint koleksi memerlukan login?**
Belum. Ini prototype pembelajaran; pengguna dipilih melalui ID di URL agar relasi attach/detach dapat didemokan. Untuk aplikasi produksi, endpoint perlu dilindungi autentikasi dan otorisasi.

## Rubrik penilaian dan letak implementasinya

Berikut enam kriteria dan bobot yang terlihat pada gambar rubrik yang diberikan (jumlah bobot bagian ini 80 poin):

| Kriteria rubrik | Bobot | Letak di project |
| --- | ---: | --- |
| CRUD API lengkap (index, show, store, update, destroy) dan status code tepat | 20 | `app/Http/Controllers/Api/MediaController.php`, `routes/api.php` |
| Eloquent relationship, migration, relasi antartabel, eager loading | 20 | `app/Models/Media.php`, `app/Models/User.php`, `database/migrations/2026_11_26_000000_create_media_table.php`, `database/migrations/2026_11_26_000001_create_media_user_table.php`, `MediaController.php` |
| Form Request Validation dan penanganan error 422 | 15 | `app/Http/Requests/StoreMediaRequest.php`, `app/Http/Requests/UpdateMediaRequest.php` |
| API Resource, format response konsisten, termasuk `whenLoaded` | 10 | `app/Http/Resources/MediaResource.php`, `app/Http/Resources/UserResource.php` |
| Custom action di luar CRUD (attach/detach relasi) | 10 | `app/Http/Controllers/Api/UserCollectionController.php`, `routes/api.php` |
| Pengujian endpoint happy path dan failure path menggunakan Postman | 5 | `postman/FlipPlay API.postman_collection.json` |

Data demo dibuat oleh `database/seeders/DatabaseSeeder.php`. Komentar `// Rubrik ...` dalam file kode menandai bagian implementasi yang terkait dengan tiap kriteria di atas.
