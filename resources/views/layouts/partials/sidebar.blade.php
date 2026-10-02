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
                </ul>
            </li>
        </ul>
    </div>
</aside>
