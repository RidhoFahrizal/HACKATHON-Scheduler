<div class="modal-backdrop" id="csv-import-modal" style="display: none;">
  <div class="modal-box modal-lg">
    <div class="modal-header">
      <h3 class="modal-title">Impor Master Jadwal Kuliah (CSV)</h3>
      <button type="button" class="modal-close-btn" onclick="closeCsvImportModal()" aria-label="Tutup modal">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <p style="font-size: 12px; color: var(--text-subtle); margin-bottom: 12px;">
        Unggah berkas CSV berformat: <code>kode_mk, nama_mk, dosen_pengampu, hari, jam, ruang</code>.
      </p>

      <div class="file-drop-area" onclick="simulateFileUpload()">
        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="var(--primary-color)" stroke-width="2" style="margin: 0 auto 8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        <span style="font-size: 12px; font-weight: 700; color: var(--text-main);">Pilih Berkas CSV atau Tarik ke Sini</span>
        <span style="font-size: 11px; color: var(--text-subtle);">Maksimum ukuran 10 MB</span>
      </div>

      <div id="csv-preview-box" style="display: none; margin-top: 14px; font-size: 11px; background: var(--bg-surface-alt); padding: 10px; border-radius: var(--radius-sm);">
        <strong>Berkas Terdeteksi:</strong> <code>jadwal_semester_ganjil_2026.csv</code> (3 Baris Contoh Uji Parsing).
      </div>

      <div id="csv-parsed-table-wrapper" style="display: none; margin-top: 14px;">
        <h4 style="font-size: 12px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">
          Konfirmasi Hasil Parsing & Koreksi Langsung:
        </h4>
        <div class="history-table-viewport">
          <table class="history-table">
            <thead>
              <tr>
                <th>Kode</th>
                <th>Nama Matakuliah</th>
                <th>Dosen Pengampu</th>
                <th>Jadwal</th>
                <th>Ruangan</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="csv-parsed-tbody">
            </tbody>
          </table>
        </div>
        <span style="display: block; margin-top: 8px; font-size: 10px; color: var(--text-subtle);">
          * Baris berlatar merah menandakan data kelas atau dosen tidak ditemukan di kurikulum. Anda dapat mengedit langsung pada sel isian di atas sebelum sinkronisasi.
        </span>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-secondary-action" onclick="closeCsvImportModal()">Batal</button>
      <button type="button" class="btn-primary-action" onclick="confirmCsvImport()">Proses Sinkronisasi</button>
    </div>
  </div>
</div>
