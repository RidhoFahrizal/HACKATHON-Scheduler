<div class="modal-backdrop" id="csv-import-modal" style="display: none;">
  <div class="modal-box modal-lg">
    <div class="modal-header">
      <h3 class="modal-title" id="csv-modal-title">Impor Master Jadwal Kuliah (CSV)</h3>
      <button type="button" class="modal-close-btn" onclick="closeCsvImportModal()" aria-label="Tutup modal">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <p style="font-size: 12px; color: var(--text-subtle); margin-bottom: 8px;" id="csv-modal-description">
        Unggah berkas CSV berformat: <code>kode_mk, nama_mk, dosen_pengampu, hari, jam, ruang</code>.
      </p>
      <p style="font-size: 11px; color: var(--text-subtle); margin-bottom: 12px;">
        Contoh baris: <code id="csv-modal-example">-</code>
      </p>
      <button type="button" class="btn-secondary-action" style="margin-bottom: 12px;" onclick="downloadCsvTemplate()">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        <span>Unduh Templat CSV</span>
      </button>

      <input type="file" id="csv-file-input" accept=".csv,text/csv" style="display: none;">
      <div class="file-drop-area" id="csv-drop-area" onclick="document.getElementById('csv-file-input').click()">
        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="var(--primary-color)" stroke-width="2" style="margin: 0 auto 8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        <span style="font-size: 12px; font-weight: 700; color: var(--text-main);">Pilih Berkas CSV atau Tarik ke Sini</span>
        <span style="font-size: 11px; color: var(--text-subtle);">Maksimum ukuran 5 MB</span>
      </div>
      <button type="button" class="btn-secondary-action" style="margin-top: 8px;" onclick="simulateFileUpload()">Gunakan Contoh Data</button>

      <div id="csv-preview-box" style="display: none; margin-top: 14px; font-size: 11px; background: var(--bg-surface-alt); padding: 10px; border-radius: var(--radius-sm);">
        <strong>Berkas Terdeteksi:</strong> <code id="csv-preview-file">-</code> <span id="csv-preview-count"></span>
      </div>

      <div id="csv-parsed-table-wrapper" style="display: none; margin-top: 14px;">
        <h4 style="font-size: 12px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">
          Konfirmasi Hasil Parsing &amp; Koreksi Langsung:
        </h4>
        <div class="history-table-viewport">
          <table class="history-table">
            <thead>
              <tr id="csv-parsed-thead-row">
              </tr>
            </thead>
            <tbody id="csv-parsed-tbody">
            </tbody>
          </table>
        </div>
        <span style="display: block; margin-top: 8px; font-size: 10px; color: var(--text-subtle);" id="csv-parsed-hint">
          * Baris berlatar merah menandakan data tidak valid. Anda dapat mengedit langsung pada sel isian di atas sebelum sinkronisasi.
        </span>
      </div>

      <div id="csv-import-status" class="process-status" style="display: none;" role="status" aria-live="polite">
        <span class="process-spinner" aria-hidden="true"></span>
        <span id="csv-import-status-text">Memproses...</span>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-secondary-action" onclick="closeCsvImportModal()">Batal</button>
      <button type="button" class="btn-primary-action" id="btn-csv-confirm" onclick="confirmCsvImport()">Proses Sinkronisasi</button>
    </div>
  </div>
</div>
