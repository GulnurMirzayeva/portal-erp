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

            {{-- Dashboard --}}
            <li class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                <a href="{{ route('dashboard') }}" class="waves-effect {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="menu-icon">
                        <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                        <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                        <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                        <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                    </svg>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            {{-- Category: Apps & ERP Modules --}}
            <li class="menu-title">ERP MODULLARI</li>

            {{-- Mühasibatlıq --}}
            @canany(['accounting.view', 'expenses.view', 'expense-classifications.view'])
            @php
                $isAccountingActive = request()->routeIs('accounting.*') || request()->routeIs('expenses.*') || request()->routeIs('expense-classifications.*');
                $isExpensesActive = request()->routeIs('expenses.*') || request()->routeIs('expense-classifications.*');
            @endphp
            <li class="{{ $isAccountingActive ? 'mm-active' : '' }}">
                <a href="javascript:void(0);" class="waves-effect has-arrow {{ $isAccountingActive ? 'active' : '' }}" data-toggle="sub-menu" data-target="#menuAccounting" title="Mühasibatlıq">
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
                <ul class="sub-menu {{ $isAccountingActive ? 'show' : '' }}" id="menuAccounting">
                    @can('accounting.view')
                    <li>
                        <a href="{{ route('accounting.index') }}" class="{{ request()->routeIs('accounting.*') ? 'active' : '' }}">
                            <span class="sub-bullet"></span> Elektron Qaimələr
                        </a>
                    </li>
                    @endcan

                    {{-- Xərclər alt bölməsi --}}
                    @canany(['expenses.view', 'expense-classifications.view'])
                    <li class="has-nested {{ $isExpensesActive ? 'mm-active' : '' }}">
                        <a href="javascript:void(0);" class="waves-effect has-arrow {{ $isExpensesActive ? 'active' : '' }}" data-toggle="sub-menu" data-target="#menuExpenses">
                            <span class="sub-bullet"></span>
                            <span class="menu-text">Xərclər</span>
                            <span class="menu-arrow">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </a>
                        <ul class="sub-menu sub-menu-nested {{ $isExpensesActive ? 'show' : '' }}" id="menuExpenses">
                            @can('expense-classifications.view')
                            <li>
                                <a href="{{ route('expense-classifications.index') }}" class="{{ request()->routeIs('expense-classifications.*') ? 'active' : '' }}">
                                    <span class="sub-bullet"></span> Təsnifatlar
                                </a>
                            </li>
                            @endcan
                            @can('expenses.view')
                            <li>
                                <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                                    <span class="sub-bullet"></span> Xərclərin siyahısı
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

            {{-- CRM --}}
            @can('crm.view')
            @php
                $isCrmActive = request()->routeIs('crm.*');
            @endphp
            <li class="{{ $isCrmActive ? 'mm-active' : '' }}">
                <a href="javascript:void(0);" class="waves-effect has-arrow {{ $isCrmActive ? 'active' : '' }}" data-toggle="sub-menu" data-target="#menuCrm" title="CRM">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="menu-icon">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span class="menu-text">CRM</span>
                    <span class="menu-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </span>
                </a>
                <ul class="sub-menu {{ $isCrmActive ? 'show' : '' }}" id="menuCrm">
                    <li>
                        <a href="{{ route('crm.inquiries.index') }}" class="{{ request()->routeIs('crm.inquiries.*') ? 'active' : '' }}">
                            <span class="sub-bullet"></span> Müraciət Statistikası
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('crm.index') }}" class="{{ request()->routeIs('crm.index') ? 'active' : '' }}">
                            <span class="sub-bullet"></span> Ümumi Statistika
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('crm.traffic.index') }}" class="{{ request()->routeIs('crm.traffic.*') ? 'active' : '' }}">
                            <span class="sub-bullet"></span> Trafik və Sosial Media
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- Davamiyyət (QR Giriş-Çıxış) --}}
            @can('attendance.view')
            @php
                $isAttendanceActive = request()->routeIs('attendance.*');
            @endphp
            <li class="{{ $isAttendanceActive ? 'mm-active' : '' }}">
                <a href="javascript:void(0);" class="waves-effect has-arrow {{ $isAttendanceActive ? 'active' : '' }}" data-toggle="sub-menu" data-target="#menuAttendance" title="Davamiyyət">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="menu-icon">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                        <path d="m9 16 2 2 4-4"></path>
                    </svg>
                    <span class="menu-text">Davamiyyət (QR)</span>
                    <span class="menu-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </span>
                </a>
                <ul class="sub-menu {{ $isAttendanceActive ? 'show' : '' }}" id="menuAttendance">
                    <li>
                        <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                            <span class="sub-bullet"></span> Giriş-Çıxış Jurnalı
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('attendance.violations') }}" class="{{ request()->routeIs('attendance.violations') ? 'active' : '' }}">
                            <span class="sub-bullet"></span> Şübhəli / Yanlış Cəhdlər
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('attendance.branches') }}" class="{{ request()->routeIs('attendance.branches') ? 'active' : '' }}">
                            <span class="sub-bullet"></span> Filiallar & QR Kodlar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('attendance.workers') }}" class="{{ request()->routeIs('attendance.workers') ? 'active' : '' }}">
                            <span class="sub-bullet"></span> İşçilər & PIN Kodlar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('attendance.settings') }}" class="{{ request()->routeIs('attendance.settings') ? 'active' : '' }}">
                            <span class="sub-bullet"></span> Tənzimləmələr
                        </a>
                    </li>
                </ul>
            </li>
            @endcan
        </ul>
    </div>
</aside>
