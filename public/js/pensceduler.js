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
  renderActiveRole(currentRole);
  loadSchedulingCatalog();
});

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
  const savedTheme = localStorage.getItem("penscheduler_theme") || "light";
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

function approveShiftRequest(requestId, classId) {
  const dosenClasses = APP_DATA.dosen.classes;
  const targetClass = dosenClasses.find(c => c.id === classId);
  if (targetClass) {
    targetClass.hasPendingRequest = false;
    targetClass.hasShift = true;
    targetClass.shiftedSchedule = `${targetClass.pendingRequestDetail.targetDay}, ${targetClass.pendingRequestDetail.targetTime} di ${targetClass.pendingRequestDetail.targetRoom} (Disetujui)`;
  }

  const req = ALL_RESCHEDULE_REQUESTS.find(r => r.id === requestId);
  if (req) {
    req.status = "Disetujui";
  }

  renderActiveRole("dosen");
  showToast("Permintaan perpindahan jadwal telah DISETUJUI.");
}

function rejectShiftRequest(requestId, classId) {
  const dosenClasses = APP_DATA.dosen.classes;
  const targetClass = dosenClasses.find(c => c.id === classId);
  if (targetClass) {
    targetClass.hasPendingRequest = false;
  }

  const req = ALL_RESCHEDULE_REQUESTS.find(r => r.id === requestId);
  if (req) {
    req.status = "Ditolak";
  }

  renderActiveRole("dosen");
  showToast("Permintaan perpindahan jadwal DITOLAK.");
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

  // Mock slots keep the form handoff usable when the course is not in the database catalog or the engine is unreachable.
  if (!useEngine) {
    renderMockRecommendationSlots(container);
    return;
  }

  container.innerHTML = '<p class="view-subtitle">Engine sedang menghitung slot dan memeriksa bentrok...</p>';

  fetch("/api/scheduling/evaluate", {
    method: "POST",
    headers: { "Content-Type": "application/json", "Accept": "application/json" },
    body: JSON.stringify({ scheduleId, scope, target: targetDate })
  }).then(async response => {
    const result = await response.json();
    if (!response.ok) throw new Error(result.message || "Permintaan rekomendasi gagal.");

    const options = Array.isArray(result.options) ? result.options : [];
    if (options.length === 0) {
      container.innerHTML = `<p class="view-subtitle">${result.success ? "Tidak ada opsi." : "Tidak ditemukan slot yang memenuhi aturan engine."}</p>`;
      return;
    }

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
        applyRecommendationSlot(option.day, `${option.start_time} - ${option.end_time}`, option.room_name);
      });
    });
  }).catch(error => {
    console.error(error);
    renderMockRecommendationSlots(container);
    showToast("Engine tidak tersedia; menampilkan slot contoh.");
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

function submitRescheduleRequest() {
  const courseSelect = document.getElementById("reschedule-course-select");
  const targetDay = document.getElementById("reschedule-target-day");
  const targetTime = document.getElementById("reschedule-target-time");
  const targetRoom = document.getElementById("reschedule-target-room");
  const durationSelect = document.getElementById("reschedule-duration-select");

  if (!courseSelect || !courseSelect.value) {
    showToast("Silakan pilih matakuliah terlebih dahulu.");
    return;
  }

  const courseTitle = courseSelect.options[courseSelect.selectedIndex].text.split("(")[0].trim();
  const isMahasiswa = currentRole === "mahasiswa";
  const isDosen = currentRole === "dosen";
  const isBaak = currentRole === "baak";

  const status = isMahasiswa ? "Menunggu Persetujuan Dosen" : "Disetujui";

  const newRequest = {
    id: `REQ-${Date.now().toString().slice(-4)}`,
    courseId: courseSelect.value,
    courseTitle: courseTitle,
    lecturerName: isDosen ? APP_DATA.dosen.name : isMahasiswa ? "Dr. Ir. Budi Sxxxx, M.T." : "BAAK",
    requesterRole: APP_DATA[currentRole].roleLabel,
    requesterName: APP_DATA[currentRole].name,
    originalSchedule: "Jadwal Reguler",
    proposedSchedule: `${targetDay.value}, ${targetTime.value} (${targetRoom.value})`,
    reason: `Perpindahan sesi perkuliahan (${durationSelect.value})`,
    status: status,
    submittedAt: "09 Okt 2026 14:30"
  };

  ALL_RESCHEDULE_REQUESTS.unshift(newRequest);

  if (!isMahasiswa) {
    const profile = APP_DATA[currentRole];
    const c = getSchedulingClass(courseSelect.value);
    if (c) {
      c.hasShift = true;
      c.shiftedSchedule = `${targetDay.value}, ${targetTime.value} di ${targetRoom.value} (${durationSelect.value})`;
    }
  }

  renderActiveRole(currentRole);
  showToast(isMahasiswa ? "Permohonan berhasil diajukan. Menunggu persetujuan dosen." : "Jadwal perkuliahan berhasil diperbarui (Otomatis Disetujui).");
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

function generateChatRecommendations(courseId) {
  if (!courseId) return;

  const profile = APP_DATA[currentRole];
  const c = profile.classes.find(item => item.id === courseId);
  if (!c) return;

  const isBaak = currentRole === "baak";
  const prefix = isBaak ? `[${c.lecturer}] ` : "";

  const slots = [
    { day: "Rabu", time: "13:00 - 16:00", room: "Lab C 103", note: "Bebas Bentrok & Siap Digunakan" },
    { day: "Kamis", time: "13:00 - 15:00", room: "SAW-06.10", note: "Kapasitas 40 Kursi" },
    { day: "Jumat", time: "08:00 - 11:00", room: "Lab C 102", note: "Workstation Lengkap" }
  ];

  const html = `
    <p>Ditemukan slot kosong rekomendasi sistem untuk <strong>${escapeHtml(prefix)}${escapeHtml(c.title)}</strong>:</p>
    <div class="chat-rec-cards-list">
      ${slots.map(s => `
        <div class="chat-rec-slot-card">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 700; color: var(--text-main); font-size: 12px;">${escapeHtml(s.day)}, ${escapeHtml(s.time)}</span>
            <span class="badge-rec-status" style="font-size: 10px;">${escapeHtml(s.note)}</span>
          </div>
          <div style="font-size: 11px; color: var(--text-subtle); margin: 4px 0;">Ruangan: <strong>${escapeHtml(s.room)}</strong></div>
          <button type="button" class="btn-card-action primary" style="width: 100%; margin-top: 6px;" onclick="applyChatSlotToReschedule('${c.id}', '${s.day}', '${s.time}', '${s.room}')">
            Terapkan ke Formulir Pindah Jadwal &rarr;
          </button>
        </div>
      `).join("")}
    </div>
  `;

  appendAiChatMessage(html);
}

function applyChatSlotToReschedule(courseId, day, time, room) {
  navigateToView("reschedule");
  const courseSelect = document.getElementById("reschedule-course-select");
  if (courseSelect) {
    courseSelect.value = courseId;
    onRescheduleCourseChange(courseId);
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

  setTimeout(() => {
    appendAiChatMessage(`Terima kasih atas pesan Anda: "${escapeHtml(text)}". Anda dapat memanfaatkan tombol template cepat di bagian atas untuk pengecekan jadwal atau pencarian slot pemindahan perkuliahan.`);
  }, 500);
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

function openBaakCrudModal(entity, editIndex = null) {
  currentCrudEntity = entity;
  currentCrudEditIndex = editIndex;

  const modal = document.getElementById("baak-crud-modal");
  const titleEl = document.getElementById("baak-crud-modal-title");
  const container = document.getElementById("baak-crud-form-container");
  if (!modal || !titleEl || !container) return;

  const isEdit = editIndex !== null;
  const entityLabels = {
    room: "Ruangan Perkuliahan",
    subject: "Subjek Perkuliahan",
    lecturer: "Dosen Pengampu",
    student: "Mahasiswa"
  };

  titleEl.textContent = `${isEdit ? 'Ubah Data' : 'Tambah'} ${entityLabels[entity]}`;

  let fieldsHtml = "";
  if (entity === "room") {
    const item = isEdit ? BAAK_MASTER_ROOMS[editIndex] : { code: "", name: "", building: "Gedung D4" };
    fieldsHtml = `
      <div class="form-group">
        <label class="form-label" for="crud-room-code">Label Ruangan</label>
        <input type="text" id="crud-room-code" class="form-input" required value="${escapeHtml(item.code)}" placeholder="Contoh: C-102">
      </div>
      <div class="form-group">
        <label class="form-label" for="crud-room-name">Nama Ruangan</label>
        <input type="text" id="crud-room-name" class="form-input" required value="${escapeHtml(item.name)}" placeholder="Contoh: Ruang Workshop Komputer">
      </div>
      <div class="form-group">
        <label class="form-label" for="crud-room-building">Gedung</label>
        <select id="crud-room-building" class="form-select" required>
          <option value="Gedung D4" ${item.building === 'Gedung D4' ? 'selected' : ''}>Gedung D4</option>
          <option value="Gedung D3" ${item.building === 'Gedung D3' ? 'selected' : ''}>Gedung D3</option>
          <option value="Gedung Pasca" ${item.building === 'Gedung Pasca' ? 'selected' : ''}>Gedung Pasca</option>
          <option value="Gedung SAW" ${item.building === 'Gedung SAW' ? 'selected' : ''}>Gedung SAW</option>
        </select>
      </div>
    `;
  } else if (entity === "subject") {
    const item = isEdit ? BAAK_MASTER_SUBJECTS[editIndex] : { code: "", name: "", sks: 3 };
    fieldsHtml = `
      <div class="form-group">
        <label class="form-label" for="crud-subj-code">Kode Subjek</label>
        <input type="text" id="crud-subj-code" class="form-input" required value="${escapeHtml(item.code)}" placeholder="Contoh: WMP301">
      </div>
      <div class="form-group">
        <label class="form-label" for="crud-subj-name">Nama Subjek</label>
        <input type="text" id="crud-subj-name" class="form-input" required value="${escapeHtml(item.name)}" placeholder="Contoh: Workshop Mesin Pembelajaran">
      </div>
      <div class="form-group">
        <label class="form-label" for="crud-subj-sks">Satuan Kredit Semester (SKS)</label>
        <input type="number" id="crud-subj-sks" class="form-input" min="1" max="6" required value="${item.sks}">
      </div>
    `;
  } else if (entity === "lecturer") {
    const item = isEdit ? BAAK_MASTER_LECTURERS[editIndex] : { name: "", nip: "" };
    fieldsHtml = `
      <div class="form-group">
        <label class="form-label" for="crud-lect-name">Nama Dosen Lengkap</label>
        <input type="text" id="crud-lect-name" class="form-input" required value="${escapeHtml(item.name)}" placeholder="Contoh: Dr. Ir. Budi Sxxxx, M.T.">
      </div>
      <div class="form-group">
        <label class="form-label" for="crud-lect-nip">Nomor Induk Pegawai (NIP)</label>
        <input type="text" id="crud-lect-nip" class="form-input" required value="${escapeHtml(item.nip)}" placeholder="Contoh: 197403252001121xxx">
      </div>
    `;
  } else if (entity === "student") {
    const item = isEdit ? BAAK_MASTER_STUDENTS[editIndex] : { name: "", nrp: "", cohort: "2023", major: "D4 Teknik Informatika" };
    fieldsHtml = `
      <div class="form-group">
        <label class="form-label" for="crud-stud-name">Nama Mahasiswa</label>
        <input type="text" id="crud-stud-name" class="form-input" required value="${escapeHtml(item.name)}" placeholder="Contoh: Realdho Fahryz">
      </div>
      <div class="form-group">
        <label class="form-label" for="crud-stud-nrp">Nomor Registrasi Pokok (NRP)</label>
        <input type="text" id="crud-stud-nrp" class="form-input" required value="${escapeHtml(item.nrp)}" placeholder="Contoh: 1234567890">
      </div>
      <div class="form-row-grid">
        <div class="form-group">
          <label class="form-label" for="crud-stud-cohort">Angkatan</label>
          <input type="text" id="crud-stud-cohort" class="form-input" required value="${escapeHtml(item.cohort)}" placeholder="Contoh: 2022">
        </div>
        <div class="form-group">
          <label class="form-label" for="crud-stud-major">Jurusan / Program Studi</label>
          <input type="text" id="crud-stud-major" class="form-input" required value="${escapeHtml(item.major)}" placeholder="Contoh: D4 Teknik Informatika">
        </div>
      </div>
    `;
  }

  container.innerHTML = fieldsHtml;
  modal.style.display = "flex";
}

function closeBaakCrudModal() {
  const modal = document.getElementById("baak-crud-modal");
  if (modal) modal.style.display = "none";
}

function saveBaakCrudItem(e) {
  if (e) e.preventDefault();

  const isEdit = currentCrudEditIndex !== null;

  if (currentCrudEntity === "room") {
    const code = document.getElementById("crud-room-code").value.trim();
    const name = document.getElementById("crud-room-name").value.trim();
    const building = document.getElementById("crud-room-building").value.trim();
    if (!code || !name) return;

    const data = { code, name, building };
    if (isEdit) {
      BAAK_MASTER_ROOMS[currentCrudEditIndex] = data;
    } else {
      BAAK_MASTER_ROOMS.unshift(data);
    }
    APP_DATA.baak.stats.ruangan = BAAK_MASTER_ROOMS.length;
    renderBaakRoomsTable();
  } else if (currentCrudEntity === "subject") {
    const code = document.getElementById("crud-subj-code").value.trim();
    const name = document.getElementById("crud-subj-name").value.trim();
    const sks = parseInt(document.getElementById("crud-subj-sks").value, 10) || 2;
    if (!code || !name) return;

    const data = { code, name, sks };
    if (isEdit) {
      BAAK_MASTER_SUBJECTS[currentCrudEditIndex] = data;
    } else {
      BAAK_MASTER_SUBJECTS.unshift(data);
    }
    APP_DATA.baak.stats.matakuliah = BAAK_MASTER_SUBJECTS.length;
    renderBaakSubjectsTable();
  } else if (currentCrudEntity === "lecturer") {
    const name = document.getElementById("crud-lect-name").value.trim();
    const nip = document.getElementById("crud-lect-nip").value.trim();
    if (!name || !nip) return;

    const data = { name, nip };
    if (isEdit) {
      BAAK_MASTER_LECTURERS[currentCrudEditIndex] = data;
    } else {
      BAAK_MASTER_LECTURERS.unshift(data);
    }
    APP_DATA.baak.stats.dosen = BAAK_MASTER_LECTURERS.length;
    renderBaakLecturersTable();
  } else if (currentCrudEntity === "student") {
    const name = document.getElementById("crud-stud-name").value.trim();
    const nrp = document.getElementById("crud-stud-nrp").value.trim();
    const cohort = document.getElementById("crud-stud-cohort").value.trim();
    const major = document.getElementById("crud-stud-major").value.trim();
    if (!name || !nrp) return;

    const data = { name, nrp, cohort, major };
    if (isEdit) {
      BAAK_MASTER_STUDENTS[currentCrudEditIndex] = data;
    } else {
      BAAK_MASTER_STUDENTS.unshift(data);
    }
    APP_DATA.baak.stats.mahasiswa = BAAK_MASTER_STUDENTS.length;
    renderBaakStudentsTable();
  }

  closeBaakCrudModal();
  showToast(isEdit ? "Perubahan data master berhasil disimpan." : "Data master baru berhasil ditambahkan.");

  if (activeCurrentView === "dashboard" && currentRole === "baak") {
    renderDashboardForRole("baak");
  }
}

function deleteBaakMasterItem(entity, index) {
  if (!confirm("Apakah Anda yakin ingin menghapus data ini?")) return;

  if (entity === "room") {
    BAAK_MASTER_ROOMS.splice(index, 1);
    APP_DATA.baak.stats.ruangan = BAAK_MASTER_ROOMS.length;
    renderBaakRoomsTable();
  } else if (entity === "subject") {
    BAAK_MASTER_SUBJECTS.splice(index, 1);
    APP_DATA.baak.stats.matakuliah = BAAK_MASTER_SUBJECTS.length;
    renderBaakSubjectsTable();
  } else if (entity === "lecturer") {
    BAAK_MASTER_LECTURERS.splice(index, 1);
    APP_DATA.baak.stats.dosen = BAAK_MASTER_LECTURERS.length;
    renderBaakLecturersTable();
  } else if (entity === "student") {
    BAAK_MASTER_STUDENTS.splice(index, 1);
    APP_DATA.baak.stats.mahasiswa = BAAK_MASTER_STUDENTS.length;
    renderBaakStudentsTable();
  }

  showToast("Data master berhasil dihapus.");

  if (activeCurrentView === "dashboard" && currentRole === "baak") {
    renderDashboardForRole("baak");
  }
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

function openCsvImportModal() {
  const modal = document.getElementById("csv-import-modal");
  if (modal) {
    modal.style.display = "flex";
    resetCsvPreview();
  }
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
}

function simulateFileUpload() {
  const box = document.getElementById("csv-preview-box");
  const parseTable = document.getElementById("csv-parsed-table-wrapper");
  const tbody = document.getElementById("csv-parsed-tbody");

  pendingCsvRows = [
    {
      id: 1,
      code: "WMP301",
      title: "Workshop Mesin Pembelajaran",
      lecturer: "Dr. Ir. Budi Sxxxx, M.T.",
      day: "Senin",
      time: "08:00 - 11:00",
      room: "Lab C 102",
      errorType: null
    },
    {
      id: 2,
      code: "IOT402",
      title: "Internet of Things Terapan",
      lecturer: "Dosen Tidak Ditemukan",
      day: "Rabu",
      time: "13:00 - 16:00",
      room: "Lab C 103",
      errorType: "lecturer"
    },
    {
      id: 3,
      code: "XYZ999",
      title: "Kelas Tidak Terdaftar",
      lecturer: "Nur Rosyid Mxxxx, S.Kom., M.T.",
      day: "Kamis",
      time: "09:00 - 11:00",
      room: "SAW-06.10",
      errorType: "class"
    }
  ];

  if (tbody) {
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

  if (box) box.style.display = "block";
  if (parseTable) parseTable.style.display = "block";
  showToast("Berkas CSV berhasil diuraikan. Periksa baris bertanda merah sebelum konfirmasi.");
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

function confirmCsvImport() {
  const hasUnresolved = pendingCsvRows.some(r => r.errorType !== null);
  if (hasUnresolved) {
    showToast("Harap perbaiki kolom bertanda merah terlebih dahulu.");
    return;
  }

  closeCsvImportModal();
  showToast("Data jadwal CSV berhasil disinkronkan ke master jadwal perkuliahan.");
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
