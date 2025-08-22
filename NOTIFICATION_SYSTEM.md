# 📧 Sistem Notifikasi Reservation Reminder

## 🔍 Cara Kerja Sistem Notifikasi

### **Jenis Notifikasi:**
- **H-1 Reminders**: Dikirim 24 jam sebelum appointment (untuk appointment besok)
- **H-0 Reminders**: Dikirim pada hari yang sama dengan appointment

### **Kapan Dikirim:**
- **Periode**: Setiap jam dari jam 08:00 - 20:00 (business hours)
- **Frequensi**: Setiap jam pada menit ke-0 (contoh: 08:00, 09:00, 10:00, dst)
- **Status**: Hanya untuk reservasi dengan status `confirmed`
- **Sekali saja**: Setiap reminder hanya dikirim sekali per jenis per reservasi

### **Siapa yang Mendapat Notifikasi:**
- **Dokter/Terapis**: Melihat notifikasi untuk reservasi mereka sendiri
- **Pasien**: Melihat notifikasi untuk reservasi mereka sendiri
- **Admin/Resepsionis**: Tidak mendapat notifikasi di dashboard

## 🚀 Cara Testing Notifikasi

### **1. Buat Test Data (Opsional):**
```bash
php artisan create:test-reservations
```

### **2. Manual Testing - Jalankan Job Sekali:**
```bash
php artisan run:scheduler
```

### **3. Test Notifications Command:**
```bash
php artisan test:notifications
```

### **4. Start Automatic Scheduler (Development):**
```bash
php artisan schedule:start
```
*Tekan Ctrl+C untuk menghentikan*

### **5. Check Notification Data:**
```sql
-- Via SQLite
sqlite3 database/database.sqlite "SELECT * FROM reservation_reminders ORDER BY created_at DESC LIMIT 10;"

-- Via Laravel Tinker
php artisan tinker
>>> App\Models\ReservationReminder::with('reservation')->latest()->take(5)->get()
```

## 📱 Melihat Notifikasi di Frontend

### **Login Credentials untuk Testing:**
- **Dokter**: 
  - Email: `sarah.wijaya@klinik.com`
  - Password: `password`
  
- **Pasien**: 
  - Email: `lisa.dewi@gmail.com` 
  - Password: `password`

### **Lokasi Notifikasi di UI:**
1. **Bell Icon** di navbar (menampilkan jumlah unread)
2. **Dropdown Notifications** saat mengklik bell icon
3. **Dashboard** - recent notifications widget

## 🔧 Troubleshooting

### **Notifikasi Tidak Muncul:**
1. **Check apakah ada reservasi confirmed untuk hari ini/besok:**
   ```sql
   SELECT * FROM reservations WHERE status = 'confirmed' 
   AND (DATE(tanggal_reservasi) = DATE('now') OR DATE(tanggal_reservasi) = DATE('now', '+1 day'));
   ```

2. **Check apakah reminder sudah dibuat:**
   ```sql
   SELECT * FROM reservation_reminders ORDER BY created_at DESC;
   ```

3. **Manual trigger job:**
   ```bash
   php artisan run:scheduler
   ```

### **Scheduler Tidak Jalan Otomatis:**
- Di **development**, gunakan: `php artisan schedule:start`
- Di **production**, setup cron job:
  ```bash
  * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
  ```

## ⚙️ Configuration

### **Business Hours (app/Console/Kernel.php):**
```php
$schedule->job(new \App\Jobs\SendReservationReminders())
         ->hourlyAt(0) // Run at the top of every hour
         ->between('08:00', '20:00'); // Only during business hours
```

### **Message Format:**
- **H-1**: "Reminder: Anda memiliki janji temu besok (19/08/2025) pukul 10:00 untuk layanan Facial Deep Cleansing dengan Dr. Dr. Sarah Wijaya."
- **H-0**: "Reminder: Anda memiliki janji temu hari ini (18/08/2025) pukul 14:00 untuk layanan Facial Deep Cleansing dengan Dr. Dr. Sarah Wijaya."

## 📊 Database Structure

### **reservation_reminders table:**
- `id` - Primary key
- `reservation_id` - Foreign key to reservations
- `type` - 'h-1' or 'h-0'
- `message` - Notification message
- `sent` - Boolean (always true when created)
- `is_read` - Boolean (false by default)
- `scheduled_at` - When job was scheduled
- `sent_at` - When reminder was created
- `created_at` / `updated_at` - Timestamps
