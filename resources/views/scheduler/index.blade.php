@extends('layouts.app')

@section('content')
<div class="app-container" id="app-shell">
    <x-sidebar />

    <div class="main-wrapper" id="main-wrapper">
        <x-topbar />

        <main class="content-body">
            <div class="view-panel active" id="view-dashboard">
              <div id="dashboard-role-content">
              </div>
            </div>

            <div class="view-panel" id="view-classes">
              <div class="view-header-bar">
                <div>
                  <h2 class="view-title">Daftar Matakuliah</h2>
                  <p class="view-subtitle">Seluruh matakuliah terdaftar, dosen pengampu, dan alokasi ruang perkuliahan aktif.</p>
                </div>
              </div>

              <div class="classes-filter-bar">
                <div class="filter-input-wrap">
                  <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" class="search-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                  <input type="text" id="classes-search-input" class="search-field" placeholder="Cari matakuliah, dosen, atau kode..." aria-label="Cari matakuliah">
                </div>

                <div class="filter-select-group">
                  <select id="classes-day-filter" class="filter-select" aria-label="Filter hari kuliah">
                    <option value="all">Semua Hari</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                  </select>

                  <div id="lecturer-filter-wrapper" style="display: none;">
                    <select id="classes-lecturer-filter" class="filter-select" aria-label="Filter nama dosen">
                      <option value="all">Semua Dosen</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="classes-grid-container" id="classes-full-grid">
              </div>
            </div>

            <div class="view-panel" id="view-class-detail">
              <div class="view-header-bar">
                <div class="detail-back-group">
                  <button type="button" class="btn-back-nav" onclick="navigateToView('classes')" title="Kembali ke Matakuliah">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    <span>Kembali</span>
                  </button>
                  <div>
                    <h2 class="view-title" id="detail-course-title">Workshop Mesin Pembelajaran</h2>
                    <p class="view-subtitle" id="detail-course-subtitle">WM &bull; 3 SKS &bull; Lab C 102</p>
                  </div>
                </div>
              </div>

              <div class="class-detail-content-layout" id="class-detail-content-area">
              </div>
            </div>

            <div class="view-panel" id="view-schedule">
              <div class="view-header-bar">
                <div>
                  <h2 class="view-title">Jadwal Kuliah Mingguan (7 Hari)</h2>
                  <p class="view-subtitle">Matriks visual alokasi ruangan per 1 jam presisi (07:00 - 18:00) sesuai jadwal perkuliahan.</p>
                </div>
              </div>

              <div class="room-schedule-matrix-card">
                <div class="matrix-table-viewport" id="matrix-table-viewport">
                  <table class="room-schedule-table">
                    <thead>
                      <tr id="matrix-header-days-row"></tr>
                      <tr id="matrix-header-hours-row"></tr>
                    </thead>
                    <tbody id="matrix-body-rooms"></tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="view-panel" id="view-reschedule">
              <div class="reschedule-centered-wrap">
                <div class="view-header-bar" style="margin-bottom: 20px; text-align: center; flex-direction: column; align-items: center;">
                  <h2 class="view-title" id="reschedule-page-title">Pindah Jadwal Perkuliahan</h2>
                  <p class="view-subtitle">Pemindahan jadwal perkuliahan dan pencarian slot ruangan kosong tanpa konflik jadwal.</p>
                </div>

                <div class="reschedule-form-card">
                  <h3 class="form-card-title">Formulir Perubahan Jadwal</h3>
                  <p class="form-card-desc">Pilih matakuliah terlebih dahulu untuk mengaktifkan parameter jadwal baru dan rekomendasi sistem.</p>

                  <div class="form-group">
                    <label for="reschedule-course-select" class="form-label">1. Matakuliah yang Dipindah</label>
                    <select id="reschedule-course-select" class="form-select" onchange="onRescheduleCourseChange(this.value)">
                      <option value="">-- Pilih Matakuliah --</option>
                    </select>
                  </div>

                  <div class="current-schedule-info-card" id="current-schedule-info-box" style="display: none;">
                    <span class="info-card-badge">Jadwal Reguler Saat Ini</span>
                    <div class="info-card-details">
                      <span id="current-course-name" style="font-weight: 700; color: var(--text-main);">Workshop Mesin Pembelajaran</span>
                      <span id="current-course-schedule" style="color: var(--text-subtle);">Senin, 08:00 - 11:00 &bull; Lab C 102</span>
                    </div>
                  </div>

                  <fieldset id="reschedule-parameters-fieldset" disabled class="reschedule-fieldset">
                    <div class="form-row-grid">
                      <div class="form-group">
                        <label for="reschedule-target-date" class="form-label">Tanggal mulai berlaku</label>
                        <input id="reschedule-target-date" class="form-select" type="date" value="{{ now()->toDateString() }}">
                      </div>
                      <div class="form-group">
                        <label for="reschedule-scope-select" class="form-label">Cakupan perubahan</label>
                        <select id="reschedule-scope-select" class="form-select">
                          <option value="once">Sekali saja</option>
                          <option value="onwards">Seterusnya sampai akhir semester</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="reschedule-duration-select" class="form-label">2. Durasi Perpindahan Jadwal</label>
                      <select id="reschedule-duration-select" class="form-select">
                        <option value="1 Pekan (Sesi Pengganti)">1 Pekan (Sesi Pengganti)</option>
                        <option value="2 Pekan">2 Pekan</option>
                        <option value="3 Pekan">3 Pekan</option>
                        <option value="4 Pekan">4 Pekan</option>
                        <option value="Permanen (Sisa Semester)">Permanen (Sisa Semester)</option>
                      </select>
                    </div>

                    <div class="form-row-grid">
                      <div class="form-group">
                        <label for="reschedule-target-day" class="form-label">Hari Baru</label>
                        <select id="reschedule-target-day" class="form-select">
                          <option value="Senin">Senin</option>
                          <option value="Selasa">Selasa</option>
                          <option value="Rabu" selected>Rabu</option>
                          <option value="Kamis">Kamis</option>
                          <option value="Jumat">Jumat</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="reschedule-target-time" class="form-label">Jam Perkuliahan</label>
                        <select id="reschedule-target-time" class="form-select">
                          <option value="08:00 - 10:00">08:00 - 10:00 (2 Jam)</option>
                          <option value="08:00 - 11:00">08:00 - 11:00 (3 Jam)</option>
                          <option value="10:00 - 12:00">10:00 - 12:00 (2 Jam)</option>
                          <option value="13:00 - 15:00">13:00 - 15:00 (2 Jam)</option>
                          <option value="13:00 - 16:00" selected>13:00 - 16:00 (3 Jam)</option>
                          <option value="15:00 - 17:00">15:00 - 17:00 (2 Jam)</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="reschedule-target-room" class="form-label">Ruangan Tujuan</label>
                      <select id="reschedule-target-room" class="form-select">
                        <option value="Lab C 102">Lab C 102 (Gedung D4 Lt. 1)</option>
                        <option value="Lab C 103" selected>Lab C 103 (Gedung D4 Lt. 1)</option>
                        <option value="Lab C 104">Lab C 104 (Gedung D4 Lt. 1)</option>
                        <option value="Lab C 105">Lab C 105 (Gedung D4 Lt. 1)</option>
                        <option value="SAW-06.10">SAW-06.10 (Pascasarjana Lt. 6)</option>
                        <option value="Lab Software SAW-08">Lab Software SAW-08 (Pascasarjana Lt. 8)</option>
                        <option value="SAW-05.02">SAW-05.02 (Pascasarjana Lt. 5)</option>
                        <option value="Lab Sinyal B 204">Lab Sinyal B 204 (Gedung D3 Lt. 2)</option>
                      </select>
                    </div>
                  </fieldset>

                  <div class="form-actions-bar">
                    <button type="button" class="btn-recommendation-trigger" id="btn-request-recommendation" disabled onclick="requestSystemRecommendations()">
                      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                      <span>Dapatkan Rekomendasi Sistem</span>
                    </button>
                    <button type="button" class="btn-primary-action" id="btn-submit-reschedule" disabled onclick="submitRescheduleRequest()">
                      Kirim Pengajuan
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="view-panel" id="view-chat">
              <div class="chat-view-container">
                <div class="chat-header-bar">
                  <div class="chat-header-title-box">
                    <div class="chat-bot-avatar">
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="10" rx="2"/>
                        <circle cx="12" cy="5" r="2"/>
                        <path d="M12 7v4"/>
                        <line x1="8" y1="16" x2="8" y2="16"/>
                        <line x1="16" y1="16" x2="16" y2="16"/>
                      </svg>
                    </div>
                    <div>
                      <h3 class="chat-header-title">Asisten AI Akademik PENSCEDULER</h3>
                      <span class="chat-header-status">Siap membantu informasi jadwal & rekomendasi pemindahan</span>
                    </div>
                  </div>
                </div>

                <div class="chat-templates-bar" id="chat-templates-container">
                </div>

                <div class="chat-messages-stream" id="chat-messages-stream">
                </div>

                <div class="chat-input-bar">
                  <input type="text" id="chat-user-input" class="chat-input-field" placeholder="Ketik pertanyaan jadwal atau instruksi pemindahan..." aria-label="Pesan chat">
                  <button type="button" class="btn-primary-action" id="btn-chat-send" aria-label="Kirim pesan">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="22" y1="2" x2="11" y2="13"/>
                      <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    <span>Kirim</span>
                  </button>
                </div>
              </div>
            </div>

            <div class="view-panel" id="view-baak-requests">
              <div class="view-header-bar">
                <div>
                  <h2 class="view-title">Daftar Permintaan Pindah Jadwal Kuliah</h2>
                  <p class="view-subtitle">Seluruh permohonan pemindahan sesi perkuliahan dari mahasiswa dan dosen pengampu.</p>
                </div>
              </div>

              <div class="clean-section-card">
                <div class="history-table-viewport">
                  <table class="history-table">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Pengaju</th>
                        <th>Matakuliah</th>
                        <th>Dosen Pengampu</th>
                        <th>Jadwal Asli</th>
                        <th>Jadwal Usulan</th>
                        <th>Alasan</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody id="baak-full-requests-tbody">
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="view-panel" id="view-baak-rooms">
              <div class="view-header-bar">
                <div>
                  <h2 class="view-title">Master Ruangan Perkuliahan</h2>
                  <p class="view-subtitle">Daftar seluruh laboratorium dan ruang kelas teori kampus terdaftar di database.</p>
                </div>
                <div class="view-header-actions">
                  <button type="button" class="btn-secondary-action" onclick="openCsvImportModal('rooms')">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span>Impor CSV</span>
                  </button>
                  <button type="button" class="btn-primary-action" onclick="openBaakCrudModal('room')">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                  <span>Tambah Ruangan</span>
                </button>
                </div>
              </div>

              <div class="clean-section-card">
                <div class="history-table-viewport">
                  <table class="history-table">
                    <thead>
                      <tr>
                        <th>Label Ruangan</th>
                        <th>Nama Ruangan</th>
                        <th>Gedung</th>
                        <th style="width: 140px;">Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="baak-rooms-tbody">
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="view-panel" id="view-baak-subjects">
              <div class="view-header-bar">
                <div>
                  <h2 class="view-title">Master Subjek Perkuliahan</h2>
                  <p class="view-subtitle">Daftar seluruh mata kuliah kurikulum akademik aktif dan bobot satuan kredit semester (SKS).</p>
                </div>
                <div class="view-header-actions">
                  <button type="button" class="btn-secondary-action" onclick="openCsvImportModal('subjects')">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span>Impor CSV</span>
                  </button>
                  <button type="button" class="btn-primary-action" onclick="openBaakCrudModal('subject')">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                  <span>Tambah Subjek</span>
                </button>
                </div>
              </div>

              <div class="clean-section-card">
                <div class="history-table-viewport">
                  <table class="history-table">
                    <thead>
                      <tr>
                        <th>Kode Subjek</th>
                        <th>Nama Subjek</th>
                        <th>SKS</th>
                        <th style="width: 140px;">Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="baak-subjects-tbody">
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="view-panel" id="view-baak-lecturers">
              <div class="view-header-bar">
                <div>
                  <h2 class="view-title">Daftar Dosen Pengampu</h2>
                  <p class="view-subtitle">Data tenaga pengajar aktif Departemen Teknik Informatika dan Rekayasa.</p>
                </div>
                <div class="view-header-actions">
                  <button type="button" class="btn-secondary-action" onclick="openCsvImportModal('lecturers')">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span>Impor CSV</span>
                  </button>
                  <button type="button" class="btn-primary-action" onclick="openBaakCrudModal('lecturer')">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                  <span>Tambah Dosen</span>
                </button>
                </div>
              </div>

              <div class="clean-section-card">
                <div class="history-table-viewport">
                  <table class="history-table">
                    <thead>
                      <tr>
                        <th>Nama Dosen</th>
                        <th>Nomor Induk Pegawai (NIP)</th>
                        <th style="width: 140px;">Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="baak-lecturers-tbody">
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="view-panel" id="view-baak-students">
              <div class="view-header-bar">
                <div>
                  <h2 class="view-title">Daftar Mahasiswa Terdaftar</h2>
                  <p class="view-subtitle">Data mahasiswa aktif, nomor registrasi pokok (NRP), angkatan, dan program studi.</p>
                </div>
                <div class="view-header-actions">
                  <button type="button" class="btn-secondary-action" onclick="openCsvImportModal('students')">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span>Impor CSV</span>
                  </button>
                  <button type="button" class="btn-primary-action" onclick="openBaakCrudModal('student')">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                  <span>Tambah Mahasiswa</span>
                </button>
                </div>
              </div>

              <div class="clean-section-card">
                <div class="history-table-viewport">
                  <table class="history-table">
                    <thead>
                      <tr>
                        <th>Nama Mahasiswa</th>
                        <th>NRP</th>
                        <th>Angkatan</th>
                        <th>Jurusan</th>
                        <th style="width: 140px;">Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="baak-students-tbody">
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="view-panel" id="view-baak-system-logs">
              <div class="view-header-bar">
                <div>
                  <h2 class="view-title">Log & Kesehatan Sistem</h2>
                  <p class="view-subtitle">Pemantauan metrik operasional infrastruktur, kesehatan penjadwalan, dan jejak audit aktivitas.</p>
                </div>
                <button type="button" class="btn-secondary-action" onclick="showToast('Data log sistem berhasil diperbarui.'); renderBaakSystemLogs();">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                  <span>Segarkan Log</span>
                </button>
              </div>

              <div class="system-health-grid">
                <div class="system-health-card">
                  <div class="health-card-header">
                    <span class="health-card-title">Status Server</span>
                    <span class="health-status-dot online"></span>
                  </div>
                  <span class="health-card-val">99.98% Uptime</span>
                  <span class="health-card-sub">Beroperasi Normal &bull; NGINX 1.24</span>
                </div>

                <div class="system-health-card">
                  <div class="health-card-header">
                    <span class="health-card-title">Database Latensi</span>
                    <span class="health-status-dot online"></span>
                  </div>
                  <span class="health-card-val">12 ms</span>
                  <span class="health-card-sub">PostgreSQL 16 &bull; Sambungan Aktif: 8</span>
                </div>

                <div class="system-health-card">
                  <div class="health-card-header">
                    <span class="health-card-title">Scheduler Engine</span>
                    <span class="health-status-dot online"></span>
                  </div>
                  <span class="health-card-val">0 Konflik</span>
                  <span class="health-card-sub">Bitmask Engine Aktif &bull; 100% Integritas</span>
                </div>

                <div class="system-health-card">
                  <div class="health-card-header">
                    <span class="health-card-title">Penggunaan Memori</span>
                    <span class="health-status-dot normal"></span>
                  </div>
                  <span class="health-card-val">24% RAM &bull; 8% CPU</span>
                  <span class="health-card-sub">Beban Ringan &bull; Kapasitas Aman</span>
                </div>

                <div class="system-health-card">
                  <div class="health-card-header">
                    <span class="health-card-title">Ruangan Terpantau</span>
                    <span class="health-status-dot normal"></span>
                  </div>
                  <span class="health-card-val">42 Ruangan</span>
                  <span class="health-card-sub">Sinkron Real-time 4 Gedung Kampus</span>
                </div>
              </div>

              <div class="clean-section-card" style="margin-top: 20px;">
                <div class="clean-section-header">
                  <div class="header-title-box">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="var(--primary-color)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <h3 class="clean-header-title">Rekaman Jejak Audit & Log Aktivitas</h3>
                  </div>
                </div>

                <div class="classes-filter-bar" style="padding: 12px 16px; border-bottom: 1px solid var(--border-subtle); background: var(--bg-surface);">
                  <div class="filter-input-wrap">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" class="search-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="log-search-input" class="search-field" placeholder="Cari aktivitas, aktor, modul..." oninput="onBaakLogSearch(this.value)" aria-label="Cari log">
                  </div>

                  <div class="log-filter-buttons-group">
                    <button type="button" class="log-filter-btn active" data-level="all" onclick="filterBaakLogs('all')">Semua</button>
                    <button type="button" class="log-filter-btn" data-level="info" onclick="filterBaakLogs('info')">Info</button>
                    <button type="button" class="log-filter-btn" data-level="shift" onclick="filterBaakLogs('shift')">Perubahan Jadwal</button>
                    <button type="button" class="log-filter-btn" data-level="sync" onclick="filterBaakLogs('sync')">Sinkronisasi CSV</button>
                    <button type="button" class="log-filter-btn" data-level="warning" onclick="filterBaakLogs('warning')">Peringatan</button>
                    <button type="button" class="log-filter-btn" data-level="error" onclick="filterBaakLogs('error')">Error</button>
                  </div>
                </div>

                <div class="history-table-viewport">
                  <table class="history-table">
                    <thead>
                      <tr>
                        <th>Waktu</th>
                        <th>Level</th>
                        <th>Aktor</th>
                        <th>Modul</th>
                        <th>Rincian Aktivitas</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody id="baak-system-logs-tbody">
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
        </main>

        <footer class="app-footer">
            <p class="footer-copyright">PENSCEDULER &copy; Geneva 1967</p>
        </footer>
    </div>
</div>

<div class="landing-page-container" id="landing-page-shell" style="display: none;">
  <header class="landing-header">
    <div class="landing-brand">
      <div class="brand-logo-container">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5">
          <rect x="3" y="4" width="18" height="18" rx="3" ry="3"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
          <circle cx="12" cy="15" r="2" fill="currentColor"/>
        </svg>
      </div>
      <span class="landing-brand-text">PENSCEDULER</span>
    </div>
    <div class="landing-nav-links">
      <a href="#solusi" class="landing-link">Fitur Solusi</a>
      <a href="#peran" class="landing-link">Peran Civitas</a>
      <a href="#keunggulan" class="landing-link">Keunggulan CAS</a>
    </div>
    <button type="button" class="btn-primary-action" onclick="enterAppDashboard()">
      Masuk ke Sistem
    </button>
  </header>

  <section class="landing-hero-section">
    <div class="hero-badge-tag">PENS Smart Academic Scheduler</div>
    <h1 class="landing-hero-title">Orkestrator Jadwal & Ruang Kuliah Akademik Cerdas</h1>
    <p class="landing-hero-desc">
      Platform penjadwalan akademik terpadu Politeknik Elektronika Negeri Surabaya. Menjamin nol konflik alokasi ruangan laboratorium, kemudahan pemindahan jadwal kuliah bagi mahasiswa dan dosen, serta pusat kendali administrasi terpusat bagi BAAK.
    </p>
    <div class="landing-cta-group">
      <button type="button" class="btn-primary-action btn-large" onclick="enterAppDashboard()">
        <span>Buka Dashboard Jadwal</span>
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>
    </div>

    <div class="landing-features-grid" id="solusi">
      <div class="feature-card">
        <div class="feature-icon-box">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <h3 class="feature-title">Matriks Ruang 7 Hari Presisi</h3>
        <p class="feature-desc">Visualisasi alokasi ruangan laboratorium dan kelas teori per 1 jam presisi. Memetakan seluruh laboratorium kampus secara real-time tanpa resiko konflik.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-box">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
        </div>
        <h3 class="feature-title">Rekomendasi Pindah Jadwal Otomatis</h3>
        <p class="feature-desc">Algoritma cerdas yang mendeteksi 5 slot waktu kosong terbaik secara instan dan mengonfirmasi ketersediaan ruangan kampus.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-box">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3 class="feature-title">Alur Persetujuan Bertingkat</h3>
        <p class="feature-desc">Permohonan mahasiswa otomatis melalui verifikasi dosen pengampu untuk menjaga ketertiban data akademik kampus.</p>
      </div>
    </div>

    <div class="landing-roles-section" id="peran">
      <h2 class="section-heading-center">Dirancang Khusus untuk Seluruh Civitas PENS</h2>
      <div class="landing-roles-grid">
        <div class="role-highlight-box">
          <span class="role-highlight-title">Mahasiswa</span>
          <p class="role-highlight-desc">Melihat jadwal mingguan, menerima pemberitahuan visual kelas pengganti, mengajukan perpindahan sesi dengan overlay rekomendasi, dan konsultasi interaktif via Chatbot AI.</p>
        </div>
        <div class="role-highlight-box">
          <span class="role-highlight-title">Dosen</span>
          <p class="role-highlight-desc">Mengatur jadwal mengajar, meninjau permohonan mahasiswa dengan aksi approve/reject, auto-approve saat memindahkan jadwal, dan melihat daftar mahasiswa per kelas.</p>
        </div>
        <div class="role-highlight-box">
          <span class="role-highlight-title">BAAK</span>
          <p class="role-highlight-desc">Pusat kendali operasional akademik, impor jadwal via CSV dengan parser interaktif, pemantauan master ruangan, subjek, dosen, dan mahasiswa.</p>
        </div>
      </div>
    </div>
  </section>

  <footer class="landing-footer">
    <p>PENSCEDULER &copy; Geneva 1967</p>
  </footer>
</div>

<x-modals.slot-recommendations />
<x-modals.csv-import />
<x-modals.baak-crud />
@endsection

@push('scripts')
<script>window.PENSCEDULER = {{ Js::from($payload) }};</script>
@endpush
