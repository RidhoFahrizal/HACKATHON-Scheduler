<header class="topbar">
  <div class="topbar-left">
    <button type="button" class="topbar-arrow-open-btn" id="topbar-open-arrow-btn" aria-label="Buka Navigasi Samping" title="Buka Menu">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9 18 15 12 9 6"/>
      </svg>
    </button>
    <span class="portal-system-name">PENSCEDULER</span>
  </div>

  <div class="topbar-right">
    <div class="profile-widget-wrapper">
      <button type="button" class="user-profile-widget" id="user-profile-widget" aria-haspopup="true" aria-expanded="false">
        <div class="user-avatar-circle" id="user-avatar-circle">M</div>
        <div class="user-meta-info">
          <span class="user-display-name" id="user-display-name">Mahasiswa Alpha</span>
          <span class="user-role-badge" id="user-role-badge">Mahasiswa</span>
        </div>
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" class="profile-chevron">
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </button>

      <div class="profile-details-popover" id="profile-details-popover">
        <div class="popover-profile-header">
          <div class="popover-avatar" id="popover-avatar">M</div>
          <div>
            <h4 class="popover-name" id="popover-name">Mahasiswa Alpha</h4>
            <span class="popover-role" id="popover-role">Mahasiswa</span>
          </div>
        </div>

        <div class="popover-details-list">
          <div class="popover-detail-row">
            <span class="detail-label" id="popover-id-label">NRP</span>
            <span class="detail-value" id="popover-id-value">3122000001</span>
          </div>
          <div class="popover-detail-row">
            <span class="detail-label">Email</span>
            <span class="detail-value" id="popover-email-value">realdho@it.student.pens.ac.id</span>
          </div>
          <div class="popover-detail-row" id="popover-class-row">
            <span class="detail-label" id="popover-class-label">Kelas</span>
            <span class="detail-value" id="popover-class-value">3 D4 IT A</span>
          </div>
        </div>

        <div class="settings-group-box">
          <span class="settings-group-title" id="popover-lang-title">Bahasa / Language</span>
          <div class="theme-button-group">
            <button type="button" class="btn-theme-pill btn-lang-pill active" id="btn-lang-id" data-lang-val="id" onclick="setAppLanguage('id')">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              <span>Indonesia</span>
            </button>
            <button type="button" class="btn-theme-pill btn-lang-pill" id="btn-lang-en" data-lang-val="en" onclick="setAppLanguage('en')">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              <span>English</span>
            </button>
          </div>
        </div>

        <div class="settings-group-box">
          <span class="settings-group-title" id="popover-theme-title">Mode Tampilan</span>
          <div class="theme-button-group">
            <button type="button" class="btn-theme-pill active" data-theme-val="light" onclick="setAppTheme('light')">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
              <span id="theme-btn-light-text">Terang</span>
            </button>
            <button type="button" class="btn-theme-pill" data-theme-val="dark" onclick="setAppTheme('dark')">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
              <span id="theme-btn-dark-text">Gelap</span>
            </button>
            <button type="button" class="btn-theme-pill" data-theme-val="auto" onclick="setAppTheme('auto')">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              <span id="theme-btn-auto-text">Otomatis</span>
            </button>
          </div>
        </div>

        <div class="settings-group-box">
          <span class="settings-group-title" id="popover-access-title">Aksesibilitas</span>
          <div class="accessibility-toggle-row">
            <span class="access-label" id="access-label-dyslexia">Ramah Disleksia (OpenDyslexic)</span>
            <label class="switch-control">
              <input type="checkbox" id="wcag-dyslexia-toggle" onchange="toggleDyslexiaMode(this.checked)">
              <span class="slider-toggle"></span>
            </label>
          </div>
          <div class="accessibility-toggle-row">
            <span class="access-label" id="access-label-contrast">Mode Kontras Tinggi</span>
            <label class="switch-control">
              <input type="checkbox" id="wcag-contrast-toggle" onchange="toggleHighContrast(this.checked)">
              <span class="slider-toggle"></span>
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<nav class="breadcrumbs-bar" aria-label="Breadcrumb">
  <div class="breadcrumb-container">
    <a href="#" class="breadcrumb-home-link" onclick="navigateToView('dashboard'); return false;">
      <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22"/>
      </svg>
      <span>PENSCEDULER</span>
    </a>
    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" class="breadcrumb-separator"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="breadcrumb-current-pill" id="breadcrumb-current-text">Dashboard</span>
  </div>
</nav>
