# PENSCEDULER - Academic Administration Intelligent Scheduler

An automated academic schedule management and course reschedule platform developed for Politeknik Elektronika Negeri Surabaya (PENS). The platform streamlines course rescheduling workflows across Students, Lecturers, and the Academic Administration Bureau (BAAK) through deterministic bitmask conflict resolution and AI-assisted interaction.

---

## Tim Pengembang (Team Information)

- **Nama Tim**: Geneva 1967
- **Anggota Kelompok**:
  - Ridho Fahrizal
  - Moch. Alif Akbar
  - Yeremia Christian Candra Octaviano

---

## Deskripsi Aplikasi (Application Description)

PENSCEDULER menggantikan alur koordinasi perubahan jadwal kuliah yang sebelumnya dilakukan secara manual dan terfragmentasi menjadi satu sistem terotomasi dan terpadu.

### Fitur Utama

1. **Alur Berbasis Peran (Role-Based Workflows)**:
   - **Mahasiswa (Student)**: Melihat jadwal kuliah mingguan, memantau indikator pergeseran jadwal (badge perubahan), mengajukan permohonan reschedule dengan durasi fleksibel (1 sampai 4 minggu atau permanen), serta berkonsultasi melalui asisten AI.
   - **Dosen (Lecturer)**: Memantau jadwal mengajar, menyetujui atau menolak permohonan jadwal pengganti dari mahasiswa, mengajukan jadwal pengganti mandiri dengan rekomendasi slot bebas bentrok otomatis, dan melihat daftar mahasiswa secara anonim.
   - **BAAK (Academic Administration Bureau)**: Dashboard monitoring kampus, manajemen CRUD master data (Ruangan lintas gedung: D4, D3, Pasca, SAW; Subjek/Mata Kuliah; Dosen; Mahasiswa), rekap permohonan reschedule institusi, impor massal data via CSV dengan validasi baris, dan log audit operasional.

2. **Mesin Penjadwalan Deterministik (Deterministic Bitmask Engine)**:
   - Mesin algoritma bitmask berbasis PHP murni untuk mendeteksi irisan waktu kosong, ketersediaan dosen, kapasitas ruangan, dan bentrok KRS mahasiswa secara instan tanpa ketergantungan pihak ketiga.

3. **Zero-Build Vanilla Architecture**:
   - Tampilan antarmuka Blade terintegrasi langsung dengan aset statis vanilla JavaScript dan CSS (`public/js/pensceduler.js` dan `public/css/pensceduler.css`). Aplikasi berjalan langsung tanpa membutuhkan runtime Node.js, npm, webpack, maupun Vite build step.

---

## User Disclosure: Penggunaan AI di dalam Aplikasi

Aplikasi ini mengintegrasikan kemampuan AI dengan batasan arsitektur yang ketat untuk menjamin keandalan data, integritas akademik, dan perlindungan privasi.

### Untuk Apa AI Digunakan?

1. **Natural Language Intent Parsing**:
   - Menerjemahkan instruksi percakapan bebas dari dosen atau mahasiswa di antarmuka chat menjadi parameter pencarian jadwal terstruktur (mata kuliah target, preferensi hari, rentang jam, dan durasi pergantian).

2. **Konsultasi & Penjelasan Jadwal (Conversational Explanation)**:
   - Menerjemahkan daftar kandidat slot bebas bentrok yang dihitung oleh mesin penjadwalan menjadi bahasa alami yang ramah pengguna, lengkap dengan konteks perbandingan antar opsi.

3. **Isolasi Akses Tulis (Read-Only AI Ingress)**:
   - Keluaran AI hanya disimpan sebagai draf sementara pada tabel `ai_drafts`. AI **tidak memiliki akses tulis langsung** ke tabel jadwal produksi maupun booking ruangan. Perubahan jadwal hanya terjadi setelah konfirmasi eksplisit dari pengguna atau persetujuan BAAK.

### Untuk Apa AI TIDAK Digunakan?

- **Bukan untuk Perhitungan Jadwal**: Model bahasa (LLM) **sama sekali tidak digunakan** untuk menghitung ketersediaan jam, mendeteksi bentrok jadwal, mengecek kapasitas ruangan, atau menentukan peringkat slot. Seluruh perhitungan matematis jadwal dilakukan secara deterministik 100% oleh pure PHP bitmask engine (`app/Domain/Scheduling`).
- **Bebas Halusinasi Alokasi**: Karena LLM hanya memformat hasil kalkulasi slot yang sudah tervalidasi oleh mesin bitmask, alokasi jadwal bebas dari risiko halusinasi AI.

### Kemampuan Offline & Privasi Data

- **Fallback Deterministik**: Jika endpoint API AI (OpenAI / 9Router) tidak tersedia atau `AI_CHAT_API_KEY` tidak diisi, sistem otomatis beralih ke generator template aturan internal. Seluruh fitur aplikasi dan konsultasi chat tetap berfungsi normal secara offline tanpa penyedia AI eksternal.
- **Privasi Terjaga**: Tidak ada data identitas sensitif mahasiswa (seperti nomor kontak pribadi) yang dikirimkan ke layanan AI pihak ketiga. Data bentrok mahasiswa diagregasikan secara anonim.

---

## Cara Clone dan Menjalankan Aplikasi (Setup & Run Guide)

Aplikasi ini menggunakan stack Laravel murni yang menyajikan aset frontend secara langsung dari direktori `public/`. **Tidak memerlukan Node.js maupun npm sama sekali.**

### Kebutuhan Sistem (Prerequisites)

- **PHP**: Versi >= 8.2 (disarankan PHP 8.3) dengan ekstensi aktif:
  - `pdo`, `sqlite3` (atau `pdo_sqlite`)
  - `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `curl`
- **Composer**: Versi >= 2.7
- **Git**

### Langkah Menjalankan

1. **Clone Repository**:

   ```bash
   git clone https://github.com/geneva-1967/hackathon-scheduler.git
   cd hackathon-scheduler
   ```

2. **Install Dependensi PHP**:

   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:

   Salin file `.env.example` menjadi `.env` lalu buat kunci aplikasi:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Inisialisasi Database (SQLite)**:

   Aplikasi dikonfigurasi menggunakan SQLite bawaan:

   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Server Aplikasi**:

   ```bash
   php artisan serve
   ```

6. **Buka Aplikasi di Browser**:

   Akses tautan berikut di browser:
   ```text
   http://127.0.0.1:8000
   ```

### Konfigurasi Opsional AI Gateway

Untuk menghubungkan asisten chat dengan LLM eksternal, atur kredensial berikut di file `.env`:

```env
AI_CHAT_BASE_URL=https://api.openai.com/v1
AI_CHAT_API_KEY=your_api_key_here
AI_CHAT_MODEL_ID=gpt-4o-mini
```

*Catatan: Jika dikosongkan, fitur chat otomatis menggunakan generator offline bawaan tanpa error.*

---

## Lisensi (License)

Proyek ini dilisensikan di bawah lisensi terbuka [MIT License](LICENSE).
