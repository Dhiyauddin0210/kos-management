# Fase 2 - Admin Panel Kos Adin (Properti, Kamar, Penghuni)

## 1. Salin file ke project (struktur folder sama persis)
| Dari zip                              | Ke project                                   |
|---------------------------------------|----------------------------------------------|
| app/Http/Middleware/EnsureUserIsAdmin.php | app/Http/Middleware/                     |
| app/Http/Controllers/Admin/*          | app/Http/Controllers/Admin/                  |
| app/Http/Requests/Admin/*             | app/Http/Requests/Admin/                     |
| bootstrap/app.php                     | timpa (isinya hanya menambah alias `admin`)  |
| routes/web.php                        | timpa (route Breeze tetap ada)               |
| resources/views/layouts/admin.blade.php | resources/views/layouts/                   |
| resources/views/components/ui/*       | resources/views/components/ui/               |
| resources/views/admin/*               | resources/views/admin/                       |
| lang/id/validation.php, lang/id.json  | lang/ (pesan error bahasa Indonesia)         |

Kalau `bootstrap/app.php` kamu sudah punya kustomisasi lain, jangan ditimpa. Cukup tambahkan ini di dalam `withMiddleware`:
```php
$middleware->alias(['admin' => \App\Http\Middleware\EnsureUserIsAdmin::class]);
```

## 2. Pengaturan & perintah
Di `.env` (agar pesan validasi berbahasa Indonesia):
```
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
```
```bash
php artisan storage:link        # wajib, supaya foto upload bisa tampil (public/storage)
php artisan optimize:clear      # bersihkan cache route/view/config
composer dump-autoload
npm install                     # kalau belum
npm run dev                     # biarkan jalan (atau `npm run build` sekali saja)
php artisan serve
php artisan route:list --path=admin   # cek semua route admin terdaftar
```

## 3. Cara test
Login sebagai `admin@kos.test` / `12345678` -> otomatis masuk `/admin/dashboard`.

**Middleware & layout**
- Login `penghuni1@kos.test` lalu buka `/admin/dashboard` -> harus **403**.
- Buka `/admin` sebagai admin -> redirect ke `/admin/dashboard`.
- Perkecil browser (< 1024px): sidebar tersembunyi, tombol hamburger muncul, klik untuk buka/tutup.
- Menu Tagihan s/d Pengaturan tampil redup "Segera" (dibuat di Fase 3).

**Dashboard**
- 4 kartu: Total Kamar 10, Terisi 8, Kosong 2, Pendapatan bulan ini (dari payment verified).
- Line chart 6 bulan; tabel 5 tagihan terbaru, 5 pembayaran pending, 5 leads.

**Properti** (`/admin/properties`)
1. Tambah: isi nama/alamat/kota, upload foto (JPG < 2 MB) -> flash sukses, thumbnail tampil. Cek file di `storage/app/public/properties/`.
2. Validasi: kosongkan nama, atau upload file > 2 MB / bukan gambar -> pesan error merah.
3. Edit: ganti foto -> foto lama terhapus dari storage; matikan toggle "aktif" -> badge Nonaktif.
4. Detail: grid kamar tampil, tombol "+ Tambah Kamar" membawa properti terpilih.
5. Hapus properti dummy -> muncul confirm(), kamar di dalamnya ikut terhapus.

**Kamar** (`/admin/rooms`)
1. Filter properti/status + cari "A0" -> tabel terfilter, pindah halaman tetap membawa filter.
2. Tambah kamar: pilih fasilitas via checkbox + isi "Fasilitas lainnya" (mis. `Kulkas, Dapur`) -> status otomatis Tersedia.
3. Nomor kamar kembar dalam properti yang sama -> ditolak. Di properti lain -> boleh.
4. Edit kamar A05 -> fasilitas seeder (mis. "Water Heater") tetap tercentang.
5. Edit kamar A01 (berpenghuni) ganti status ke Tersedia -> ditolak. Hapus A01 -> ditolak.
6. Detail A01: info penghuni aktif + riwayat 3 tagihan.

**Penghuni** (`/admin/tenants`)
1. Cari nama / KTP / "A03"; filter status & kamar.
2. Tambah: dropdown hanya A09 & A10 (kamar tersedia). Isi durasi 12 + tanggal mulai -> "Tanggal Selesai" terhitung otomatis.
   Kosongkan password -> setelah simpan muncul password otomatis di flash message. Coba login dengan akun itu.
   KTP bukan 16 digit -> ditolak. Email kembar -> ditolak.
3. Cek `/admin/rooms`: kamar yang dipilih berubah jadi Terisi.
4. Edit: pindahkan penghuni ke kamar tersedia lain -> kamar lama Tersedia, kamar baru Terisi.
5. Checkout (tombol merah di halaman Edit): status Nonaktif, tanggal selesai = hari ini, kamar Tersedia.
6. Detail: 3 tab Tagihan / Maintenance / Perpanjangan (isi dari seeder).
7. Hapus penghuni -> tenant + akun user terhapus; kalau masih aktif kamar dikosongkan.

## 4. Catatan desain
- Nama lengkap dipakai sekaligus sebagai nama akun (`users.name`), jadi form tidak punya 2 kolom nama.
- Saat CREATE kamar, status hanya Tersedia/Perbaikan. "Terisi" diatur otomatis lewat modul Penghuni.
- Password otomatis hanya ditampilkan sekali di flash message setelah simpan.
- Hapus properti/kamar/penghuni adalah hard delete (cascade lewat FK), file foto dihapus manual oleh controller.
- Nama brand "Kos Adin" ditulis langsung di `layouts/admin.blade.php` (2 tempat). Kalau mau mengikuti tabel settings, ganti dengan `\App\Models\Setting::get('kos_name', 'Kos Adin')`.
- Ikon sidebar memakai emoji agar tanpa dependensi; mudah diganti SVG di array `$menu`.
