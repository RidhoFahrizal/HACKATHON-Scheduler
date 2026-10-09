# AGENTS.md — Digital Campus Worker (Hackathon PENS 2026)

## 1. Konteks
Aplikasi penjadwalan ulang kuliah. Alur: **Dosen mengajukan → engine cek KRS & ruang → BAAK setujui satu klik → pengumuman otomatis**. Semua perubahan bisa di-rollback.

Sumber kebenaran: `agent/context/SPEC.MD` (baca bagian relevan sebelum implementasi). Jika instruksi ini bertentangan dengan SPEC, tanyakan ke user.


## 2. Stack
- Laravel (PHP 8.x), Inertia + React (TypeScript), Tailwind, shadcn/ui, FullCalendar
- PostgreSQL, Laravel Queue (driver `database`), Pest
- Auth starter kit + `spatie/laravel-permission` + Policy
- LLM dengan tool/function calling (hanya untuk parsing chat)

## 3. Perintah
```bash
composer dev                       # jalankan server + queue + vite
php artisan migrate:fresh --seed   # reset DB + seed data demo
php artisan test                   # Pest
./vendor/bin/pint                  # format kode PHP
npm run build                      # build frontend
```

## 4. Struktur Kode
```
app/Domain/Scheduling/   Engine/, DTO/, Actions/, Announcements/
app/Domain/AI/           DraftParser.php, Tools/
app/Policies/, app/Http/Resources/
```
Engine harus terpisah dari UI. Jika aturan berubah, hanya `app/Domain/Scheduling` yang disentuh.

## 5. Aturan Wajib
1. **Engine irisan = PHP murni (bitmask).** Dilarang memakai LLM untuk menghitung slot, skor, atau alasan gugur.
2. **LLM tidak punya jalur tulis ke jadwal.** LLM hanya menulis ke `ai_drafts`; jadi `change_requests` hanya setelah dosen menekan Ajukan.
3. **`change_requests.scope` wajib (`once` / `onwards`), tanpa nilai bawaan.**
4. **Konflik dihitung dari `enrollments` (KRS)**, bukan dari voting.
5. **Setiap perubahan jadwal** harus menyimpan `before`/`after` di `schedule_change_items` (dasar rollback). Rollback harus mencocokkan keadaan sekarang dengan `after`; jika sudah berubah, tampilkan konflik, jangan menimpa.
6. **Terapkan perubahan dalam satu transaksi:** cek ulang bentrok + kunci baris (`FOR UPDATE`) sebelum menulis.
7. **Privasi:** nama mahasiswa bentrok hanya di `option_conflict_students` dan hanya boleh dibaca BAAK. Dosen hanya melihat agregat + sumber bentrok. Terapkan lewat Policy dan API Resource, bukan sembunyi di UI.
8. **Sumber bentrok harus ditampilkan** (mis. "bentrok dari Dosen X, matkul Y"), bukan menyalahkan mahasiswa.
9. **Teks pengumuman dan alasan gugur dirender dari templat/kode alasan**, bukan dikarang AI.
10. **Aturan = data:** jam istirahat, durasi slot, bobot skor, durasi "Batalkan" ada di `time_slots` / `settings`. Jangan hardcode.
11. **Transisi status** dijaga lewat enum + daftar transisi yang diizinkan.
12. Tombol "Batalkan" 10 detik dengan delayed job (`ApplyChange::dispatch()->delay()`); job keluar jika ajuan dibatalkan.

## 6. Konvensi
- Logika bisnis di `Actions`/`Domain`, bukan di controller. Controller tipis.
- Validasi lewat Form Request. Status dan tipe memakai PHP Enum.
- Migrasi berurutan sesuai SPEC bagian 5.14. Kunci asing master `restrictOnDelete`, tabel anak `cascadeOnDelete`.
- UI berbahasa Indonesia; kode, nama variabel, dan commit berbahasa Inggris.
- Ikuti gaya kode yang sudah ada; jalankan `pint` sebelum selesai.
- Pertahankan komentar yang tidak terkait perubahan.

## 7. Pengujian
- Kasus asli (SPEC bagian 1) = **golden test pertama** untuk engine.
- Tambahkan: tidak ada slot sempurna, mahasiswa mengulang, ruang penuh, hari Jumat, scope `once` vs `onwards`, rollback.
- Seeder harus memuat skenario kasus asli (dosen dua matkul bentrok, kelas besar lintas kelas, mahasiswa mengulang).

## 8. Definition of Done
- [ ] `php artisan test` hijau
- [ ] `pint` bersih
- [ ] `migrate:fresh --seed` berjalan tanpa error
- [ ] Tidak melanggar aturan wajib di bagian 5
- [ ] Demo bisa jalan lokal (tanpa internet; LLM punya mock/fallback)

## 9. Constraint
- Jangan menambah dependensi besar (mis. Filament) tanpa konfirmasi user.
- Jangan memodifikasi folder `agent/` kecuali diminta eksplisit.
- Jangan menjalankan perintah destruktif (`migrate:fresh`, `rm -rf`, `git reset --hard`) pada data non-demo tanpa konfirmasi.
- Jangan menebak keputusan yang belum final; tandai TBD dan tanyakan.
- apabila token habis 30.000 dalam 1 prompt maka langsung stop. jangan dilanjutkan. 

## 10. Peran Agent / Subskill
Tugas dipisah per area; baca subskill terkait di `agent/subskills/` bila tersedia.

| Peran | Area |
|---|---|
| db-schema | `database/`, `app/Models` |
| scheduling-engine | `app/Domain/Scheduling`, `tests/` |
| ai-draft | `app/Domain/AI` |
| frontend-ui | `resources/js` |
| auth-policy | `app/Policies`, `app/Http/Resources` |

