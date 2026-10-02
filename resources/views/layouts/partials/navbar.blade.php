<header id="page-topbar">
    <div class="navbar-header">
        {{-- Left side: Hamburger toggle only --}}
        <div class="navbar-left d-flex align-items-center">
            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn" aria-label="Toggle Navigation">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>

        {{-- Right side: User dropdown with only Logout ("Çıxış") --}}
        <div class="navbar-right d-flex align-items-center">
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-dropdown="userProfileDropdown">
                    <div class="header-profile-user-wrapper">
                        <div class="header-avatar-circle">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                    </div>
                    <span class="d-none d-xl-inline-block ms-1 font-weight-500 text-dark">
                        {{ auth()->user()->name ?? 'ERP Admin' }}
                    </span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="ms-1 d-none d-xl-inline-block">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-sm" id="userProfileDropdown" style="min-width: 170px; padding: 6px 0; border: 1px solid #e9ecef; border-radius: 6px;">
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start py-2 px-3 d-flex align-items-center" style="cursor: pointer; font-weight: 500; font-size: 13px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 text-danger">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>Çıxış</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

