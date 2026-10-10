<aside class="sidebar" id="app-sidebar">
  <div class="sidebar-header">
    <a href="#" class="brand-wrapper" onclick="navigateToView('dashboard'); return false;">
      <div class="brand-logo-container">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5">
          <rect x="3" y="4" width="18" height="18" rx="3" ry="3"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
          <circle cx="12" cy="15" r="2" fill="currentColor"/>
        </svg>
      </div>
      <div class="brand-text-block">
        <span class="brand-title">PENSCEDULER</span>
        <span class="brand-sub">Academic Scheduler</span>
      </div>
    </a>

    <button type="button" class="sidebar-arrow-close-btn" id="sidebar-close-arrow-btn" aria-label="Tutup Navigasi Samping" title="Ciutkan Menu">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
    </button>
  </div>

  <div class="role-switcher-box">
    <div class="role-switcher-label">
      <span id="role-switcher-label-text">Ganti Peran</span>
      <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
    </div>

    <button type="button" class="role-select-trigger" id="role-select-trigger" aria-haspopup="listbox" aria-expanded="false">
      <div class="role-trigger-inner">
        <div class="role-avatar-badge" id="role-avatar-badge">R</div>
        <div class="role-option-text">
          <span class="role-option-title" id="current-role-title">Mahasiswa</span>
          <span class="role-option-desc" id="current-role-desc">3 D4 IT A</span>
        </div>
      </div>
      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" class="trigger-chevron">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </button>

    <div class="role-dropdown-menu" id="role-dropdown-menu" role="listbox">
      <div class="role-option-item selected" data-role="mahasiswa" role="option">
        <div class="role-avatar-badge" style="background:#4f46e5;">M</div>
        <div class="role-option-text">
          <span class="role-option-title">Mahasiswa</span>
          <span class="role-option-desc">Mahasiswa Alpha (3122000001)</span>
        </div>
      </div>
      <div class="role-option-item" data-role="dosen" role="option">
        <div class="role-avatar-badge" style="background:#059669;">D</div>
        <div class="role-option-text">
          <span class="role-option-title">Dosen</span>
          <span class="role-option-desc">Dosen Alpha, S.Kom., M.T.</span>
        </div>
      </div>
      <div class="role-option-item" data-role="baak" role="option">
        <div class="role-avatar-badge" style="background:#d97706;">B</div>
        <div class="role-option-text">
          <span class="role-option-title">BAAK</span>
          <span class="role-option-desc">Biro Administrasi Akademik PENS</span>
        </div>
      </div>
    </div>
  </div>

  <nav class="sidebar-nav" id="sidebar-dynamic-nav">
  </nav>

  <div class="sidebar-bottom-action">
    <button type="button" class="btn-logout-sidebar" id="btn-sidebar-logout" onclick="performLogout()" title="Keluar ke Landing Page">
      <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
        <polyline points="16 17 21 12 16 7"/>
        <line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
      <span class="logout-text">Keluar</span>
    </button>
  </div>
</aside>
