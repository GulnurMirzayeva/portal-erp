<aside class="vertical-menu" id="vertical-menu">
    {{-- Brand Logo --}}
    <div class="navbar-brand-box">
        <a href="{{ route('dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="brand-icon">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </span>
            <span class="logo-lg">
                <span class="brand-wrapper">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="brand-icon">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                    <span class="brand-title">PORTAL <span class="brand-accent">ERP</span></span>
                </span>
            </span>
        </a>
    </div>

    {{-- Menu Scroll Container --}}
    <div class="sidebar-scroll" id="sidebarScroll">
        <ul class="metismenu list-unstyled" id="side-menu">
            {{-- Category: Menu --}}
            <li class="menu-title">MENU</li>

            {{-- Dashboards --}}
            <li class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                <a href="javascript:void(0);" class="waves-effect has-arrow {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-toggle="sub-menu" data-target="#menuDashboards" title="Dashboards">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="menu-icon">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span class="menu-text">Dashboards</span>
                    <span class="menu-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </span>
                </a>
                <ul class="sub-menu {{ request()->routeIs('dashboard') ? 'show' : '' }}" id="menuDashboards">
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="sub-bullet"></span> Default (Əsas)</a></li>
                    <li><a href="javascript:void(0);" class="disabled-link"><span class="sub-bullet"></span> Analitika</a></li>
                </ul>
            </li>

            {{-- Category: Apps & ERP Modules --}}
            <li class="menu-title">ERP MODULLARI</li>

            {{-- Mühasibatlıq --}}
            <li class="{{ request()->routeIs('accounting.*') ? 'mm-active' : '' }}">
                <a href="javascript:void(0);" class="waves-effect has-arrow {{ request()->routeIs('accounting.*') ? 'active' : '' }}" data-toggle="sub-menu" data-target="#menuAccounting" title="Mühasibatlıq">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="menu-icon">
                        <rect width="16" height="20" x="4" y="2" rx="2"></rect>
                        <line x1="8" x2="16" y1="6" y2="6"></line>
                        <line x1="16" x2="16" y1="14"></line>
                        <path d="M16 10h.01"></path>
                        <path d="M12 10h.01"></path>
                        <path d="M8 10h.01"></path>
                        <path d="M12 14h.01"></path>
                        <path d="M8 14h.01"></path>
                        <path d="M12 18h.01"></path>
                        <path d="M8 18h.01"></path>
                    </svg>
                    <span class="menu-text">Mühasibatlıq</span>
                    <span class="menu-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </span>
                </a>
                <ul class="sub-menu {{ request()->routeIs('accounting.*') ? 'show' : '' }}" id="menuAccounting">
                    <li><a href="{{ route('accounting.index') }}" class="{{ request()->routeIs('accounting.index') ? 'active' : '' }}"><span class="sub-bullet"></span> Elektron Qaimələr</a></li>
                    <li><a href="{{ route('accounting.index') }}"><span class="sub-bullet"></span> İşçi Bonus Hesabatı</a></li>
                    <li><a href="javascript:void(0);" class="disabled-link"><span class="sub-bullet"></span> Kassa və Bank Girişləri</a></li>
                </ul>
            </li>

            {{-- Satışlar --}}
            <li class="{{ request()->routeIs('sales.*') ? 'mm-active' : '' }}">
                <a href="javascript:void(0);" class="waves-effect has-arrow {{ request()->routeIs('sales.*') ? 'active' : '' }}" data-toggle="sub-menu" data-target="#menuSales" title="Satışlar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="menu-icon">
                        <circle cx="8" cy="21" r="1"></circle>
                        <circle cx="19" cy="21" r="1"></circle>
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                    </svg>
                    <span class="menu-text">Satışlar</span>
                    <span class="menu-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </span>
                </a>
                <ul class="sub-menu {{ request()->routeIs('sales.*') ? 'show' : '' }}" id="menuSales">
                    <li><a href="javascript:void(0);"><span class="sub-bullet"></span> Rezervasiyalar</a></li>
                    <li><a href="javascript:void(0);"><span class="sub-bullet"></span> Oyun Satışları</a></li>
                </ul>
            </li>

            {{-- İşçilər & Komanda --}}
            <li class="{{ request()->routeIs('employees.*') ? 'mm-active' : '' }}">
                <a href="javascript:void(0);" class="waves-effect has-arrow {{ request()->routeIs('employees.*') ? 'active' : '' }}" data-toggle="sub-menu" data-target="#menuEmployees" title="İşçilər & Komanda">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="menu-icon">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span class="menu-text">İşçilər & Komanda</span>
                    <span class="menu-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </span>
                </a>
                <ul class="sub-menu {{ request()->routeIs('employees.*') ? 'show' : '' }}" id="menuEmployees">
                    <li><a href="javascript:void(0);"><span class="sub-bullet"></span> İşçi Siyahısı</a></li>
                    <li><a href="javascript:void(0);"><span class="sub-bullet"></span> Növbələr & Əvəzlər</a></li>
                </ul>
            </li>

            {{-- Filiallar --}}
            <li>
                <a href="javascript:void(0);" class="waves-effect" title="Filiallar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="menu-icon">
                        <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                        <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                        <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                        <path d="M10 6h4"></path>
                        <path d="M10 10h4"></path>
                        <path d="M10 14h4"></path>
                        <path d="M10 18h4"></path>
                    </svg>
                    <span class="menu-text">Filiallar</span>
                </a>
            </li>

            {{-- Category: System & Security --}}
            <li class="menu-title">SİSTEM & İDARƏETMƏ</li>

            {{-- Rollar & İcazələr --}}
            <li class="{{ request()->routeIs('roles.*') ? 'mm-active' : '' }}">
                <a href="javascript:void(0);" class="waves-effect has-arrow {{ request()->routeIs('roles.*') ? 'active' : '' }}" data-toggle="sub-menu" data-target="#menuRoles" title="Rollar & İcazələr">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="menu-icon">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <span class="menu-text">Rollar & İcazələr</span>
                    <span class="menu-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </span>
                </a>
                <ul class="sub-menu {{ request()->routeIs('roles.*') ? 'show' : '' }}" id="menuRoles">
                    <li><a href="javascript:void(0);"><span class="sub-bullet"></span> Rolların İdarəsi</a></li>
                    <li><a href="javascript:void(0);"><span class="sub-bullet"></span> İcazələr Siyahısı</a></li>
                </ul>
            </li>

            {{-- Tənzimləmələr --}}
            <li>
                <a href="javascript:void(0);" class="waves-effect" title="Tənzimləmələr">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="menu-icon">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    <span class="menu-text">Tənzimləmələr</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
