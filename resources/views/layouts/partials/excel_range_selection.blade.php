{{-- Excel-like Multi-Cell Drag Selection, Keyboard Navigation & Horizontal Scroll Engine --}}
<style>
    /* Prevent text selection during table drag-selection */
    .excel-table.is-selecting,
    .excel-table.is-selecting * {
        user-select: none !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        cursor: cell !important;
    }

    /* Panning / Moving table mode */
    .excel-table-wrapper.is-panning,
    .excel-table-wrapper.is-panning * {
        cursor: grabbing !important;
        user-select: none !important;
    }

    /* Selected cell highlight (Authentic Microsoft Excel green tint) */
    table.excel-table tbody td.excel-cell-selected {
        background-color: rgba(16, 124, 65, 0.15) !important;
        position: relative;
    }

    /* Primary anchor cell inside selection stays clean */
    table.excel-table tbody td.excel-cell-selected.sel-anchor {
        background-color: rgba(255, 255, 255, 0.95) !important;
        box-shadow: inset 0 0 0 1px #107c41 !important;
    }

    /* Outer border of selected cell range (Excel green boundary) */
    table.excel-table tbody td.excel-cell-selected.sel-border-top {
        box-shadow: inset 0 2px 0 #107c41 !important;
    }
    table.excel-table tbody td.excel-cell-selected.sel-border-left {
        box-shadow: inset 2px 0 0 #107c41 !important;
    }
    table.excel-table tbody td.excel-cell-selected.sel-border-top.sel-border-left {
        box-shadow: inset 2px 2px 0 #107c41 !important;
    }
    table.excel-table tbody td.excel-cell-selected.sel-border-bottom {
        border-bottom: 2px solid #107c41 !important;
    }
    table.excel-table tbody td.excel-cell-selected.sel-border-right {
        border-right: 2px solid #107c41 !important;
    }

    /* Row header index highlight when row is in selection */
    table.excel-table tbody tr.row-in-selection > td.row-idx,
    table.excel-table tbody tr.row-in-selection > td.row-idx-header {
        background-color: #cce5d6 !important;
        color: #0b5e30 !important;
        font-weight: 700 !important;
        border-right: 2px solid #107c41 !important;
    }

    /* Column header highlight when column is in selection */
    table.excel-table thead th.col-in-selection {
        background-color: #cce5d6 !important;
        color: #0b5e30 !important;
        font-weight: 700 !important;
        box-shadow: inset 0 -2px 0 #107c41 !important;
    }

    /* Bottom Status Bar Selection Statistics Badge */
    .excel-selection-bar {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #eaf5ea;
        border: 1px solid #81c784;
        border-radius: 5px;
        padding: 3px 10px;
        margin-right: 12px;
        font-size: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        animation: selBadgeFadeIn 0.15s ease-out;
        white-space: nowrap;
    }

    @keyframes selBadgeFadeIn {
        from { opacity: 0; transform: translateY(2px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .excel-selection-bar .sel-label {
        font-weight: 700;
        color: #1b5e20;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .excel-selection-bar .sel-val {
        font-weight: 700;
        color: #1b5e20;
    }

    .excel-selection-bar .sel-sum {
        font-weight: 800;
        font-size: 13px;
        color: #0b5e30;
        background: #c3e6cb;
        padding: 2px 7px;
        border-radius: 4px;
        letter-spacing: 0.2px;
    }

    .excel-selection-bar .sel-clear {
        border: none;
        background: transparent;
        color: #2e7d32;
        cursor: pointer;
        font-size: 15px;
        padding: 0 3px;
        line-height: 1;
        opacity: 0.65;
        transition: all 0.15s ease;
        margin-left: 2px;
    }

    .excel-selection-bar .sel-clear:hover {
        opacity: 1;
        color: #c62828;
        transform: scale(1.15);
    }

    /* Floating Real-Time Drag Tooltip */
    #excelDragTooltip {
        display: none;
        position: fixed;
        z-index: 10090;
        pointer-events: none;
        background: rgba(15, 23, 42, 0.94);
        color: #ffffff;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 4px 14px rgba(0,0,0,0.25);
        letter-spacing: 0.2px;
        white-space: nowrap;
        transform: translate(16px, 16px);
        border: 1px solid rgba(255,255,255,0.15);
    }

    /* Synchronized Top Horizontal Scrollbar for Windows */
    .excel-top-scrollbar-wrap {
        overflow-x: auto;
        overflow-y: hidden;
        height: 11px;
        background: #f1f3f4;
        border-top: 1px solid #d4d4d4;
        border-bottom: 1px solid #d4d4d4;
        scrollbar-width: thin;
        scrollbar-color: #107c41 #f1f3f4;
        display: none;
    }
    .excel-top-scrollbar-wrap::-webkit-scrollbar {
        height: 8px;
    }
    .excel-top-scrollbar-wrap::-webkit-scrollbar-track {
        background: #f1f3f4;
    }
    .excel-top-scrollbar-wrap::-webkit-scrollbar-thumb {
        background: #a0aec0;
        border-radius: 4px;
    }
    .excel-top-scrollbar-wrap::-webkit-scrollbar-thumb:hover {
        background: #107c41;
    }
    .excel-top-scrollbar-inner {
        height: 1px;
    }

    /* Quick Horizontal Scroll Nav Buttons in Ribbon */
    .excel-scroll-nav-group {
        display: inline-flex;
        align-items: center;
        background: rgba(0, 0, 0, 0.18);
        border-radius: 5px;
        padding: 2px 3px;
        gap: 3px;
    }
    .excel-scroll-nav-btn {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 9px;
        border-radius: 4px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
        line-height: 1.2;
        user-select: none;
    }
    .excel-scroll-nav-btn:hover {
        background: #ffffff;
        color: #107c41;
        border-color: #ffffff;
    }
</style>

{{-- Floating Live Drag Tooltip --}}
<div id="excelDragTooltip">
    <span id="excelDragTooltipText">Cəm: 0.00 ₼</span>
</div>

<script>
/**
 * ExcelRangeSelector: Reusable Microsoft Excel Engine
 * Handles:
 *  1. Multi-Cell Drag Selection & Real-Time Calculation
 *  2. Keyboard Navigation across cells (→, ←, ↑, ↓, Tab, Enter) with auto-scroll
 *  3. Horizontal Mouse Scroll (Shift + Wheel anywhere over table)
 *  4. Move / Pan table with Mouse (Middle-click drag OR Alt + Left-click drag)
 *  5. Top Horizontal Scrollbar synchronization
 */
class ExcelRangeSelector {
    constructor(config) {
        this.table = typeof config.table === 'string' ? document.getElementById(config.table) : config.table;
        this.tbody = typeof config.tbody === 'string' ? document.getElementById(config.tbody) : config.tbody;
        this.wrapper = typeof config.wrapper === 'string' ? document.getElementById(config.wrapper) : config.wrapper;
        this.statusBar = typeof config.statusBar === 'string' ? document.getElementById(config.statusBar) : config.statusBar;
        this.topScrollWrap = typeof config.topScrollWrap === 'string' ? document.getElementById(config.topScrollWrap) : config.topScrollWrap;
        this.topScrollInner = typeof config.topScrollInner === 'string' ? document.getElementById(config.topScrollInner) : config.topScrollInner;
        
        // Callbacks & Configs
        this.isNumericCell = config.isNumericCell || this.defaultIsNumericCell.bind(this);
        this.getCellNumericValue = config.getCellNumericValue || this.defaultGetCellNumericValue.bind(this);
        this.getRowTotal = config.getRowTotal || this.defaultGetRowTotal.bind(this);
        this.isDateCell = config.isDateCell || this.defaultIsDateCell.bind(this);
        this.emptyRowSelector = config.emptyRowSelector || '#emptyRow, #emptyRowPlaceholder';

        // Drag Selection State
        this.isMouseDown = false;
        this.isDragging = false;
        this.dragStartX = 0;
        this.dragStartY = 0;
        this.dragMode = 'cell'; // 'cell', 'row', 'col'
        this.anchorCoords = null; // { row, col }
        this.currentCoords = null; // { row, col }
        this.selectedRange = null; // { minR, maxR, minC, maxC }
        this.tooltip = document.getElementById('excelDragTooltip');
        this.tooltipText = document.getElementById('excelDragTooltipText');

        // Pan State (Move tool)
        this.isPanning = false;
        this.panStartX = 0;
        this.panStartY = 0;
        this.panScrollLeft = 0;
        this.panScrollTop = 0;

        if (this.table && this.tbody) {
            this.init();
        }
    }

    init() {
        this.bindEvents();
        this.bindKeyboardNavigation();
        this.bindWheelAndPan();
        this.initTopScrollbar();
    }

    getRows() {
        return Array.from(this.tbody.querySelectorAll('tr')).filter(tr => !tr.matches(this.emptyRowSelector));
    }

    getCellCoords(cell) {
        if (!cell) return null;
        const tr = cell.closest('tr');
        if (!tr) return null;

        // If in tbody
        if (tr.parentElement === this.tbody) {
            const rows = this.getRows();
            const rIdx = rows.indexOf(tr);
            const cIdx = Array.from(tr.children).indexOf(cell);
            if (rIdx === -1 || cIdx === -1) return null;
            return { row: rIdx, col: cIdx, isHeader: false };
        }

        // If in thead
        const thead = this.table.querySelector('thead');
        if (thead && thead.contains(tr)) {
            if (cell.getAttribute('colspan') && parseInt(cell.getAttribute('colspan')) > 1) {
                return null;
            }
            const cIdx = Array.from(tr.children).indexOf(cell);
            return { row: -1, col: cIdx, isHeader: true };
        }

        return null;
    }

    bindEvents() {
        const self = this;

        // Prevent native HTML5 text drag-and-drop
        this.table.addEventListener('dragstart', function (e) {
            e.preventDefault();
        });

        // Table Mousedown
        this.table.addEventListener('mousedown', function (e) {
            // Ignore if Middle click or Alt+click (handled by Pan tool)
            if (e.button === 1 || (e.altKey && e.button === 0)) {
                return;
            }

            if (e.button !== 0) return; // Only left click

            // Ignore action buttons, dropdowns, badges, inputs
            if (e.target.closest('button, a, input, select, textarea, .badge-images, .cell-created-indicator, .del-row-btn, .history-row-btn, .expense-autocomplete-menu, .expense-autocomplete-item')) {
                return;
            }

            const cell = e.target.closest('td, th');
            if (!cell) return;

            const coords = self.getCellCoords(cell);
            if (!coords) return;

            // Header selection
            if (coords.isHeader) {
                e.preventDefault();
                const rows = self.getRows();
                if (rows.length === 0) return;

                self.isMouseDown = true;
                self.isDragging = true;
                self.dragMode = 'col';
                self.anchorCoords = { row: 0, col: coords.col };
                self.currentCoords = { row: rows.length - 1, col: coords.col };
                self.table.classList.add('is-selecting');
                self.applySelection(0, rows.length - 1, coords.col, coords.col);
                return;
            }

            // Row header index selection (col 0 or row-idx)
            const isRowHeader = cell.classList.contains('row-idx-header') || cell.classList.contains('row-idx') || coords.col === 0;
            if (isRowHeader) {
                e.preventDefault();
                const tr = cell.closest('tr');
                const lastColIdx = tr.children.length - 1;

                self.isMouseDown = true;
                self.isDragging = true;
                self.dragMode = 'row';
                self.anchorCoords = { row: coords.row, col: 0 };
                self.currentCoords = { row: coords.row, col: lastColIdx };
                self.table.classList.add('is-selecting');
                self.applySelection(coords.row, coords.row, 0, lastColIdx);
                return;
            }

            // Normal Data Cell
            if (e.shiftKey && self.anchorCoords) {
                // Shift + Click: extend selection from existing anchor
                e.preventDefault();
                self.currentCoords = coords;
                self.applySelectionBetween(self.anchorCoords, self.currentCoords);
                return;
            }

            // Start potential drag
            self.isMouseDown = true;
            self.isDragging = false;
            self.dragMode = 'cell';
            self.dragStartX = e.clientX;
            self.dragStartY = e.clientY;
            self.anchorCoords = coords;
            self.currentCoords = coords;
        });

        // Document Mousemove
        document.addEventListener('mousemove', function (e) {
            if (!self.isMouseDown) return;

            const dist = Math.hypot(e.clientX - self.dragStartX, e.clientY - self.dragStartY);

            if (!self.isDragging) {
                if (dist >= 4) {
                    self.isDragging = true;
                    self.table.classList.add('is-selecting');
                    window.getSelection()?.removeAllRanges();
                    if (document.activeElement && document.activeElement.blur) {
                        document.activeElement.blur();
                    }
                } else {
                    return;
                }
            }

            // Dragging is active
            window.getSelection()?.removeAllRanges();
            self.handleEdgeAutoScroll(e);

            // Find cell under mouse
            const elemUnder = document.elementFromPoint(e.clientX, e.clientY);
            if (!elemUnder) return;

            const targetCell = elemUnder.closest('td, th');
            if (!targetCell || !self.table.contains(targetCell)) return;

            const targetCoords = self.getCellCoords(targetCell);
            if (!targetCoords) return;

            if (self.dragMode === 'row') {
                const tr = self.tbody.querySelector('tr');
                const maxCol = tr ? tr.children.length - 1 : 0;
                const targetRow = targetCoords.isHeader ? 0 : targetCoords.row;
                self.currentCoords = { row: targetRow, col: maxCol };
                self.applySelectionBetween(self.anchorCoords, self.currentCoords);
            } else if (self.dragMode === 'col') {
                const rows = self.getRows();
                const maxRow = rows.length - 1;
                self.currentCoords = { row: maxRow, col: targetCoords.col };
                self.applySelection(0, maxRow, Math.min(self.anchorCoords.col, targetCoords.col), Math.max(self.anchorCoords.col, targetCoords.col));
            } else {
                if (!targetCoords.isHeader) {
                    self.currentCoords = targetCoords;
                    self.applySelectionBetween(self.anchorCoords, self.currentCoords);
                }
            }

            // Update floating tooltip position
            if (self.tooltip && self.isDragging) {
                self.tooltip.style.left = e.clientX + 'px';
                self.tooltip.style.top = e.clientY + 'px';
            }
        });

        // Document Mouseup
        document.addEventListener('mouseup', function (e) {
            if (!self.isMouseDown) return;
            self.isMouseDown = false;
            self.table.classList.remove('is-selecting');

            if (self.tooltip) {
                self.tooltip.style.display = 'none';
            }

            if (!self.isDragging) {
                // Just a single click without dragging
                if (self.selectedRange && !e.shiftKey) {
                    const r = self.selectedRange;
                    // If previously had a multi-cell selection, clear it on single click
                    if (r.minR !== r.maxR || r.minC !== r.maxC) {
                        self.clearSelection();
                    }
                }
            } else {
                self.isDragging = false;
                self.calculateAndRenderStats();
            }
        });

        // Clear selection button click
        if (this.statusBar) {
            const clearBtn = this.statusBar.querySelector('.sel-clear');
            if (clearBtn) {
                clearBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.clearSelection();
                });
            }
        }

        // Global Keyboard shortcuts
        document.addEventListener('keydown', function (e) {
            // Escape: Deselect
            if (e.key === 'Escape') {
                self.clearSelection();
            }

            // Ctrl+C / Cmd+C: Copy selected cell matrix to clipboard
            if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'C')) {
                if (self.selectedRange && (!document.activeElement || document.activeElement.tagName !== 'INPUT')) {
                    if (self.selectedRange.minR !== self.selectedRange.maxR || self.selectedRange.minC !== self.selectedRange.maxC) {
                        self.copySelectionToClipboard();
                    }
                }
            }
        });

        // Click outside table to clear selection
        document.addEventListener('mousedown', function (e) {
            if (!self.selectedRange) return;
            if (self.table.contains(e.target)) return;
            if (self.statusBar && self.statusBar.contains(e.target)) return;
            if (e.target.closest('#excelTabsContainer, .branch-pill, .excel-ribbon, .expense-autocomplete-menu, .excel-scroll-nav-group')) return;

            self.clearSelection();
        });
    }

    /**
     * Keyboard Navigation Across Cells (→, ←, ↑, ↓, Tab, Enter)
     * Automatically scrolls the table horizontally as user presses → and ←!
     */
    bindKeyboardNavigation() {
        const self = this;

        this.table.addEventListener('keydown', function (e) {
            // Ignore autocomplete menu navigation
            const acMenu = document.getElementById('expenseAutocompleteDropdown');
            if (acMenu && acMenu.style.display === 'block') {
                return;
            }

            // If focus is inside an input, textarea, or button
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'BUTTON') {
                return;
            }

            const activeTd = document.activeElement ? document.activeElement.closest('td') : null;
            if (!activeTd || !self.table.contains(activeTd)) return;

            const coords = self.getCellCoords(activeTd);
            if (!coords || coords.isHeader) return;

            let dRow = 0;
            let dCol = 0;

            if (e.key === 'ArrowRight') {
                // If cursor is at the end of cell text, or text is selected
                if (self.isCaretAtEnd(activeTd)) {
                    e.preventDefault();
                    dCol = 1;
                }
            } else if (e.key === 'ArrowLeft') {
                // If cursor is at the start of cell text
                if (self.isCaretAtStart(activeTd)) {
                    e.preventDefault();
                    dCol = -1;
                }
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                dRow = 1;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                dRow = -1;
            } else if (e.key === 'Tab') {
                e.preventDefault();
                dCol = e.shiftKey ? -1 : 1;
            } else if (e.key === 'Enter') {
                e.preventDefault();
                dRow = e.shiftKey ? -1 : 1;
            }

            if (dRow !== 0 || dCol !== 0) {
                self.navigateToCell(coords.row, coords.col, dRow, dCol);
            }
        });
    }

    navigateToCell(currRow, currCol, dRow, dCol) {
        const rows = this.getRows();
        if (rows.length === 0) return;

        let targetRow = currRow + dRow;
        let targetCol = currCol + dCol;

        const tr = rows[currRow];
        if (!tr) return;
        const cells = Array.from(tr.children);

        if (dCol !== 0) {
            // Horizontal navigation
            // Column 0 is row index (#)
            if (targetCol < 1) {
                if (targetRow > 0) {
                    targetRow--;
                    const prevTr = rows[targetRow];
                    targetCol = prevTr.children.length - 1;
                } else {
                    targetCol = 1;
                }
            } else if (targetCol >= cells.length) {
                if (targetRow < rows.length - 1) {
                    targetRow++;
                    targetCol = 1;
                } else {
                    targetCol = cells.length - 1;
                }
            }
        }

        if (targetRow < 0) targetRow = 0;
        if (targetRow >= rows.length) targetRow = rows.length - 1;

        const targetTr = rows[targetRow];
        if (!targetTr) return;

        let targetCell = targetTr.children[targetCol];
        if (!targetCell) return;

        // Skip non-navigable cells like row-idx
        if (targetCell.classList.contains('row-idx') || targetCell.classList.contains('row-idx-header')) {
            targetCol = 1;
            targetCell = targetTr.children[targetCol] || targetCell;
        }

        // Scroll into view horizontally and vertically
        this.scrollCellIntoView(targetCell);

        // Focus cell
        targetCell.focus();

        // Place caret at end or select contents so next arrow key moves immediately
        const range = document.createRange();
        range.selectNodeContents(targetCell);
        const sel = window.getSelection();
        sel?.removeAllRanges();
        sel?.addRange(range);

        // Update selection highlight & stats
        this.anchorCoords = { row: targetRow, col: targetCol };
        this.currentCoords = { row: targetRow, col: targetCol };
        this.applySelection(targetRow, targetRow, targetCol, targetCol);
    }

    scrollCellIntoView(cell) {
        if (!cell || !this.wrapper) return;
        const cellRect = cell.getBoundingClientRect();
        const wrapRect = this.wrapper.getBoundingClientRect();

        // Horizontal scroll
        if (cellRect.right > wrapRect.right - 25) {
            this.wrapper.scrollLeft += (cellRect.right - wrapRect.right + 70);
        } else if (cellRect.left < wrapRect.left + 25) {
            this.wrapper.scrollLeft -= (wrapRect.left - cellRect.left + 70);
        }

        // Vertical scroll
        if (cellRect.bottom > wrapRect.bottom - 20) {
            this.wrapper.scrollTop += (cellRect.bottom - wrapRect.bottom + 40);
        } else if (cellRect.top < wrapRect.top + 70) {
            this.wrapper.scrollTop -= (wrapRect.top + 70 - cellRect.top);
        }
    }

    isCaretAtEnd(element) {
        const sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return true;
        const range = sel.getRangeAt(0);
        if (!element.contains(range.commonAncestorContainer)) return true;
        if (!range.collapsed) return true;

        const preCaretRange = range.cloneRange();
        preCaretRange.selectNodeContents(element);
        preCaretRange.setStart(range.endContainer, range.endOffset);
        return preCaretRange.toString().length === 0;
    }

    isCaretAtStart(element) {
        const sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return true;
        const range = sel.getRangeAt(0);
        if (!element.contains(range.commonAncestorContainer)) return true;
        if (!range.collapsed) return true;

        const preCaretRange = range.cloneRange();
        preCaretRange.selectNodeContents(element);
        preCaretRange.setEnd(range.startContainer, range.startOffset);
        return preCaretRange.toString().length === 0;
    }

    /**
     * Mouse Horizontal Scrolling (Shift + Wheel) & Pan / Move Tool (Alt + Drag or Middle Click)
     */
    bindWheelAndPan() {
        const self = this;
        if (!this.wrapper) return;

        // 1. Shift + Mouse Wheel for horizontal scroll anywhere over table
        this.wrapper.addEventListener('wheel', function (e) {
            if (e.shiftKey) {
                e.preventDefault();
                self.wrapper.scrollLeft += (e.deltaY * 1.5);
            } else if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
                // Native horizontal wheel / trackpad
                self.wrapper.scrollLeft += e.deltaX;
            }
        }, { passive: false });

        // 2. Pan / Move table: Middle click OR Alt + Left click drag
        this.table.addEventListener('mousedown', function (e) {
            if (e.button === 1 || (e.altKey && e.button === 0)) {
                e.preventDefault();
                self.isPanning = true;
                self.panStartX = e.clientX;
                self.panStartY = e.clientY;
                self.panScrollLeft = self.wrapper.scrollLeft;
                self.panScrollTop = self.wrapper.scrollTop;
                self.wrapper.classList.add('is-panning');
            }
        });

        document.addEventListener('mousemove', function (e) {
            if (!self.isPanning) return;
            e.preventDefault();
            const dx = e.clientX - self.panStartX;
            const dy = e.clientY - self.panStartY;
            self.wrapper.scrollLeft = self.panScrollLeft - dx;
            self.wrapper.scrollTop = self.panScrollTop - dy;
        });

        document.addEventListener('mouseup', function (e) {
            if (self.isPanning) {
                self.isPanning = false;
                self.wrapper.classList.remove('is-panning');
            }
        });
    }

    /**
     * Top Horizontal Scrollbar Synchronization (Mirrors bottom scrollbar right at top of table)
     */
    initTopScrollbar() {
        if (!this.topScrollWrap || !this.topScrollInner || !this.wrapper || !this.table) return;

        const topWrap = this.topScrollWrap;
        const topInner = this.topScrollInner;
        const wrapper = this.wrapper;
        const table = this.table;

        function syncDimensions() {
            const scrollW = table.scrollWidth;
            topInner.style.width = scrollW + 'px';
            if (scrollW > wrapper.clientWidth + 10) {
                topWrap.style.display = 'block';
            } else {
                topWrap.style.display = 'none';
            }
        }

        syncDimensions();
        window.addEventListener('resize', syncDimensions);
        setTimeout(syncDimensions, 300);

        let isSyncingTop = false;
        let isSyncingBottom = false;

        topWrap.addEventListener('scroll', function () {
            if (isSyncingTop) {
                isSyncingTop = false;
                return;
            }
            isSyncingBottom = true;
            wrapper.scrollLeft = topWrap.scrollLeft;
        });

        wrapper.addEventListener('scroll', function () {
            if (isSyncingBottom) {
                isSyncingBottom = false;
                return;
            }
            isSyncingTop = true;
            topWrap.scrollLeft = wrapper.scrollLeft;
        });

        if (window.ResizeObserver) {
            const ro = new ResizeObserver(() => syncDimensions());
            ro.observe(table);
        }
    }

    handleEdgeAutoScroll(e) {
        if (!this.wrapper) return;
        const rect = this.wrapper.getBoundingClientRect();
        const edgeThreshold = 35;
        const scrollSpeed = 16;

        if (e.clientX > rect.right - edgeThreshold) {
            this.wrapper.scrollLeft += scrollSpeed;
        } else if (e.clientX < rect.left + edgeThreshold) {
            this.wrapper.scrollLeft -= scrollSpeed;
        }

        if (e.clientY > rect.bottom - edgeThreshold) {
            this.wrapper.scrollTop += scrollSpeed;
        } else if (e.clientY < rect.top + edgeThreshold) {
            this.wrapper.scrollTop -= scrollSpeed;
        }
    }

    applySelectionBetween(c1, c2) {
        if (!c1 || !c2) return;
        const minR = Math.min(c1.row, c2.row);
        const maxR = Math.max(c1.row, c2.row);
        const minC = Math.min(c1.col, c2.col);
        const maxC = Math.max(c1.col, c2.col);
        this.applySelection(minR, maxR, minC, maxC);
    }

    applySelection(minR, maxR, minC, maxC) {
        this.selectedRange = { minR, maxR, minC, maxC };
        const rows = this.getRows();

        // 1. Remove previous selection classes
        this.tbody.querySelectorAll('.excel-cell-selected, .sel-border-top, .sel-border-bottom, .sel-border-left, .sel-border-right, .sel-anchor').forEach(el => {
            el.classList.remove('excel-cell-selected', 'sel-border-top', 'sel-border-bottom', 'sel-border-left', 'sel-border-right', 'sel-anchor');
        });
        this.tbody.querySelectorAll('tr.row-in-selection').forEach(tr => tr.classList.remove('row-in-selection'));
        this.table.querySelectorAll('thead th.col-in-selection').forEach(th => th.classList.remove('col-in-selection'));

        // 2. Highlight column headers in thead
        const theadTrs = this.table.querySelectorAll('thead tr');
        theadTrs.forEach(tr => {
            const ths = Array.from(tr.children);
            for (let c = minC; c <= maxC; c++) {
                if (ths[c]) ths[c].classList.add('col-in-selection');
            }
        });

        // 3. Highlight cells and row headers in tbody
        for (let r = minR; r <= maxR; r++) {
            const tr = rows[r];
            if (!tr) continue;
            tr.classList.add('row-in-selection');

            const cells = Array.from(tr.children);
            for (let c = minC; c <= maxC; c++) {
                const cell = cells[c];
                if (!cell) continue;

                cell.classList.add('excel-cell-selected');

                if (r === minR) cell.classList.add('sel-border-top');
                if (r === maxR) cell.classList.add('sel-border-bottom');
                if (c === minC) cell.classList.add('sel-border-left');
                if (c === maxC) cell.classList.add('sel-border-right');

                // Mark anchor cell (first clicked cell)
                if (this.anchorCoords && r === this.anchorCoords.row && c === this.anchorCoords.col && (minR !== maxR || minC !== maxC)) {
                    cell.classList.add('sel-anchor');
                }
            }
        }

        // 4. Calculate live statistics
        this.calculateAndRenderStats();
    }

    calculateAndRenderStats() {
        if (!this.selectedRange) {
            this.hideStats();
            return;
        }

        const { minR, maxR, minC, maxC } = this.selectedRange;
        const rows = this.getRows();
        if (rows.length === 0) {
            this.hideStats();
            return;
        }

        const selectedRows = [];
        for (let r = minR; r <= maxR; r++) {
            if (rows[r]) selectedRows.push(rows[r]);
        }

        const selectedCells = [];
        selectedRows.forEach(tr => {
            const cells = Array.from(tr.children);
            for (let c = minC; c <= maxC; c++) {
                if (cells[c]) selectedCells.push(cells[c]);
            }
        });

        if (selectedCells.length === 0) {
            this.hideStats();
            return;
        }

        // Check if whole row selection
        const firstTr = selectedRows[0];
        const isWholeRow = (this.dragMode === 'row') || (minC === 0 && maxC >= firstTr.children.length - 2);

        // Filter numeric cells
        const numericCells = selectedCells.filter(cell => this.isNumericCell(cell));

        let sum = 0;
        let count = 0;
        let avg = 0;
        let isDateOnly = false;
        let labelText = 'Seçim:';

        if (isWholeRow) {
            isDateOnly = true;
            selectedRows.forEach(tr => {
                sum += this.getRowTotal(tr);
            });
            count = selectedRows.length;
            avg = count > 0 ? sum / count : 0;
            labelText = 'Seçilən sətirlər:';
        } else if (numericCells.length > 0) {
            numericCells.forEach(cell => {
                const val = this.getCellNumericValue(cell);
                sum += val;
            });
            count = numericCells.length;
            avg = count > 0 ? sum / count : 0;
            labelText = 'Seçim:';
        } else {
            isDateOnly = true;
            selectedRows.forEach(tr => {
                sum += this.getRowTotal(tr);
            });
            count = selectedRows.length;
            avg = count > 0 ? sum / count : 0;
            labelText = (minC === 0 && maxC === 0) ? 'Seçilən sətirlər:' : 'Seçilən tarixlər:';
        }

        this.renderStats({
            sum,
            count,
            avg,
            isDateOnly,
            labelText,
            rowCount: selectedRows.length,
            cellCount: selectedCells.length
        });
    }

    renderStats(data) {
        const formattedSum = this.formatMoney(data.sum);
        const formattedAvg = this.formatMoney(data.avg);

        // Update Bottom Status Bar
        if (this.statusBar) {
            this.statusBar.style.display = 'inline-flex';

            const labelEl = this.statusBar.querySelector('#selLabelText') || this.statusBar.querySelector('.sel-label span');
            if (labelEl) labelEl.textContent = data.labelText;

            const countEl = this.statusBar.querySelector('#selCountVal');
            if (countEl) {
                countEl.textContent = data.isDateOnly ? `${data.rowCount} gün` : `${data.count}`;
            }

            const avgEl = this.statusBar.querySelector('#selAvgVal');
            if (avgEl) avgEl.textContent = formattedAvg;

            const sumEl = this.statusBar.querySelector('#selSumVal');
            if (sumEl) sumEl.textContent = formattedSum;
        }

        // Update Floating Drag Tooltip
        if (this.tooltip && this.isDragging) {
            this.tooltip.style.display = 'block';
            if (this.tooltipText) {
                if (data.isDateOnly) {
                    this.tooltipText.innerHTML = `<span style="color: #86efac; font-weight: 700;">${data.labelText}</span> ${data.rowCount} gün &nbsp;|&nbsp; <strong>Cəm: ${formattedSum}</strong>`;
                } else {
                    this.tooltipText.innerHTML = `<strong>Cəm: ${formattedSum}</strong> &nbsp;|&nbsp; <span style="opacity: 0.85;">Say: ${data.count}</span>`;
                }
            }
        }
    }

    hideStats() {
        if (this.statusBar) {
            this.statusBar.style.display = 'none';
        }
        if (this.tooltip) {
            this.tooltip.style.display = 'none';
        }
    }

    clearSelection() {
        this.selectedRange = null;
        this.anchorCoords = null;
        this.currentCoords = null;
        this.isDragging = false;
        this.isMouseDown = false;

        this.tbody.querySelectorAll('.excel-cell-selected, .sel-border-top, .sel-border-bottom, .sel-border-left, .sel-border-right, .sel-anchor').forEach(el => {
            el.classList.remove('excel-cell-selected', 'sel-border-top', 'sel-border-bottom', 'sel-border-left', 'sel-border-right', 'sel-anchor');
        });
        this.tbody.querySelectorAll('tr.row-in-selection').forEach(tr => tr.classList.remove('row-in-selection'));
        this.table.querySelectorAll('thead th.col-in-selection').forEach(th => th.classList.remove('col-in-selection'));

        this.hideStats();
    }

    copySelectionToClipboard() {
        if (!this.selectedRange) return;
        const { minR, maxR, minC, maxC } = this.selectedRange;
        const rows = this.getRows();

        const lines = [];
        let totalCellsCopied = 0;

        for (let r = minR; r <= maxR; r++) {
            const tr = rows[r];
            if (!tr) continue;
            const cells = Array.from(tr.children);
            const lineValues = [];
            for (let c = minC; c <= maxC; c++) {
                const cell = cells[c];
                lineValues.push(cell ? cell.innerText.replace(/[\r\n\t]/g, ' ').trim() : '');
                totalCellsCopied++;
            }
            lines.push(lineValues.join('\t'));
        }

        const tsvText = lines.join('\n');
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(tsvText).then(() => {
                if (typeof showToast === 'function') {
                    showToast(`📋 ${totalCellsCopied} xana kopyalandı!`, '#107c41');
                }
            }).catch(() => {});
        }
    }

    formatMoney(val) {
        if (isNaN(val) || val === null || val === undefined) val = 0;
        return Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₼';
    }

    defaultIsNumericCell(cell) {
        const field = cell.dataset.field;
        if (field === 'date' || field === 'time' || field === 'customer_phone' || field === 'game_name' || 
            field === 'title' || field === 'note' || field === 'discount_note' || field === 'customer_name' || 
            field === 'individual_discounts_display' || field === 'individual_discounts' || cell.classList.contains('ind-discounts-cell') ||
            field === 'classification' || cell.classList.contains('row-idx') || cell.classList.contains('row-idx-header')) {
            return false;
        }

        if (cell.classList.contains('row-total-cell') || cell.classList.contains('total-price-cell') ||
            field === 'amount_cash' || field === 'amount_card' || field === 'cash_amount' || field === 'card_amount' ||
            field === 'total_price' || field === 'price_per_person' || field === 'player_count') {
            return true;
        }

        const text = cell.innerText.trim();
        if (!text) return false;
        if (/^\d{1,4}[.\/-]\d{1,2}[.\/-]\d{2,4}$/.test(text)) return false;
        if (/^\d{1,2}:\d{2}(:\d{2})?$/.test(text)) return false;

        const cleaned = text.replace(/[₼AZN\s,]/g, '');
        return /^[+-]?(\d+(\.\d*)?|\.\d+)$/.test(cleaned);
    }

    defaultGetCellNumericValue(cell) {
        const text = cell.innerText.trim();
        if (!text) return 0;
        let cleaned = text.replace(/[₼AZN\s]/g, '');
        if (cleaned.includes(',') && cleaned.includes('.')) {
            cleaned = cleaned.replace(/,/g, '');
        } else if (cleaned.includes(',') && !cleaned.includes('.')) {
            cleaned = cleaned.replace(',', '.');
        }
        cleaned = cleaned.replace(/[^0-9.-]/g, '');
        const num = parseFloat(cleaned);
        return isNaN(num) ? 0 : num;
    }

    defaultGetRowTotal(tr) {
        const totalCell = tr.querySelector('.row-total-cell, .total-price-cell, [data-field="total_price"]');
        if (totalCell) {
            return this.getCellNumericValue(totalCell);
        }
        const cashCell = tr.querySelector('[data-field="amount_cash"], [data-field="cash_amount"]');
        const cardCell = tr.querySelector('[data-field="amount_card"], [data-field="card_amount"]');
        const cash = cashCell ? this.getCellNumericValue(cashCell) : 0;
        const card = cardCell ? this.getCellNumericValue(cardCell) : 0;
        return cash + card;
    }

    defaultIsDateCell(cell) {
        return cell.dataset.field === 'date' || cell.classList.contains('date-cell');
    }
}

/**
 * Global helper to scroll the active table wrapper horizontally
 */
window.scrollTableHorizontally = function(delta) {
    const wrapper = document.getElementById('excelWrapper') || document.getElementById('excelTableWrapper');
    if (wrapper) {
        wrapper.scrollBy({ left: delta, behavior: 'smooth' });
    }
};
</script>
