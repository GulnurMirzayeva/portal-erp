@extends('layouts.erp')

@section('title', 'Xərclər — ' . $selectedBranchName . ' (' . ($selectedMonthName ?? $filterMonth) . ')')
@section('page-title', 'XƏRCLƏR CƏDVƏLİ')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Xərclər</a></li>
    <li class="breadcrumb-item active">Xərclərin siyahısı</li>
@endsection

@section('content')
{{-- CSRF Token for AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* Excel Workbook Container */
    .excel-container {
        background: #ffffff;
        border: 1px solid #d4d4d4;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* Green Excel Ribbon / Action Toolbar */
    .excel-ribbon {
        background: #107c41;
        color: #ffffff;
        padding: 10px 18px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .excel-ribbon-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.2px;
    }

    .excel-ribbon-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .excel-btn {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
        font-size: 12px;
        font-weight: 500;
        padding: 6px 14px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
    }

    .excel-btn:hover {
        background: rgba(255, 255, 255, 0.28);
        color: #ffffff;
        border-color: #ffffff;
    }

    .excel-btn-save {
        background: #ffffff;
        color: #107c41 !important;
        font-weight: 700;
        border: 1px solid #ffffff;
    }

    .excel-btn-save:hover {
        background: #f0fdf4;
        color: #0b5e30 !important;
    }

    .excel-btn-save.has-changes {
        background: #ffecb5;
        color: #856404 !important;
        border-color: #ffeeba;
        animation: pulse-btn 1.5s infinite;
    }

    @keyframes pulse-btn {
        0% { transform: scale(1); }
        50% { transform: scale(1.03); }
        100% { transform: scale(1); }
    }

    /* Filters Form Selects in Ribbon */
    .excel-select {
        background: rgba(255,255,255,0.9);
        color: #212529;
        font-size: 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 4px 8px;
        font-weight: 500;
        outline: none;
    }

    /* Top Banner Row (Exact Match from User's Screenshot: Lime-Green banner) */
    .excel-banner-row {
        background: #d9f286;
        border-bottom: 2px solid #b8d966;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .excel-banner-title {
        font-size: 17px;
        font-weight: 700;
        color: #1a2e05;
        flex: 1;
        text-align: center;
        letter-spacing: 0.3px;
    }

    .excel-banner-total {
        font-size: 20px;
        font-weight: 800;
        color: #1a2e05;
        font-family: 'Segoe UI', monospace, sans-serif;
    }

    /* Table Grid Styling */
    .excel-table-wrapper {
        max-height: 720px;
        overflow-x: auto;
        overflow-y: auto;
        position: relative;
        background: #ffffff;
    }

    table.excel-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        font-size: 12px;
        color: #212529;
        table-layout: fixed;
    }

    table.excel-table th,
    table.excel-table td {
        border-right: 1px solid #d4d4d4;
        border-bottom: 1px solid #d4d4d4;
        padding: 4px 8px;
        white-space: nowrap;
        user-select: text;
        vertical-align: middle;
    }

    /* Pink Column Header Row (Exact match from User's Screenshot: Pink Mauve) */
    table.excel-table thead tr.pink-header th {
        background: #f7b4ce;
        color: #222222;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        padding: 8px 6px;
        position: sticky;
        top: 0;
        z-index: 10;
        border-bottom: 2px solid #e290af;
        letter-spacing: 0.2px;
    }

    /* Row Number Header */
    td.row-idx {
        background: #f8f9fa;
        color: #6c757d;
        font-weight: 600;
        text-align: center;
        font-size: 11px;
        position: sticky;
        left: 0;
        z-index: 5;
        border-right: 2px solid #adb5bd !important;
        width: 44px;
        min-width: 44px;
        max-width: 44px;
        user-select: none;
    }

    table.excel-table tbody tr:hover td {
        background-color: #fafdf7;
    }

    /* Editable cells */
    .cell-input {
        width: 100%;
        border: none;
        background: transparent;
        font-size: 12px;
        color: inherit;
        padding: 3px 4px;
        outline: none;
        font-family: inherit;
    }

    .cell-input:focus {
        background-color: #ffffff;
        box-shadow: inset 0 0 0 2px #107c41;
        border-radius: 2px;
    }

    .cell-select {
        width: 100%;
        border: none;
        background: transparent;
        font-size: 12px;
        color: inherit;
        padding: 3px 2px;
        outline: none;
        cursor: pointer;
        font-family: inherit;
    }

    .cell-select:focus {
        background-color: #ffffff;
        box-shadow: inset 0 0 0 2px #107c41;
        border-radius: 2px;
    }

    /* When cell is edited */
    td.is-dirty {
        background-color: #fff9db !important;
    }

    /* Alignments */
    .align-right { text-align: right; }
    .align-center { text-align: center; }
    .align-left { text-align: left; }

    /* Footer Row */
    table.excel-table tfoot tr td {
        background: #eaf5ea;
        color: #0b5e30;
        font-weight: 700;
        font-size: 12px;
        position: sticky;
        bottom: 0;
        z-index: 9;
        border-top: 2px solid #107c41;
        border-bottom: 2px solid #107c41;
    }

    /* Excel Sheet Tabs Bar at bottom */
    .excel-sheets-bar {
        background: #f1f3f4;
        border-top: 1px solid #d4d4d4;
        padding: 6px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 12px;
    }

    .excel-tabs-nav-wrapper {
        display: flex;
        align-items: center;
        gap: 4px;
        overflow-x: auto;
    }

    .excel-sheet-tab {
        background: #e8eaed;
        color: #3c4043;
        border: 1px solid #dadce0;
        border-bottom: none;
        border-radius: 4px 4px 0 0;
        padding: 5px 14px;
        font-weight: 500;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .excel-sheet-tab:hover {
        background: #ffffff;
        color: #107c41;
    }

    .excel-sheet-tab.active {
        background: #ffffff;
        color: #107c41;
        font-weight: 700;
        border-top: 2px solid #107c41;
    }

    .del-row-btn {
        opacity: 0.3;
        color: #dc3545;
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 3px;
        transition: opacity 0.15s;
        border: none;
        background: transparent;
    }

    tr:hover .del-row-btn {
        opacity: 1;
    }

    .del-row-btn:hover {
        background: #ffe3e3;
    }
</style>

{{-- Action Toolbar / Ribbon --}}
<div class="excel-ribbon">
    <div class="excel-ribbon-title">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
            <path d="M3 9h18"></path>
            <path d="M3 15h18"></path>
            <path d="M9 3v18"></path>
            <path d="M15 3v18"></path>
        </svg>
        <span>Xərclər Cədvəli</span>
        <span class="badge bg-light text-dark fw-bold px-2 py-1">{{ $selectedBranchName }}</span>
        <span class="badge bg-white text-success fw-bold px-2 py-1">{{ $selectedMonthName ?? $filterMonth }}</span>

        @if($hasCustomEdits)
            <span class="badge bg-warning text-dark ms-2" title="ERP yerli yaddaşında xüsusi redaktələr mövcuddur">
                <i class="mdi mdi-content-save-edit"></i> Yadda saxlanılıb ({{ $lastSavedAt }})
            </span>
        @endif
    </div>

    <div class="excel-ribbon-actions">
        {{-- Filial Seçimi --}}
        <div class="d-flex align-items-center gap-1">
            <span class="text-white small fw-semibold">Filial:</span>
            <select class="excel-select" onchange="changeFilter('branch_id', this.value)">
                @foreach($branches as $b)
                    <option value="{{ $b['id'] }}" {{ $filterBranch == $b['id'] ? 'selected' : '' }}>
                        {{ $b['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Ay Seçimi --}}
        <div class="d-flex align-items-center gap-1">
            <span class="text-white small fw-semibold">Ay:</span>
            <select class="excel-select" onchange="changeFilter('month', this.value)">
                @foreach($availableMonths as $m)
                    <option value="{{ $m['key'] }}" {{ $filterMonth == $m['key'] ? 'selected' : '' }}>
                        {{ $m['name'] }} {{ $m['is_current'] ? '(Cari Ay)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Sətir Əlavə Et Button --}}
        <button type="button" class="excel-btn" id="addRowBtn" title="Yeni xərc sətri əlavə et">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Sətir Əlavə Et
        </button>

        {{-- Excel Export --}}
        <a href="{{ route('expenses.export', ['branch_id' => $filterBranch, 'month' => $filterMonth]) }}" class="excel-btn" title="Excel (.xlsx) faylı kimi yüklə">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Excel İxrac
        </a>

        {{-- Yadda Saxla Button --}}
        <button type="button" class="excel-btn excel-btn-save" id="saveBtn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            <span>Yadda Saxla</span>
        </button>
    </div>
</div>

{{-- Excel Sheet Container --}}
<div class="excel-container">

    {{-- Top Banner: Exact match to User's Screenshot (Lime Green Banner with Branch Name & Grand Total) --}}
    <div class="excel-banner-row">
        <div style="width: 140px;"></div>
        <div class="excel-banner-title">
            Xərclər {{ $selectedBranchName }}
        </div>
        <div class="excel-banner-total" id="bannerGrandTotal">
            ₼ {{ number_format($summary['grand_total'] ?? 0, 2, ',', ' ') }}
        </div>
    </div>

    {{-- Table Grid --}}
    <div class="excel-table-wrapper" id="excelTableWrapper">
        <table class="excel-table" id="expensesTable">
            <colgroup>
                <col style="width: 44px;">  {{-- № --}}
                <col style="width: 110px;"> {{-- Tarix --}}
                <col style="width: 160px;"> {{-- Oyun --}}
                <col style="width: 180px;"> {{-- Xərclər --}}
                <col style="width: 200px;"> {{-- Qeyd --}}
                <col style="width: 120px;"> {{-- Məbləğ nəğd --}}
                <col style="width: 120px;"> {{-- Məbləğ nəğdsiz --}}
                <col style="width: 160px;"> {{-- Təsnifat --}}
                <col style="width: 130px;"> {{-- Cəmi Məbləğ --}}
                <col style="width: 40px;">  {{-- Əməliyyat --}}
            </colgroup>
            <thead>
                {{-- Pink Mauve Header: Exact match to Screenshot --}}
                <tr class="pink-header">
                    <th>№</th>
                    <th>Tarix</th>
                    <th>Oyun</th>
                    <th>Xərclər</th>
                    <th>Qeyd</th>
                    <th>Məbləğ nəğd</th>
                    <th>Məbləğ nəğdsiz</th>
                    <th>Təsnifat</th>
                    <th>Cəmi Məbləğ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="expensesTableBody">
                @forelse($expenses as $idx => $row)
                    @php
                        $cash = (float)($row['amount_cash'] ?? 0);
                        $card = (float)($row['amount_card'] ?? 0);
                        $total = $cash + $card;
                    @endphp
                    <tr data-id="{{ $row['id'] ?? '' }}">
                        <td class="row-idx">{{ $idx + 1 }}</td>

                        {{-- 2. Tarix --}}
                        <td class="align-center">
                            <input type="date" class="cell-input text-center date-input"
                                   value="{{ !empty($row['raw_date']) ? \Carbon\Carbon::parse($row['raw_date'])->format('Y-m-d') : (!empty($row['date']) ? \Carbon\Carbon::parse($row['date'])->format('Y-m-d') : '') }}">
                        </td>

                        {{-- 3. Oyun --}}
                        <td>
                            <select class="cell-select game-select">
                                <option value="general" {{ empty($row['game_id']) || $row['game_id'] === 'general' ? 'selected' : '' }}>Ümumi</option>
                                @foreach($branchGames as $game)
                                    <option value="{{ $game['id'] }}" {{ (isset($row['game_id']) && $row['game_id'] == $game['id']) ? 'selected' : '' }}>
                                        {{ $game['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        {{-- 4. Xərclər (Yazıla bilsin, dropdown yox) --}}
                        <td>
                            <input type="text" class="cell-input title-input" value="{{ $row['title'] ?? '' }}" placeholder="Xərc adı...">
                        </td>

                        {{-- 5. Qeyd --}}
                        <td>
                            <input type="text" class="cell-input note-input" value="{{ $row['note'] ?? '' }}" placeholder="Qeyd...">
                        </td>

                        {{-- 6. Məbləğ nəğd --}}
                        <td class="align-right">
                            <input type="number" step="0.01" min="0" class="cell-input text-end amount-cash-input"
                                   value="{{ $cash > 0 ? $cash : '' }}" placeholder="0.00">
                        </td>

                        {{-- 7. Məbləğ nəğdsiz --}}
                        <td class="align-right">
                            <input type="number" step="0.01" min="0" class="cell-input text-end amount-card-input"
                                   value="{{ $card > 0 ? $card : '' }}" placeholder="0.00">
                        </td>

                        {{-- 8. Təsnifat (Dropdown) --}}
                        <td>
                            <select class="cell-select classification-select">
                                <option value="">-- Seçin --</option>
                                @foreach($classifications as $c)
                                    <option value="{{ $c }}" {{ (isset($row['classification']) && $row['classification'] === $c) ? 'selected' : '' }}>
                                        {{ $c }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        {{-- 9. Cəmi Məbləğ (Avtomatik hesablanır) --}}
                        <td class="align-right fw-bold row-total-cell" style="background-color: #fafbfc;">
                            {{ $total > 0 ? number_format($total, 2, '.', '') : '0.00' }}
                        </td>

                        {{-- Əməliyyat (Sətir sil) --}}
                        <td class="text-center">
                            <button type="button" class="del-row-btn" title="Sətri sil">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    {{-- Boş cədvəl üçün ilkin 3 boş sətir göstəririk --}}
                    @for($i = 1; $i <= 3; $i++)
                        <tr data-id="">
                            <td class="row-idx">{{ $i }}</td>
                            <td class="align-center"><input type="date" class="cell-input text-center date-input" value="{{ date('Y-m-d') }}"></td>
                            <td>
                                <select class="cell-select game-select">
                                    <option value="general" selected>Ümumi</option>
                                    @foreach($branchGames as $game)
                                        <option value="{{ $game['id'] }}">{{ $game['name'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" class="cell-input title-input" placeholder="Xərc adı..."></td>
                            <td><input type="text" class="cell-input note-input" placeholder="Qeyd..."></td>
                            <td class="align-right"><input type="number" step="0.01" min="0" class="cell-input text-end amount-cash-input" placeholder="0.00"></td>
                            <td class="align-right"><input type="number" step="0.01" min="0" class="cell-input text-end amount-card-input" placeholder="0.00"></td>
                            <td>
                                <select class="cell-select classification-select">
                                    <option value="">-- Seçin --</option>
                                    @foreach($classifications as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="align-right fw-bold row-total-cell" style="background-color: #fafbfc;">0.00</td>
                            <td class="text-center">
                                <button type="button" class="del-row-btn" title="Sətri sil">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endfor
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end pe-3 fw-bold">YEKUN CƏMİ:</td>
                    <td class="align-right fw-bold" id="footerTotalCash">{{ number_format($summary['total_cash'] ?? 0, 2, '.', '') }}</td>
                    <td class="align-right fw-bold" id="footerTotalCard">{{ number_format($summary['total_card'] ?? 0, 2, '.', '') }}</td>
                    <td></td>
                    <td class="align-right fw-bold" id="footerGrandTotal">{{ number_format($summary['grand_total'] ?? 0, 2, '.', '') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Bottom Excel Sheet Tabs Bar --}}
    <div class="excel-sheets-bar">
        <div class="excel-tabs-nav-wrapper">
            <span class="text-muted small me-2"><i class="mdi mdi-table me-1"></i> Filiallar:</span>
            @foreach($branches as $b)
                <a href="{{ route('expenses.index', ['branch_id' => $b['id'], 'month' => $filterMonth]) }}"
                   class="excel-sheet-tab {{ $filterBranch == $b['id'] ? 'active' : '' }}">
                    <i class="mdi mdi-file-document-outline"></i>
                    {{ $b['name'] }}
                </a>
            @endforeach
        </div>

        <div class="text-muted small">
            <span>Cəmi Sətir: <strong id="rowCountLabel">{{ count($expenses) }}</strong></span>
        </div>
    </div>
</div>

@push('js')
<script>
    const BRANCH_ID = "{{ $filterBranch }}";
    const MONTH = "{{ $filterMonth }}";
    const BRANCH_GAMES = @json($branchGames);
    const CLASSIFICATIONS = @json($classifications);

    let hasUnsavedChanges = false;

    function changeFilter(param, val) {
        if (hasUnsavedChanges) {
            if (!confirm('Yadda saxlanılmamış dəyişiklikləriniz var. Səhifəni dəyişmək istədiyinizdən əminsiniz?')) {
                return;
            }
        }
        const url = new URL(window.location.href);
        url.searchParams.set(param, val);
        window.location.href = url.toString();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.getElementById('expensesTableBody');
        const saveBtn = document.getElementById('saveBtn');
        const addRowBtn = document.getElementById('addRowBtn');

        // Hadisələri dinlə (input dəyişdikdə)
        tableBody.addEventListener('input', function (e) {
            const tr = e.target.closest('tr');
            if (!tr) return;

            markDirty(e.target);
            recalcRow(tr);
            recalcTotals();
        });

        tableBody.addEventListener('change', function (e) {
            const tr = e.target.closest('tr');
            if (!tr) return;

            markDirty(e.target);
            recalcRow(tr);
            recalcTotals();
        });

        // Sətir silmə
        tableBody.addEventListener('click', function (e) {
            const btn = e.target.closest('.del-row-btn');
            if (!btn) return;

            const tr = btn.closest('tr');
            if (confirm('Bu sətri silmək istədiyinizdən əminsiniz?')) {
                tr.remove();
                renumberRows();
                recalcTotals();
                setChangesPending(true);
            }
        });

        // Yeni sətir əlavə et
        addRowBtn.addEventListener('click', function () {
            addNewRow();
        });

        // Yadda saxla
        saveBtn.addEventListener('click', function () {
            saveAllExpenses();
        });

        function markDirty(el) {
            const td = el.closest('td');
            if (td) td.classList.add('is-dirty');
            setChangesPending(true);
        }

        function setChangesPending(pending) {
            hasUnsavedChanges = pending;
            if (pending) {
                saveBtn.classList.add('has-changes');
                saveBtn.querySelector('span').textContent = 'Yadda Saxla *';
            } else {
                saveBtn.classList.remove('has-changes');
                saveBtn.querySelector('span').textContent = 'Yadda Saxla';
                document.querySelectorAll('td.is-dirty').forEach(td => td.classList.remove('is-dirty'));
            }
        }

        function recalcRow(tr) {
            const cashInput = tr.querySelector('.amount-cash-input');
            const cardInput = tr.querySelector('.amount-card-input');
            const totalCell = tr.querySelector('.row-total-cell');

            const cash = parseFloat(cashInput ? cashInput.value : 0) || 0;
            const card = parseFloat(cardInput ? cardInput.value : 0) || 0;
            const total = cash + card;

            if (totalCell) {
                totalCell.textContent = total.toFixed(2);
            }
        }

        function recalcTotals() {
            let totalCash = 0;
            let totalCard = 0;
            let count = 0;

            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(tr => {
                const cashInput = tr.querySelector('.amount-cash-input');
                const cardInput = tr.querySelector('.amount-card-input');

                const cash = parseFloat(cashInput ? cashInput.value : 0) || 0;
                const card = parseFloat(cardInput ? cardInput.value : 0) || 0;

                totalCash += cash;
                totalCard += card;
                count++;
            });

            const grandTotal = totalCash + totalCard;

            document.getElementById('footerTotalCash').textContent = totalCash.toFixed(2);
            document.getElementById('footerTotalCard').textContent = totalCard.toFixed(2);
            document.getElementById('footerGrandTotal').textContent = grandTotal.toFixed(2);

            // Banner grand total format: ₼ 17 535,33
            const formattedTotal = '₼ ' + grandTotal.toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('bannerGrandTotal').textContent = formattedTotal;

            const rowCountLabel = document.getElementById('rowCountLabel');
            if (rowCountLabel) rowCountLabel.textContent = count;
        }

        function renumberRows() {
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach((tr, idx) => {
                const idxCell = tr.querySelector('.row-idx');
                if (idxCell) idxCell.textContent = idx + 1;
            });
        }

        function addNewRow() {
            const count = tableBody.querySelectorAll('tr').length + 1;
            const today = new Date().toISOString().split('T')[0];

            let gamesOptions = '<option value="general" selected>Ümumi</option>';
            BRANCH_GAMES.forEach(g => {
                gamesOptions += `<option value="${g.id}">${g.name}</option>`;
            });

            let classOptions = '<option value="">-- Seçin --</option>';
            CLASSIFICATIONS.forEach(c => {
                classOptions += `<option value="${c}">${c}</option>`;
            });

            const tr = document.createElement('tr');
            tr.setAttribute('data-id', '');
            tr.innerHTML = `
                <td class="row-idx">${count}</td>
                <td class="align-center"><input type="date" class="cell-input text-center date-input" value="${today}"></td>
                <td><select class="cell-select game-select">${gamesOptions}</select></td>
                <td><input type="text" class="cell-input title-input" placeholder="Xərc adı..."></td>
                <td><input type="text" class="cell-input note-input" placeholder="Qeyd..."></td>
                <td class="align-right"><input type="number" step="0.01" min="0" class="cell-input text-end amount-cash-input" placeholder="0.00"></td>
                <td class="align-right"><input type="number" step="0.01" min="0" class="cell-input text-end amount-card-input" placeholder="0.00"></td>
                <td><select class="cell-select classification-select">${classOptions}</select></td>
                <td class="align-right fw-bold row-total-cell" style="background-color: #fafbfc;">0.00</td>
                <td class="text-center">
                    <button type="button" class="del-row-btn" title="Sətri sil">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </td>
            `;

            tableBody.appendChild(tr);
            tr.querySelectorAll('td').forEach(td => td.classList.add('is-dirty'));
            setChangesPending(true);
            recalcTotals();

            // Scroll to bottom
            const wrapper = document.getElementById('excelTableWrapper');
            if (wrapper) wrapper.scrollTop = wrapper.scrollHeight;
        }

        function saveAllExpenses() {
            const rows = tableBody.querySelectorAll('tr');
            const data = [];

            rows.forEach((tr, idx) => {
                const id = tr.getAttribute('data-id');
                const date = tr.querySelector('.date-input')?.value || '';
                const gameSelect = tr.querySelector('.game-select');
                const gameId = gameSelect ? gameSelect.value : 'general';
                const gameName = gameSelect ? gameSelect.options[gameSelect.selectedIndex]?.text : 'Ümumi';
                const title = tr.querySelector('.title-input')?.value || '';
                const note = tr.querySelector('.note-input')?.value || '';
                const cash = parseFloat(tr.querySelector('.amount-cash-input')?.value || 0) || 0;
                const card = parseFloat(tr.querySelector('.amount-card-input')?.value || 0) || 0;
                const classification = tr.querySelector('.classification-select')?.value || '';

                // Əgər tamamilə boş sətirdirsə nəzərə almırıq
                if (!title && !note && cash === 0 && card === 0 && !classification) {
                    return;
                }

                data.push({
                    id: id ? id : null,
                    row_num: idx + 1,
                    date: date,
                    raw_date: date,
                    game_id: gameId,
                    game_name: gameName,
                    title: title,
                    note: note,
                    amount_cash: cash,
                    amount_card: card,
                    classification: classification,
                });
            });

            saveBtn.disabled = true;
            saveBtn.querySelector('span').textContent = 'Saxlanılır...';

            fetch("{{ route('expenses.save') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    branch_id: BRANCH_ID,
                    month: MONTH,
                    expenses: data,
                }),
            })
            .then(res => res.json())
            .then(resData => {
                saveBtn.disabled = false;
                if (resData.success) {
                    setChangesPending(false);
                    alert(resData.message || 'Xərclər uğurla yadda saxlanıldı.');
                } else {
                    saveBtn.querySelector('span').textContent = 'Yadda Saxla';
                    alert('Xəta baş verdi: ' + (resData.message || 'Məlumatları saxlamaq mümkün olmadı.'));
                }
            })
            .catch(err => {
                saveBtn.disabled = false;
                saveBtn.querySelector('span').textContent = 'Yadda Saxla';
                console.error('Save error:', err);
                alert('Serverlə əlaqə xətası baş verdi.');
            });
        }

        // Beforeunload xəbərdarlığı
        window.addEventListener('beforeunload', function (e) {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    });
</script>
@endpush
@endsection
