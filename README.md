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
php artisan migrate:refresh
```
```
php artisan storage:link
```
```
php artisan optimize:clear
```
```
php artisan serve
```

## Update Instruction
```
git pull
```
```
php artisan migrate:refresh
```
```
php artisan optimize:clear
```
```
php artisan serve
```

## Running Instruction
```
php artisan serve
```
```
./ngrok.exe http 8000
```

## Important Link
```
http://localhost:3000/404
```
```
http://localhost:3000/order/thank-you
```
```
http://localhost:3000/auth/success-verified
```
```
http://localhost:3000/auth/failed-verified
```
```
http://localhost:8000/api/pembelian/1/pay
```
## Features
### Akun
```
- Membuat akun
```
### Produk (UMKM)
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
### Pesanan (UMKM)
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