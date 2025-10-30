---------------------------------------------------------------------------
# 🧾 LARAVEL + FASTAPI INVENTORY TRACEABILITY APP untuk UMKM DAPOER NJOWO
---------------------------------------------------------------------------

Aplikasi ini terdiri dari dua bagian:
1. LARAVEL_main_app -> Backend & Frontend (Laravel)
2. PYTHON_generate_label_QRCode -> label QR Code Generator (FastAPI)

--------------------------------
## ⚙️ SETUP PROJECT
### 🔧 Setup `LARAVEL_main_app`
--------------------------------

1. Ubah file .env sesuai dengan konfigurasi database (DB_DATABASE, DB_USERNAME, DB_PASSWORD).
2. Nyalakan database (MySQL, PostgreSQL, dll).
3. Buka terminal dan arahkan ke folder "LARAVEL_main_app".
4. Jalankan perintah berikut:
   • composer install
   • php artisan storage:link
   • php artisan migrate --seed
   • npm install

--------------------------------------------
### 🔧 Setup `PYTHON_generate_label_QRCode`
--------------------------------------------

1. Buka terminal dan arahkan ke folder "PYTHON_generate_label_QRCode".
2. Aktifkan virtual environment:
   • venv\Scripts\activate    (tanpa tanda kutip)

3. Install semua dependency:
   • pip install -r requirements.txt


-------------------------------
## 🚀 MENJALANKAN APLIKASI
-------------------------------

NOTE: Pastikan database (MySQL, PostgreSQL, dll) sudah dinyalakan.

#1 MENJALANKAN LARAVEL_main_app:
----------------------------------
1. Buka terminal dan arahkan ke folder "LARAVEL_main_app".
2. Jalankan perintah:
   • php artisan serve
   -> Salin link yang muncul (misal: http://127.0.0.1:8000)

3. Buka terminal baru dan arahkan ke folder "LARAVEL_main_app" (JANGAN tutup terminal pertama).
4. Jalankan:
   • npm run dev

#2 MENJALANKAN PYTHON_generate_label_QRCode:
---------------------------------------------
1. Aktifkan virtual environment:
   • venv\Scripts\activate

2. Jalankan FastAPI:
   • py main.py

NOTE: Pastikan sudah mengaktifkan virtual environment sebelum menjalankan main.py

-------------------------------
## 🌐 AKSES APLIKASI
-------------------------------

Jika kedua bagian sudah berjalan, buka browser dan akses URL Laravel yang telah disalin:
Contoh: http://127.0.0.1:8000

-------------------------------
# 📝 CATATAN
-------------------------------

- Jalankan venv\Scripts\activate setiap kali ingin menjalankan FastAPI.
- Pastikan semua dependency terinstal dan database dalam keadaan aktif.

===============================
