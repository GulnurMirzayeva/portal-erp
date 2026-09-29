<header id="page-topbar">
    <div class="navbar-header">
        {{-- Left side --}}
        <div class="navbar-left d-flex align-items-center">
            {{-- Hamburger Toggle Button --}}
            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn" aria-label="Toggle Navigation">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>

            {{-- App Search --}}
            <form class="app-search d-none d-lg-block">
                <div class="position-relative">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" class="form-control" placeholder="Search...">
                </div>
            </form>

            {{-- Mega Menu Dropdown --}}
            <div class="dropdown dropdown-mega d-none d-lg-block">
                <button type="button" class="btn header-item waves-effect dropdown-toggle" id="megaMenuBtn" data-dropdown="megaMenuDropdown">
                    <span>Mega Menu</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="ms-1"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="dropdown-menu dropdown-megamenu" id="megaMenuDropdown">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="font-size-14 mt-0">ERP Əməliyyatları</h5>
                            <ul class="list-unstyled megamenu-list">
                                <li><a href="javascript:void(0);">Mühasibatlıq 32 Sütun</a></li>
                                <li><a href="javascript:void(0);">İşçi Bonus Hesabatı</a></li>
                                <li><a href="javascript:void(0);">Gündəlik Satışlar</a></li>
                                <li><a href="javascript:void(0);">Filiallar və Növbələr</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <h5 class="font-size-14 mt-0">Sürətli Keçidlər</h5>
                            <ul class="list-unstyled megamenu-list">
                                <li><a href="javascript:void(0);">Rollar və İcazələr</a></li>
                                <li><a href="javascript:void(0);">PortalWebsite API Statusu</a></li>
                                <li><a href="javascript:void(0);">Sistem Parametrləri</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right side --}}
        <div class="navbar-right d-flex align-items-center">

            {{-- Search button for Mobile --}}
            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="mobileSearchBtn" data-dropdown="mobileSearchDropdown">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" id="mobileSearchDropdown">
                    <form class="p-3">
                        <div class="m-0">
                            <input type="text" class="form-control" placeholder="Search ...">
                        </div>
                    </form>
                </div>
            </div>

            {{-- Language Switcher --}}
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="langBtn" data-dropdown="langDropdown">
                    <span class="flag-icon">🇦🇿</span>
                    <span class="d-none d-xl-inline-block ms-1">AZ</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end" id="langDropdown">
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <span class="flag-icon me-1">🇦🇿</span> <span>Azərbaycan</span>
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <span class="flag-icon me-1">🇬🇧</span> <span>English</span>
                    </a>
                </div>
            </div>

            {{-- Bento / Grid Apps --}}
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" id="appsGridBtn" data-dropdown="appsGridDropdown">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="6" height="6" x="3" y="3" rx="1"></rect>
                        <rect width="6" height="6" x="15" y="3" rx="1"></rect>
                        <rect width="6" height="6" x="3" y="15" rx="1"></rect>
                        <rect width="6" height="6" x="15" y="15" rx="1"></rect>
                    </svg>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-2" id="appsGridDropdown">
                    <div class="row g-0 text-center">
                        <div class="col-4 p-2">
                            <a class="dropdown-icon-item" href="javascript:void(0);">
                                <div class="grid-app-icon bg-soft-primary">📊</div>
                                <span>Hesabat</span>
                            </a>
                        </div>
                        <div class="col-4 p-2">
                            <a class="dropdown-icon-item" href="javascript:void(0);">
                                <div class="grid-app-icon bg-soft-success">💰</div>
                                <span>Kassa</span>
                            </a>
                        </div>
                        <div class="col-4 p-2">
                            <a class="dropdown-icon-item" href="javascript:void(0);">
                                <div class="grid-app-icon bg-soft-warning">👥</div>
                                <span>İşçilər</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fullscreen Toggle --}}
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" id="fullscreen-btn" title="Toggle Fullscreen">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                    </svg>
                </button>
            </div>

            {{-- Notifications Dropdown --}}
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown" data-dropdown="notificationsDropdown">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                        <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                    </svg>
                    <span class="badge bg-danger rounded-pill">3</span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" id="notificationsDropdown">
                    <div class="p-3 border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-size-14 font-weight-600">Bildirişlər</h6>
                            <span class="badge bg-success font-size-11">3 Yeni</span>
                        </div>
                    </div>
                    <div class="notifications-list">
                        <a href="javascript:void(0);" class="text-reset notification-item">
                            <div class="d-flex align-items-start">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title bg-primary rounded-circle font-size-16">
                                        🎮
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 font-size-13">Yeni Rezervasiya</h6>
                                    <div class="font-size-12 text-muted">
                                        <p class="mb-1">Dəlixana oyunu üçün yeni qeydiyyat</p>
                                        <p class="mb-0 text-muted"><small>5 dəq əvvəl</small></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0);" class="text-reset notification-item">
                            <div class="d-flex align-items-start">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title bg-success rounded-circle font-size-16">
                                        ⭐
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 font-size-13">PlusBir Qeydi</h6>
                                    <div class="font-size-12 text-muted">
                                        <p class="mb-1">Aktyor üçün +1 bonus qeydə alındı</p>
                                        <p class="mb-0 text-muted"><small>22 dəq əvvəl</small></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0);" class="text-reset notification-item">
                            <div class="d-flex align-items-start">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title bg-info rounded-circle font-size-16">
                                        🔄
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 font-size-13">API Sinxronizasiyası</h6>
                                    <div class="font-size-12 text-muted">
                                        <p class="mb-1">PortalWebsite ilə əlaqə aktivdir</p>
                                        <p class="mb-0 text-muted"><small>1 saat əvvəl</small></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-13 text-center" href="javascript:void(0);">
                            Hamısına bax
                        </a>
                    </div>
                </div>
            </div>

            {{-- User Profile Dropdown --}}
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-dropdown="userProfileDropdown">
                    <div class="header-profile-user-wrapper">
                        <div class="header-avatar-circle">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                    </div>
                    <span class="d-none d-xl-inline-block ms-1 font-weight-500 text-dark">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="ms-1 d-none d-xl-inline-block"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="dropdown-menu dropdown-menu-end" id="userProfileDropdown">
                    <div class="dropdown-header">
                        <span class="d-block font-size-13 text-dark font-weight-600">{{ auth()->user()->name ?? 'ERP Admin' }}</span>
                        <span class="d-block font-size-11 text-muted">{{ auth()->user()->email ?? 'admin@portal.land' }}</span>
                    </div>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Profil
                    </a>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        Mühasibatlıq
                    </a>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        Tənzimləmələr
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start" style="cursor: pointer;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 text-danger"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            Çıxış
                        </button>
                    </form>
                </div>
            </div>

            {{-- Settings Cog Button --}}
            <div class="dropdown d-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect" id="settingsCogBtn" title="Tənzimləmələr">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-spin-hover">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>
</header>
