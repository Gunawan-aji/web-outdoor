# Rencana Fitur Pemesanan Online untuk Customer

## Kebutuhan:

1. **Menu Halaman (menu.php)**:
   - Tombol "Keranjang" di samping tombol "Pesan"
   - Fitur keranjang belanja seperti di kasir/pos.php
   - Saat klik produk → bisa langsung tambah ke keranjang dengan jumlah

2. **Form Pemesanan** (wajib):
   - Nama pelanggan
   - Nomor Kursi/Meja
   - Metode Pembayaran (Tunai, QRIS, Transfer)
3. **Form Pemesanan** (opsional):
   - Catatan/Note

4. **Data Pesanan**:
   - Masuk ke admin panel (rekap semua pesanan)
   - Masuk ke karyawan/kasir panel

---

## Langkah Implementasi:

### 1. Update database.sql

- Tambah kolom `tipe_order` di tabel orders (online/offline)
- Atau buat tabel baru `online_orders`

### 2. Update config/functions.php

- Fungsi tambah ke keranjang (session-based)
- Fungsi simpan pesanan online
- Fungsi ambil pesanan online

### 3. Update pages/menu.php

- Tambah fitur keranjang
- Modal/form pemesanan
- Tampilkan total keranjang

### 4. Update admin/orders/index.php

- Tampilkan pesanan dari online dan offline
- Filter berdasarkan tipe order

### 5. Update kasir/orders/index.php

- Tampilkan pesanan online dan offline

---

## File yang Diedit:

- database.sql
- config/functions.php
- pages/menu.php
- admin/orders/index.php
- kasir/orders/index.php
