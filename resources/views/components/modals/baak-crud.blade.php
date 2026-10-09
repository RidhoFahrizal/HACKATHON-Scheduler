<div class="modal-backdrop" id="baak-crud-modal" style="display: none;">
  <div class="modal-box">
    <div class="modal-header">
      <h3 class="modal-title" id="baak-crud-modal-title">Form Data Master</h3>
      <button type="button" class="modal-close-btn" onclick="closeBaakCrudModal()" aria-label="Tutup modal">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form id="baak-crud-form" onsubmit="saveBaakCrudItem(event)">
      <div class="modal-body" id="baak-crud-form-container">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary-action" onclick="closeBaakCrudModal()">Batal</button>
        <button type="submit" class="btn-primary-action">Simpan Data</button>
      </div>
    </form>
  </div>
</div>
