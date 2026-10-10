const {
  appData: APP_DATA,
  masterRooms: BAAK_MASTER_ROOMS,
  masterSubjects: BAAK_MASTER_SUBJECTS,
  masterLecturers: BAAK_MASTER_LECTURERS,
  masterStudents: BAAK_MASTER_STUDENTS,
  studentRoster: DUMMY_STUDENTS_ROSTER,
  systemLogs: BAAK_SYSTEM_LOGS,
  recommendationSlots: RECOMMENDATION_SLOTS
} = window.PENSCEDULER;
let ALL_RESCHEDULE_REQUESTS = window.PENSCEDULER.rescheduleRequests;
let DATABASE_SCHEDULES = [];
let DATABASE_ROOMS = [];

const I18N_DICTIONARY = {
  id: {
    roleMahasiswa: "Mahasiswa",
    roleDosen: "Dosen",
    roleBaak: "BAAK",
    roleDescMahasiswa: "3 D4 IT A",
    roleDescDosen: "Departemen Teknik Informatika",
    roleDescBaak: "Pusat Pelayanan & Penjadwalan",
    roleOptDescMahasiswa: "Realdho Fahryz (1234567890)",
    roleOptDescDosen: "Dr. Ir. Budi Sxxxx, M.T.",
    roleOptDescBaak: "Biro Administrasi Akademik PENS",
    switchRole: "Ganti Peran",
    signOut: "Keluar",
    classLabel: "Kelas",
    langTitle: "Bahasa / Language",
    themeTitle: "Mode Tampilan",
    themeLight: "Terang",
    themeDark: "Gelap",
    themeAuto: "Otomatis",
    accessTitle: "Aksesibilitas",
    accessDyslexia: "Ramah Disleksia (OpenDyslexic)",
    accessContrast: "Mode Kontras Tinggi",
    navDashboard: "Dashboard",
    navClasses: "Matakuliah",
    navSchedule: "Jadwal",
    navReschedule: "Pindah Jadwal",
    navChat: "Chat AI",
    navRequests: "Permintaan Jadwal",
    navRooms: "Ruangan",
    navSubjects: "Subjek",
    navLecturers: "Dosen",
    navStudents: "Mahasiswa",
    navSystemLogs: "Log & Sistem",
    toastLangSwitched: "Bahasa antarmuka diubah ke Bahasa Indonesia."
  },
  en: {
    roleMahasiswa: "Student",
    roleDosen: "Lecturer",
    roleBaak: "Academic Office",
    roleDescMahasiswa: "3rd Year Informatics Engineering A",
    roleDescDosen: "Informatics Engineering Department",
    roleDescBaak: "Academic Administration Service Center",
    roleOptDescMahasiswa: "Realdho Fahryz (1234567890)",
    roleOptDescDosen: "Dr. Ir. Budi Sxxxx, M.T.",
    roleOptDescBaak: "PENS Academic Administration Bureau",
    switchRole: "Switch Role",
    signOut: "Sign Out",
    classLabel: "Class",
    langTitle: "Language",
    themeTitle: "Appearance",
    themeLight: "Light",
    themeDark: "Dark",
    themeAuto: "Auto",
    accessTitle: "Accessibility",
    accessDyslexia: "Dyslexia Friendly (OpenDyslexic)",
    accessContrast: "High Contrast Mode",
    navDashboard: "Dashboard",
    navClasses: "Courses",
    navSchedule: "Timetable",
    navReschedule: "Reschedule",
    navChat: "AI Assistant",
    navRequests: "Change Requests",
    navRooms: "Rooms",
    navSubjects: "Subjects",
    navLecturers: "Lecturers",
    navStudents: "Students",
    navSystemLogs: "Logs & System",
    toastLangSwitched: "Interface language switched to English."
  }
};

let currentLanguage = localStorage.getItem("penscheduler_lang") || "id";

function initLanguage() {
  const savedLang = localStorage.getItem("penscheduler_lang") || "id";
  setAppLanguage(savedLang, false);
}

function setAppLanguage(langKey, showToastNotification = true) {
  currentLanguage = langKey;
  localStorage.setItem("penscheduler_lang", langKey);
  document.documentElement.setAttribute("lang", langKey);

  document.querySelectorAll(".btn-lang-pill").forEach(btn => {
    btn.classList.toggle("active", btn.dataset.langVal === langKey);
  });

  applyLocalization();
  if (showToastNotification) {
    showToast(I18N_DICTIONARY[langKey].toastLangSwitched);
  }
}

function applyLocalization() {
  const dict = I18N_DICTIONARY[currentLanguage] || I18N_DICTIONARY.id;

  const switchRoleEl = document.getElementById("role-switcher-label-text");
  if (switchRoleEl) switchRoleEl.textContent = dict.switchRole;

  const currentRoleTitleEl = document.getElementById("current-role-title");
  if (currentRoleTitleEl) {
    currentRoleTitleEl.textContent = currentRole === "mahasiswa" ? dict.roleMahasiswa : currentRole === "dosen" ? dict.roleDosen : dict.roleBaak;
  }

  const currentRoleDescEl = document.getElementById("current-role-desc");
  if (currentRoleDescEl) {
    currentRoleDescEl.textContent = currentRole === "mahasiswa" ? dict.roleDescMahasiswa : currentRole === "dosen" ? dict.roleDescDosen : dict.roleDescBaak;
  }

  const userRoleBadge = document.getElementById("user-role-badge");
  if (userRoleBadge) {
    userRoleBadge.textContent = currentRole === "mahasiswa" ? dict.roleMahasiswa : currentRole === "dosen" ? dict.roleDosen : dict.roleBaak;
  }

  const popoverRole = document.getElementById("popover-role");
  if (popoverRole) {
    popoverRole.textContent = currentRole === "mahasiswa" ? dict.roleMahasiswa : currentRole === "dosen" ? dict.roleDosen : dict.roleBaak;
  }

  const classLabel = document.getElementById("popover-class-label");
  if (classLabel) classLabel.textContent = dict.classLabel;

  const langTitle = document.getElementById("popover-lang-title");
  if (langTitle) langTitle.textContent = dict.langTitle;

  const themeTitle = document.getElementById("popover-theme-title");
  if (themeTitle) themeTitle.textContent = dict.themeTitle;

  const lightText = document.getElementById("theme-btn-light-text");
  if (lightText) lightText.textContent = dict.themeLight;

  const darkText = document.getElementById("theme-btn-dark-text");
  if (darkText) darkText.textContent = dict.themeDark;

  const autoText = document.getElementById("theme-btn-auto-text");
  if (autoText) autoText.textContent = dict.themeAuto;

  const accessTitle = document.getElementById("popover-access-title");
  if (accessTitle) accessTitle.textContent = dict.accessTitle;

  const accessDys = document.getElementById("access-label-dyslexia");
  if (accessDys) accessDys.textContent = dict.accessDyslexia;

  const accessCont = document.getElementById("access-label-contrast");
  if (accessCont) accessCont.textContent = dict.accessContrast;

  const logoutText = document.querySelector(".logout-text");
  if (logoutText) logoutText.textContent = dict.signOut;

  document.querySelectorAll(".role-option-item").forEach(item => {
    const role = item.dataset.role;
    const titleEl = item.querySelector(".role-option-title");
    const descEl = item.querySelector(".role-option-desc");
    if (role === "mahasiswa") {
      if (titleEl) titleEl.textContent = dict.roleMahasiswa;
      if (descEl) descEl.textContent = dict.roleOptDescMahasiswa;
    } else if (role === "dosen") {
      if (titleEl) titleEl.textContent = dict.roleDosen;
      if (descEl) descEl.textContent = dict.roleOptDescDosen;
    } else if (role === "baak") {
      if (titleEl) titleEl.textContent = dict.roleBaak;
      if (descEl) descEl.textContent = dict.roleOptDescBaak;
    }
  });

  renderSidebarNavForRole(currentRole);
  updateBreadcrumbText();
}

function updateBreadcrumbText() {
  const dict = I18N_DICTIONARY[currentLanguage] || I18N_DICTIONARY.id;
  const breadcrumbText = document.getElementById("breadcrumb-current-text");
  const breadcrumbLabels = {
    dashboard: dict.navDashboard,
    classes: dict.navClasses,
    "class-detail": currentLanguage === "en" ? "Course Detail" : "Detail Matakuliah",
    schedule: dict.navSchedule,
    reschedule: currentRole === "mahasiswa"
      ? (currentLanguage === "en" ? "Course Reschedule Request" : "Pengajuan Pindah Jadwal Perkuliahan")
      : (currentLanguage === "en" ? "Course Rescheduling" : "Pindah Jadwal Perkuliahan"),
    "baak-requests": currentLanguage === "en" ? "Schedule Requests Ledger" : "Daftar Permintaan Pindah Jadwal",
    "baak-rooms": dict.navRooms,
    "baak-subjects": dict.navSubjects,
    "baak-lecturers": dict.navLecturers,
    "baak-students": dict.navStudents,
    "baak-system-logs": dict.navSystemLogs,
    chat: dict.navChat
  };
  if (breadcrumbText) {
    breadcrumbText.textContent = breadcrumbLabels[activeCurrentView] || dict.navDashboard;
  }
}

let currentRole = window.PENSCEDULER.activeRole;
let activeCurrentView = "dashboard";
let selectedDashboardDay = "Hari Ini";
let activeDetailCourseId = "m-wm";
let currentLecturerFilter = "all";
let currentDayFilter = "all";
let currentSearchQuery = "";
let pendingCsvRows = [];
let currentCrudEntity = null;
let currentCrudEditIndex = null;
let currentLogLevelFilter = "all";
let currentLogSearchQuery = "";

// Weekends map to Monday so the dashboard always has a teaching day to show
function getSimulatedTodayDay() {
  const dayNames = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
  const currentDayIndex = new Date().getDay();
  const dayName = dayNames[currentDayIndex];
  return (dayName === "Sabtu" || dayName === "Minggu") ? "Senin" : dayName;
}

document.addEventListener("DOMContentLoaded", () => {
  initLanguage();
  initThemeAndAccessibility();
  setupSidebarAndNavbarToggles();
  setupRoleSwitcher();
  setupProfilePopover();
  setupDashboardDragCarousel();
  setupClassesViewInteractions();
  setupChatModule();
  setupCsvDropArea();
  setupChatSlotDelegation();
  renderActiveRole(currentRole);
  loadSchedulingCatalog();
  loadRescheduleRequests();
  loadMasterData();
});

async function apiRequest(method, url, body) {
  const token = document.querySelector('meta[name="csrf-token"]');
  let response;
  try {
    response = await fetch(url, {
      method,
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-TOKEN": token ? token.content : ""
      },
      body: body === undefined ? undefined : JSON.stringify(body)
    });
  } catch (networkError) {
    const error = new Error("Server tidak terjangkau.");
    error.offline = true;
    throw error;
  }

  if (response.status === 204) return null;

  const payload = await response.json().catch(() => ({}));
  if (!response.ok) {
    const error = new Error(payload.message || "Permintaan ke server gagal.");
    error.status = response.status;
    error.payload = payload;
    error.offline = response.status >= 500;
    throw error;
  }
  return payload;
}

async function loadSchedulingCatalog() {
  try {
    const response = await fetch("/api/scheduling/catalog", {
      headers: { "Accept": "application/json" }
    });
    if (!response.ok) throw new Error("Katalog jadwal gagal dimuat.");

    const catalog = await response.json();
    DATABASE_ROOMS = Array.isArray(catalog.rooms) ? catalog.rooms : [];
    DATABASE_SCHEDULES = (Array.isArray(catalog.schedules) ? catalog.schedules : []).map(schedule => {
      const start = schedule.start_time || "08:00";
      const end = schedule.end_time || start;
      const durationHours = Math.max(1, Math.round((timeToMinutes(end) - timeToMinutes(start)) / 60));

      return {
        id: schedule.id,
        code: schedule.code || "",
        title: schedule.subject,
        lecturer: schedule.lecturer,
        day: schedule.day,
        time: `${start} - ${end}`,
        startHour: Number(start.split(":")[0]),
        durationHours,
        room: schedule.room,
        roomId: schedule.room_id,
        sks: `${schedule.credits} SKS`
      };
    });

    populateRoomOptions();
    if (currentRole !== "baak") render7Day1HourMatrix();
    populateRescheduleCourseOptions();
  } catch (error) {
    console.error(error);
    showToast("Katalog database tidak tersedia; tampilan contoh tetap bisa digunakan.");
  }
}

function timeToMinutes(value) {
  const [hours, minutes] = value.split(":").map(Number);
  return hours * 60 + minutes;
}

// The Blade view ships the default campus rooms so the form stays usable offline; catalog rooms replace them only when present.
function populateRoomOptions() {
  const select = document.getElementById("reschedule-target-room");
  if (!select || DATABASE_ROOMS.length === 0) return;

  select.innerHTML = DATABASE_ROOMS.map(room => {
    const building = room.building ? ` (${room.building.name}, Lt. ${room.floor})` : "";
    return `<option value="${escapeHtml(room.name)}">${escapeHtml(room.code || room.name)} - ${escapeHtml(room.name)}${escapeHtml(building)}</option>`;
  }).join("");
}

function getSchedulingClass(courseId) {
  return DATABASE_SCHEDULES.find(item => item.id === courseId)
    || APP_DATA[currentRole]?.classes?.find(item => item.id === courseId);
}

function initThemeAndAccessibility() {
  const savedTheme = localStorage.getItem("penscheduler_theme") || "auto";
  setAppTheme(savedTheme);

  const isDyslexia = localStorage.getItem("penscheduler_dyslexia") === "true";
  const isContrast = localStorage.getItem("penscheduler_contrast") === "true";

  if (isDyslexia) {
    document.body.classList.add("dyslexia-mode");
    const el = document.getElementById("wcag-dyslexia-toggle");
    if (el) el.checked = true;
  }
  if (isContrast) {
    document.body.classList.add("high-contrast-mode");
    const el = document.getElementById("wcag-contrast-toggle");
    if (el) el.checked = true;
  }
}

function setAppTheme(themeVal) {
  localStorage.setItem("penscheduler_theme", themeVal);
  const html = document.documentElement;

  if (themeVal === "auto") {
    const isDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
    html.setAttribute("data-theme", isDark ? "dark" : "light");
  } else {
    html.setAttribute("data-theme", themeVal);
  }

  document.querySelectorAll(".btn-theme-pill").forEach(btn => {
    btn.classList.toggle("active", btn.dataset.themeVal === themeVal);
  });
}

function toggleDyslexiaMode(enabled) {
  document.body.classList.toggle("dyslexia-mode", enabled);
  localStorage.setItem("penscheduler_dyslexia", enabled);
  showToast(enabled ? "Mode Ramah Disleksia Aktif" : "Mode Ramah Disleksia Nonaktif");
}

function toggleHighContrast(enabled) {
  document.body.classList.toggle("high-contrast-mode", enabled);
  localStorage.setItem("penscheduler_contrast", enabled);
  showToast(enabled ? "Mode Kontras Tinggi Aktif" : "Mode Kontras Standar Aktif");
}

// Must match the max-width: 768px media query in pensceduler.css
function setupSidebarAndNavbarToggles() {
  const sidebar = document.getElementById("app-sidebar");
  const closeArrowBtn = document.getElementById("sidebar-close-arrow-btn");
  const openArrowBtn = document.getElementById("topbar-open-arrow-btn");

  if (closeArrowBtn) {
    closeArrowBtn.addEventListener("click", () => {
      if (window.innerWidth <= 768) {
        sidebar.classList.remove("mobile-open");
      } else {
        sidebar.classList.add("collapsed");
      }
    });
  }

  if (openArrowBtn) {
    openArrowBtn.addEventListener("click", () => {
      if (window.innerWidth <= 768) {
        sidebar.classList.add("mobile-open");
      } else {
        sidebar.classList.remove("collapsed");
      }
    });
  }

  document.addEventListener("click", (e) => {
    if (window.innerWidth <= 768 && sidebar.classList.contains("mobile-open")) {
      if (!sidebar.contains(e.target) && !openArrowBtn.contains(e.target)) {
        sidebar.classList.remove("mobile-open");
      }
    }
  });
}

// Best-effort: the UI role state is client-side and must not block on a failed request
function persistRole(role) {
  const token = document.querySelector('meta[name="csrf-token"]');
  fetch(window.PENSCEDULER.switchRoleUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json",
      "X-CSRF-TOKEN": token ? token.content : ""
    },
    body: JSON.stringify({ role })
  }).catch(() => {});
}

function setupRoleSwitcher() {
  const trigger = document.getElementById("role-select-trigger");
  const menu = document.getElementById("role-dropdown-menu");
  const items = document.querySelectorAll(".role-option-item");

  if (!trigger || !menu) return;

  trigger.addEventListener("click", (e) => {
    e.stopPropagation();
    menu.classList.toggle("active");
  });

  document.addEventListener("click", () => {
    menu.classList.remove("active");
  });

  items.forEach(item => {
    item.addEventListener("click", () => {
      const selected = item.dataset.role;
      if (selected && selected !== currentRole) {
        currentRole = selected;
        renderActiveRole(currentRole);
        persistRole(selected);
        showToast(`Beralih ke peran ${APP_DATA[selected].roleLabel}: ${APP_DATA[selected].name}`);
      }
      menu.classList.remove("active");
    });
  });
}

function navigateToView(viewName) {
  if (currentRole === "mahasiswa" && viewName === "class-detail") {
    showToast("Halaman detail matakuliah tidak diperuntukkan bagi mahasiswa.");
    return;
  }

  activeCurrentView = viewName;

  document.querySelectorAll(".nav-item-link").forEach(link => {
    link.classList.toggle("active", link.dataset.nav === viewName);
  });

  document.querySelectorAll(".view-panel").forEach(panel => {
    panel.classList.remove("active");
  });

  const targetPanel = document.getElementById(`view-${viewName}`);
  if (targetPanel) {
    targetPanel.classList.add("active");
  }

  updateBreadcrumbText();

  if (window.innerWidth <= 768) {
    document.getElementById("app-sidebar").classList.remove("mobile-open");
  }

  window.scrollTo({ top: 0, behavior: "smooth" });
}

function renderActiveRole(roleKey) {
  const profile = APP_DATA[roleKey];
  if (!profile) return;

  const dict = I18N_DICTIONARY[currentLanguage] || I18N_DICTIONARY.id;
  const localizedRoleLabel = roleKey === "mahasiswa" ? dict.roleMahasiswa : roleKey === "dosen" ? dict.roleDosen : dict.roleBaak;
  const localizedDeptClass = roleKey === "mahasiswa" ? dict.roleDescMahasiswa : roleKey === "dosen" ? dict.roleDescDosen : dict.roleDescBaak;

  document.getElementById("user-display-name").textContent = profile.name;
  document.getElementById("user-role-badge").textContent = localizedRoleLabel;
  document.getElementById("user-avatar-circle").textContent = profile.avatarChar;
  document.getElementById("role-avatar-badge").textContent = profile.avatarChar;
  document.getElementById("current-role-title").textContent = localizedRoleLabel;
  document.getElementById("current-role-desc").textContent = localizedDeptClass;

  document.querySelectorAll(".role-option-item").forEach(item => {
    item.classList.toggle("selected", item.dataset.role === roleKey);
  });

  document.getElementById("popover-avatar").textContent = profile.avatarChar;
  document.getElementById("popover-name").textContent = profile.name;
  document.getElementById("popover-role").textContent = localizedRoleLabel;
  document.getElementById("popover-id-label").textContent = profile.idType;
  document.getElementById("popover-id-value").textContent = profile.idNumber;
  document.getElementById("popover-email-value").textContent = profile.email;

  const classRow = document.getElementById("popover-class-row");
  if (profile.departmentClass && roleKey === "mahasiswa") {
    classRow.style.display = "flex";
    document.getElementById("popover-class-value").textContent = profile.departmentClass;
  } else {
    classRow.style.display = "none";
  }

  renderSidebarNavForRole(roleKey);

  renderDashboardForRole(roleKey);

  renderFullClassesView();

  if (roleKey !== "baak") {
    render7Day1HourMatrix();
  }

  populateRescheduleCourseOptions();

  if (roleKey === "baak") {
    renderBaakRoomsTable();
    renderBaakSubjectsTable();
    renderBaakLecturersTable();
    renderBaakStudentsTable();
    renderBaakFullRequestsTable();
    renderBaakSystemLogs();
  }

  resetChatInterface();

  navigateToView("dashboard");
}

function renderSidebarNavForRole(roleKey) {
  const navContainer = document.getElementById("sidebar-dynamic-nav");
  if (!navContainer) return;
  const dict = I18N_DICTIONARY[currentLanguage] || I18N_DICTIONARY.id;

  if (roleKey === "mahasiswa") {
    navContainer.innerHTML = `
      <a href="#" class="nav-item-link ${activeCurrentView === 'dashboard' ? 'active' : ''}" data-nav="dashboard" onclick="navigateToView('dashboard'); return false;" title="${dict.navDashboard}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span class="nav-text">${dict.navDashboard}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'classes' ? 'active' : ''}" data-nav="classes" onclick="navigateToView('classes'); return false;" title="${dict.navClasses}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        <span class="nav-text">${dict.navClasses}</span>
        <span class="nav-badge" id="nav-badge-classes-count">${APP_DATA.mahasiswa.classes.length}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'schedule' ? 'active' : ''}" data-nav="schedule" onclick="navigateToView('schedule'); return false;" title="${dict.navSchedule}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span class="nav-text">${dict.navSchedule}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'reschedule' ? 'active' : ''}" data-nav="reschedule" onclick="navigateToView('reschedule'); return false;" title="${dict.navReschedule}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
        <span class="nav-text">${dict.navReschedule}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'chat' ? 'active' : ''}" data-nav="chat" onclick="navigateToView('chat'); return false;" title="${dict.navChat}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span class="nav-text">${dict.navChat}</span>
      </a>
    `;
  } else if (roleKey === "dosen") {
    navContainer.innerHTML = `
      <a href="#" class="nav-item-link ${activeCurrentView === 'dashboard' ? 'active' : ''}" data-nav="dashboard" onclick="navigateToView('dashboard'); return false;" title="${dict.navDashboard}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span class="nav-text">${dict.navDashboard}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'classes' ? 'active' : ''}" data-nav="classes" onclick="navigateToView('classes'); return false;" title="${dict.navClasses}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        <span class="nav-text">${dict.navClasses}</span>
        <span class="nav-badge" id="nav-badge-classes-count">${APP_DATA.dosen.classes.length}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'schedule' ? 'active' : ''}" data-nav="schedule" onclick="navigateToView('schedule'); return false;" title="${dict.navSchedule}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span class="nav-text">${dict.navSchedule}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'reschedule' ? 'active' : ''}" data-nav="reschedule" onclick="navigateToView('reschedule'); return false;" title="${dict.navReschedule}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
        <span class="nav-text">${dict.navReschedule}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'chat' ? 'active' : ''}" data-nav="chat" onclick="navigateToView('chat'); return false;" title="${dict.navChat}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span class="nav-text">${dict.navChat}</span>
      </a>
    `;
  } else if (roleKey === "baak") {
    navContainer.innerHTML = `
      <a href="#" class="nav-item-link ${activeCurrentView === 'dashboard' ? 'active' : ''}" data-nav="dashboard" onclick="navigateToView('dashboard'); return false;" title="${dict.navDashboard}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span class="nav-text">${dict.navDashboard}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'baak-requests' ? 'active' : ''}" data-nav="baak-requests" onclick="navigateToView('baak-requests'); return false;" title="${dict.navRequests}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        <span class="nav-text">${dict.navRequests}</span>
        <span class="nav-badge alert">${ALL_RESCHEDULE_REQUESTS.filter(r => r.status.includes('Menunggu')).length}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'classes' ? 'active' : ''}" data-nav="classes" onclick="navigateToView('classes'); return false;" title="${dict.navClasses}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        <span class="nav-text">${dict.navClasses}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'baak-rooms' ? 'active' : ''}" data-nav="baak-rooms" onclick="navigateToView('baak-rooms'); return false;" title="${dict.navRooms}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span class="nav-text">${dict.navRooms}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'baak-subjects' ? 'active' : ''}" data-nav="baak-subjects" onclick="navigateToView('baak-subjects'); return false;" title="${dict.navSubjects}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        <span class="nav-text">${dict.navSubjects}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'baak-lecturers' ? 'active' : ''}" data-nav="baak-lecturers" onclick="navigateToView('baak-lecturers'); return false;" title="${dict.navLecturers}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span class="nav-text">${dict.navLecturers}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'baak-students' ? 'active' : ''}" data-nav="baak-students" onclick="navigateToView('baak-students'); return false;" title="${dict.navStudents}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        <span class="nav-text">${dict.navStudents}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'reschedule' ? 'active' : ''}" data-nav="reschedule" onclick="navigateToView('reschedule'); return false;" title="${dict.navReschedule}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
        <span class="nav-text">${dict.navReschedule}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'chat' ? 'active' : ''}" data-nav="chat" onclick="navigateToView('chat'); return false;" title="${dict.navChat}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span class="nav-text">${dict.navChat}</span>
      </a>
      <a href="#" class="nav-item-link ${activeCurrentView === 'baak-system-logs' ? 'active' : ''}" data-nav="baak-system-logs" onclick="navigateToView('baak-system-logs'); return false;" title="${dict.navSystemLogs}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        <span class="nav-text">${dict.navSystemLogs}</span>
      </a>
    `;
  }
}

function renderDashboardForRole(roleKey) {
  const container = document.getElementById("dashboard-role-content");
  if (!container) return;

  if (roleKey === "baak") {
    const stats = APP_DATA.baak.stats;
    const top10Requests = ALL_RESCHEDULE_REQUESTS.slice(0, 10);

    container.innerHTML = `
      <section class="section-wrapper">
        <div class="baak-summary-header-row">
          <div>
            <h2 class="section-main-title">Ringkasan Platform</h2>
            <span class="section-helper-sub">Metrik utama kapasitas akademik dan pemanfaatan sumber daya kampus</span>
          </div>
          <button type="button" class="btn-primary-action" onclick="openCsvImportModal()">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Impor Jadwal CSV</span>
          </button>
        </div>

        <div class="platform-metrics-grid">
          <div class="platform-stat-card">
            <div class="stat-icon-wrapper">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <div>
              <span class="stat-number">${stats.mahasiswa.toLocaleString('id-ID')}</span>
              <span class="stat-label">Jumlah Mahasiswa</span>
            </div>
          </div>

          <div class="platform-stat-card">
            <div class="stat-icon-wrapper">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
              <span class="stat-number">${stats.dosen.toLocaleString('id-ID')}</span>
              <span class="stat-label">Jumlah Dosen</span>
            </div>
          </div>

          <div class="platform-stat-card">
            <div class="stat-icon-wrapper">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <div>
              <span class="stat-number">${stats.matakuliah.toLocaleString('id-ID')}</span>
              <span class="stat-label">Jumlah Matakuliah</span>
            </div>
          </div>

          <div class="platform-stat-card">
            <div class="stat-icon-wrapper">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            </div>
            <div>
              <span class="stat-number">${stats.ruangan.toLocaleString('id-ID')}</span>
              <span class="stat-label">Jumlah Ruangan</span>
            </div>
          </div>
        </div>
      </section>

      <section class="clean-section-card" style="margin-top: 24px;">
        <div class="clean-section-header">
          <div class="header-title-box">
            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="var(--primary-color)" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
            <h3 class="clean-header-title">Jadwal Kuliah Kampus</h3>
          </div>
          <div class="quick-day-picker">
            <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Hari Ini' ? 'active' : ''}" data-day="Hari Ini" onclick="selectDashboardDay('Hari Ini')">Hari Ini</button>
            <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Senin' ? 'active' : ''}" data-day="Senin" onclick="selectDashboardDay('Senin')">Senin</button>
            <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Selasa' ? 'active' : ''}" data-day="Selasa" onclick="selectDashboardDay('Selasa')">Selasa</button>
            <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Rabu' ? 'active' : ''}" data-day="Rabu" onclick="selectDashboardDay('Rabu')">Rabu</button>
            <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Kamis' ? 'active' : ''}" data-day="Kamis" onclick="selectDashboardDay('Kamis')">Kamis</button>
            <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Jumat' ? 'active' : ''}" data-day="Jumat" onclick="selectDashboardDay('Jumat')">Jumat</button>
          </div>
        </div>

        <div class="clean-schedule-flow" id="dashboard-schedules-container">
        </div>
      </section>

      <section class="clean-section-card" style="margin-top: 24px;">
        <div class="clean-section-header">
          <div class="header-title-box">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="var(--primary-color)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <h3 class="clean-header-title">Permintaan Perubahan Jadwal Kuliah Terkini</h3>
          </div>
          <button type="button" class="btn-header-link" onclick="navigateToView('baak-requests')">
            <span>Lihat Semua Permintaan</span>
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>

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
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              ${top10Requests.map((r, idx) => `
                <tr>
                  <td>${idx + 1}</td>
                  <td><strong>${escapeHtml(r.requesterName)}</strong> <span style="font-size: 10px; color: var(--text-subtle);">(${r.requesterRole})</span></td>
                  <td>${escapeHtml(r.courseTitle)}</td>
                  <td>${escapeHtml(r.lecturerName)}</td>
                  <td>${escapeHtml(r.originalSchedule)}</td>
                  <td><strong>${escapeHtml(r.proposedSchedule)}</strong></td>
                  <td>
                    <span class="status-badge ${r.status === 'Disetujui' ? 'approved' : r.status === 'Ditolak' ? 'rejected' : 'pending'}">
                      ${escapeHtml(r.status)}
                    </span>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </section>
    `;
    return;
  }

  const classes = APP_DATA[roleKey].classes;
  const isMahasiswa = roleKey === "mahasiswa";

  container.innerHTML = `
    ${classes.some(c => c.hasShift) ? `
      <div class="schedule-shift-alert-banner" role="alert">
        <div class="alert-banner-left">
          <span class="alert-pill-tag">Jadwal Kelas Berubah</span>
          <p class="alert-banner-text">
            Terdapat penyesuaian jadwal kuliah aktif. Silakan tinjau kartu matakuliah bertanda amber di bawah.
          </p>
        </div>
      </div>
    ` : ''}

    <section class="section-wrapper">
      <div class="section-header-row">
        <div class="section-heading-group">
          <h2 class="section-main-title">Matakuliah - ${classes.length}</h2>
          <span class="section-helper-sub">Daftar perkuliahan aktif terdaftar semester ini</span>
        </div>
        <a href="#" class="section-action-link" onclick="navigateToView('classes'); return false;">
          <span>Lihat semua</span>
          <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      </div>

      <div class="classes-drag-viewport" id="classes-drag-viewport">
        <div class="classes-drag-track">
          ${classes.map(c => `
            <article class="dashboard-class-card ${c.hasShift ? 'has-schedule-shift' : ''} ${c.hasPendingRequest ? 'has-pending-request' : ''}">
              <div>
                <div class="card-top-row">
                  <h3 class="card-title">${escapeHtml(c.title)}</h3>
                  <span class="card-code">${escapeHtml(c.code)}</span>
                </div>
                ${!isMahasiswa ? '' : `<p class="card-lecturer">${escapeHtml(c.lecturer)}</p>`}

                ${c.hasShift ? `
                  <span class="schedule-shift-highlight-badge">Jadwal Kelas Berubah</span>
                  <div class="schedule-shift-meta-diff">
                    <span class="strikethrough-old">${escapeHtml(c.day)}, ${escapeHtml(c.time)} &bull; ${escapeHtml(c.room)}</span>
                    <span class="highlight-new">${escapeHtml(c.shiftedSchedule)}</span>
                  </div>
                ` : `
                  <div class="card-meta-box">
                    <div class="card-meta-line">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                      <span>${escapeHtml(c.day)}, ${escapeHtml(c.time)}</span>
                    </div>
                    <div class="card-meta-line">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                      <span>${escapeHtml(c.room)}</span>
                    </div>
                  </div>
                `}

                ${c.hasPendingRequest ? `
                  <div class="pending-request-card-banner">
                    <span class="pending-request-title">Permintaan Pindah Jadwal:</span>
                    <span class="pending-request-desc">${escapeHtml(c.pendingRequestDetail.requester)} mengusulkan ke ${escapeHtml(c.pendingRequestDetail.targetDay)} (${escapeHtml(c.pendingRequestDetail.targetTime)})</span>
                  </div>
                ` : ''}
              </div>

              ${isMahasiswa ? '' : `
                <div class="card-bottom-row">
                  <button type="button" class="btn-card-action outline" onclick="openClassDetailView('${c.id}')">
                    Detail Kuliah
                  </button>
                  ${c.hasPendingRequest ? `
                    <div class="card-approve-btn-group">
                      <button type="button" class="btn-card-action primary" onclick="approveShiftRequest('${c.pendingRequestDetail.id}', '${c.id}')" title="Setujui permohonan">Setujui</button>
                      <button type="button" class="btn-card-action outline" onclick="rejectShiftRequest('${c.pendingRequestDetail.id}', '${c.id}')" title="Tolak permohonan">Tolak</button>
                    </div>
                  ` : ''}
                </div>
              `}
            </article>
          `).join('')}
        </div>
      </div>
    </section>

    <section class="clean-section-card" style="margin-top: 24px;">
      <div class="clean-section-header">
        <div class="header-title-box">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="var(--primary-color)" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
          <h3 class="clean-header-title">Jadwal Kuliah</h3>
        </div>
        <div class="quick-day-picker">
          <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Hari Ini' ? 'active' : ''}" data-day="Hari Ini" onclick="selectDashboardDay('Hari Ini')">Hari Ini</button>
          <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Senin' ? 'active' : ''}" data-day="Senin" onclick="selectDashboardDay('Senin')">Senin</button>
          <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Selasa' ? 'active' : ''}" data-day="Selasa" onclick="selectDashboardDay('Selasa')">Selasa</button>
          <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Rabu' ? 'active' : ''}" data-day="Rabu" onclick="selectDashboardDay('Rabu')">Rabu</button>
          <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Kamis' ? 'active' : ''}" data-day="Kamis" onclick="selectDashboardDay('Kamis')">Kamis</button>
          <button type="button" class="quick-day-btn ${selectedDashboardDay === 'Jumat' ? 'active' : ''}" data-day="Jumat" onclick="selectDashboardDay('Jumat')">Jumat</button>
        </div>
      </div>

      <div class="clean-schedule-flow" id="dashboard-schedules-container">
      </div>
    </section>
  `;

  renderDashboardScheduleForDay(selectedDashboardDay);
  setupDashboardDragCarousel();
}

function selectDashboardDay(dayName) {
  selectedDashboardDay = dayName;
  document.querySelectorAll(".quick-day-btn").forEach(btn => {
    btn.classList.toggle("active", btn.dataset.day === dayName);
  });
  renderDashboardScheduleForDay(dayName);
}

function renderDashboardScheduleForDay(dayName) {
  const container = document.getElementById("dashboard-schedules-container");
  if (!container) return;

  const profile = APP_DATA[currentRole];
  if (!profile || !profile.classes) return;

  const actualDay = dayName === "Hari Ini" ? getSimulatedTodayDay() : dayName;
  const matchingClasses = profile.classes.filter(c => c.day === actualDay);

  if (matchingClasses.length === 0) {
    container.innerHTML = `
      <div style="padding: 28px; text-align: center; color: var(--text-subtle); font-size: 12px;">
        Tidak ada jadwal perkuliahan pada hari ${escapeHtml(actualDay)}${dayName === 'Hari Ini' ? ' (Hari Ini)' : ''}.
      </div>
    `;
    return;
  }

  const isMahasiswa = currentRole === "mahasiswa";

  container.innerHTML = matchingClasses.map(c => `
    <div class="clean-schedule-row ${c.hasShift ? 'row-shifted' : ''}" ${isMahasiswa ? '' : `onclick="openClassDetailView('${c.id}')"`} style="${isMahasiswa ? 'cursor: default;' : 'cursor: pointer;'}">
      <div class="clean-schedule-time">${escapeHtml(c.time)}</div>
      <div class="clean-schedule-main">
        <h4 class="clean-schedule-title">${escapeHtml(c.title)}</h4>
        <p class="clean-schedule-sub">${escapeHtml(c.room)} &bull; ${escapeHtml(c.lecturer)}</p>
      </div>
      ${c.hasShift ? `<span class="schedule-shift-highlight-badge" style="font-size: 10px;">Jadwal Berubah</span>` : ''}
      <span class="clean-schedule-sks-badge">${escapeHtml(c.sks)}</span>
    </div>
  `).join("");
}

function setupDashboardDragCarousel() {
  const viewport = document.getElementById("classes-drag-viewport");
  if (!viewport) return;

  let isDown = false;
  let startX;
  let scrollLeft;

  viewport.addEventListener("mousedown", (e) => {
    isDown = true;
    viewport.classList.add("active");
    startX = e.pageX - viewport.offsetLeft;
    scrollLeft = viewport.scrollLeft;
  });

  viewport.addEventListener("mouseleave", () => {
    isDown = false;
    viewport.classList.remove("active");
  });

  viewport.addEventListener("mouseup", () => {
    isDown = false;
    viewport.classList.remove("active");
  });

  viewport.addEventListener("mousemove", (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - viewport.offsetLeft;
    const walk = (x - startX) * 1.5;
    viewport.scrollLeft = scrollLeft - walk;
  });
}

function setupClassesViewInteractions() {
  const searchInput = document.getElementById("classes-search-input");
  const dayFilter = document.getElementById("classes-day-filter");
  const lecturerFilter = document.getElementById("classes-lecturer-filter");

  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      currentSearchQuery = e.target.value.toLowerCase().trim();
      renderFullClassesView();
    });
  }

  if (dayFilter) {
    dayFilter.addEventListener("change", (e) => {
      currentDayFilter = e.target.value;
      renderFullClassesView();
    });
  }

  if (lecturerFilter) {
    lecturerFilter.addEventListener("change", (e) => {
      currentLecturerFilter = e.target.value;
      renderFullClassesView();
    });
  }
}

function renderFullClassesView() {
  const grid = document.getElementById("classes-full-grid");
  const lecturerFilterWrapper = document.getElementById("lecturer-filter-wrapper");
  const lecturerSelect = document.getElementById("classes-lecturer-filter");

  if (!grid) return;

  if (lecturerFilterWrapper) {
    lecturerFilterWrapper.style.display = currentRole === "baak" ? "block" : "none";
  }

  if (currentRole === "baak" && lecturerSelect && lecturerSelect.options.length <= 1) {
    lecturerSelect.innerHTML = `<option value="all">Semua Dosen</option>` +
      BAAK_MASTER_LECTURERS.map(l => `<option value="${escapeHtml(l.name)}">${escapeHtml(l.name)}</option>`).join("");
  }

  const classes = APP_DATA[currentRole].classes;
  const isMahasiswa = currentRole === "mahasiswa";
  const isDosen = currentRole === "dosen";
  const isBaak = currentRole === "baak";

  const filtered = classes.filter(c => {
    const matchSearch = !currentSearchQuery ||
      c.title.toLowerCase().includes(currentSearchQuery) ||
      c.code.toLowerCase().includes(currentSearchQuery) ||
      (c.lecturer && c.lecturer.toLowerCase().includes(currentSearchQuery)) ||
      c.room.toLowerCase().includes(currentSearchQuery);

    const matchDay = currentDayFilter === "all" || c.day === currentDayFilter;
    const matchLecturer = currentRole !== "baak" || currentLecturerFilter === "all" || c.lecturer === currentLecturerFilter;

    return matchSearch && matchDay && matchLecturer;
  });

  if (filtered.length === 0) {
    grid.innerHTML = `
      <div style="grid-column: 1 / -1; padding: 48px; text-align: center; color: var(--text-subtle); background: var(--bg-surface); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
        Tidak ditemukan matakuliah yang sesuai dengan filter pencarian.
      </div>
    `;
    return;
  }

  grid.innerHTML = filtered.map(c => `
    <div class="class-card-item ${c.hasShift ? 'has-schedule-shift' : ''} ${c.hasPendingRequest && isDosen ? 'has-pending-request' : ''}">
      <div>
        <div class="card-top-row">
          <h3 class="card-title">${escapeHtml(c.title)}</h3>
          <span class="card-code">${escapeHtml(c.code)}</span>
        </div>
        ${isDosen ? '' : `<p class="card-lecturer">${escapeHtml(c.lecturer)}</p>`}

        ${c.hasShift ? `
          <span class="schedule-shift-highlight-badge">Jadwal Kelas Berubah</span>
          <div class="schedule-shift-meta-diff">
            <span class="strikethrough-old">${escapeHtml(c.day)}, ${escapeHtml(c.time)} &bull; ${escapeHtml(c.room)}</span>
            <span class="highlight-new">${escapeHtml(c.shiftedSchedule)}</span>
          </div>
        ` : `
          <div class="card-meta-box">
            <div class="card-meta-line">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <span>Jadwal: ${escapeHtml(c.day)}, ${escapeHtml(c.time)}</span>
            </div>
            <div class="card-meta-line">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              <span>Ruangan: ${escapeHtml(c.room)}</span>
            </div>
            <div class="card-meta-line">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/></svg>
              <span>Bobot: ${escapeHtml(c.sks)}</span>
            </div>
          </div>
        `}

        ${c.hasPendingRequest && isDosen ? `
          <div class="pending-request-card-banner">
            <span class="pending-request-title">Permintaan Pindah Jadwal:</span>
            <span class="pending-request-desc">${escapeHtml(c.pendingRequestDetail.requester)} mengajukan perpindahan ke ${escapeHtml(c.pendingRequestDetail.targetDay)} (${escapeHtml(c.pendingRequestDetail.targetTime)}) di ${escapeHtml(c.pendingRequestDetail.targetRoom)}</span>
            <div style="display: flex; gap: 6px; margin-top: 8px;">
              <button type="button" class="btn-card-action primary" onclick="approveShiftRequest('${c.pendingRequestDetail.id}', '${c.id}')">Setujui Permintaan</button>
              <button type="button" class="btn-card-action outline" onclick="rejectShiftRequest('${c.pendingRequestDetail.id}', '${c.id}')">Tolak</button>
            </div>
          </div>
        ` : ''}
      </div>

      ${isMahasiswa ? '' : `
        <div class="card-detailed-actions">
          <button type="button" class="btn-card-action outline" onclick="openClassDetailView('${c.id}')">
            Detail Kuliah
          </button>
        </div>
      `}
    </div>
  `).join("");
}

function mapApiRequestToLedger(request) {
  return {
    id: request.id,
    uuid: request.uuid,
    courseId: request.courseId,
    courseTitle: request.courseTitle,
    lecturerName: request.lecturerName,
    requesterRole: request.requesterRole,
    requesterName: request.requesterName,
    originalSchedule: request.originalSchedule,
    proposedSchedule: request.proposedSchedule,
    reason: request.reason,
    status: request.status,
    submittedAt: request.submittedAt
  };
}

async function loadRescheduleRequests() {
  try {
    const result = await apiRequest("GET", "/api/v1/reschedule-requests");
    const remote = (result.data || []).map(mapApiRequestToLedger);
    if (remote.length === 0) return;

    const remoteIds = new Set(remote.map(item => item.id));
    ALL_RESCHEDULE_REQUESTS.splice(
      0,
      ALL_RESCHEDULE_REQUESTS.length,
      ...remote,
      ...ALL_RESCHEDULE_REQUESTS.filter(item => !remoteIds.has(item.id))
    );
    if (currentRole === "baak") renderBaakFullRequestsTable();
  } catch (error) {
    console.error(error);
  }
}

function findPendingRequestUuid(requestId) {
  return ALL_RESCHEDULE_REQUESTS.find(item => item.id === requestId)?.uuid || null;
}

function reviewRequestRemotely(action, requestId, reviewNotes) {
  const uuid = findPendingRequestUuid(requestId);
  if (!uuid) return Promise.resolve(null);
  return apiRequest("POST", `/api/v1/reschedule-requests/${uuid}/${action}`, reviewNotes ? { reviewNotes } : {});
}

function approveShiftRequest(requestId, classId) {
  const applyLocally = () => {
    const targetClass = APP_DATA.dosen.classes.find(c => c.id === classId);
    if (targetClass) {
      targetClass.hasPendingRequest = false;
      targetClass.hasShift = true;
      targetClass.shiftedSchedule = `${targetClass.pendingRequestDetail.targetDay}, ${targetClass.pendingRequestDetail.targetTime} di ${targetClass.pendingRequestDetail.targetRoom} (Disetujui)`;
    }
    const req = ALL_RESCHEDULE_REQUESTS.find(r => r.id === requestId);
    if (req) req.status = "Disetujui";
    renderActiveRole("dosen");
    showToast("Permintaan perpindahan jadwal telah DISETUJUI.");
  };

  showToast("Memeriksa bentrok dan menerapkan perubahan jadwal...");
  reviewRequestRemotely("approve", requestId)
    .then(applyLocally)
    .catch(error => {
      if (error.offline) {
        applyLocally();
        return;
      }
      const detail = Array.isArray(error.payload?.conflicts) && error.payload.conflicts.length > 0
        ? ` ${error.payload.conflicts.join(" ")}`
        : "";
      showToast(`${error.message}${detail}`);
    });
}

function rejectShiftRequest(requestId, classId) {
  const applyLocally = () => {
    const targetClass = APP_DATA.dosen.classes.find(c => c.id === classId);
    if (targetClass) targetClass.hasPendingRequest = false;
    const req = ALL_RESCHEDULE_REQUESTS.find(r => r.id === requestId);
    if (req) req.status = "Ditolak";
    renderActiveRole("dosen");
    showToast("Permintaan perpindahan jadwal DITOLAK.");
  };

  reviewRequestRemotely("reject", requestId, "Ditolak oleh dosen pengampu.")
    .then(applyLocally)
    .catch(error => {
      if (error.offline) {
        applyLocally();
        return;
      }
      showToast(error.message);
    });
}

function openClassDetailView(classId) {
  if (currentRole === "mahasiswa") {
    showToast("Halaman detail matakuliah tidak diperuntukkan bagi mahasiswa.");
    return;
  }

  activeDetailCourseId = classId;
  const profile = APP_DATA[currentRole];
  const c = profile.classes.find(item => item.id === classId) || profile.classes[0];
  if (!c) return;

  const isDosen = currentRole === "dosen";
  const isBaak = currentRole === "baak";

  document.getElementById("detail-course-title").textContent = c.title;
  document.getElementById("detail-course-subtitle").textContent = `${c.code} \u2022 ${c.sks} \u2022 ${c.room}`;

  const container = document.getElementById("class-detail-content-area");
  if (!container) return;

  container.innerHTML = `
    ${c.hasShift ? `
      <div class="schedule-shift-alert-banner">
        <div class="alert-banner-left">
          <span class="alert-pill-tag">Kelas Pengganti Aktif</span>
          <p class="alert-banner-text">
            Sesi pekan ini telah dialihkan ke <strong>${escapeHtml(c.shiftedSchedule)}</strong>.
          </p>
        </div>
      </div>
    ` : ''}

    ${c.hasPendingRequest && isDosen ? `
      <div class="pending-request-card-banner" style="margin-bottom: 16px;">
        <span class="pending-request-title">Permintaan Persetujuan Pindah Jadwal:</span>
        <span class="pending-request-desc">${escapeHtml(c.pendingRequestDetail.requester)} mengajukan pemindahan ke <strong>${escapeHtml(c.pendingRequestDetail.targetDay)} (${escapeHtml(c.pendingRequestDetail.targetTime)})</strong> di <strong>${escapeHtml(c.pendingRequestDetail.targetRoom)}</strong> dengan alasan: "${escapeHtml(c.pendingRequestDetail.reason)}"</span>
        <div style="display: flex; gap: 8px; margin-top: 10px;">
          <button type="button" class="btn-primary-action" onclick="approveShiftRequest('${c.pendingRequestDetail.id}', '${c.id}')">Setujui Permintaan</button>
          <button type="button" class="btn-card-action outline" onclick="rejectShiftRequest('${c.pendingRequestDetail.id}', '${c.id}')">Tolak Permintaan</button>
        </div>
      </div>
    ` : ''}

    <div class="detail-summary-strip">
      <div class="detail-metric-card">
        <span class="detail-metric-title">Jadwal Perkuliahan</span>
        <span class="detail-metric-value">${escapeHtml(c.day)}, ${escapeHtml(c.time)}</span>
      </div>
      <div class="detail-metric-card">
        <span class="detail-metric-title">Alokasi Ruangan</span>
        <span class="detail-metric-value">${escapeHtml(c.room)}</span>
      </div>
      ${isBaak ? `
        <div class="detail-metric-card">
          <span class="detail-metric-title">Dosen Pengampu</span>
          <span class="detail-metric-value">${escapeHtml(c.lecturer)}</span>
        </div>
      ` : ''}
      <div class="detail-metric-card">
        <span class="detail-metric-title">Total Mahasiswa Terdaftar</span>
        <span class="detail-metric-value">${DUMMY_STUDENTS_ROSTER.length} Mahasiswa</span>
      </div>
    </div>

    <div class="detail-grid-sections">
      <div class="clean-section-card">
        <div class="clean-section-header">
          <h3 class="clean-header-title">Rencana Perkuliahan 16 Pekan (RPS)</h3>
        </div>
        <div style="padding: 12px; overflow-x: auto;">
          <table class="syllabus-timeline-table">
            <thead>
              <tr>
                <th>Pekan</th>
                <th>Materi Pokok Bahasan</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr><td>Pekan 1 - 2</td><td>Pengenalan Konsep & Arsitektur Sistem</td><td><span class="status-badge approved">Selesai</span></td></tr>
              <tr><td>Pekan 3 - 5</td><td>Implementasi Model Dasar & Preprocessing</td><td><span class="status-badge approved">Selesai</span></td></tr>
              <tr><td>Pekan 6 - 7</td><td>Algoritma Optimasi & Pipeline Evaluasi</td><td><span class="status-badge approved">Selesai</span></td></tr>
              <tr class="active-week-highlight-row">
                <td><strong>Pekan 8</strong></td>
                <td><strong>Transfer Learning & Deep Neural Network</strong></td>
                <td><span class="status-badge pending">Sedang Berlangsung</span></td>
              </tr>
              <tr><td>Pekan 9</td><td>Evaluasi Tengah Semester (ETS)</td><td><span class="status-badge">Mendatang</span></td></tr>
              <tr><td>Pekan 10 - 12</td><td>Model Deployment & Servicing API</td><td><span class="status-badge">Mendatang</span></td></tr>
              <tr><td>Pekan 13 - 15</td><td>Pengujian Performa & Fine-Tuning</td><td><span class="status-badge">Mendatang</span></td></tr>
              <tr><td>Pekan 16</td><td>Evaluasi Akhir Semester (EAS)</td><td><span class="status-badge">Mendatang</span></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="clean-section-card">
        <div class="clean-section-header">
          <h3 class="clean-header-title">Daftar Mahasiswa Terdaftar</h3>
          <span style="font-size: 11px; color: var(--text-subtle);">${DUMMY_STUDENTS_ROSTER.length} Orang</span>
        </div>
        <div style="padding: 12px; max-height: 420px; overflow-y: auto;">
          <table class="history-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Mahasiswa</th>
                <th>NRP</th>
              </tr>
            </thead>
            <tbody>
              ${DUMMY_STUDENTS_ROSTER.map(s => `
                <tr>
                  <td>${s.no}</td>
                  <td><strong>${escapeHtml(s.name)}</strong></td>
                  <td><code>${escapeHtml(s.nrp)}</code></td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  `;

  navigateToView("class-detail");
}

function render7Day1HourMatrix() {
  const daysHeaderRow = document.getElementById("matrix-header-days-row");
  const hoursHeaderRow = document.getElementById("matrix-header-hours-row");
  const tbody = document.getElementById("matrix-body-rooms");

  if (!daysHeaderRow || !hoursHeaderRow || !tbody) return;

  const dayNames = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"];
  const monday = new Date();
  monday.setDate(monday.getDate() - ((monday.getDay() + 6) % 7));
  const weekDays = dayNames.map((name, index) => {
    const date = new Date(monday);
    date.setDate(monday.getDate() + index);
    return { name, date: new Intl.DateTimeFormat("id-ID", { day: "2-digit", month: "short" }).format(date) };
  });

  const hours = [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17];
  const sampleRooms = [
    "Lab C 102",
    "Lab C 103",
    "Lab C 104",
    "Lab C 105",
    "SAW-06.10",
    "Lab Software SAW-08",
    "SAW-05.02",
    "Lab Sinyal B 204",
    "B-101",
    "D4-201"
  ];
  const campusRooms = DATABASE_ROOMS.length > 0
    ? DATABASE_ROOMS.map(room => room.name)
    : sampleRooms;

  let daysHtml = '<th class="room-col-header" rowspan="2">Ruangan</th>';
  weekDays.forEach(day => {
    daysHtml += `<th class="matrix-day-th" colspan="${hours.length}">${day.name}, ${day.date}</th>`;
  });
  daysHeaderRow.innerHTML = daysHtml;

  let hoursHtml = "";
  weekDays.forEach(() => {
    hours.forEach(h => {
      const hStr = h < 10 ? `0${h}:00` : `${h}:00`;
      hoursHtml += `<th class="matrix-hour-th">${hStr}</th>`;
    });
  });
  hoursHeaderRow.innerHTML = hoursHtml;

  const classes = DATABASE_SCHEDULES.length > 0
    ? DATABASE_SCHEDULES
    : (APP_DATA[currentRole].classes || []);
  const isMahasiswa = currentRole === "mahasiswa";
  let bodyHtml = "";

  campusRooms.forEach(roomName => {
    bodyHtml += `<tr><td class="room-col-cell">${escapeHtml(roomName)}</td>`;

    weekDays.forEach(day => {
      let skipHours = 0;

      hours.forEach(h => {
        if (skipHours > 0) {
          skipHours--;
          return;
        }

        const matched = classes.find(c => {
          const roomMatch = c.room.includes(roomName) || roomName.includes(c.room.replace("Lab Software ", ""));
          const dayMatch = c.day === day.name;
          const hourMatch = c.startHour === h;
          return roomMatch && dayMatch && hourMatch;
        });

        if (matched) {
          const colSpan = matched.durationHours || 2;
          skipHours = colSpan - 1;

          const isShifted = matched.hasShift;
          const isRequested = matched.hasPendingRequest;
          const blockClass = isShifted ? 'matrix-slot-block shifted' : isRequested ? 'matrix-slot-block requested' : 'matrix-slot-block';

          bodyHtml += `
            <td class="matrix-slot-cell" colspan="${colSpan}">
              <span class="${blockClass}" ${isMahasiswa ? 'style="cursor: default;"' : `onclick="openClassDetailView('${matched.id}')"`} title="${isMahasiswa ? matched.title : 'Buka detail kelas'}">
                ${escapeHtml(matched.code)} - ${escapeHtml(matched.title.substring(0, 16))} (${matched.time})
                ${isShifted ? ' [Berubah]' : isRequested ? ' [Permintaan]' : ''}
              </span>
            </td>
          `;
        } else {
          bodyHtml += `<td class="matrix-slot-cell"></td>`;
        }
      });
    });

    bodyHtml += `</tr>`;
  });

  tbody.innerHTML = bodyHtml;
}

function populateRescheduleCourseOptions() {
  const select = document.getElementById("reschedule-course-select");
  const titleEl = document.getElementById("reschedule-page-title");
  if (!select) return;

  const isMahasiswa = currentRole === "mahasiswa";
  const isBaak = currentRole === "baak";

  if (titleEl) {
    titleEl.textContent = isMahasiswa ? "Pengajuan Pindah Jadwal Perkuliahan" : "Pindah Jadwal Perkuliahan";
  }

  const classes = DATABASE_SCHEDULES.length > 0
    ? DATABASE_SCHEDULES
    : (APP_DATA[currentRole].classes || []);

  select.innerHTML = `<option value="">-- Pilih Matakuliah --</option>` +
    classes.map(c => {
      const prefix = isBaak ? `[${c.lecturer}] ` : "";
      return `<option value="${c.id}">${escapeHtml(prefix)}${escapeHtml(c.title)} (${escapeHtml(c.day)}, ${escapeHtml(c.time)} &bull; ${escapeHtml(c.room)})</option>`;
    }).join("");

  onRescheduleCourseChange("");
}

function onRescheduleCourseChange(courseId) {
  const fieldset = document.getElementById("reschedule-parameters-fieldset");
  const btnRec = document.getElementById("btn-request-recommendation");
  const btnSubmit = document.getElementById("btn-submit-reschedule");
  const infoBox = document.getElementById("current-schedule-info-box");

  if (!courseId) {
    if (fieldset) fieldset.disabled = true;
    if (btnRec) btnRec.disabled = true;
    if (btnSubmit) btnSubmit.disabled = true;
    if (infoBox) infoBox.style.display = "none";
    return;
  }

  const c = getSchedulingClass(courseId);

  if (c && infoBox) {
    infoBox.style.display = "block";
    document.getElementById("current-course-name").textContent = c.title;
    document.getElementById("current-course-schedule").textContent = `${c.day}, ${c.time} \u2022 ${c.room} (${c.sks})`;
  }

  if (fieldset) fieldset.disabled = false;
  if (btnRec) btnRec.disabled = false;
  if (btnSubmit) btnSubmit.disabled = false;
}

function renderMockRecommendationSlots(container) {
  container.innerHTML = RECOMMENDATION_SLOTS.map(s => `
    <div class="overlay-rec-card" onclick="applyRecommendationSlot('${s.day}', '${s.time}', '${s.room}')">
      <div class="overlay-rec-header">
        <span class="overlay-rec-day">${escapeHtml(s.day)}</span>
        <span class="overlay-rec-badge">${escapeHtml(s.note)}</span>
      </div>
      <div class="overlay-rec-time">${escapeHtml(s.time)}</div>
      <div class="overlay-rec-room">Ruangan: <strong>${escapeHtml(s.room)}</strong></div>
      <button type="button" class="btn-card-action primary" style="width: 100%; margin-top: 10px;">
        Pilih Slot Ini &rarr;
      </button>
    </div>
  `).join("");
}

function setRecommendationStatus(state, text) {
  const box = document.getElementById("recommendation-status");
  const label = document.getElementById("recommendation-status-text");
  if (!box || !label) return;

  if (!state) {
    box.style.display = "none";
    return;
  }

  box.style.display = "flex";
  box.classList.toggle("done", state === "done");
  box.classList.toggle("failed", state === "failed");
  label.textContent = text;
}

function runRecommendationEngine(container, scheduleId, scope, targetDate) {
  container.innerHTML = "";
  setRecommendationStatus("busy", "Algoritma bitmask menyusun jadwal dosen dan mahasiswa...");

  const engineStages = [
    "Algoritma bitmask menyusun jadwal dosen dan mahasiswa...",
    "Mencari jendela slot kosong berurutan...",
    "Menyaring ruang berdasarkan kapasitas dan bentrok...",
    "Menghitung skor tiap kandidat..."
  ];
  let stage = 0;
  const timer = window.setInterval(() => {
    stage = Math.min(stage + 1, engineStages.length - 1);
    setRecommendationStatus("busy", engineStages[stage]);
  }, 1200);

  return apiRequest("POST", "/api/scheduling/evaluate", { scheduleId, scope, target: targetDate })
    .finally(() => window.clearInterval(timer));
}

function renderEngineOptions(container, options) {
  container.innerHTML = options.map((option, index) => `
    <div class="overlay-rec-card">
      <div class="overlay-rec-header">
        <span class="overlay-rec-day">${escapeHtml(option.day)}</span>
        <span class="overlay-rec-badge">Skor ${Number(option.score)}</span>
      </div>
      <div class="overlay-rec-time">${escapeHtml(option.start_time)} - ${escapeHtml(option.end_time)}</div>
      <div class="overlay-rec-room">Ruangan: <strong>${escapeHtml(option.room_name)}</strong></div>
      <button type="button" class="btn-card-action primary apply-engine-option" data-option-index="${index}" style="width: 100%; margin-top: 10px;">
        Pilih Slot Ini &rarr;
      </button>
    </div>
  `).join("");

  container.querySelectorAll(".apply-engine-option").forEach(button => {
    button.addEventListener("click", () => {
      const option = options[Number(button.dataset.optionIndex)];
      const time = `${option.start_time} - ${option.end_time}`;
      const timeSelect = document.getElementById("reschedule-target-time");
      if (timeSelect && ![...timeSelect.options].some(item => item.value === time)) {
        timeSelect.add(new Option(time, time));
      }
      applyRecommendationSlot(option.day, time, option.room_name);
    });
  });
}

function requestSystemRecommendations() {
  const modal = document.getElementById("recommendations-overlay-modal");
  const container = document.getElementById("modal-recommendations-container");
  if (!modal || !container) return;

  const courseSelect = document.getElementById("reschedule-course-select");
  const scheduleId = courseSelect?.value;
  const selectedText = scheduleId ? courseSelect.options[courseSelect.selectedIndex].text : "Matakuliah";
  const useEngine = DATABASE_SCHEDULES.some(item => item.id === scheduleId);

  const targetDate = document.getElementById("reschedule-target-date")?.value;
  const scope = document.getElementById("reschedule-scope-select")?.value;
  if (useEngine && !targetDate) {
    showToast("Tanggal mulai berlaku wajib diisi.");
    return;
  }

  document.getElementById("modal-rec-course-title").textContent = useEngine
    ? getSchedulingClass(scheduleId).title
    : selectedText.split("(")[0].trim();
  modal.style.display = "flex";

  if (!useEngine) {
    setRecommendationStatus(null);
    renderMockRecommendationSlots(container);
    return;
  }

  runRecommendationEngine(container, scheduleId, scope, targetDate)
    .then(result => {
      const options = Array.isArray(result.options) ? result.options : [];
      if (options.length === 0) {
        setRecommendationStatus("failed", "Tidak ditemukan slot yang memenuhi aturan engine.");
        return;
      }
      setRecommendationStatus("done", `Algoritma selesai: ${options.length} opsi bebas konflik ditemukan.`);
      renderEngineOptions(container, options);
    })
    .catch(error => {
      console.error(error);
      setRecommendationStatus("failed", "Engine tidak tersedia; menampilkan slot contoh.");
      renderMockRecommendationSlots(container);
    });
}

function closeRecommendationOverlay() {
  const modal = document.getElementById("recommendations-overlay-modal");
  if (modal) modal.style.display = "none";
}

function applyRecommendationSlot(day, time, room) {
  const daySelect = document.getElementById("reschedule-target-day");
  const timeSelect = document.getElementById("reschedule-target-time");
  const roomSelect = document.getElementById("reschedule-target-room");

  if (daySelect) daySelect.value = day;
  if (timeSelect) timeSelect.value = time;
  if (roomSelect) roomSelect.value = room;

  closeRecommendationOverlay();
  showToast(`Slot rekomendasi ${day} (${time} di ${room}) diterapkan ke formulir.`);
}

const DURATION_CODES = {
  "1 Pekan (Sesi Pengganti)": "1_minggu",
  "2 Pekan": "2_minggu",
  "3 Pekan": "3_minggu",
  "4 Pekan": "4_minggu",
  "Permanen (Sisa Semester)": "permanen"
};

function submitRescheduleRequest() {
  const courseSelect = document.getElementById("reschedule-course-select");
  const targetDay = document.getElementById("reschedule-target-day");
  const targetTime = document.getElementById("reschedule-target-time");
  const targetRoom = document.getElementById("reschedule-target-room");
  const durationSelect = document.getElementById("reschedule-duration-select");
  const submitButton = document.getElementById("btn-submit-reschedule");

  if (!courseSelect || !courseSelect.value) {
    showToast("Silakan pilih matakuliah terlebih dahulu.");
    return;
  }

  const courseTitle = courseSelect.options[courseSelect.selectedIndex].text.split("(")[0].trim();
  const isMahasiswa = currentRole === "mahasiswa";
  const isDosen = currentRole === "dosen";
  const scheduleId = courseSelect.value;
  const persisted = DATABASE_SCHEDULES.some(item => item.id === scheduleId);

  const applyLocally = () => {
    const newRequest = {
      id: `REQ-${Date.now().toString().slice(-4)}`,
      courseId: scheduleId,
      courseTitle: courseTitle,
      lecturerName: isDosen ? APP_DATA.dosen.name : isMahasiswa ? "Dr. Ir. Budi Sxxxx, M.T." : "BAAK",
      requesterRole: APP_DATA[currentRole].roleLabel,
      requesterName: APP_DATA[currentRole].name,
      originalSchedule: "Jadwal Reguler",
      proposedSchedule: `${targetDay.value}, ${targetTime.value} (${targetRoom.value})`,
      reason: `Perpindahan sesi perkuliahan (${durationSelect.value})`,
      status: isMahasiswa ? "Menunggu Persetujuan Dosen" : "Disetujui",
      submittedAt: "09 Okt 2026 14:30"
    };

    ALL_RESCHEDULE_REQUESTS.unshift(newRequest);

    if (!isMahasiswa) {
      const c = getSchedulingClass(scheduleId);
      if (c) {
        c.hasShift = true;
        c.shiftedSchedule = `${targetDay.value}, ${targetTime.value} di ${targetRoom.value} (${durationSelect.value})`;
      }
    }

    renderActiveRole(currentRole);
    showToast(isMahasiswa ? "Permohonan berhasil diajukan. Menunggu persetujuan dosen." : "Jadwal perkuliahan berhasil diperbarui (Otomatis Disetujui).");
  };

  if (!persisted) {
    applyLocally();
    return;
  }

  const [startTime, endTime] = targetTime.value.split(" - ");
  const targetDate = document.getElementById("reschedule-target-date")?.value;
  if (!targetDate) {
    showToast("Tanggal mulai berlaku wajib diisi.");
    return;
  }

  if (submitButton) submitButton.disabled = true;
  showToast("Mengirim pengajuan dan memeriksa bentrok jadwal...");

  apiRequest("POST", "/api/v1/reschedule-requests", {
    scheduleId,
    targetDate,
    targetDay: targetDay.value,
    targetStartTime: startTime,
    targetEndTime: endTime,
    targetRoomName: targetRoom.value,
    durationType: DURATION_CODES[durationSelect.value] || "1_minggu",
    reason: `Perpindahan sesi perkuliahan (${durationSelect.value})`
  })
    .then(result => {
      const created = mapApiRequestToLedger(result.data || result);
      ALL_RESCHEDULE_REQUESTS.unshift(created);
      renderActiveRole(currentRole);
      loadSchedulingCatalog();
      showToast(isMahasiswa ? "Permohonan berhasil diajukan. Menunggu persetujuan dosen." : "Jadwal perkuliahan berhasil diperbarui (Otomatis Disetujui).");
    })
    .catch(error => {
      if (error.offline) {
        applyLocally();
        return;
      }
      const detail = Array.isArray(error.payload?.conflicts) && error.payload.conflicts.length > 0
        ? ` ${error.payload.conflicts.join(" ")}`
        : "";
      showToast(`${error.message}${detail}`);
    })
    .finally(() => {
      if (submitButton) submitButton.disabled = false;
    });
}

function setupChatSlotDelegation() {
  const stream = document.getElementById("chat-messages-stream");
  if (!stream) return;

  stream.addEventListener("click", event => {
    const button = event.target.closest(".chat-apply-slot");
    if (!button) return;
    applyChatSlotToReschedule(button.dataset.courseId, button.dataset.day, button.dataset.time, button.dataset.room);
  });
}

function setupChatModule() {
  const sendBtn = document.getElementById("btn-chat-send");
  const chatInput = document.getElementById("chat-user-input");

  if (sendBtn && chatInput) {
    sendBtn.addEventListener("click", () => handleUserChatMessage());
    chatInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        handleUserChatMessage();
      }
    });
  }
}

function resetChatInterface() {
  const stream = document.getElementById("chat-messages-stream");
  const templatesContainer = document.getElementById("chat-templates-container");
  if (!stream || !templatesContainer) return;

  const isMahasiswa = currentRole === "mahasiswa";
  const isDosen = currentRole === "dosen";
  const isBaak = currentRole === "baak";

  let templates = [];
  if (isMahasiswa) {
    templates = [
      { id: "tmpl-schedule-today", label: "Apa jadwal saya hari ini?" },
      { id: "tmpl-my-courses", label: "List mata kuliah saya" },
      { id: "tmpl-reschedule-rec", label: "Cari rekomendasi jadwal kosong untuk pindah jadwal" }
    ];
  } else if (isDosen) {
    templates = [
      { id: "tmpl-schedule-today", label: "Apa jadwal mengajar saya hari ini?" },
      { id: "tmpl-my-courses", label: "Daftar kelas yang saya ampu" },
      { id: "tmpl-reschedule-rec", label: "Cari slot kosong untuk memindahkan perkuliahan" }
    ];
  } else if (isBaak) {
    templates = [
      { id: "tmpl-baak-rooms", label: "Status okupansi ruangan kampus hari ini" },
      { id: "tmpl-baak-util", label: "Ringkasan pemanfaatan jadwal mingguan" },
      { id: "tmpl-reschedule-rec", label: "Cari slot ruangan kosong untuk perubahan jadwal" }
    ];
  }

  templatesContainer.innerHTML = templates.map(t => `
    <button type="button" class="chat-prompt-pill" onclick="triggerChatTemplate('${t.id}')">
      ${escapeHtml(t.label)}
    </button>
  `).join("");

  const welcomeText = isMahasiswa
    ? `Halo Realdho Fahryz. Saya Asisten AI Akademik PENSCEDULER. Anda dapat menanyakan jadwal hari ini, daftar matakuliah, atau mencari rekomendasi slot kosong untuk pindah jadwal.`
    : isDosen
    ? `Selamat datang Dr. Ir. Budi Sxxxx, M.T. Saya siap membantu memeriksa jadwal mengajar, daftar kelas, serta mencarikan slot kosong bebas konflik untuk memindahkan jadwal perkuliahan.`
    : `Halo Tim BAAK. Saya siap membantu memantau ketersediaan ruangan kampus, rekapitulasi jadwal, dan rekomendasi slot perpindahan perkuliahan.`;

  stream.innerHTML = `
    <div class="chat-bubble ai">
      <div class="chat-bubble-avatar">AI</div>
      <div class="chat-bubble-content">
        <p>${escapeHtml(welcomeText)}</p>
      </div>
    </div>
  `;
}

function triggerChatTemplate(templateId) {
  if (templateId === "tmpl-schedule-today") {
    appendUserChatMessage("Apa jadwal saya hari ini?");
    setTimeout(() => {
      const classes = APP_DATA[currentRole].classes || [];
      const todayClasses = classes.filter(c => c.day === "Senin");
      if (todayClasses.length === 0) {
        appendAiChatMessage("Tidak ada jadwal perkuliahan pada hari ini (Senin). Anda dapat memeriksa hari lain melalui menu Jadwal.");
      } else {
        const text = `Berikut adalah jadwal perkuliahan Anda untuk hari ini (Senin):<br><br>` +
          todayClasses.map(c => `&bull; <strong>${escapeHtml(c.title)}</strong> (${c.time}) di <strong>${escapeHtml(c.room)}</strong>`).join("<br>");
        appendAiChatMessage(text);
      }
    }, 400);
  } else if (templateId === "tmpl-my-courses") {
    appendUserChatMessage("List mata kuliah saya");
    setTimeout(() => {
      const classes = APP_DATA[currentRole].classes || [];
      const text = `Berikut adalah seluruh matakuliah terdaftar Anda semester ini (${classes.length} matakuliah):<br><br>` +
        classes.map((c, idx) => `${idx + 1}. <strong>${escapeHtml(c.title)}</strong> (${c.sks}) &bull; ${c.day}, ${c.time}`).join("<br>");
      appendAiChatMessage(text);
    }, 400);
  } else if (templateId === "tmpl-baak-rooms") {
    appendUserChatMessage("Status okupansi ruangan kampus hari ini");
    setTimeout(() => {
      appendAiChatMessage(`Dari 42 total ruangan perkuliahan di sistem, 36 ruangan sedang digunakan aktif untuk praktikum dan teori. 6 laboratorium memiliki slot kosong pada sesi siang (pukul 13:00 - 16:00).`);
    }, 400);
  } else if (templateId === "tmpl-baak-util") {
    appendUserChatMessage("Ringkasan pemanfaatan jadwal mingguan");
    setTimeout(() => {
      appendAiChatMessage(`Terdapat 248 matakuliah terjadwal untuk semester ini dengan tingkat utilitas ruang sebesar 85.7%. Beban hari terpadat adalah Selasa dan Kamis.`);
    }, 400);
  } else if (templateId === "tmpl-reschedule-rec") {
    const isBaak = currentRole === "baak";
    appendUserChatMessage(isBaak ? "Cari slot ruangan kosong untuk perubahan jadwal" : "Cari rekomendasi jadwal kosong untuk pindah jadwal");

    setTimeout(() => {
      const classes = APP_DATA[currentRole].classes || [];
      const dropdownHtml = `
        <p>Silakan pilih matakuliah yang ingin dipindahkan jadwalnya:</p>
        <div style="margin: 12px 0;">
          <select class="form-select" id="chat-course-picker" onchange="generateChatRecommendations(this.value)">
            <option value="">-- Pilih Matakuliah --</option>
            ${classes.map(c => {
              const prefix = isBaak ? `[${c.lecturer}] ` : "";
              return `<option value="${c.id}">${escapeHtml(prefix)}${escapeHtml(c.title)} (${c.day}, ${c.time})</option>`;
            }).join("")}
          </select>
        </div>
      `;
      appendAiChatMessage(dropdownHtml);
    }, 400);
  }
}

function renderChatSlotCards(slots, courseId, headline) {
  return `
    <p>${headline}</p>
    <div class="chat-rec-cards-list">
      ${slots.map(s => `
        <div class="chat-rec-slot-card">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 700; color: var(--text-main); font-size: 12px;">${escapeHtml(s.day)}, ${escapeHtml(s.time)}</span>
            <span class="badge-rec-status" style="font-size: 10px;">${escapeHtml(s.note)}</span>
          </div>
          <div style="font-size: 11px; color: var(--text-subtle); margin: 4px 0;">Ruangan: <strong>${escapeHtml(s.room)}</strong></div>
          <button type="button" class="btn-card-action primary chat-apply-slot" style="width: 100%; margin-top: 6px;"
            data-course-id="${escapeHtml(courseId)}" data-day="${escapeHtml(s.day)}" data-time="${escapeHtml(s.time)}" data-room="${escapeHtml(s.room)}">
            Terapkan ke Formulir Pindah Jadwal &rarr;
          </button>
        </div>
      `).join("")}
    </div>
  `;
}

function generateChatRecommendations(courseId) {
  if (!courseId) return;

  const c = getSchedulingClass(courseId);
  if (!c) return;

  const prefix = currentRole === "baak" ? `[${c.lecturer}] ` : "";
  const headline = `Ditemukan slot kosong rekomendasi sistem untuk <strong>${escapeHtml(prefix)}${escapeHtml(c.title)}</strong>:`;
  const isPersisted = DATABASE_SCHEDULES.some(item => item.id === courseId);

  const showMock = () => appendAiChatMessage(renderChatSlotCards(MOCK_CHAT_SLOTS, courseId, headline));

  if (!isPersisted) {
    showMock();
    return;
  }

  const status = appendChatStatus("Algoritma bitmask sedang menghitung slot bebas konflik...");
  requestChatReply(`Cari slot pengganti untuk ${c.title}`, courseId, status)
    .then(reply => finishChatReply(status, reply, courseId, headline))
    .catch(() => {
      removeChatStatus(status);
      showMock();
    });
}

const MOCK_CHAT_SLOTS = [
  { day: "Rabu", time: "13:00 - 16:00", room: "Lab C 103", note: "Bebas Bentrok & Siap Digunakan" },
  { day: "Kamis", time: "13:00 - 15:00", room: "SAW-06.10", note: "Kapasitas 40 Kursi" },
  { day: "Jumat", time: "08:00 - 11:00", room: "Lab C 102", note: "Workstation Lengkap" }
];

let chatSessionId = null;

function appendChatStatus(text) {
  const stream = document.getElementById("chat-messages-stream");
  if (!stream) return null;

  const bubble = document.createElement("div");
  bubble.className = "chat-bubble ai";
  bubble.innerHTML = `
    <div class="chat-bubble-avatar">AI</div>
    <div class="chat-bubble-content">
      <div class="process-status" role="status" aria-live="polite">
        <span class="process-spinner" aria-hidden="true"></span>
        <span class="chat-status-text">${escapeHtml(text)}</span>
      </div>
    </div>
  `;
  stream.appendChild(bubble);
  stream.scrollTop = stream.scrollHeight;
  return bubble;
}

function updateChatStatus(bubble, text) {
  const label = bubble?.querySelector(".chat-status-text");
  if (label) label.textContent = text;
}

function removeChatStatus(bubble) {
  if (bubble && bubble.parentNode) bubble.parentNode.removeChild(bubble);
}

function requestChatReply(message, scheduleId, statusBubble) {
  const payload = { message };
  if (scheduleId) {
    payload.scheduleId = scheduleId;
    payload.scope = "once";
    payload.target = new Date().toISOString().slice(0, 10);
  }
  if (chatSessionId) payload.sessionId = chatSessionId;

  const stages = scheduleId
    ? ["Algoritma bitmask sedang menghitung slot bebas konflik...", "Asisten AI menyusun penjelasan..."]
    : ["Asisten AI menyusun jawaban..."];
  let stage = 0;
  const timer = window.setInterval(() => {
    stage = Math.min(stage + 1, stages.length - 1);
    updateChatStatus(statusBubble, stages[stage]);
  }, 2500);
  updateChatStatus(statusBubble, stages[0]);

  return apiRequest("POST", "/api/v1/chat/message", payload).finally(() => window.clearInterval(timer));
}

function finishChatReply(statusBubble, reply, courseId, fallbackHeadline) {
  removeChatStatus(statusBubble);
  if (reply.sessionId) chatSessionId = reply.sessionId;

  const slots = Array.isArray(reply.slots) ? reply.slots : [];
  const body = `<p>${escapeHtml(reply.reply || "")}</p>`;
  if (slots.length > 0 && courseId) {
    appendAiChatMessage(body + renderChatSlotCards(slots, courseId, escapeHtml(fallbackHeadline ? "Opsi dari algoritma bitmask:" : "Opsi:")));
  } else {
    appendAiChatMessage(body);
  }
}

function applyChatSlotToReschedule(courseId, day, time, room) {
  navigateToView("reschedule");
  const courseSelect = document.getElementById("reschedule-course-select");
  if (courseSelect) {
    courseSelect.value = courseId;
    onRescheduleCourseChange(courseId);
  }

  const roomSelect = document.getElementById("reschedule-target-room");
  if (roomSelect && ![...roomSelect.options].some(option => option.value === room)) {
    roomSelect.add(new Option(room, room));
  }
  const timeSelect = document.getElementById("reschedule-target-time");
  if (timeSelect && ![...timeSelect.options].some(option => option.value === time)) {
    timeSelect.add(new Option(time, time));
  }

  applyRecommendationSlot(day, time, room);
  showToast("Parameter rekomendasi dari AI berhasil diterapkan ke formulir.");
}

function handleUserChatMessage() {
  const input = document.getElementById("chat-user-input");
  if (!input) return;

  const text = input.value.trim();
  if (!text) return;

  appendUserChatMessage(text);
  input.value = "";

  const status = appendChatStatus("Asisten AI menyusun jawaban...");
  requestChatReply(text, null, status)
    .then(reply => finishChatReply(status, reply, null, ""))
    .catch(() => {
      removeChatStatus(status);
      appendAiChatMessage(`Terima kasih atas pesan Anda: "${escapeHtml(text)}". Anda dapat memanfaatkan tombol template cepat di bagian atas untuk pengecekan jadwal atau pencarian slot pemindahan perkuliahan.`);
    });
}

function appendUserChatMessage(text) {
  const stream = document.getElementById("chat-messages-stream");
  if (!stream) return;

  const bubble = document.createElement("div");
  bubble.className = "chat-bubble user";
  bubble.innerHTML = `
    <div class="chat-bubble-content">
      <p>${escapeHtml(text)}</p>
    </div>
    <div class="chat-bubble-avatar user">${APP_DATA[currentRole].avatarChar}</div>
  `;
  stream.appendChild(bubble);
  stream.scrollTop = stream.scrollHeight;
}

function appendAiChatMessage(htmlContent) {
  const stream = document.getElementById("chat-messages-stream");
  if (!stream) return;

  const bubble = document.createElement("div");
  bubble.className = "chat-bubble ai";
  bubble.innerHTML = `
    <div class="chat-bubble-avatar">AI</div>
    <div class="chat-bubble-content">
      ${htmlContent}
    </div>
  `;
  stream.appendChild(bubble);
  stream.scrollTop = stream.scrollHeight;
}

const BUILDING_CODES = {
  "Gedung D4": "D4",
  "Gedung D3": "D3",
  "Gedung Pasca": "PASCA",
  "Gedung SAW": "SAW"
};

const MASTER_ENTITIES = {
  room: { endpoint: "rooms", label: "Ruangan Perkuliahan", list: () => BAAK_MASTER_ROOMS, render: () => renderBaakRoomsTable() },
  subject: { endpoint: "subjects", label: "Subjek Perkuliahan", list: () => BAAK_MASTER_SUBJECTS, render: () => renderBaakSubjectsTable() },
  lecturer: { endpoint: "lecturers", label: "Dosen Pengampu", list: () => BAAK_MASTER_LECTURERS, render: () => renderBaakLecturersTable() },
  student: { endpoint: "students", label: "Mahasiswa", list: () => BAAK_MASTER_STUDENTS, render: () => renderBaakStudentsTable() }
};

function mapRoomFromApi(room) {
  return {
    id: room.id,
    code: room.code,
    name: room.name,
    building: room.building?.name || "",
    building_code: room.building?.code || "",
    capacity: room.capacity,
    type: room.type,
    floor: room.floor
  };
}

function mapSubjectFromApi(subject) {
  return {
    id: subject.id,
    code: subject.code || "",
    name: subject.name,
    sks: subject.sks ?? subject.credits,
    semester: subject.semester ?? "",
    department: subject.department || ""
  };
}

function mapLecturerFromApi(lecturer) {
  return {
    id: lecturer.id,
    name: lecturer.name,
    nip: lecturer.nip || "",
    code: lecturer.code || "",
    academic_title: lecturer.academic_title || "",
    department: lecturer.department || "",
    email: lecturer.email || ""
  };
}

function mapStudentFromApi(student) {
  return {
    id: student.id,
    name: student.name,
    nrp: student.nrp || "",
    class: student.class || "",
    cohort: String(student.cohort_year ?? ""),
    major: student.department || "",
    email: student.email || ""
  };
}

const MASTER_MAPPERS = {
  room: mapRoomFromApi,
  subject: mapSubjectFromApi,
  lecturer: mapLecturerFromApi,
  student: mapStudentFromApi
};

function syncMasterStats() {
  APP_DATA.baak.stats.ruangan = BAAK_MASTER_ROOMS.length;
  APP_DATA.baak.stats.matakuliah = BAAK_MASTER_SUBJECTS.length;
  APP_DATA.baak.stats.dosen = BAAK_MASTER_LECTURERS.length;
  APP_DATA.baak.stats.mahasiswa = BAAK_MASTER_STUDENTS.length;
  if (activeCurrentView === "dashboard" && currentRole === "baak") {
    renderDashboardForRole("baak");
  }
}

async function loadMasterData() {
  for (const [entity, config] of Object.entries(MASTER_ENTITIES)) {
    try {
      const result = await apiRequest("GET", `/api/v1/${config.endpoint}`);
      const rows = (result.data || []).map(MASTER_MAPPERS[entity]);
      if (rows.length > 0) {
        const list = config.list();
        list.splice(0, list.length, ...rows);
        config.render();
      }
    } catch (error) {
      console.error(error);
    }
  }
  syncMasterStats();
}

function apiErrorMessage(error) {
  const errors = error.payload?.errors;
  if (errors) {
    const first = Object.values(errors).flat()[0];
    if (first) return first;
  }
  return error.message;
}

function openBaakCrudModal(entity, editIndex = null) {
  currentCrudEntity = entity;
  currentCrudEditIndex = editIndex;

  const modal = document.getElementById("baak-crud-modal");
  const titleEl = document.getElementById("baak-crud-modal-title");
  const container = document.getElementById("baak-crud-form-container");
  if (!modal || !titleEl || !container) return;

  const isEdit = editIndex !== null;
  titleEl.textContent = `${isEdit ? "Ubah Data" : "Tambah"} ${MASTER_ENTITIES[entity].label}`;

  const item = isEdit ? MASTER_ENTITIES[entity].list()[editIndex] : {};
  const text = (id, label, value, placeholder, required = true, type = "text", extra = "") => `
      <div class="form-group">
        <label class="form-label" for="${id}">${label}</label>
        <input type="${type}" id="${id}" class="form-input" ${required ? "required" : ""} ${extra} value="${escapeHtml(value ?? "")}" placeholder="${placeholder}">
      </div>`;
  const select = (id, label, options, selected) => `
      <div class="form-group">
        <label class="form-label" for="${id}">${label}</label>
        <select id="${id}" class="form-select" required>
          ${options.map(option => `<option value="${option}" ${option === selected ? "selected" : ""}>${option}</option>`).join("")}
        </select>
      </div>`;

  let fieldsHtml = "";
  if (entity === "room") {
    fieldsHtml = text("crud-room-code", "Label Ruangan", item.code, "Contoh: C-102")
      + text("crud-room-name", "Nama Ruangan", item.name, "Contoh: Ruang Workshop Komputer")
      + select("crud-room-building", "Gedung", Object.keys(BUILDING_CODES), item.building || "Gedung D4")
      + `<div class="form-row-grid">`
      + text("crud-room-capacity", "Kapasitas", item.capacity ?? 30, "Contoh: 40", true, "number", 'min="1" max="1000"')
      + text("crud-room-floor", "Lantai", item.floor ?? 1, "Contoh: 2", true, "number", 'min="1" max="20"')
      + `</div>`
      + select("crud-room-type", "Tipe Ruangan", ["teori", "lab", "aula"], item.type || "teori");
  } else if (entity === "subject") {
    fieldsHtml = text("crud-subj-code", "Kode Subjek", item.code, "Contoh: WMP301")
      + text("crud-subj-name", "Nama Subjek", item.name, "Contoh: Workshop Mesin Pembelajaran")
      + `<div class="form-row-grid">`
      + text("crud-subj-sks", "Satuan Kredit Semester (SKS)", item.sks ?? 3, "", true, "number", 'min="1" max="6"')
      + text("crud-subj-semester", "Semester", item.semester ?? "", "Contoh: 5", false, "number", 'min="1" max="14"')
      + `</div>`
      + text("crud-subj-department", "Departemen", item.department, "Contoh: Teknik Informatika", false);
  } else if (entity === "lecturer") {
    fieldsHtml = text("crud-lect-name", "Nama Dosen Lengkap", item.name, "Contoh: Dr. Ir. Budi Sxxxx, M.T.")
      + text("crud-lect-nip", "Nomor Induk Pegawai (NIP)", item.nip, "Contoh: 197403252001121xxx")
      + `<div class="form-row-grid">`
      + text("crud-lect-code", "Kode Dosen", item.code, "Contoh: BS", false)
      + text("crud-lect-title", "Gelar Akademik", item.academic_title, "Contoh: Dr. Ir., M.T.", false)
      + `</div>`
      + text("crud-lect-department", "Departemen", item.department, "Contoh: Teknik Informatika", false)
      + text("crud-lect-email", "Email", item.email, "Contoh: budi@pens.ac.id", true, "email");
  } else if (entity === "student") {
    fieldsHtml = text("crud-stud-name", "Nama Mahasiswa", item.name, "Contoh: Realdho Fahryz")
      + text("crud-stud-nrp", "Nomor Registrasi Pokok (NRP)", item.nrp, "Contoh: 1234567890")
      + `<div class="form-row-grid">`
      + text("crud-stud-class", "Kelas", item.class ?? "", "Contoh: 3 D4 IT A")
      + text("crud-stud-cohort", "Angkatan", item.cohort ?? "2023", "Contoh: 2023", true, "number", 'min="2000" max="2100"')
      + `</div>`
      + text("crud-stud-major", "Jurusan / Program Studi", item.major ?? "D4 Teknik Informatika", "Contoh: D4 Teknik Informatika")
      + text("crud-stud-email", "Email", item.email, "Contoh: realdho@student.pens.ac.id", false, "email");
  }

  container.innerHTML = fieldsHtml;
  modal.style.display = "flex";
}

function closeBaakCrudModal() {
  const modal = document.getElementById("baak-crud-modal");
  if (modal) modal.style.display = "none";
}

function readCrudForm(entity) {
  const value = id => document.getElementById(id).value.trim();

  if (entity === "room") {
    const building = value("crud-room-building");
    const local = {
      code: value("crud-room-code"),
      name: value("crud-room-name"),
      building,
      building_code: BUILDING_CODES[building],
      capacity: parseInt(value("crud-room-capacity"), 10) || 30,
      type: value("crud-room-type"),
      floor: parseInt(value("crud-room-floor"), 10) || 1
    };
    const { building: label, ...payload } = local;
    return { local, payload };
  }

  if (entity === "subject") {
    const local = {
      code: value("crud-subj-code"),
      name: value("crud-subj-name"),
      sks: parseInt(value("crud-subj-sks"), 10) || 2,
      semester: value("crud-subj-semester"),
      department: value("crud-subj-department")
    };
    return { local, payload: { ...local, semester: local.semester ? Number(local.semester) : null, department: local.department || null } };
  }

  if (entity === "lecturer") {
    const local = {
      name: value("crud-lect-name"),
      nip: value("crud-lect-nip"),
      code: value("crud-lect-code"),
      academic_title: value("crud-lect-title"),
      department: value("crud-lect-department"),
      email: value("crud-lect-email")
    };
    return {
      local,
      payload: { ...local, code: local.code || null, academic_title: local.academic_title || null, department: local.department || null }
    };
  }

  const local = {
    name: value("crud-stud-name"),
    nrp: value("crud-stud-nrp"),
    class: value("crud-stud-class"),
    cohort: value("crud-stud-cohort"),
    major: value("crud-stud-major"),
    email: value("crud-stud-email")
  };
  return {
    local,
    payload: {
      name: local.name,
      nrp: local.nrp,
      class: local.class,
      cohort_year: Number(local.cohort),
      department: local.major,
      email: local.email || null
    }
  };
}

function saveBaakCrudItem(e) {
  if (e) e.preventDefault();

  const entity = currentCrudEntity;
  const config = MASTER_ENTITIES[entity];
  const editIndex = currentCrudEditIndex;
  const isEdit = editIndex !== null;
  const list = config.list();
  const existing = isEdit ? list[editIndex] : null;
  const { local, payload } = readCrudForm(entity);

  const applyLocally = savedId => {
    const saved = { ...(existing || {}), ...local, ...(savedId ? { id: savedId } : {}) };
    if (isEdit) list[editIndex] = saved;
    else list.unshift(saved);
    config.render();
    syncMasterStats();
    closeBaakCrudModal();
    showToast(isEdit ? "Perubahan data master berhasil disimpan." : "Data master baru berhasil ditambahkan.");
  };

  if (isEdit && !existing.id) {
    applyLocally();
    return;
  }

  const request = isEdit
    ? apiRequest("PUT", `/api/v1/${config.endpoint}/${existing.id}`, payload)
    : apiRequest("POST", `/api/v1/${config.endpoint}`, payload);

  request
    .then(result => applyLocally((result?.data || result)?.id))
    .catch(error => {
      if (error.offline) {
        applyLocally();
        return;
      }
      showToast(apiErrorMessage(error));
    });
}

function deleteBaakMasterItem(entity, index) {
  if (!confirm("Apakah Anda yakin ingin menghapus data ini?")) return;

  const config = MASTER_ENTITIES[entity];
  const list = config.list();
  const item = list[index];

  const removeLocally = () => {
    list.splice(index, 1);
    config.render();
    syncMasterStats();
    showToast("Data master berhasil dihapus.");
  };

  if (!item.id) {
    removeLocally();
    return;
  }

  apiRequest("DELETE", `/api/v1/${config.endpoint}/${item.id}`)
    .then(removeLocally)
    .catch(error => {
      if (error.offline) {
        removeLocally();
        return;
      }
      showToast(apiErrorMessage(error));
    });
}

function renderBaakRoomsTable() {
  const tbody = document.getElementById("baak-rooms-tbody");
  if (!tbody) return;

  tbody.innerHTML = BAAK_MASTER_ROOMS.map((r, idx) => `
    <tr>
      <td><code>${escapeHtml(r.code)}</code></td>
      <td><strong>${escapeHtml(r.name)}</strong></td>
      <td>${escapeHtml(r.building)}</td>
      <td>
        <div style="display: flex; gap: 6px;">
          <button type="button" class="btn-card-action outline" style="flex: initial; padding: 4px 10px;" onclick="openBaakCrudModal('room', ${idx})">Ubah</button>
          <button type="button" class="btn-card-action outline" style="flex: initial; padding: 4px 10px; color: var(--accent-rose); border-color: var(--accent-rose);" onclick="deleteBaakMasterItem('room', ${idx})">Hapus</button>
        </div>
      </td>
    </tr>
  `).join("");
}

function renderBaakSubjectsTable() {
  const tbody = document.getElementById("baak-subjects-tbody");
  if (!tbody) return;

  tbody.innerHTML = BAAK_MASTER_SUBJECTS.map((s, idx) => `
    <tr>
      <td><code>${escapeHtml(s.code)}</code></td>
      <td><strong>${escapeHtml(s.name)}</strong></td>
      <td>${s.sks} SKS</td>
      <td>
        <div style="display: flex; gap: 6px;">
          <button type="button" class="btn-card-action outline" style="flex: initial; padding: 4px 10px;" onclick="openBaakCrudModal('subject', ${idx})">Ubah</button>
          <button type="button" class="btn-card-action outline" style="flex: initial; padding: 4px 10px; color: var(--accent-rose); border-color: var(--accent-rose);" onclick="deleteBaakMasterItem('subject', ${idx})">Hapus</button>
        </div>
      </td>
    </tr>
  `).join("");
}

function renderBaakLecturersTable() {
  const tbody = document.getElementById("baak-lecturers-tbody");
  if (!tbody) return;

  tbody.innerHTML = BAAK_MASTER_LECTURERS.map((l, idx) => `
    <tr>
      <td><strong>${escapeHtml(l.name)}</strong></td>
      <td><code>${escapeHtml(l.nip)}</code></td>
      <td>
        <div style="display: flex; gap: 6px;">
          <button type="button" class="btn-card-action outline" style="flex: initial; padding: 4px 10px;" onclick="openBaakCrudModal('lecturer', ${idx})">Ubah</button>
          <button type="button" class="btn-card-action outline" style="flex: initial; padding: 4px 10px; color: var(--accent-rose); border-color: var(--accent-rose);" onclick="deleteBaakMasterItem('lecturer', ${idx})">Hapus</button>
        </div>
      </td>
    </tr>
  `).join("");
}

function renderBaakStudentsTable() {
  const tbody = document.getElementById("baak-students-tbody");
  if (!tbody) return;

  tbody.innerHTML = BAAK_MASTER_STUDENTS.map((s, idx) => `
    <tr>
      <td><strong>${escapeHtml(s.name)}</strong></td>
      <td><code>${escapeHtml(s.nrp)}</code></td>
      <td>${escapeHtml(s.cohort)}</td>
      <td>${escapeHtml(s.major)}</td>
      <td>
        <div style="display: flex; gap: 6px;">
          <button type="button" class="btn-card-action outline" style="flex: initial; padding: 4px 10px;" onclick="openBaakCrudModal('student', ${idx})">Ubah</button>
          <button type="button" class="btn-card-action outline" style="flex: initial; padding: 4px 10px; color: var(--accent-rose); border-color: var(--accent-rose);" onclick="deleteBaakMasterItem('student', ${idx})">Hapus</button>
        </div>
      </td>
    </tr>
  `).join("");
}

function renderBaakFullRequestsTable() {
  const tbody = document.getElementById("baak-full-requests-tbody");
  if (!tbody) return;

  tbody.innerHTML = ALL_RESCHEDULE_REQUESTS.map((r, idx) => `
    <tr>
      <td>${idx + 1}</td>
      <td><strong>${escapeHtml(r.requesterName)}</strong> <span style="font-size: 10px; color: var(--text-subtle);">(${r.requesterRole})</span></td>
      <td>${escapeHtml(r.courseTitle)}</td>
      <td>${escapeHtml(r.lecturerName)}</td>
      <td>${escapeHtml(r.originalSchedule)}</td>
      <td><strong>${escapeHtml(r.proposedSchedule)}</strong></td>
      <td>${escapeHtml(r.reason)}</td>
      <td>
        <span class="status-badge ${r.status === 'Disetujui' ? 'approved' : r.status === 'Ditolak' ? 'rejected' : 'pending'}">
          ${escapeHtml(r.status)}
        </span>
      </td>
    </tr>
  `).join("");
}

function filterBaakLogs(level) {
  currentLogLevelFilter = level;
  document.querySelectorAll(".log-filter-btn").forEach(btn => {
    btn.classList.toggle("active", btn.dataset.level === level);
  });
  renderBaakSystemLogs();
}

function onBaakLogSearch(query) {
  currentLogSearchQuery = query.toLowerCase().trim();
  renderBaakSystemLogs();
}

function renderBaakSystemLogs() {
  const tbody = document.getElementById("baak-system-logs-tbody");
  if (!tbody) return;

  const filtered = BAAK_SYSTEM_LOGS.filter(log => {
    const matchLevel = currentLogLevelFilter === "all" || log.level === currentLogLevelFilter;
    const matchSearch = !currentLogSearchQuery ||
      log.detail.toLowerCase().includes(currentLogSearchQuery) ||
      log.actor.toLowerCase().includes(currentLogSearchQuery) ||
      log.module.toLowerCase().includes(currentLogSearchQuery) ||
      log.id.toLowerCase().includes(currentLogSearchQuery);
    return matchLevel && matchSearch;
  });

  if (filtered.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" style="text-align: center; color: var(--text-subtle); padding: 24px;">
          Tidak ada rekaman log yang sesuai dengan filter pencarian.
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = filtered.map(log => `
    <tr>
      <td><span style="font-family: monospace; font-size: 10px; color: var(--text-subtle);">${escapeHtml(log.timestamp)}</span></td>
      <td>
        <span class="status-badge ${log.level === 'error' ? 'rejected' : log.level === 'warning' ? 'pending' : log.level === 'shift' ? 'shifted' : 'approved'}">
          ${escapeHtml(log.levelLabel)}
        </span>
      </td>
      <td><strong>${escapeHtml(log.actor)}</strong></td>
      <td><code>${escapeHtml(log.module)}</code></td>
      <td>${escapeHtml(log.detail)}</td>
      <td><span class="status-badge ${log.status === 'Dicegah' ? 'rejected' : 'approved'}">${escapeHtml(log.status)}</span></td>
    </tr>
  `).join("");
}

const CSV_ENTITIES = {
  schedules: {
    title: "Impor Master Jadwal Kuliah (CSV)",
    columns: ["kode_mk", "nama_mk", "dosen_pengampu", "hari", "jam", "ruang"],
    example: "WMP301, Workshop Mesin Pembelajaran, Dr. Ir. Budi Sxxxx, M.T., Senin, 08:00 - 11:00, Lab C 102",
    file: "jadwal_kuliah.csv"
  },
  rooms: {
    title: "Impor Data Ruangan (CSV)",
    columns: ["code", "name", "building_code", "capacity", "type", "floor"],
    example: "SAW-05.02, Ruang Teori SAW, SAW, 40, teori, 5",
    file: "ruangan.csv"
  },
  subjects: {
    title: "Impor Data Subjek (CSV)",
    columns: ["code", "name", "sks", "semester", "department"],
    example: "WM, Workshop Mesin Pembelajaran, 3, 5, Teknik Informatika",
    file: "subjek.csv"
  },
  lecturers: {
    title: "Impor Data Dosen (CSV)",
    columns: ["nip", "name", "code", "academic_title", "department", "email"],
    example: "198001012005011001, Dr. Ir. Budi S, M.T., BS, Dr. Ir. ..., M.T., Teknik Informatika, budi@pens.ac.id",
    file: "dosen.csv"
  },
  students: {
    title: "Impor Data Mahasiswa (CSV)",
    columns: ["nrp", "name", "class", "cohort_year", "department", "email"],
    example: "3123500001, Ahmad Realdho, 3 D4 IT A, 2023, Teknik Informatika, realdho@student.pens.ac.id",
    file: "mahasiswa.csv"
  }
};

const CSV_SAMPLE_ROWS = {
  rooms: [
    { code: "SAW-05.03", name: "Ruang Teori SAW 05.03", building_code: "SAW", capacity: "40", type: "teori", floor: "5" },
    { code: "D3-301", name: "Laboratorium Jaringan D3", building_code: "D3", capacity: "32", type: "lab", floor: "3" },
    { code: "", name: "Ruang Tanpa Kode", building_code: "D4", capacity: "30", type: "teori", floor: "1" }
  ],
  subjects: [
    { code: "WMP301", name: "Workshop Mesin Pembelajaran", sks: "3", semester: "5", department: "Teknik Informatika" },
    { code: "IOT402", name: "Internet of Things Terapan", sks: "3", semester: "7", department: "Teknik Informatika" },
    { code: "XYZ999", name: "", sks: "2", semester: "3", department: "Teknik Informatika" }
  ],
  lecturers: [
    { nip: "198001012005011001", name: "Dr. Ir. Budi S, M.T.", code: "BS", academic_title: "Dr. Ir., M.T.", department: "Teknik Informatika", email: "budi@pens.ac.id" },
    { nip: "198505052010122002", name: "Sri Wahyuni, S.Kom., M.T.", code: "SW", academic_title: "M.T.", department: "Teknik Informatika", email: "sri@pens.ac.id" },
    { nip: "", name: "Dosen Tanpa NIP", code: "DT", academic_title: "", department: "Teknik Informatika", email: "dt@pens.ac.id" }
  ],
  students: [
    { nrp: "3123500001", name: "Ahmad Realdho", class: "3 D4 IT A", cohort_year: "2023", department: "Teknik Informatika", email: "realdho@student.pens.ac.id" },
    { nrp: "3123500002", name: "Dewi Anggraini", class: "3 D4 IT A", cohort_year: "2023", department: "Teknik Informatika", email: "dewi@student.pens.ac.id" },
    { nrp: "", name: "Mahasiswa Tanpa NRP", class: "3 D4 IT B", cohort_year: "2023", department: "Teknik Informatika", email: "" }
  ]
};

const CSV_REQUIRED_FIELDS = {
  rooms: ["code", "name", "building_code", "capacity"],
  subjects: ["code", "name", "sks"],
  lecturers: ["nip", "name"],
  students: ["nrp", "name", "class", "cohort_year", "department"]
};

let currentCsvEntity = "schedules";

function openCsvImportModal(entity = "schedules") {
  const modal = document.getElementById("csv-import-modal");
  if (!modal) return;

  currentCsvEntity = CSV_ENTITIES[entity] ? entity : "schedules";
  const config = CSV_ENTITIES[currentCsvEntity];

  document.getElementById("csv-modal-title").textContent = config.title;
  document.getElementById("csv-modal-description").innerHTML =
    `Unggah berkas CSV dengan kolom: <code>${escapeHtml(config.columns.join(", "))}</code>.`;
  document.getElementById("csv-modal-example").textContent = config.example;
  document.getElementById("csv-file-input").value = "";

  modal.style.display = "flex";
  resetCsvPreview();
}

function closeCsvImportModal() {
  const modal = document.getElementById("csv-import-modal");
  if (modal) modal.style.display = "none";
}

function resetCsvPreview() {
  const box = document.getElementById("csv-preview-box");
  const parseTable = document.getElementById("csv-parsed-table-wrapper");
  if (box) box.style.display = "none";
  if (parseTable) parseTable.style.display = "none";
  pendingCsvRows = [];
  setCsvStatus(null);
}

function setCsvStatus(state, text) {
  const box = document.getElementById("csv-import-status");
  const label = document.getElementById("csv-import-status-text");
  const confirmButton = document.getElementById("btn-csv-confirm");
  if (!box || !label) return;

  if (!state) {
    box.style.display = "none";
    if (confirmButton) confirmButton.disabled = false;
    return;
  }

  box.style.display = "flex";
  box.classList.toggle("done", state === "done");
  box.classList.toggle("failed", state === "failed");
  label.textContent = text;
  if (confirmButton) confirmButton.disabled = state === "busy";
}

function downloadCsvTemplate() {
  const config = CSV_ENTITIES[currentCsvEntity];
  const content = `${config.columns.join(",")}\n`;
  const link = document.createElement("a");
  link.href = URL.createObjectURL(new Blob([content], { type: "text/csv" }));
  link.download = config.file;
  link.click();
  URL.revokeObjectURL(link.href);
}

function parseCsvText(text) {
  const rows = [];
  let row = [];
  let cell = "";
  let quoted = false;
  const source = text.replace(/^\uFEFF/, "");

  for (let i = 0; i < source.length; i++) {
    const char = source[i];
    if (quoted) {
      if (char === '"' && source[i + 1] === '"') {
        cell += '"';
        i++;
      } else if (char === '"') {
        quoted = false;
      } else {
        cell += char;
      }
    } else if (char === '"') {
      quoted = true;
    } else if (char === ",") {
      row.push(cell.trim());
      cell = "";
    } else if (char === "\n" || char === "\r") {
      if (char === "\r" && source[i + 1] === "\n") i++;
      row.push(cell.trim());
      if (row.some(value => value !== "")) rows.push(row);
      row = [];
      cell = "";
    } else {
      cell += char;
    }
  }
  row.push(cell.trim());
  if (row.some(value => value !== "")) rows.push(row);
  return rows;
}

function csvTextToRecords(text, columns) {
  const table = parseCsvText(text);
  if (table.length === 0) return { error: "Berkas CSV kosong.", records: [] };

  const header = table[0].map(name => name.toLowerCase());
  const missing = columns.filter(name => !header.includes(name));
  if (missing.length > 0) {
    return { error: `Kolom wajib tidak ditemukan: ${missing.join(", ")}.`, records: [] };
  }

  const records = table.slice(1).map((cells, index) => {
    const record = { id: index + 1, errorType: null };
    columns.forEach(name => {
      record[name] = cells[header.indexOf(name)] ?? "";
    });
    return record;
  });
  return { error: null, records };
}

function validateMasterCsvRow(entity, row) {
  const missing = (CSV_REQUIRED_FIELDS[entity] || []).filter(field => !String(row[field] ?? "").trim());
  row.errorType = missing.length > 0 ? "field" : null;
  row.missingFields = missing;
}

function renderCsvPreview(fileLabel) {
  const config = CSV_ENTITIES[currentCsvEntity];
  const head = document.getElementById("csv-parsed-thead-row");
  const tbody = document.getElementById("csv-parsed-tbody");
  const box = document.getElementById("csv-preview-box");
  const wrapper = document.getElementById("csv-parsed-table-wrapper");

  document.getElementById("csv-preview-file").textContent = fileLabel;
  document.getElementById("csv-preview-count").textContent = `(${pendingCsvRows.length} baris)`;

  if (currentCsvEntity === "schedules") {
    head.innerHTML = ["Kode", "Nama Matakuliah", "Dosen Pengampu", "Jadwal", "Ruangan", "Status"].map(h => `<th>${h}</th>`).join("");
    renderScheduleCsvRows(tbody);
  } else {
    head.innerHTML = config.columns.map(name => `<th>${escapeHtml(name)}</th>`).join("") + "<th>Status</th>";
    tbody.innerHTML = pendingCsvRows.map(row => `
      <tr class="${row.errorType ? "csv-error-row" : ""}">
        ${config.columns.map(name => `
          <td><input type="text" class="csv-cell-input" value="${escapeHtml(row[name])}" onchange="updateMasterCsvCell(${row.id}, '${name}', this.value)" aria-label="${escapeHtml(name)}"></td>
        `).join("")}
        <td>${row.errorType
          ? `<span class="badge-rec-status" style="background: var(--accent-rose-light); color: var(--accent-rose); border-color: var(--accent-rose);">Lengkapi ${escapeHtml(row.missingFields.join(", "))}</span>`
          : `<span class="status-badge approved">Valid</span>`}</td>
      </tr>
    `).join("");
  }

  box.style.display = "block";
  wrapper.style.display = "block";
}

function updateMasterCsvCell(rowId, field, value) {
  const row = pendingCsvRows.find(r => r.id === rowId);
  if (!row) return;
  row[field] = value.trim();
  validateMasterCsvRow(currentCsvEntity, row);
  renderCsvPreview(document.getElementById("csv-preview-file").textContent);
}

function handleCsvFileSelected(file) {
  if (!file) return;
  if (file.size > 5 * 1024 * 1024) {
    showToast("Ukuran berkas melebihi 5 MB.");
    return;
  }

  const reader = new FileReader();
  reader.onload = () => {
    const config = CSV_ENTITIES[currentCsvEntity];
    const { error, records } = csvTextToRecords(String(reader.result), config.columns);
    if (error) {
      resetCsvPreview();
      showToast(error);
      return;
    }

    if (currentCsvEntity === "schedules") {
      pendingCsvRows = records.map(row => ({
        id: row.id,
        code: row.kode_mk,
        title: row.nama_mk,
        lecturer: row.dosen_pengampu,
        day: row.hari,
        time: row.jam,
        room: row.ruang,
        errorType: BAAK_MASTER_LECTURERS.some(l => l.name === row.dosen_pengampu) ? null : "lecturer"
      }));
    } else {
      pendingCsvRows = records;
      pendingCsvRows.forEach(row => validateMasterCsvRow(currentCsvEntity, row));
    }
    renderCsvPreview(file.name);
    showToast("Berkas CSV berhasil diuraikan. Periksa baris bertanda merah sebelum konfirmasi.");
  };
  reader.readAsText(file);
}

function setupCsvDropArea() {
  const input = document.getElementById("csv-file-input");
  const area = document.getElementById("csv-drop-area");
  if (!input || !area) return;

  input.addEventListener("change", () => handleCsvFileSelected(input.files[0]));
  area.addEventListener("dragover", event => event.preventDefault());
  area.addEventListener("drop", event => {
    event.preventDefault();
    handleCsvFileSelected(event.dataTransfer.files[0]);
  });
}

function simulateFileUpload() {
  if (currentCsvEntity === "schedules") {
    pendingCsvRows = [
      { id: 1, code: "WMP301", title: "Workshop Mesin Pembelajaran", lecturer: "Dr. Ir. Budi Sxxxx, M.T.", day: "Senin", time: "08:00 - 11:00", room: "Lab C 102", errorType: null },
      { id: 2, code: "IOT402", title: "Internet of Things Terapan", lecturer: "Dosen Tidak Ditemukan", day: "Rabu", time: "13:00 - 16:00", room: "Lab C 103", errorType: "lecturer" },
      { id: 3, code: "XYZ999", title: "Kelas Tidak Terdaftar", lecturer: "Nur Rosyid Mxxxx, S.Kom., M.T.", day: "Kamis", time: "09:00 - 11:00", room: "SAW-06.10", errorType: "class" }
    ];
  } else {
    pendingCsvRows = CSV_SAMPLE_ROWS[currentCsvEntity].map((row, index) => ({ id: index + 1, errorType: null, ...row }));
    pendingCsvRows.forEach(row => validateMasterCsvRow(currentCsvEntity, row));
  }
  renderCsvPreview(CSV_ENTITIES[currentCsvEntity].file);
  showToast("Contoh data dimuat. Periksa baris bertanda merah sebelum konfirmasi.");
}

function renderScheduleCsvRows(tbody) {
  tbody.innerHTML = pendingCsvRows.map(row => `
    <tr class="${row.errorType ? 'csv-error-row' : ''}">
      <td>
        ${row.errorType === 'class' ? `
          <input type="text" class="csv-cell-input" value="${escapeHtml(row.code)}" onchange="updateCsvCell(${row.id}, 'code', this.value)" title="Kode tidak terdaftar, silakan perbaiki">
        ` : `<code>${escapeHtml(row.code)}</code>`}
      </td>
      <td>
        ${row.errorType === 'class' ? `
          <input type="text" class="csv-cell-input" value="${escapeHtml(row.title)}" onchange="updateCsvCell(${row.id}, 'title', this.value)" title="Kelas tidak ditemukan di kurikulum">
        ` : escapeHtml(row.title)}
      </td>
      <td>
        ${row.errorType === 'lecturer' ? `
          <select class="csv-cell-select" onchange="updateCsvCell(${row.id}, 'lecturer', this.value)" title="Dosen tidak ditemukan, silakan pilih dosen valid">
            <option value="">-- Pilih Dosen Pengampu --</option>
            ${BAAK_MASTER_LECTURERS.map(l => `<option value="${escapeHtml(l.name)}">${escapeHtml(l.name)}</option>`).join('')}
          </select>
        ` : escapeHtml(row.lecturer)}
      </td>
      <td>${escapeHtml(row.day)}, ${escapeHtml(row.time)}</td>
      <td>${escapeHtml(row.room)}</td>
      <td>
        ${row.errorType ? `<span class="badge-rec-status" style="background: var(--accent-rose-light); color: var(--accent-rose); border-color: var(--accent-rose);">Perlu Koreksi</span>` : `<span class="status-badge approved">Valid</span>`}
      </td>
    </tr>
  `).join("");
}

function updateCsvCell(rowId, field, value) {
  const row = pendingCsvRows.find(r => r.id === rowId);
  if (row) {
    row[field] = value;
    if (field === "lecturer" && value) {
      row.errorType = null;
    }
    if (field === "code" && value !== "XYZ999") {
      row.errorType = null;
    }
    showToast(`Data baris ${rowId} diperbarui.`);
  }
}

function applyImportedRowsLocally(entity, rows) {
  const upsert = (list, keyOf, item) => {
    const index = list.findIndex(existing => keyOf(existing) === keyOf(item));
    if (index >= 0) list[index] = item;
    else list.unshift(item);
  };
  const buildings = { D4: "Gedung D4", D3: "Gedung D3", PASCA: "Gedung Pasca", SAW: "Gedung SAW" };

  rows.forEach(row => {
    if (entity === "rooms") {
      const building = buildings[String(row.building_code).toUpperCase()] || row.building_code;
      upsert(BAAK_MASTER_ROOMS, r => r.code, { code: row.code, name: row.name, building });
    } else if (entity === "subjects") {
      upsert(BAAK_MASTER_SUBJECTS, r => r.code, { code: row.code, name: row.name, sks: parseInt(row.sks, 10) || 2 });
    } else if (entity === "lecturers") {
      upsert(BAAK_MASTER_LECTURERS, r => r.nip, { name: row.name, nip: row.nip });
    } else if (entity === "students") {
      upsert(BAAK_MASTER_STUDENTS, r => r.nrp, { name: row.name, nrp: row.nrp, cohort: row.cohort_year, major: row.department });
    }
  });

  APP_DATA.baak.stats.ruangan = BAAK_MASTER_ROOMS.length;
  APP_DATA.baak.stats.matakuliah = BAAK_MASTER_SUBJECTS.length;
  APP_DATA.baak.stats.dosen = BAAK_MASTER_LECTURERS.length;
  APP_DATA.baak.stats.mahasiswa = BAAK_MASTER_STUDENTS.length;
  renderBaakRoomsTable();
  renderBaakSubjectsTable();
  renderBaakLecturersTable();
  renderBaakStudentsTable();
}

function confirmCsvImport() {
  if (pendingCsvRows.length === 0) {
    showToast("Pilih berkas CSV atau muat contoh data terlebih dahulu.");
    return;
  }

  const hasUnresolved = pendingCsvRows.some(r => r.errorType !== null);
  if (hasUnresolved) {
    showToast("Harap perbaiki kolom bertanda merah terlebih dahulu.");
    return;
  }

  const entity = currentCsvEntity;
  const config = CSV_ENTITIES[entity];
  const rows = pendingCsvRows.map(row => {
    if (entity === "schedules") {
      return { kode_mk: row.code, nama_mk: row.title, dosen_pengampu: row.lecturer, hari: row.day, jam: row.time, ruang: row.room };
    }
    return Object.fromEntries(config.columns.map(name => [name, row[name]]));
  });

  setCsvStatus("busy", `Mengirim ${rows.length} baris ke server...`);

  apiRequest("POST", `/api/v1/import/${entity}`, { rows })
    .then(result => {
      const summary = `${result.imported} dari ${result.total} baris tersimpan${result.failed > 0 ? `, ${result.failed} gagal` : ""}.`;
      setCsvStatus(result.failed > 0 ? "failed" : "done", summary);
      if (result.failed === 0) {
        applyImportedRowsLocally(entity, rows);
        window.setTimeout(closeCsvImportModal, 900);
      }
      showToast(`Impor selesai: ${summary}`);
    })
    .catch(error => {
      if (error.offline) {
        applyImportedRowsLocally(entity, rows);
        setCsvStatus("done", "Server tidak terjangkau; data diterapkan pada tampilan lokal saja.");
        window.setTimeout(closeCsvImportModal, 1200);
        showToast("Server tidak terjangkau; impor diterapkan pada tampilan lokal.");
        return;
      }
      setCsvStatus("failed", error.message);
      showToast(error.message);
    });
}

function setupProfilePopover() {
  const widget = document.getElementById("user-profile-widget");
  const popover = document.getElementById("profile-details-popover");

  if (!widget || !popover) return;

  widget.addEventListener("click", (e) => {
    e.stopPropagation();
    popover.classList.toggle("active");
  });

  document.addEventListener("click", (e) => {
    if (!popover.contains(e.target) && !widget.contains(e.target)) {
      popover.classList.remove("active");
    }
  });
}

function performLogout() {
  document.getElementById("app-shell").style.display = "none";
  document.getElementById("landing-page-shell").style.display = "flex";
  window.scrollTo({ top: 0, behavior: "smooth" });
  showToast("Anda telah keluar ke landing page PENSCEDULER.");
}

function enterAppDashboard() {
  document.getElementById("landing-page-shell").style.display = "none";
  document.getElementById("app-shell").style.display = "flex";
  navigateToView("dashboard");
  showToast("Selamat datang di sistem PENSCEDULER.");
}

function showToast(msg) {
  let toast = document.getElementById("app-toast");
  if (!toast) {
    toast = document.createElement("div");
    toast.id = "app-toast";
    toast.className = "toast-container";
    document.body.appendChild(toast);
  }

  toast.innerHTML = `
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
    <span>${escapeHtml(msg)}</span>
  `;
  toast.classList.add("active");

  setTimeout(() => {
    toast.classList.remove("active");
  }, 2800);
}

function escapeHtml(str) {
  if (!str) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
