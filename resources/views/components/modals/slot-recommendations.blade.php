<div class="modal-backdrop" id="recommendations-overlay-modal" style="display: none;">
  <div class="modal-box modal-lg">
    <div class="modal-header">
      <div>
        <h3 class="modal-title">Rekomendasi Slot Bebas Konflik</h3>
        <span style="font-size: 11px; color: var(--text-subtle);" id="modal-rec-course-title">Workshop Mesin Pembelajaran</span>
      </div>
      <button type="button" class="modal-close-btn" onclick="closeRecommendationOverlay()" aria-label="Tutup rekomendasi">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <p style="font-size: 12px; color: var(--text-subtle); margin-bottom: 14px;">
        Pilih salah satu slot rekomendasi hasil algoritma sistem di bawah ini untuk menerapkan parameter ke formulir pemindahan jadwal:
      </p>
      <div id="recommendation-status" class="process-status" style="display: none;" role="status" aria-live="polite">
        <span class="process-spinner" aria-hidden="true"></span>
        <span id="recommendation-status-text">Menyiapkan perhitungan...</span>
      </div>
      <div class="overlay-rec-grid" id="modal-recommendations-container">
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-secondary-action" onclick="closeRecommendationOverlay()">Tutup</button>
    </div>
  </div>
</div>
