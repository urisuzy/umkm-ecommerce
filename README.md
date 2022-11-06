# UMKM E-Commerce
Tugas umkm

## Install
```bash
cp .env.example .env
```
```
composer i
```
```
php artisan key:generate
```
> Edit .env, setting db
```
php artisan migrate
```
```
php artisan serve
```

## Features
### Akun
```
- Membuat akun
```
### Produk (Seller)
```
- CRUD Produk
```
### Produk (Buyer)
```
- Melihat Produk
```
### Pesanan (Buyer)
```
- Melihat Pesanan
- Membuat Pesanan
- Membayar Pesanan (IPaymu)
```
### Pesanan (Seller)
```
- Mengubah status Pesanan [reject, process]
- Menginput resi
```
### Saldo (Seller)
```
- Melihat jumlah saldo
```

## Notes
### Produk
```
- Harga akhir yang akan masuk ke total bayar adalah (harga * diskon/100)
```