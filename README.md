# Test nobi: Wandi Suhandi

# Instruksi Build:
# 1. Buat database terlebih dahulu di lokal
# 2. Ubah koneksi database di env sesuai dengan yang dibuat dilokal
# 3. Lakukan composer install
# 4. Jalankan php artisan key:generate
# 5. Setelah itu jalankan php artisan serve
#
#


# Dokumentasi:
# 1. POST Register (untuk proses register)
# endpoint: http://localhost:8000/api/v1/auth/register , BODY formdata = (email,password)

# 2. POST Login (untuk proses login)
# endpoint: http://localhost:8000/api/v1/auth/login , BODY formdata = (email,password)

# 3. GET Quote (untuk menampilkan quote)
# endpoint: http://localhost:8000/api/v1/quote

# 4. POST Transaction (untuk proses transaksi)
# endpoint: http://localhost:8000/api/v1/transaction , 
# AUTHORIZATION = Bearer Token <token>, BODY formdata = (amount,trx_id,user_id)

# 5. POST Price Upload (untuk proses upload harga)
# endpoint: http://localhost:8000/api/v1/price/upload , 
# AUTHORIZATION = Bearer Token <token>, BODY formdata = (file)

# 5.a POST Price Low High
# endpoint: http://localhost:8000/api/v1/price/low-high , 
# AUTHORIZATION = Bearer Token <token>, BODY formdata = (week,year,ticker,currency)

# 5.b POST Price History
# endpoint: http://localhost:8000/api/v1/price/history , 
# AUTHORIZATION = Bearer Token <token>, BODY formdata = (timeframe,ticker,currency)
# untuk bagian 5.b timeframe menggunakan format: 2017-08-14 - 2017-08-21 

# Disini juga sudah di sediakan file postman, atau jika tidak ada bisa didownload di: https://documenter.getpostman.com/view/544869/Uyr5nJiW

