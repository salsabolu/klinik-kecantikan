Buatkan aplikasi Klinik Kecantikan berdasarkan file berikut dari sisi Frontend hingga Backend. Untuk langkah awal, saya ingin implementasi modul autentikasi dasar (login, register) untuk setiap role. Kemudian dilanjutkan develop:

1\. Modul Manajemen Pasien

2\. Modul Manajemen Produk dan Layanan

3\. Modul Manajemen Reservasi dan Jadwal

4\. Modul Manajemen Rekam Medis

5\. Modul Manajemen Transaksi dan Keuangan

6\. Modul Laporan



---



### Testing Manual



#### Role

1\. Admin

   admin@klinik.com



   password

2\. Resepsionis

   resepsionis@klinik.com



   password

3\. Dokter/Terapis

   sarah.wijaya@klinik.com

   maria.sari@klinik.com

   andi.pratama@klinik.com

   lisa.permata@klinik.com

   sarah.melati@klinik.com

   andi.wijaya@klinik.com

   maya.sari@klinik.com



   password

4\. Manajer Stok

   stok@klinik.com



   password

5\. Pasien

   a. Dengan Akun

      lisa.dewi@gmail.com

      sari.dewi@gmail.com

      budi.santoso@gmail.com

      maya.putri@gmail.com



      password

   b. Tanpa Akun

      sari.cantik@email.com

      null

      mayang.sari@gmail.com

      ade.pratama@gmail.com



#### Modul

1\. Modul Manajemen Pasien

 	a. Pendaftaran pasien baru

 		- Error Prevention

 			\* Nama: sudah ada maksimum karakter✅

 			\* Tanggal Lahir: sudah ada prevention, harus sebelum tanggal hari ini✅

 			\* Jenis Kelamin: sudah required✅

 			\* Alamat: tidak ada indikator maksimum karakter✅

 			\* Nomor Telepon: sudah unique✅

 			\* Email: sudah unique, sudah wajib kalau Pasien ingin membuat akun✅

 			\* Pilih Role: harus pilih salah satu✅

 			\* Password: harus mengandung 8 karakter, password dan konfirmasi tidak match✅

 		- Perbaikan

 			\* Halaman create Patients tdak perlu sidebar✅

 			\* Tidak ada button 'Batal' dan error prevention ketika form Daftar Pasien Baru sudah diisi sebagian✅

 			\* Password: maksimum karakter, karakter yang tidak boleh digunakan✅

 			\* Icon hide/show password✅

 			\* Button 'Batal' pada bagian atas form Tambah Pasien yang sejajar dengan teks 'Tambah Pasien Baru' diganti dengan button 'Kembali' seperti pada halaman tambah Services✅

 			\* Tambah error prevention pada button 'Kembali' seperti button 'Batal' ketika form Tambah Pasien sudah diisi✅

 			\* Tidak ada notifikasi melalui email mengenai pembuatan akun❌
\* Error Message ketika coba 'Kirim Link Reset': Connection could not be established with host "127.0.0.1:1025": stream\_socket\_client(): Unable to connect to 127.0.0.1:1025 (No connection could be made because the target machine actively refused it).

 			\* Icon hide/show password pada form Login dan Daftar✅

 	b. Pencarian dan melihat profil pasien

 		- Well Done

 			\* Button 'Reset Filter' berjalan✅

 			\* Ada pop up konfirmasi ketika menghapus data Pasien✅

 			\* Filter 'Pencarian' dan 'Jenis Kelamin' berjalan baik✅

 			\* Pasien yang didaftarkan oleh Admin dan Resepsionis muncul di index Patients✅

 			\* Error prevention pada button 'Kembali' dan 'Batal' ketika terdapat perubahan data✅

 		- Perbaikan

 			\* Button 'Reset Filter' pada halaman index Patients sama MedicalRecords tidak seragam dan tidak dijadikan komponen✅

 			\* Role Resepsionis harus bisa melihat Data Pasien, Detail Data Pasien, maupun edit Data Pasien✅

 			\* Role Admin harus bisa melihat Detail Data Pasien, maupun edit Data Pasien✅

 			\* Halaman Detail Data Pasien harus menampilkan  informasi pribadi, riwayat kunjungan, dan rekam medis terkait✅

 			\* Halaman edit Patients tidak perlu sidebar✅

 			\* Halaman edit Patients belum menampilkan tanggal lahir yang telah diinputkan sebelum data diedit✅

 			\* Jumlah 'Data Pasien' di role Admin ada 13, sedangkan di seeder cuma 8✅

 			\* Pasien yang baru daftar mandiri melalui halaman Daftar harus masuk di Data Pasien yang ada di halaman index Patients✅

 			\* Resepsionis belum bisa mengakses halaman Data Pasien✅

 	c. Riwayat kunjungan dan treatment pasien

 		- Well Done

 			\* Pasien dapat mengakses halaman Reservasi Saya + button 'Buat Reservasi'✅

 			\* Pasien dapat mengakses halaman Riwayat Kunjungan✅

 			\* Pasien dapat mengakses halaman Katalog Layanan✅

 		- Perbaikan

2\. Modul Manajemen Produk dan Layanan

 	a. Daftar produk dan Harga

 		- Error Prevention

 			\* Nama Produk: sudah ada maksimum karakter dan unique✅

 			\* Deskripsi: tidak ada maksimum karakter dan character counting✅

 			\* Harga: input Harga harus lebih dari 0✅

 			\* Stok: input jumlah Stok harus lebih dari 0✅

 			\* Ketika form penambahan Products sudah diisi sebagian dan Manajer Stok atau Admin menekan tombol 'Batal'✅

 		- Perbaikan

 			\* Field 'Pencarian' tidak ada icon 'X' untuk menghapus pencarian❌

 			\* Samakan style kolom 'Aksi' seperti pada halaman index Patients✅
\* Halaman Detail Products tidak perlu button 'Edit' di samping button 'Kembali'. Cukup button pada field 'Aksi Cepat'✅

 			\* Halaman edit Products tidak muncul✅

 			\* Halaman create Products tidak muncul✅

 			\* Edit Products harus menampilkan informasi Products yang ingin diedit sesuai dengan data yang ada✅

 			\* Tidak perlu sidebar di halaman edit dan tambah Products✅

 			\* Harus ada button 'Batal' dan error prevention ketika form tambah Products sudah diisi sebagian✅

 			\* Tidak ada pop up konfirmasi saat menghapus data Products✅

 			\* Role Pasien harus bisa melihat katalog Products✅

 			\* Role Pasien tidak bisa melakukan edit dan delete Products✅

 			\* Role Pasien tidak bisa melihat informasi created at dan updated at✅

 			\* Button 'Perbarui Produk' belum bisa jalan✅

 	b. Daftar layanan (treatment) dan Harga

 		- Error Prevention

 			\* Nama Layanan: sudah ada maksimum karakter dan unique✅

 			\* Deskripsi: tidak ada maksimum karakter dan character counting✅

 			\* Harga: input Harga harus lebih dari 0✅

 			\* Durasi Menit: input durasi harus lebih dari 1✅

 			\* Ketika form penambahan Products sudah diisi sebagian dan Manajer Stok atau Admin menekan tombol 'Batal'✅

 		- Perbaikan

 			\* Field 'Pencarian' tidak ada icon 'X' untuk menghapus pencarian❌

 			\* Samakan style kolom 'Aksi' seperti pada halaman index Patients✅
\* Halaman Detail Services tidak perlu button 'Edit' di samping button 'Kembali'. Cukup button pada field 'Aksi Cepat'❌

 			\* Edit Products tidak menampilkan informasi Services yang ingin diedit✅

 			\* Tidak perlu sidebar di halaman edit dan tambah Services✅

 			\* Tidak ada button 'Batal' dan error prevention ketika form tambah Services sudah diisi sebagian✅

 			\* Tidak ada pop up konfirmasi saat menghapus data Services✅

 			\* Tambah field 3 gambar untuk tiap Services yang dapat diupload dan diganti❌

 			\* Detail Layanan yang ada di role Pasien menampilkan 1 gambar yang memenuhi height screen dan ada preview kecil ketiga gambar di atasnya. Gambar yang ditampilkan bisa diganti dengan menekan preview kecil ketiga gambar❌

 			\* Detail Layanan yang ada di role Pasien juga menampilkan nama layanan, deskripsi layanan, Harga layanan, durasi layanan, kategori durasi, dan button 'Buat Reservasi'✅

 			\* Role Pasien harus bisa melihat katalog Services✅

 			\* Role Pasien tidak bisa melakukan edit dan delete Services✅

 			\* Role Pasien tidak bisa melihat informasi created at dan updated at✅

 			\* Halaman show Services pada role Pasien menampilkan button 'Reservasi Sekarang' yang mengarah ke create Reservations❌

 	c. Manajemen stok produk

 		- Perbaikan

 			\* Tidak ada fitur penyesuaian stok secara manual di halaman index Products✅

3\. Modul Manajemen Reservasi dan Jadwal

 	a. Reservasi online/offline

 		- Perbaikan

 			\* Halaman index Reservations tidak dilengkapi sidebar✅

 			\* Halaman create Reservations tidak muncul Ketika diakses oleh Admin dan Resepsionis✅

 			\* Jam yang sudah berlalu tidak muncul di section Waktu Tersedia✅

 			\* Pemilihan jam pada section Waktu Tersedia sudah disesuaikan dengan durasi layanan✅

 			\* Hapus section diffDays dan diffHours pada ShowPatient Reservations✅

 			\* Fitur filter pada halaman MyReservations role Pasien belum berjalan✅

 	b. Manajemen jadwal dokter/terapis

 		- Perbaikan

 			\* Role Resepsionis tidak bisa melihat index Reservation✅

 			\* Role Dokter/Terapis harus bisa melihat jadwal Reservasi hari itu. Sehingga tambahkan halaman Jadwal Reservasi yang ditampilkan sebagai daftar reservasi yang akan ditangani✅

 			\* Tambahkan halaman Rwayat Reservasi bagi role Dokter/Terapis. Halaman Riwayat Reservasi menampilkan daftar Reservasi yang telah berlalu. Halaman ini dilengkapi dengan filter pencarian berdasarkan nama pasien, tanggal dari, dan tanggal sampai✅

 			\* Role Dokter/Terapis hanya bisa melihat data pasien yang pernah melakukan layanan dengan Dokter/Terapis terkait. Sehingga index Patients milik Dokter/Terapis hanya menampilkan daftar pasien yang melakukan reservasi dengan Dokter/Terapis terkait. Pastikan Dokter/Terapis juga bisa mengakses show Patient melalui tombol 'Lihat Pasien' di section Aksi Cepat halaman show Reservations✅

 			\* Role Dokter/Terapis hanya bisa membuat rekam medis dari jadwal reservasi yang ada✅

 			\* Role Resepsionis harus bisa melihat Reservation Schedule, tambahkan ke sidebar milik Resepsionis. Namun Reservation Schedule yang ditampilkan bagi Resepsionis adalah milik seluruh Dokter/Terapis, bukan Dokter/Terapis tertentu✅

 			\* Filter Tanggal pada index Reservations belum berjalan, benarkan!✅

 			\* Jadikan tombol 'Filter' dan 'Reset' pada DoctorHistory sebagai komponen✅

 	c. Notifikasi pengingat reservasi

 		- Perbaikan

 			\* Tambahkan notifikasi bagi role Pasien, Resepsionis, Admin, maupun Dokter mengenai reservasi dan jadwal pada h-1 dan h-0 jadwal reservasi. Notifikasi ditampilkan pada ikon notifikasi halaman index Dashboard. Pastikan berjalan!✅

4\. Modul Manajemen Rekam Medis

 	a. Pencatatan hasil diagnosa dan treatment

 		- Perbaikan

 			\* Gunakan SimpleLayout pada halaman create MedicalRecords✅

 			\* Gunakan SimpleLayout pada halaman edit MedicalRecords✅

 			\* Tampilkan layanan yang diambil oleh pasien setelah Dokter menekan tombol 'Buat Rekam Medis' pada halaman DoctorSchedule yang redirect ke create MedicalRecords. Sehingga ketika Dokter/Terapis masuk ke halaman create MedicalRecords dari pasien yang terpilih, maka informasi nama layanan, durasi layanan, dan biaya akan muncul✅

 			\* Pastikan error prevention message hanya muncul Ketika terdeteksi perubahan pada form di tombol 'Kembali' dan 'Batal' pada halaman create MedicalRecords dan edit MedicalRecords✅

 			\* Halaman index MedicalRecords untuk role Admin menampilkan seluruh daftar Rekam Medis dari seluruh Pasien dan Dokter/Terapis✅

 			\* Show Patients sudah terhubung ke show Reservations dan show MedicalRecords✅

 			\* Section Riwayat Reservasi dan Rekam Medis menampilkan 3 card dalam satu halaman, sudah ada pagination✅

 			\* Section MedicalRecords pada show Patients harus bisa menampilkan informasi tanggal Rekam Medis dibuat, keluhan, diagnosis, treatment, dan catatan dari Rekam Medis milik pasien✅

 			\* Rekam Medis dibuat berdasarkan data Reservasi yang ada, jadi satu Reservasi bisa memiliki banyak Rekam Medis. Perbaiki logicnya. Sehingga pada show Reservations ada section Rekam Medis yang berhubungan✅

 			\* Tambahkan tombol 'Reservasi Mendatang ' pada halaman DoctorSchedule agar bisa melihat reservasi mendatang✅

 			\* Tambahkan juga Jadwal Mendatang di halaman AllDoctorSchedule untuk role Admin✅

 			\* Aku lihat pada folder Reservations ada file yang belum bekerja dengan baik, yaitu CreatePatient. Role Pasien masih belum bisa melakukan Reservasi mandiri. Perbaiki masalah ini. Saat ini halaman CreatePatient bahkan tidak menampilkan layanan yang ada. Pastikan halaman ini bekerja hingga Pasien bisa membuat reservasi❌

 	b. Riwayat alergi dan obat-obatan

 		- Perbaikan

 			\* Tambahkan section Riwayat Alergi dan Obat-Obatan pada create MedicalRecords. Lakukan penyesuaian juga pada model, controller, database, dan seeder. Section ini bukan berupa text, namun varchar. Sehingga jumlah text inputnya bisa banyak dan bisa dihapus maupun di tambah pada halaman create MedicalRcords✅

 			\* Lalu pada show MedicalRecords, section Riwayat Alergi dan Obat-Obatan ditampilkan dalam bentuk list✅

 	c. Lampiran dokumen (foto, hasil lab, dll.)

 		- Perbaikan

 			\* Tambahkan section lampiran: lampiran foto (sebelum dan sesudah) dan lampiran hasil lab yang nullable pada create Medical Records. Lakukan penyesuaian pada model, controller, migration, dan seeder yang sudah ada. Lampiran ini bisa diupload. Sesuai dengan file proses bisnis, gunakan:
a. POST /api/medical-records/{id}/attachments : Mengunggah lampiran untuk rekam medis✅

 				b. Tambahkan tabel attachments (untuk menyimpan metadata lampiran)✅

 			\* Tampilkan section dari lampiran yang sudah dibuat pada Medical Records. Baik pada create, edit, dan show✅

 			\* Tambahkan aturan error prevention pada tombol Kembali dan Batal di halaman Edit Medical Records dan halaman Create Medical Records. Selain perubahan pada form, perubahan pada section Tambah Lampiran Baru pada halaman Edit dan section Lampiran Dokumen pada halaman Create juga disertakan pada error prevention.✅

 			\* Lampiran yang sudah ada pada halaman Edit Medical Records belum bisa dihapus, benarkan agar bisa dihapus.✅

 			\* Jadikan konfirmasi pengapusan pada halaman Edit Medical Rcords sebagai modal, bukan pop up default dari browser.✅

 			\* Penambahan lampiran pada halaman Edit Medical Records harus sama dengan yang ada pada Create Medical Records, yaitu ada section Tipe Dokumen dan Keterangan pada tiap file.✅

 			\* Perubahan data Rekam Medis di halaman Edit Medical Records belum bisa tersimpan. Ketika saya mengubah form, halaman Edit tidak mau menyimpan data, namun ketika saya menambahkan lampiran malah halaman Edit mau menyimpan perubahan. Pastikan halaman Edit bisa bekerja di tiap kondisi!✅

 			\* Saat dilakukan testing pada Edit Medical Records, perubahan pada form sudah tersimpan. Namun saat saya ingin menghapus Lampiran Yang Ada masih gagal. Setelah saya menekan OK pada alert Gagal, lampiran yang ingin saya upload malah tersimpan. Perbaiki hal ini!✅

 			\* Jadikan alert Gagal menghapus lampiran pada halaman Edit Medical Records sebagai Modal.❌

5\. Modul Manajemen Transaksi dan Keuangan

 	a. Pencatatan transaksi penjualan (produk dan layanan)

 		- Perbaikan

 			\* Tambahkan error prevention berbentuk modal pada tombol 'Kembali' di halaman create Transaction ketika terdeteksi perubahan pada form. Tambahkan tombol 'Batal' dan beri error prevention juga✅

 			\* Pada halaman create Transaction, beri aturan atau tambahkan logic jika stok produk yang dipilih adalah 12, sedangkan pada field jumlah dimasukkan angka lebih dari jumlah stok maka muncul pesan warning atau peringatan bahwa stok tidak mencukupi✅

 			\* Tombol 'Simpan' masih belum bekerja. Sehingga data yang diisi belum tersimpan pada database✅

 			\* Tambahkan logic baru pada create Transactions. Jika pada card Item 1, produk yang dipilih sudah mencapai batas stok. Maka pada card item 2 dengan produk yang sama, stok-nya sudah terupdate menjadi habis sehingga tidak bisa ditambahkan pada card Item 2✅
\* Tambahkan juga logic pada create Transaction. Karena transaksi dilakukan setelah Pasien menjalani treatment, sehingga untuk memudahkan proses transaksi maka pada form terdapat sectino yang menampilkan jadwal reservasi hari ini. Di mana jadwal tersebut bisa dipilih, sehingga kasir yang bertugas tidak perlu menanyakan identitas Pasien dan treament apa saja yang diterima✅

 			\* Halaman show Transaction sudah bisa diakses, namun:

 				a. Pada section Informasi Transaksi, Total Harga masih muncul sebagai RpNan✅

 				b. Pada section Informasi Transaksi, Status Pembayaran dan Metode Pembayaran belum menampilkan informasi apa-apa✅

 				c. Pada section Informasi Pasien, field Nama Lengkap belum menampilkan apa-apa✅

 				d. Pada section Detail Item, Harga Satuan dan Subtotal hanya menampilkan RpNan. Sedangkan field jumlah malah kosongan. Benarkan hingga memunculkan informasi yang sebenarnya✅

   				e. Section Statistik, Total Qty juga tidak menampilkan informasi apa-apa. Benarkan!✅

 			\* Create Transaction tidak bisa disimpan ketika saya menggunakan Metode Pembayaran Kartu Kredit dan Kartu Debit. Selain itu, Create Transaction juga tidak bisa disimpan Ketika saya mengisi Status Pembayaran dengan Gagal. Benarkan agar bisa disimpan pada semua kondisi Metode Pembayaran dan Status Pembayaran!✅

 			\* Index Transaction

 				a. Jadikan row table Daftar transaksi menjadi clickable untuk masuk ke show Transaction seperti pada index Reservations.✅

 				b. Jadikan perubahan pada kolom Status pada index Transaction bisa diubah menggunakan dropdown seperti pada kolom Status pada index Reservations.✅

 				c. Jadikan tampilan kolom Aksi pada index Transaction menjadi seperti pada index Reservations.✅

 				d. Role Resepsionis juga bisa melakukan edit Transaction. Namun saat ini pada index Transaction, Resepsionis masih belum bisa. Tambahkan hak edit!✅

 				e. Field filter pencarian Dari Tanggal dan Samai Tanggal pada index Transaction belum bekerja, benarkan!✅

 			\* Hapus field tax dan notes dari Transaction✅

 			\* Perbaiki halaman Edit Transaction sehingga dapat menampilkan data Transaksi seperti pada show Transaction dengan format tampilan seperti pada create Transaction.✅

 			\* Tombol Update Transaksi pada edit Transaction belum bekerja.✅

 			\* Edit Transaction

 				a. Gunakan SimpleLayout pada edit Transaction.✅

 				b. Section Informasi Transaksi

 					1) Field Pasien belum menampilkan nama Pasien.✅

 					2) Field Tanggal Transaksi belum menampilkan tanggal dari data transaksi yang diedit.✅

 					3) Field Metode Pembayaran dan Status Pembayaran belum menampilkan data dari data transaksi yang diedit.✅

 				c. Section Item Transaksi

 					1) Jadikan isi section Item Transaksi pada halaman edit Transaction mirip dengan create Transaction. Pastikan data yang dipanggil cocok dengan data Transaction yang akan diedit.✅

 					2) Field Layanan, Produk, Jumlah, Harga Satuan, dan Subtotal belum menampilkan informasi dari data transaksi yang diedit.✅

 				e. Section Ringkasan

 					1) Field Total Qty belum menampilkan informasi apapun.✅

 				f. Tambahkan tombol 'Batal'.✅

 				g. Tambahkan error prevention pada tombol 'Kembali' dan 'Batal' jika terdeteksi perubahan dari form.✅

 			\* Show Transaction

 				a. Tombol Update Status pada section Aksi Cepat belum bisa menyimpan data saat dilakukan perubahan status, benarkan.✅

 	b. Pembayaran (tunai, non-tunai)✅

 	c. Laporan keuangan (pendapatan, pengeluaran)

6\. Modul Laporan

 	a. Laporan kunjungan pasien

 	b. Laporan penjualan produk/layanan

 		- Perbaikan

 			\* Tambahkan kolom Nomor pada section Pergerakan Produk Bulan Ini dan section Peringatan Stok Menipis di halaman Stok Reports.✅

 			\* Tambahkan pagination pada section Pergerakan Produk Bulan Ini dan section Peringatan Stok Menipis di halaman Stok Reports.✅

 			\* Tambahkan filter untuk merubah tanggal pada section Pergerakan Produk Bulan Ini di halaman Stok Reports supaya bisa melihat laporan untuk bulan sebelumnya.✅

 			\* Tampilkan 3 Layanan Terpopuler dan 3 Produk Terlaris saja pada Index Reports.✅

 	c. Laporan kinerja dokter/terapis

7\. Autentikasi

 	- Error Prevention

 		\* Pengecekan email sudah terdaftar atau belum✅

 	- Perbaikan

 		\* Hapus checkbox 'Ingat Saya'✅

 		\* Tidak ada button 'Lupa Password'✅

8\. Modul Manajemen Pengguna

 	a. Manajemen akun pengguna

&nbsp;		- Perbaikan

 			\* Tambahkan halaman Profil Saya untuk menampilkan informasi akun dan bisa mengedit Detail Akun dan mengganti password pada semua role❌
			\* Samakan style kolom 'Aksi' pada halaman index Patients✅

 			\* Setelah menekan button 'Logout' harus di refresh dahulu baru bisa tampil halaman login✅

 			\* Ketika session habis dan perlu login, masih redirect ke halaman yang terakhir di buka. Bukan kembali ke dashboard✅

 			\* Hapus akun User✅

 			\* Mengubah role User✅

 	b. Penentuan hak akses (role-based access control)

9\. Dashboard

 	- Perbaikan

 		\* Dashboard Pasien tidak ada isinya✅
10. Notifikasi

🔍 Sistem Notifikasi Yang Digunakan:

* Hanya reservation\_reminders table yang digunakan - tidak ada notifications table di database
* Controller yang digunakan: NotificationController menggunakan model ReservationReminder
* Job scheduler: SendReservationReminders berjalan setiap 30 menit



##### Penjelasan Cara Kerja Sistem Notifikasi



📋 Berdasarkan Apa Notifikasi Dikirim:

* H-1 Reminders: Reservasi yang terkonfirmasi untuk besok (status 'confirmed')
* H-0 Reminders: Reservasi yang terkonfirmasi untuk hari ini (status 'confirmed')
* Filter: Hanya kirim sekali per jenis (H-1 atau H-0) per reservasi



⚙️ Bagaimana Sistem Bekerja:

* Job Scheduler: SendReservationReminders dijalankan setiap 30 menit via Laravel Scheduler
* Pembuatan Notifikasi: Job membuat record di reservation\_reminders table
* Akses Berdasarkan Role:

 	\* Dokter: Melihat notifikasi untuk reservasi mereka sendiri

 	\* Pasien: Melihat notifikasi untuk reservasi mereka sendiri

 	\* Admin/Resepsionis: Tidak mendapat notifikasi di dashboard



📱 Tampilan di Frontend:

* Ikon notifikasi di Dashboard untuk role Dokter dan Pasien
* Badge unread count
* Dropdown dengan daftar notifikasi
* Mark as read functionality
