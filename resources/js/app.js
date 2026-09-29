/**
 * Portal ERP - Skote Dashboard UI Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initDropdowns();
    initFullscreen();
    initSubmenus();
});

/**
 * Sidebar Toggle & Responsiveness
 */
function initSidebar() {
    const toggleBtn = document.getElementById('vertical-menu-btn');
    const backdrop = document.getElementById('sidebarBackdrop');
    const body = document.body;

    if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();

            if (window.innerWidth >= 992) {
                // Desktop: Toggle mini-sidebar
                body.classList.toggle('vertical-collpsed');
                body.classList.remove('sidebar-enable');
            } else {
                // Mobile: Toggle off-canvas drawer
                body.classList.toggle('sidebar-enable');
            }
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', () => {
            body.classList.remove('sidebar-enable');
        });
    }

    // Handle resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            body.classList.remove('sidebar-enable');
        }
    });
}

/**
 * Sidebar Accordion Submenus
 */
function initSubmenus() {
    const toggles = document.querySelectorAll('#side-menu [data-toggle="collapse"]');

    toggles.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = btn.getAttribute('data-target');
            if (!targetId) return;

            const target = document.querySelector(targetId);
            const parentLi = btn.closest('li');

            if (!target) return;

            const isShown = target.classList.contains('show');

            // Close other sibling submenus
            const siblingSubmenus = btn.closest('ul').querySelectorAll('.sub-menu.show');
            siblingSubmenus.forEach((sub) => {
                if (sub !== target) {
                    sub.classList.remove('show');
                    const siblingLi = sub.closest('li');
                    if (siblingLi) siblingLi.classList.remove('mm-active');
                }
            });

            if (isShown) {
                target.classList.remove('show');
                if (parentLi) parentLi.classList.remove('mm-active');
            } else {
                target.classList.add('show');
                if (parentLi) parentLi.classList.add('mm-active');
            }
        });
    });
}

/**
 * Navbar Dropdowns
 */
function initDropdowns() {
    const dropdownBtns = document.querySelectorAll('[data-dropdown]');

    dropdownBtns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const targetId = btn.getAttribute('data-dropdown');
            const targetMenu = document.getElementById(targetId);

            if (!targetMenu) return;

            const isOpen = targetMenu.classList.contains('show');

            // Close all dropdowns first
            document.querySelectorAll('.dropdown-menu.show').forEach((menu) => {
                if (menu !== targetMenu) {
                    menu.classList.remove('show');
                }
            });

            if (isOpen) {
                targetMenu.classList.remove('show');
            } else {
                targetMenu.classList.add('show');
            }
        });
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown-menu') && !e.target.closest('[data-dropdown]')) {
            document.querySelectorAll('.dropdown-menu.show').forEach((menu) => {
                menu.classList.remove('show');
            });
        }
    });
}

/**
 * Fullscreen API Toggle
 */
function initFullscreen() {
    const btn = document.getElementById('fullscreen-btn');
    if (!btn) return;

    btn.addEventListener('click', (e) => {
        e.preventDefault();

        if (
            !document.fullscreenElement &&
            !document.mozFullScreenElement &&
            !document.webkitFullscreenElement
        ) {
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullscreen) {
                document.documentElement.webkitRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }
    });
}
