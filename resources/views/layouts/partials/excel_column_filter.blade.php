{{-- Excel Column AutoFilter & Sorting Engine --}}
<style>
    /* Column Header Inner Layout with Filter Trigger */
    .excel-th-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 4px;
        width: 100%;
        position: relative;
    }

    .excel-th-title {
        flex: 1 1 auto;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Filter Trigger Button on Header */
    .excel-col-filter-btn {
        background: transparent;
        border: 1px solid transparent;
        color: #64748b;
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 3px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        line-height: 1;
        opacity: 0.65;
        flex-shrink: 0;
    }

    .excel-col-filter-btn:hover {
        background: rgba(0, 0, 0, 0.08);
        color: #107c41;
        opacity: 1;
        border-color: rgba(0, 0, 0, 0.15);
    }

    /* When this column has an active filter applied */
    .excel-col-filter-btn.is-filtered {
        background: #107c41 !important;
        color: #ffffff !important;
        opacity: 1 !important;
        border-color: #0b5e30 !important;
        box-shadow: 0 1px 4px rgba(16, 124, 65, 0.4) !important;
    }

    .excel-col-filter-btn.is-filtered svg {
        filter: drop-shadow(0 1px 1px rgba(0,0,0,0.2));
    }

    /* Global Clear Filters Ribbon Button */
    .excel-btn-clear-all-filters {
        background: #fee2e2 !important;
        color: #b91c1c !important;
        border: 1px solid #f87171 !important;
        font-weight: 700 !important;
        animation: pulseFilterBtn 1.5s infinite;
    }
    .excel-btn-clear-all-filters:hover {
        background: #fca5a5 !important;
        color: #991b1b !important;
    }
    @keyframes pulseFilterBtn {
        0% { transform: scale(1); }
        50% { transform: scale(1.03); }
        100% { transform: scale(1); }
    }

    /* Filter Dropdown Popup (Authentic Microsoft Excel Look & Feel) */
    .excel-filter-popup {
        position: fixed;
        z-index: 10070;
        background: #ffffff;
        border: 1px solid #107c41;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22);
        width: 250px;
        max-width: 90vw;
        display: none;
        flex-direction: column;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 12px;
        color: #1e293b;
        animation: filterPopupIn 0.12s ease-out;
        overflow: hidden;
    }

    @keyframes filterPopupIn {
        from { opacity: 0; transform: translateY(-4px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .excel-filter-header {
        background: #f8fafc;
        padding: 9px 12px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .excel-filter-col-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .excel-filter-close-btn {
        background: transparent;
        border: none;
        color: #64748b;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        padding: 0 2px;
        transition: color 0.15s;
    }
    .excel-filter-close-btn:hover {
        color: #0f172a;
    }

    /* Sort Section */
    .excel-filter-sort-section {
        padding: 4px;
        display: flex;
        flex-direction: column;
        gap: 2px;
        background: #ffffff;
    }

    .excel-filter-sort-btn {
        background: transparent;
        border: none;
        padding: 6px 10px;
        border-radius: 5px;
        text-align: left;
        font-size: 12px;
        font-weight: 500;
        color: #334155;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.12s ease;
    }

    .excel-filter-sort-btn:hover {
        background: #f1f5f9;
        color: #107c41;
        font-weight: 600;
    }

    .excel-filter-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 2px 0;
    }

    /* Search Box */
    .excel-filter-search-box {
        padding: 6px 10px;
        position: relative;
        display: flex;
        align-items: center;
        background: #ffffff;
    }

    .excel-filter-search-box svg {
        position: absolute;
        left: 18px;
        color: #94a3b8;
    }

    .excel-filter-search-input {
        width: 100%;
        padding: 5px 8px 5px 26px;
        font-size: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        outline: none;
        transition: border-color 0.15s;
    }
    .excel-filter-search-input:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 2px rgba(16, 124, 65, 0.15);
    }

    /* Checklist */
    .excel-filter-checklist-header {
        padding: 4px 10px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }

    .excel-filter-checklist-body {
        max-height: 180px;
        overflow-y: auto;
        padding: 4px 10px;
        display: flex;
        flex-direction: column;
        gap: 3px;
        background: #ffffff;
    }

    .excel-filter-checklist-body::-webkit-scrollbar {
        width: 6px;
    }
    .excel-filter-checklist-body::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .excel-filter-checklist-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .excel-filter-item {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 3px 4px;
        border-radius: 4px;
        cursor: pointer;
        user-select: none;
        font-size: 12px;
        color: #334155;
        transition: background 0.1s;
    }
    .excel-filter-item:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .excel-filter-item input[type="checkbox"] {
        accent-color: #107c41;
        cursor: pointer;
        width: 13px;
        height: 13px;
    }
    .excel-filter-item-text {
        flex: 1 1 auto;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .excel-filter-item-count {
        font-size: 10px;
        color: #94a3b8;
        font-weight: 500;
        margin-left: 4px;
    }

    /* Footer Buttons */
    .excel-filter-footer {
        padding: 8px 10px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .excel-filter-btn-clear {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.12s ease;
    }
    .excel-filter-btn-clear:hover {
        background: #f1f5f9;
        color: #dc2626;
        border-color: #f87171;
    }

    .excel-filter-btn-apply {
        background: #107c41;
        border: 1px solid #0b5e30;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 14px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.12s ease;
    }
    .excel-filter-btn-apply:hover {
        background: #0b5e30;
    }

    /* Filter Empty State Message in Table */
    .excel-no-filter-match {
        text-align: center;
        padding: 30px 16px;
        color: #64748b;
        font-size: 13px;
        background: #ffffff;
    }
</style>

{{-- Reusable Filter Dropdown Popup (Single instance shared across table headers) --}}
<div id="excelColumnFilterPopup" class="excel-filter-popup">
    <div class="excel-filter-header">
        <span class="excel-filter-col-title">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.5">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            <span id="excelFilterColName">Sütun</span>
        </span>
        <button type="button" class="excel-filter-close-btn" id="excelFilterCloseBtn" title="Bağla">&times;</button>
    </div>

    {{-- Sort Options --}}
    <div class="excel-filter-sort-section">
        <button type="button" class="excel-filter-sort-btn" id="excelFilterSortAsc">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m3 8 4-4 4 4"></path><path d="M7 4v16"></path><path d="M15 4h5"></path><path d="M15 10h7"></path><path d="M15 16h9"></path></svg>
            <span id="excelFilterSortAscLabel">A-dan Z-yə Sırala (Artan)</span>
        </button>
        <button type="button" class="excel-filter-sort-btn" id="excelFilterSortDesc">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m3 16 4 4 4-4"></path><path d="M7 20V4"></path><path d="M15 4h9"></path><path d="M15 10h7"></path><path d="M15 16h5"></path></svg>
            <span id="excelFilterSortDescLabel">Z-dən A-ya Sırala (Azalan)</span>
        </button>
    </div>

    <div class="excel-filter-divider"></div>

    {{-- Search Input --}}
    <div class="excel-filter-search-box">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <input type="text" class="excel-filter-search-input" id="excelFilterSearchInput" placeholder="Dəyərləri axtar..." autocomplete="off">
    </div>

    {{-- Select All Checkbox --}}
    <div class="excel-filter-checklist-header">
        <label class="excel-filter-item" style="font-weight: 600;">
            <input type="checkbox" id="excelFilterSelectAll" checked>
            <span>(Hamısını seç)</span>
        </label>
    </div>

    {{-- Checklist of Unique Values --}}
    <div class="excel-filter-checklist-body" id="excelFilterChecklistBody">
        {{-- Populated dynamically --}}
    </div>

    {{-- Footer Actions --}}
    <div class="excel-filter-footer">
        <button type="button" class="excel-filter-btn-clear" id="excelFilterClearBtn">Filtri Sıfırla</button>
        <button type="button" class="excel-filter-btn-apply" id="excelFilterApplyBtn">Tətbiq Et</button>
    </div>
</div>

<script>
/**
 * ExcelColumnFilter: Client-Side Microsoft Excel AutoFilter & Sorting Engine
 */
class ExcelColumnFilter {
    constructor(config) {
        this.table = typeof config.table === 'string' ? document.getElementById(config.table) : config.table;
        this.tbody = typeof config.tbody === 'string' ? document.getElementById(config.tbody) : config.tbody;
        this.headerRowSelector = config.headerRowSelector || 'thead tr:last-child';
        this.clearAllBtnContainer = typeof config.clearAllBtnContainer === 'string' ? document.getElementById(config.clearAllBtnContainer) : config.clearAllBtnContainer;
        this.onFilterChanged = config.onFilterChanged || function() {};
        this.emptyRowSelector = config.emptyRowSelector || '#emptyRow, #emptyRowPlaceholder, .excel-no-filter-match';

        // Filter State: { [colIdx]: Set of allowed string values }
        this.activeFilters = {};
        this.currentColIdx = null;

        // Popup elements
        this.popup = document.getElementById('excelColumnFilterPopup');
        this.colNameEl = document.getElementById('excelFilterColName');
        this.searchInput = document.getElementById('excelFilterSearchInput');
        this.selectAllCheckbox = document.getElementById('excelFilterSelectAll');
        this.checklistBody = document.getElementById('excelFilterChecklistBody');
        this.sortAscBtn = document.getElementById('excelFilterSortAsc');
        this.sortDescBtn = document.getElementById('excelFilterSortDesc');
        this.sortAscLabel = document.getElementById('excelFilterSortAscLabel');
        this.sortDescLabel = document.getElementById('excelFilterSortDescLabel');
        this.clearColBtn = document.getElementById('excelFilterClearBtn');
        this.applyBtn = document.getElementById('excelFilterApplyBtn');
        this.closeBtn = document.getElementById('excelFilterCloseBtn');

        if (this.table && this.tbody && this.popup) {
            this.init();
        }
    }

    init() {
        this.injectHeaderFilterButtons();
        this.bindPopupEvents();
        this.injectClearAllFiltersButton();
    }

    getRows() {
        return Array.from(this.tbody.querySelectorAll('tr')).filter(tr => !tr.matches(this.emptyRowSelector));
    }

    injectHeaderFilterButtons() {
        const headerTr = this.table.querySelector(this.headerRowSelector);
        if (!headerTr) return;

        const ths = Array.from(headerTr.children);
        ths.forEach((th, colIdx) => {
            // Skip action columns or row index (#)
            if (th.classList.contains('row-idx-header') || th.classList.contains('row-idx') || th.textContent.trim() === '#' || th.dataset.noFilter === 'true') {
                return;
            }

            if (th.querySelector('.excel-col-filter-btn')) return;

            const originalContent = th.innerHTML;
            th.innerHTML = '';

            const inner = document.createElement('div');
            inner.className = 'excel-th-inner';

            const titleSpan = document.createElement('span');
            titleSpan.className = 'excel-th-title';
            titleSpan.innerHTML = originalContent;
            inner.appendChild(titleSpan);

            const filterBtn = document.createElement('button');
            filterBtn.type = 'button';
            filterBtn.className = 'excel-col-filter-btn';
            filterBtn.title = 'Filtr və Sıralama';
            filterBtn.setAttribute('data-col-idx', colIdx);
            filterBtn.innerHTML = `
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 22 3"></polygon>
                </svg>
            `;

            const self = this;
            filterBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.openFilterPopup(colIdx, filterBtn, titleSpan.innerText.trim());
            });

            inner.appendChild(filterBtn);
            th.appendChild(inner);
        });
    }

    injectClearAllFiltersButton() {
        if (!this.clearAllBtnContainer) return;
        if (this.clearAllBtnContainer.querySelector('#excelClearAllFiltersBtn')) return;

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.id = 'excelClearAllFiltersBtn';
        btn.className = 'excel-btn excel-btn-clear-all-filters';
        btn.style.display = 'none';
        btn.title = 'Tətbiq edilmiş bütün filtrləri sıfırla və bütün sətirləri göstər';
        btn.innerHTML = `
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            <span id="excelClearAllFiltersText">Filtrləri Sıfırla</span>
        `;

        const self = this;
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            self.clearAllFilters();
        });

        this.clearAllBtnContainer.appendChild(btn);
    }

    openFilterPopup(colIdx, buttonEl, colTitle) {
        this.currentColIdx = colIdx;
        this.colNameEl.textContent = colTitle || `Sütun ${colIdx}`;

        // Get unique values from all rows (including currently hidden by other filters)
        const rows = this.getRows();
        const valueCounts = new Map();

        rows.forEach(tr => {
            const cell = tr.children[colIdx];
            let val = cell ? cell.innerText.replace(/[\r\n\t]/g, ' ').trim() : '';
            if (val === '') val = '(Boş)';
            valueCounts.set(val, (valueCounts.get(val) || 0) + 1);
        });

        // Determine type for sorting label (number, date, text)
        const isNumeric = this.detectColumnType(rows, colIdx);
        if (isNumeric === 'number') {
            this.sortAscLabel.textContent = 'Kiçikdən böyüyə sırala (Artan)';
            this.sortDescLabel.textContent = 'Böyükdən kiçiyə sırala (Azalan)';
        } else if (isNumeric === 'date') {
            this.sortAscLabel.textContent = 'Köhnədən yeniyə sırala (Artan)';
            this.sortDescLabel.textContent = 'Yenidən köhnəyə sırala (Azalan)';
        } else {
            this.sortAscLabel.textContent = 'A-dan Z-yə sırala (Artan)';
            this.sortDescLabel.textContent = 'Z-dən A-ya sırala (Azalan)';
        }

        // Render checklist
        this.renderChecklist(valueCounts, colIdx);

        // Position popup
        const rect = buttonEl.getBoundingClientRect();
        this.popup.style.display = 'flex';

        let left = rect.left;
        const popupWidth = 250;
        if (left + popupWidth > window.innerWidth - 15) {
            left = window.innerWidth - popupWidth - 15;
        }
        if (left < 10) left = 10;

        let top = rect.bottom + 4;
        if (top + 340 > window.innerHeight) {
            top = Math.max(10, rect.top - 340);
        }

        this.popup.style.left = left + 'px';
        this.popup.style.top = top + 'px';

        // Clear search
        this.searchInput.value = '';
        this.searchInput.focus();
    }

    closeFilterPopup() {
        this.popup.style.display = 'none';
        this.currentColIdx = null;
    }

    renderChecklist(valueCounts, colIdx) {
        this.checklistBody.innerHTML = '';
        const sortedValues = Array.from(valueCounts.keys()).sort((a, b) => {
            if (a === '(Boş)') return 1;
            if (b === '(Boş)') return -1;
            return a.localeCompare(b, 'az', { numeric: true, sensitivity: 'base' });
        });

        const activeSet = this.activeFilters[colIdx];
        let allChecked = true;

        sortedValues.forEach(val => {
            const isChecked = activeSet ? activeSet.has(val) : true;
            if (!isChecked) allChecked = false;

            const label = document.createElement('label');
            label.className = 'excel-filter-item';
            label.setAttribute('data-val', val.toLowerCase());

            const chk = document.createElement('input');
            chk.type = 'checkbox';
            chk.value = val;
            chk.checked = isChecked;

            const spanText = document.createElement('span');
            spanText.className = 'excel-filter-item-text';
            spanText.textContent = val;

            const spanCount = document.createElement('span');
            spanCount.className = 'excel-filter-item-count';
            spanCount.textContent = `(${valueCounts.get(val)})`;

            label.appendChild(chk);
            label.appendChild(spanText);
            label.appendChild(spanCount);
            this.checklistBody.appendChild(label);
        });

        this.selectAllCheckbox.checked = allChecked;
    }

    bindPopupEvents() {
        const self = this;

        // Close button
        this.closeBtn.addEventListener('click', () => self.closeFilterPopup());

        // Close on Esc
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && self.popup.style.display === 'flex') {
                self.closeFilterPopup();
            }
        });

        // Click outside popup to close
        document.addEventListener('mousedown', (e) => {
            if (self.popup.style.display === 'flex') {
                if (!self.popup.contains(e.target) && !e.target.closest('.excel-col-filter-btn')) {
                    self.closeFilterPopup();
                }
            }
        });

        // Search in checklist
        this.searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            const items = self.checklistBody.querySelectorAll('.excel-filter-item');
            items.forEach(it => {
                const val = it.getAttribute('data-val') || '';
                it.style.display = (q === '' || val.includes(q)) ? 'flex' : 'none';
            });
        });

        // Select All Checkbox
        this.selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            const items = self.checklistBody.querySelectorAll('.excel-filter-item');
            items.forEach(it => {
                if (it.style.display !== 'none') {
                    const chk = it.querySelector('input[type="checkbox"]');
                    if (chk) chk.checked = isChecked;
                }
            });
        });

        // Clear Column Filter
        this.clearColBtn.addEventListener('click', function() {
            if (self.currentColIdx === null) return;
            delete self.activeFilters[self.currentColIdx];
            self.updateColumnFilterButtonState(self.currentColIdx, false);
            self.applyFilters();
            self.closeFilterPopup();
        });

        // Apply Column Filter
        this.applyBtn.addEventListener('click', function() {
            if (self.currentColIdx === null) return;

            const chks = self.checklistBody.querySelectorAll('input[type="checkbox"]');
            const totalCount = chks.length;
            const checkedVals = new Set();

            chks.forEach(c => {
                if (c.checked) checkedVals.add(c.value);
            });

            if (checkedVals.size === totalCount) {
                // All checked: filter is not needed for this column
                delete self.activeFilters[self.currentColIdx];
                self.updateColumnFilterButtonState(self.currentColIdx, false);
            } else {
                self.activeFilters[self.currentColIdx] = checkedVals;
                self.updateColumnFilterButtonState(self.currentColIdx, true);
            }

            self.applyFilters();
            self.closeFilterPopup();
        });

        // Sort Ascending
        this.sortAscBtn.addEventListener('click', function() {
            if (self.currentColIdx === null) return;
            self.sortRows(self.currentColIdx, 'asc');
            self.closeFilterPopup();
        });

        // Sort Descending
        this.sortDescBtn.addEventListener('click', function() {
            if (self.currentColIdx === null) return;
            self.sortRows(self.currentColIdx, 'desc');
            self.closeFilterPopup();
        });
    }

    updateColumnFilterButtonState(colIdx, isFiltered) {
        const headerTr = this.table.querySelector(this.headerRowSelector);
        if (!headerTr) return;
        const th = headerTr.children[colIdx];
        if (!th) return;
        const btn = th.querySelector('.excel-col-filter-btn');
        if (!btn) return;

        if (isFiltered) {
            btn.classList.add('is-filtered');
            btn.title = 'Aktiv Filtr! (Sıfırlamaq üçün klikləyin)';
        } else {
            btn.classList.remove('is-filtered');
            btn.title = 'Filtr və Sıralama';
        }
    }

    applyFilters() {
        const rows = this.getRows();
        const filterCols = Object.keys(this.activeFilters).map(Number);
        let visibleCount = 0;

        rows.forEach(tr => {
            let matches = true;
            for (const cIdx of filterCols) {
                const allowed = this.activeFilters[cIdx];
                const cell = tr.children[cIdx];
                let text = cell ? cell.innerText.replace(/[\r\n\t]/g, ' ').trim() : '';
                if (text === '') text = '(Boş)';

                if (!allowed.has(text)) {
                    matches = false;
                    break;
                }
            }

            if (matches) {
                tr.style.display = '';
                visibleCount++;
            } else {
                tr.style.display = 'none';
            }
        });

        // Show/hide empty match banner
        let emptyBanner = this.tbody.querySelector('.excel-no-filter-match');
        if (visibleCount === 0 && rows.length > 0) {
            if (!emptyBanner) {
                emptyBanner = document.createElement('tr');
                emptyBanner.className = 'excel-no-filter-match';
                const colSpan = rows[0].children.length;
                emptyBanner.innerHTML = `
                    <td colspan="${colSpan}" class="text-center py-4 text-muted">
                        <div class="py-2">
                            <span style="font-size: 20px;">🔍</span>
                            <p class="font-size-13 fw-semibold text-dark mt-1 mb-1">Filtrə uyğun heç bir məlumat tapılmadı</p>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="window.excelClearAllFiltersGlobal()">
                                🧹 Bütün Filtrləri Sıfırla
                            </button>
                        </div>
                    </td>
                `;
                this.tbody.appendChild(emptyBanner);
            } else {
                emptyBanner.style.display = '';
            }
        } else if (emptyBanner) {
            emptyBanner.style.display = 'none';
        }

        // Update Clear All Filters Ribbon Button
        const clearAllBtn = document.getElementById('excelClearAllFiltersBtn');
        if (clearAllBtn) {
            if (filterCols.length > 0) {
                clearAllBtn.style.display = 'inline-flex';
                const countText = document.getElementById('excelClearAllFiltersText');
                if (countText) countText.textContent = `Filtrləri Sıfırla (${filterCols.length})`;
            } else {
                clearAllBtn.style.display = 'none';
            }
        }

        // Trigger callback to recompute footer sums & counts
        if (typeof this.onFilterChanged === 'function') {
            this.onFilterChanged(visibleCount, filterCols.length);
        }
    }

    clearAllFilters() {
        const filterCols = Object.keys(this.activeFilters).map(Number);
        filterCols.forEach(cIdx => {
            this.updateColumnFilterButtonState(cIdx, false);
        });

        this.activeFilters = {};
        this.applyFilters();
    }

    detectColumnType(rows, colIdx) {
        for (let i = 0; i < Math.min(rows.length, 15); i++) {
            const cell = rows[i].children[colIdx];
            if (!cell) continue;
            const text = cell.innerText.trim();
            if (!text) continue;

            if (/^\d{1,2}[.\/-]\d{1,2}[.\/-]\d{4}$/.test(text)) {
                return 'date';
            }
            const cleaned = text.replace(/[₼AZN\s,]/g, '');
            if (/^[+-]?(\d+(\.\d*)?|\.\d+)$/.test(cleaned)) {
                return 'number';
            }
        }
        return 'text';
    }

    sortRows(colIdx, direction = 'asc') {
        const rows = this.getRows();
        if (rows.length === 0) return;

        const type = this.detectColumnType(rows, colIdx);

        rows.sort((trA, trB) => {
            const cellA = trA.children[colIdx];
            const cellB = trB.children[colIdx];
            const textA = cellA ? cellA.innerText.trim() : '';
            const textB = cellB ? cellB.innerText.trim() : '';

            let comp = 0;

            if (type === 'number') {
                const numA = parseFloat(textA.replace(/[₼AZN\s,]/g, '')) || 0;
                const numB = parseFloat(textB.replace(/[₼AZN\s,]/g, '')) || 0;
                comp = numA - numB;
            } else if (type === 'date') {
                const parseDate = s => {
                    const m = s.match(/^(\d{1,2})[.\/-](\d{1,2})[.\/-](\d{4})$/);
                    if (m) return new Date(parseInt(m[3]), parseInt(m[2]) - 1, parseInt(m[1])).getTime();
                    return 0;
                };
                comp = parseDate(textA) - parseDate(textB);
            } else {
                comp = textA.localeCompare(textB, 'az', { numeric: true, sensitivity: 'base' });
            }

            return direction === 'asc' ? comp : -comp;
        });

        // Re-append sorted rows to tbody
        rows.forEach(tr => this.tbody.appendChild(tr));

        // Trigger callback to recompute row counts/stats
        if (typeof this.onFilterChanged === 'function') {
            this.onFilterChanged(rows.length, Object.keys(this.activeFilters).length);
        }
    }
}

// Global shortcut for clearing all filters
window.excelClearAllFiltersGlobal = function() {
    if (window.accountingColumnFilter) window.accountingColumnFilter.clearAllFilters();
    if (window.expensesColumnFilter) window.expensesColumnFilter.clearAllFilters();
};
</script>
