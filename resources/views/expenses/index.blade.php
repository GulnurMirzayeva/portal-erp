@extends('layouts.erp')

@section('title', 'Xərclər — ' . $selectedBranchName . ' (' . ($selectedMonthName ?? $filterMonth) . ')')
@section('page-title', 'XƏRCLƏR CƏDVƏLİ')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item active">Xərclər</li>
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

    /* Branch selector tabs (Top Filiallar bar) */
    .branch-pill {
        border-radius: 5px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #dcdcdc;
        background: #ffffff;
        color: #495057;
        transition: all 0.15s ease;
    }

    .branch-pill:hover {
        background: #eef7ee;
        border-color: #107c41;
        color: #107c41;
    }

    .branch-pill.active {
        background: #107c41;
        color: #ffffff;
        border-color: #107c41;
        box-shadow: 0 2px 6px rgba(16, 124, 65, 0.25);
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
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    /* Table Grid Styling */
    .excel-table-wrapper {
        overflow-x: auto;
        max-height: calc(100vh - 280px);
        background: #ffffff;
    }

    table.excel-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        font-size: 13px;
        color: #212529;
        table-layout: fixed;
        margin-bottom: 0;
    }

    table.excel-table th,
    table.excel-table td {
        border-right: 1px solid #d4d4d4;
        border-bottom: 1px solid #d4d4d4;
        padding: 4px 8px;
        white-space: nowrap;
        vertical-align: middle;
    }

    /* Pink Header: Exact match to Screenshot */
    table.excel-table thead tr.pink-header th {
        background: #e39bb5;
        color: #3b0818;
        font-weight: 700;
        font-size: 13px;
        text-align: center;
        padding: 8px 10px;
        position: sticky;
        top: 0;
        z-index: 15;
        border-bottom: 2px solid #c97f9b;
        letter-spacing: 0.2px;
    }

    table.excel-table tbody tr:hover td {
        background-color: #fafbfc;
    }

    /* Excel Editable Cells */
    td.excel-cell {
        cursor: cell;
        transition: background-color 0.1s ease;
        outline: none;
    }

    td.excel-cell:focus {
        background-color: #ffffff !important;
        box-shadow: inset 0 0 0 2px #107c41;
        z-index: 3;
        position: relative;
    }

    td.excel-cell.is-dirty,
    td.is-dirty {
        background-color: #fff9db !important;
    }

    tr.col-letter-header th {
        background: #f1f3f4;
        color: #5f6368;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        padding: 3px 0;
        border-bottom: 1px solid #d4d4d4;
        border-right: 1px solid #e0e0e0;
    }

    .align-right { text-align: right; }
    .align-center { text-align: center; }
    .align-left { text-align: left; }

    /* Footer Totals Row */
    table.excel-table tfoot tr td {
        background: #f4f6f8;
        color: #1a2e05;
        font-weight: 700;
        font-size: 13px;
        position: sticky;
        bottom: 0;
        z-index: 10;
        border-top: 2px solid #b8d966;
        border-bottom: 2px solid #b8d966;
        padding: 8px 10px;
    }

    .row-idx {
        background: #f8f9fa;
        color: #6c757d;
        font-weight: 600;
        text-align: center;
        font-size: 12px;
        user-select: none;
    }

    /* Bottom Excel Sheet Tabs Bar (Aylar aşağıda) */
    .excel-sheets-bar {
        background: #f1f3f4;
        border-top: 1px solid #d4d4d4;
        padding: 4px 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 12px;
        position: relative;
    }

    .excel-tabs-nav-wrapper {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1 1 auto;
        min-width: 0;
        overflow: hidden;
    }

    .excel-tabs-scroll-container {
        display: flex;
        align-items: center;
        gap: 3px;
        overflow-x: auto;
        white-space: nowrap;
        scroll-behavior: smooth;
        flex: 1 1 auto;
        min-width: 0;
        padding: 3px 4px 6px 4px;
        scrollbar-width: thin;
        scrollbar-color: #b0b8c0 #edf0f2;
    }

    .excel-tabs-scroll-container::-webkit-scrollbar {
        height: 6px;
    }

    .excel-tabs-scroll-container::-webkit-scrollbar-track {
        background: #edf0f2;
        border-radius: 3px;
    }

    .excel-tabs-scroll-container::-webkit-scrollbar-thumb {
        background: #b0b8c0;
        border-radius: 3px;
    }

    .excel-tabs-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #107c41;
    }

    .excel-tab {
        background: #e9ecef;
        color: #495057;
        font-weight: 500;
        padding: 5px 13px;
        border-radius: 4px 4px 0 0;
        border: 1px solid #ced4da;
        border-bottom: 1px solid #ced4da;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        flex-shrink: 0;
        user-select: none;
        transition: all 0.15s ease;
        font-size: 12px;
    }

    .excel-tab:hover {
        background: #f8f9fa;
        color: #107c41;
        border-color: #adb5bd;
        text-decoration: none;
    }

    .excel-tab.active {
        background: #ffffff;
        color: #107c41;
        font-weight: 700;
        border-color: #ced4da #ced4da transparent #ced4da;
        border-bottom: 3px solid #107c41;
        box-shadow: 0 -1px 3px rgba(0,0,0,0.04);
        position: relative;
        z-index: 2;
    }

    .excel-sheets-summary {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        white-space: nowrap;
        padding-left: 12px;
        border-left: 1px solid #d4d4d4;
        font-size: 12px;
    }

    .excel-stat-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .excel-stat-divider {
        color: #ced4da;
        font-weight: 300;
    }

    .row-actions-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .history-row-btn {
        opacity: 0.35;
        color: #107c41;
        cursor: pointer;
        padding: 3px 4px;
        border-radius: 4px;
        transition: all 0.15s ease;
        border: none;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    tr:hover .history-row-btn {
        opacity: 0.85;
    }

    .history-row-btn:hover {
        opacity: 1 !important;
        background: #eaf5ea;
        color: #0b5e30;
        transform: scale(1.15);
    }

    .del-row-btn {
        opacity: 0.3;
        color: #dc3545;
        cursor: pointer;
        padding: 3px 4px;
        border-radius: 4px;
        transition: all 0.15s ease;
        border: none;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    tr:hover .del-row-btn {
        opacity: 0.85;
    }

    .del-row-btn:hover {
        opacity: 1 !important;
        background: #ffe3e3;
        transform: scale(1.15);
    }

    /* Tarix xanası və created_at saat indikatoru */
    .date-cell {
        position: relative;
        padding-right: 22px !important;
    }

    .cell-created-indicator {
        position: absolute;
        right: 4px;
        top: 50%;
        transform: translateY(-50%);
        width: 17px;
        height: 17px;
        border-radius: 4px;
        border: none;
        background: transparent;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        transition: all 0.15s ease;
        opacity: 0.45;
        user-select: none;
    }

    .date-cell:hover .cell-created-indicator,
    tr:hover .cell-created-indicator {
        opacity: 1;
        color: #107c41;
    }

    .cell-created-indicator:hover {
        background: #eaf5ea;
        color: #0b5e30 !important;
        transform: translateY(-50%) scale(1.15);
    }

    /* Modal Backdrop & Dialog (Balaca Modal) */
    .expense-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 10050;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        animation: modalFadeIn 0.18s ease-out;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .expense-modal-dialog {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.04);
        width: 100%;
        max-width: 480px;
        overflow: hidden;
        animation: modalSlideUp 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
    }

    @keyframes modalSlideUp {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .expense-modal-header {
        padding: 14px 18px;
        border-bottom: 1px solid #eef2f6;
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fafbfc;
    }

    .expense-modal-header-icon {
        width: 38px;
        height: 38px;
        border-radius: 9px;
        background: #eaf5ea;
        color: #107c41;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .expense-modal-header-text {
        flex: 1;
        min-width: 0;
    }

    .expense-modal-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        line-height: 1.25;
    }

    .expense-modal-subtitle {
        font-size: 11px;
        color: #64748b;
        margin: 2px 0 0 0;
        font-weight: 500;
    }

    .expense-modal-close {
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 6px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .expense-modal-close:hover {
        background: #f1f5f9;
        color: #334155;
    }

    .expense-modal-body {
        padding: 16px 18px;
    }

    /* Created At Highlight Card */
    .created-at-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(16, 124, 65, 0.08);
    }

    .created-at-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #107c41;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(16, 124, 65, 0.25);
    }

    .created-at-content {
        flex: 1;
        min-width: 0;
    }

    .created-at-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #0b5e30;
        display: block;
        margin-bottom: 2px;
    }

    .created-at-value-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .created-at-val {
        font-size: 18px;
        font-weight: 800;
        color: #064e3b;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        letter-spacing: -0.2px;
    }

    .btn-copy-timestamp {
        background: #ffffff;
        border: 1px solid #86efac;
        color: #0b5e30;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 5px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }

    .btn-copy-timestamp:hover {
        background: #0b5e30;
        color: #ffffff;
        border-color: #0b5e30;
    }

    .created-at-relative {
        font-size: 11px;
        color: #166534;
        display: block;
        margin-top: 3px;
        font-weight: 500;
    }

    /* Unsaved notification */
    .unsaved-row-card {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
    }

    /* Updated at info */
    .updated-at-card {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 12px;
    }

    /* Meta Grid */
    .expense-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .meta-item-full {
        grid-column: span 2;
    }

    .meta-label {
        font-size: 10px;
        text-transform: uppercase;
        font-weight: 600;
        color: #64748b;
        letter-spacing: 0.3px;
    }

    .meta-value {
        font-size: 12px;
        font-weight: 600;
        color: #1e293b;
        word-break: break-word;
    }

    .expense-modal-footer {
        padding: 10px 18px;
        background: #fafbfc;
        border-top: 1px solid #eef2f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .expense-modal-btn-close {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .expense-modal-btn-close:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }
</style>

{{-- Toast Notification Box --}}
<div id="excelToast" style="display: none; position: fixed; top: 20px; right: 25px; z-index: 9999; background: #107c41; color: white; padding: 12px 20px; border-radius: 6px; box-shadow: 0 4px 14px rgba(0,0,0,0.18); font-size: 13px; font-weight: 600; align-items: center; gap: 8px;">
    <span id="toastIcon">✓</span>
    <span id="toastMessage">Uğurla yadda saxlanıldı!</span>
</div>

{{-- 1. Filial Selector Pills Bar (YUXARIDA - Filiallar) --}}
<div class="card mb-3 border-0 shadow-sm" style="border-radius: 8px;">
    <div class="card-body p-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex flex-wrap gap-2 flex-grow-1 align-items-center">
            @foreach($branches as $b)
                @php
                    $isActive = ($filterBranch == $b['id']);
                @endphp
                <a href="{{ route('expenses.index', ['branch_id' => $b['id'], 'month' => $filterMonth]) }}"
                   class="branch-pill {{ $isActive ? 'active' : '' }}"
                   title="{{ $b['name'] }}">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 21h18"></path>
                        <path d="M5 21V7l8-4v18"></path>
                        <path d="M19 21V11l-6-3"></path>
                    </svg>
                    <span>{{ $b['name'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- 2. Excel Ribbon Toolbar (ORTADA - Action Bar) --}}
<div class="excel-ribbon mb-3">
    <div class="excel-ribbon-title">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
            <path d="M3 9h18"></path>
            <path d="M3 15h18"></path>
            <path d="M9 3v18"></path>
            <path d="M15 3v18"></path>
        </svg>
        <span>{{ $selectedBranchName }} — Xərclər ({{ $selectedMonthName ?? $filterMonth }})</span>

        @if($hasCustomEdits && $lastSavedAt)
            <span id="saveStatusIndicator" class="badge bg-success text-white ms-2">
                Bazada yeniləndi ({{ $lastSavedAt }})
            </span>
        @else
            <span id="saveStatusIndicator" class="badge bg-secondary text-white ms-2">
                İlkin məlumatlar
            </span>
        @endif
    </div>

    <div class="excel-ribbon-actions">
        {{-- Yadda Saxla Button --}}
        <button type="button" class="excel-btn excel-btn-save" id="saveBtn" onclick="saveAllExpenses()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            <span id="saveBtnText">Yadda Saxla (Ctrl+S)</span>
        </button>

        {{-- Sətir Əlavə Et Button --}}
        <button type="button" class="excel-btn" id="addRowBtn" onclick="addNewRow()" title="Yeni xərc sətri əlavə et">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>+ Sətir Əlavə Et</span>
        </button>

        {{-- Excel Export Button --}}
        <a href="{{ route('expenses.export', ['branch_id' => $filterBranch, 'month' => $filterMonth, 'format' => 'xlsx']) }}" class="excel-btn" title="Excel (.xlsx) faylı kimi yüklə">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Excel Yüklə (.xlsx)</span>
        </a>
    </div>
</div>

{{-- 3. Excel Sheet Container (CƏDVƏL) --}}
<div class="excel-container">

    {{-- Top Banner: Lime Green Banner with Branch Name & Grand Total --}}
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
                <col style="width: 115px;"> {{-- Tarix --}}
                <col style="width: 160px;"> {{-- Oyun --}}
                <col style="width: 180px;"> {{-- Xərclər --}}
                <col style="width: 200px;"> {{-- Qeyd --}}
                <col style="width: 120px;"> {{-- Məbləğ nəğd --}}
                <col style="width: 120px;"> {{-- Məbləğ nəğdsiz --}}
                <col style="width: 160px;"> {{-- Təsnifat --}}
                <col style="width: 130px;"> {{-- Cəmi Məbləğ --}}
                <col style="width: 60px;">  {{-- Əməliyyat --}}
            </colgroup>
            <thead>
                {{-- Pink Mauve Header --}}
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
                {{-- Column Letter Headers (A, B, C, D...) --}}
                <tr class="col-letter-header">
                    <th>#</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>E</th>
                    <th>F</th>
                    <th>G</th>
                    <th>H</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="expensesTableBody">
                @forelse($expenses as $idx => $row)
                    @php
                        $cash = (float)($row['amount_cash'] ?? 0);
                        $card = (float)($row['amount_card'] ?? 0);
                        $total = (float)($row['total_amount'] ?? ($cash + $card));
                        $rawDateVal = $row['raw_date'] ?? $row['date'] ?? '';
                        try {
                            if (preg_match('/^(\d{1,2})[.\/-](\d{1,2})[.\/-](\d{4})$/', trim($rawDateVal), $dm)) {
                                $displayDate = sprintf('%02d.%02d.%04d', (int)$dm[1], (int)$dm[2], (int)$dm[3]);
                            } elseif (!empty($rawDateVal)) {
                                $displayDate = \Carbon\Carbon::parse($rawDateVal)->format('d.m.Y');
                            } else {
                                $displayDate = '';
                            }
                        } catch (\Exception $e) {
                            $displayDate = $rawDateVal;
                        }
                    @endphp
                    <tr data-row-id="{{ $row['id'] ?? '' }}"
                        data-created-at="{{ $row['created_at'] ?? '' }}"
                        data-updated-at="{{ $row['updated_at'] ?? '' }}"
                        data-raw-created-at="{{ $row['raw_created_at'] ?? '' }}">
                        <td class="row-idx align-center" style="background: #f8f9fa; color: #6c757d; font-weight: 600; cursor: pointer;" onclick="openExpenseDetails(this, event)" title="Sətir detalları və yaradılma vaxtına bax (created_at)">{{ $idx + 1 }}</td>
                        <td class="excel-cell align-center date-cell" contenteditable="true" data-field="date" data-col="A" title="Tarixi dəyişmək üçün klikləyin. Yaradılma vaxtına baxmaq üçün saat ikonuna klikləyin.">
                            {{ $displayDate }}
                            <button type="button" class="cell-created-indicator" contenteditable="false" onclick="openExpenseDetails(this, event)" title="Yaradılma vaxtına bax (created_at)">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </button>
                        </td>
                        <td class="excel-cell align-left font-weight-600 text-primary" contenteditable="true" data-field="game_name" data-col="B">{{ $row['game_name'] ?? 'Ümumi' }}</td>
                        <td class="excel-cell align-left font-weight-500" contenteditable="true" data-field="title" data-col="C">{{ $row['title'] ?? '' }}</td>
                        <td class="excel-cell align-left" contenteditable="true" data-field="note" data-col="D">{{ $row['note'] ?? '' }}</td>
                        <td class="excel-cell align-right font-weight-600 text-dark" contenteditable="true" data-field="amount_cash" data-col="E" oninput="recalcRow(this)">{{ $cash > 0 ? number_format($cash, 2, '.', '') : '0.00' }}</td>
                        <td class="excel-cell align-right font-weight-600 text-dark" contenteditable="true" data-field="amount_card" data-col="F" oninput="recalcRow(this)">{{ $card > 0 ? number_format($card, 2, '.', '') : '0.00' }}</td>
                        <td class="excel-cell align-left" contenteditable="true" data-field="classification" data-col="G">{{ $row['classification'] ?? '' }}</td>
                        <td class="align-right fw-bold row-total-cell" style="background-color: #fafbfc;">{{ number_format($total, 2, '.', '') }}</td>
                        <td class="text-center">
                            <div class="row-actions-group">
                                <button type="button" class="history-row-btn" onclick="openExpenseDetails(this, event)" title="Yaradılma vaxtına bax (created_at)">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </button>
                                <button type="button" class="del-row-btn" onclick="deleteRow(this)" title="Sətri sil">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="emptyRowPlaceholder">
                        <td colspan="10" class="text-center py-5 text-muted" style="background: #ffffff; font-size: 13px;">
                            <div class="py-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="d-block mx-auto mb-2 text-muted">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <p class="font-size-14 mb-1">Bu filial və ay üçün heç bir xərc qeydi tapılmadı.</p>
                                <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addNewRow()">
                                    ➕ İlk Sətri Əlavə Et
                                </button>
                            </div>
                        </td>
                    </tr>
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

    {{-- 4. Bottom Excel Sheet Tabs Bar (AŞAĞIDA - Aylar və Sticky Yekun Statistikası) --}}
    <div class="excel-sheets-bar">
        {{-- Left Area: Horizontally Scrollable Months Tabs --}}
        <div class="excel-tabs-nav-wrapper">
            <div class="excel-tabs-scroll-container" id="excelTabsContainer">
                @foreach($availableMonths as $m)
                    @php
                        $isMonthActive = ($filterMonth === $m['key']);
                    @endphp
                    <a href="{{ route('expenses.index', ['branch_id' => $filterBranch, 'month' => $m['key']]) }}"
                       class="excel-tab {{ $isMonthActive ? 'active' : '' }}"
                       id="month-tab-{{ $m['key'] }}"
                       title="{{ $m['name'] }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        <span>{{ $m['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Right Area: Pinned Summary Statistics --}}
        <div class="excel-sheets-summary">
            <span class="excel-stat-item">
                <span class="text-muted">Sətir sayı:</span>
                <strong id="rowCountDisplay">{{ count($expenses) }}</strong>
            </span>
            <span class="excel-stat-divider">|</span>
            <span class="excel-stat-item">
                <span class="text-muted">Yekun Xərc:</span>
                <strong class="text-success font-size-13" id="bottomGrandTotalDisplay">{{ number_format($summary['grand_total'] ?? 0, 2) }} ₼</strong>
            </span>
        </div>
    </div>
</div>

{{-- Expense Details & Created At Modal (Balaca Modal) --}}
<div id="expenseDetailsModal" class="expense-modal-backdrop" style="display: none;" onclick="closeModalOnBackdrop(event)">
    <div class="expense-modal-dialog" role="dialog" aria-modal="true">
        {{-- Modal Header --}}
        <div class="expense-modal-header">
            <div class="expense-modal-header-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div class="expense-modal-header-text">
                <h3 class="expense-modal-title">Xərc Qeydiyyatı və Tarixçə</h3>
                <p class="expense-modal-subtitle" id="modalRowSubtitle">Sətir № 1 • Qeyd ID: #2</p>
            </div>
            <button type="button" class="expense-modal-close" onclick="closeExpenseModal()" title="Bağla (Esc)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="expense-modal-body">
            {{-- Highlight: Created At (Yaradılma Tarixi) --}}
            <div class="created-at-card" id="modalCreatedAtCard">
                <div class="created-at-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="created-at-content">
                    <span class="created-at-label">Yaradılma Tarixi və Vaxtı (created_at)</span>
                    <div class="created-at-value-row">
                        <span class="created-at-val" id="modalCreatedAtVal">—</span>
                        <button type="button" class="btn-copy-timestamp" onclick="copyModalTimestamp()" title="Vaxtı kopyala">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect>
                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path>
                            </svg>
                            <span id="copyTimestampText">Kopyala</span>
                        </button>
                    </div>
                    <span class="created-at-relative" id="modalCreatedAtRelative">Sistemdə ilkin qeydiyyat anı</span>
                </div>
            </div>

            {{-- Unsaved Row Notice --}}
            <div class="unsaved-row-card" id="modalUnsavedNotice" style="display: none;">
                <div class="font-size-20">⚠️</div>
                <div>
                    <div class="fw-bold text-dark font-size-13 mb-1">Hələ Yadda Saxlanılmayıb</div>
                    <div class="text-muted font-size-12">Bu xərc sətri yenicə əlavə edilib. Yuxarıdakı "Yadda Saxla (Ctrl+S)" düyməsini sıxdıqdan sonra serverdə qeydə alınacaq və `created_at` vaxtı təyin ediləcək.</div>
                </div>
            </div>

            {{-- Updated At info card (if present) --}}
            <div class="updated-at-card" id="modalUpdatedAtCard" style="display: none;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2">
                            <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
                        </svg>
                        <span class="font-size-12 text-muted fw-semibold">Son redaktə (updated_at):</span>
                    </div>
                    <span class="font-size-12 fw-bold text-dark font-monospace" id="modalUpdatedAtVal">—</span>
                </div>
            </div>

            {{-- Context Grid --}}
            <div class="expense-meta-grid">
                <div class="meta-item">
                    <span class="meta-label">Sənəd Tarixi</span>
                    <span class="meta-value font-monospace" id="modalMetaDate">—</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Cəmi Məbləğ</span>
                    <span class="meta-value text-success font-monospace" id="modalMetaTotal">—</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Oyun / Filial</span>
                    <span class="meta-value" id="modalMetaGame">—</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Təsnifat</span>
                    <span class="meta-value" id="modalMetaClassification">—</span>
                </div>
                <div class="meta-item meta-item-full">
                    <span class="meta-label">Xərcin Başlığı</span>
                    <span class="meta-value" id="modalMetaTitle">—</span>
                </div>
                <div class="meta-item meta-item-full" id="modalMetaNoteWrapper" style="display: none;">
                    <span class="meta-label">Qeyd</span>
                    <span class="meta-value text-muted" id="modalMetaNote">—</span>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="expense-modal-footer">
            <span class="font-size-11 text-muted">Bağlamaq üçün <kbd class="px-1 py-0.5 rounded bg-light border text-dark font-size-10">ESC</kbd> basın</span>
            <button type="button" class="expense-modal-btn-close" onclick="closeExpenseModal()">Bağla</button>
        </div>
    </div>
</div>

<script>
    const BRANCH_ID = "{{ $filterBranch }}";
    const MONTH = "{{ $filterMonth }}";
    const BRANCH_GAMES = @json($branchGames);
    const CLASSIFICATIONS = @json($classifications);

    let isDirty = false;
    let deletedIds = [];
    let activeCell = null;

    document.addEventListener('DOMContentLoaded', function () {
        initCellEvents();
        initExcelTabs();

        // Sətir silmə və sətirə ikiqat klik (event delegation)
        const tableBody = document.getElementById('expensesTableBody');
        if (tableBody) {
            tableBody.addEventListener('click', function (e) {
                const btn = e.target.closest('.del-row-btn');
                if (btn) deleteRow(btn);
            });

            // Cədvəl sətrinə ikiqat klik etdikdə yaradılma tarixini aç
            tableBody.addEventListener('dblclick', function (e) {
                const tr = e.target.closest('tr:not(#emptyRowPlaceholder)');
                if (tr && !e.target.closest('.del-row-btn')) {
                    openExpenseDetails(tr, e);
                }
            });
        }

        // Add Row Button
        const addRowBtn = document.getElementById('addRowBtn');
        if (addRowBtn) {
            addRowBtn.addEventListener('click', addNewRow);
        }

        // Save Button
        const saveBtn = document.getElementById('saveBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', saveAllExpenses);
        }

        // Klaviatura qısayolları: Ctrl+S (Yadda Saxla) və Escape (Modalı bağla)
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveAllExpenses();
            } else if (e.key === 'Escape') {
                closeExpenseModal();
            }
        });

        // Səhifə keçidləri zamanı yadda saxlanılmamış dəyişiklik xəbərdarlığı
        document.querySelectorAll('.excel-tab, .branch-pill').forEach(link => {
            link.addEventListener('click', function (e) {
                if (isDirty) {
                    if (!confirm('Dəyişiklikləriniz hələ yadda saxlanılmayıb! Başqa aya və ya filiala keçmək istədiyinizdən əminsiniz?')) {
                        e.preventDefault();
                    }
                }
            });
        });

        // Beforeunload xəbərdarlığı
        window.addEventListener('beforeunload', function (e) {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    });

    function initCellEvents() {
        const cells = document.querySelectorAll('td.excel-cell');
        cells.forEach(cell => {
            cell.addEventListener('focus', function () {
                activeCell = this;
            });

            cell.addEventListener('input', function () {
                this.classList.add('is-dirty');
                markDirty();
            });

            // Enter moves down, Tab moves right
            cell.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const tr = this.closest('tr');
                    const nextTr = tr.nextElementSibling;
                    if (nextTr && !nextTr.id) {
                        const cellIdx = Array.from(tr.children).indexOf(this);
                        if (nextTr.children[cellIdx]) {
                            nextTr.children[cellIdx].focus();
                        }
                    }
                }
            });
        });
    }

    function markDirty() {
        isDirty = true;
        const btn = document.getElementById('saveBtn');
        if (btn) btn.classList.add('has-changes');
        const btnText = document.getElementById('saveBtnText');
        if (btnText) btnText.textContent = '💾 Yadda Saxla (Ctrl+S) *';

        const statusInd = document.getElementById('saveStatusIndicator');
        if (statusInd) {
            statusInd.className = 'badge bg-warning text-dark border ms-2';
            statusInd.innerText = 'Yadda saxlanılmamış dəyişikliklər var';
        }
    }

    function recalcRow(cell) {
        const tr = cell.closest('tr');
        const cashCell = tr.querySelector('[data-field="amount_cash"]');
        const cardCell = tr.querySelector('[data-field="amount_card"]');
        const totalCell = tr.querySelector('.row-total-cell');

        const cash = parseFloat(cashCell ? cashCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;
        const card = parseFloat(cardCell ? cardCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;
        const total = cash + card;

        if (totalCell) {
            totalCell.textContent = total.toFixed(2);
            totalCell.classList.add('is-dirty');
        }

        recalcTotals();
    }

    function recalcTotals() {
        let totalCash = 0;
        let totalCard = 0;
        let count = 0;

        const rows = document.querySelectorAll('#expensesTableBody tr:not(#emptyRowPlaceholder)');
        rows.forEach(tr => {
            const cashCell = tr.querySelector('[data-field="amount_cash"]');
            const cardCell = tr.querySelector('[data-field="amount_card"]');

            const cash = parseFloat(cashCell ? cashCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;
            const card = parseFloat(cardCell ? cardCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;

            totalCash += cash;
            totalCard += card;
            count++;
        });

        const grandTotal = totalCash + totalCard;

        const footerCash = document.getElementById('footerTotalCash');
        if (footerCash) footerCash.textContent = totalCash.toFixed(2);

        const footerCard = document.getElementById('footerTotalCard');
        if (footerCard) footerCard.textContent = totalCard.toFixed(2);

        const footerGrand = document.getElementById('footerGrandTotal');
        if (footerGrand) footerGrand.textContent = grandTotal.toFixed(2);

        const bannerTotal = document.getElementById('bannerGrandTotal');
        if (bannerTotal) {
            bannerTotal.textContent = '₼ ' + grandTotal.toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        const rowCountDisplay = document.getElementById('rowCountDisplay');
        if (rowCountDisplay) rowCountDisplay.textContent = count;

        const bottomGrandTotal = document.getElementById('bottomGrandTotalDisplay');
        if (bottomGrandTotal) bottomGrandTotal.textContent = grandTotal.toFixed(2) + ' ₼';
    }

    function renumberRows() {
        const rows = document.querySelectorAll('#expensesTableBody tr:not(#emptyRowPlaceholder)');
        rows.forEach((tr, idx) => {
            const idxCell = tr.querySelector('.row-idx');
            if (idxCell) idxCell.textContent = idx + 1;
        });
    }

    function addNewRow() {
        const tableBody = document.getElementById('expensesTableBody');
        const placeholder = document.getElementById('emptyRowPlaceholder');
        if (placeholder) placeholder.remove();

        const count = tableBody.querySelectorAll('tr:not(#emptyRowPlaceholder)').length + 1;
        const today = new Date();
        const dd = String(today.getDate()).padStart(2, '0');
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const yyyy = today.getFullYear();
        const todayFormatted = `${dd}.${mm}.${yyyy}`;

        const tr = document.createElement('tr');
        tr.setAttribute('data-row-id', '');
        tr.setAttribute('data-created-at', '');
        tr.setAttribute('data-updated-at', '');
        tr.innerHTML = `
            <td class="row-idx align-center" style="background: #f8f9fa; color: #6c757d; font-weight: 600; cursor: pointer;" onclick="openExpenseDetails(this, event)" title="Sətir detalları və yaradılma vaxtına bax (created_at)">${count}</td>
            <td class="excel-cell align-center date-cell is-dirty" contenteditable="true" data-field="date" data-col="A" title="Tarixi dəyişmək üçün klikləyin. Yaradılma vaxtına baxmaq üçün saat ikonuna klikləyin.">
                ${todayFormatted}
                <button type="button" class="cell-created-indicator" contenteditable="false" onclick="openExpenseDetails(this, event)" title="Yaradılma vaxtına bax (created_at)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </button>
            </td>
            <td class="excel-cell align-left font-weight-600 text-primary is-dirty" contenteditable="true" data-field="game_name" data-col="B">Ümumi</td>
            <td class="excel-cell align-left font-weight-500 is-dirty" contenteditable="true" data-field="title" data-col="C"></td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="note" data-col="D"></td>
            <td class="excel-cell align-right font-weight-600 is-dirty" contenteditable="true" data-field="amount_cash" data-col="E" oninput="recalcRow(this)">0.00</td>
            <td class="excel-cell align-right font-weight-600 is-dirty" contenteditable="true" data-field="amount_card" data-col="F" oninput="recalcRow(this)">0.00</td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="classification" data-col="G"></td>
            <td class="align-right fw-bold row-total-cell is-dirty" style="background-color: #fafbfc;">0.00</td>
            <td class="text-center">
                <div class="row-actions-group">
                    <button type="button" class="history-row-btn" onclick="openExpenseDetails(this, event)" title="Yaradılma vaxtına bax (created_at)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </button>
                    <button type="button" class="del-row-btn" onclick="deleteRow(this)" title="Sətri sil">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>
            </td>
        `;

        tableBody.appendChild(tr);
        initCellEvents();
        markDirty();
        recalcTotals();

        const titleCell = tr.querySelector('[data-field="title"]');
        if (titleCell) titleCell.focus();
    }

    function deleteRow(btn) {
        const tr = btn.closest('tr');
        if (!tr) return;

        if (confirm('Bu xərc sətrini silmək istədiyinizdən əminsiniz?')) {
            const rowId = tr.getAttribute('data-row-id');
            if (rowId && !isNaN(rowId) && parseInt(rowId) > 0) {
                deletedIds.push(parseInt(rowId));
            }

            tr.remove();

            const tableBody = document.getElementById('expensesTableBody');
            const remainingRows = tableBody.querySelectorAll('tr:not(#emptyRowPlaceholder)');
            if (remainingRows.length === 0) {
                tableBody.innerHTML = `
                    <tr id="emptyRowPlaceholder">
                        <td colspan="10" class="text-center py-5 text-muted" style="background: #ffffff; font-size: 13px;">
                            <div class="py-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="d-block mx-auto mb-2 text-muted">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <p class="font-size-14 mb-1">Bu filial və ay üçün heç bir xərc qeydi tapılmadı.</p>
                                <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addNewRow()">
                                    ➕ İlk Sətri Əlavə Et
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                renumberRows();
            }

            recalcTotals();
            markDirty();
        }
    }

    function saveAllExpenses() {
        const rowsData = [];
        const rows = document.querySelectorAll('#expensesTableBody tr:not(#emptyRowPlaceholder)');

        rows.forEach((tr, idx) => {
            const rowObj = {};
            rowObj['id'] = tr.getAttribute('data-row-id') || '';
            tr.querySelectorAll('td.excel-cell').forEach(c => {
                const field = c.dataset.field;
                if (field) {
                    let val = c.innerText.trim();
                    if (field === 'date') {
                        const m = val.match(/\d{1,2}[.\/-]\d{1,2}[.\/-]\d{4}/);
                        if (m) val = m[0];
                    } else if (field === 'amount_cash' || field === 'amount_card') {
                        val = parseFloat(val.replace(/[^0-9.-]/g, '')) || 0;
                    }
                    rowObj[field] = val;
                }
            });
            rowObj['row_num'] = idx + 1;
            rowsData.push(rowObj);
        });

        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        const btnText = document.getElementById('saveBtnText');
        if (btnText) btnText.textContent = 'Bazada yenilənir...';

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
                expenses: rowsData,
                deleted_ids: deletedIds,
            }),
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            if (btnText) btnText.textContent = 'Yadda Saxla (Ctrl+S)';

            if (data.success) {
                isDirty = false;
                deletedIds = [];
                saveBtn.classList.remove('has-changes');

                const statusInd = document.getElementById('saveStatusIndicator');
                if (statusInd) {
                    statusInd.className = 'badge bg-success text-white ms-2';
                    statusInd.innerText = 'Bazada yeniləndi (' + data.saved_at + ')';
                }

                // Remove dirty classes
                document.querySelectorAll('.is-dirty').forEach(c => c.classList.remove('is-dirty'));

                // Əgər yeni sətirlər əlavə edilibsə və ya qeydlər yenilənibsə, data atributlarını yeniləyirik
                if (Array.isArray(data.expenses)) {
                    const activeRows = document.querySelectorAll('#expensesTableBody tr:not(#emptyRowPlaceholder)');
                    activeRows.forEach((tr, i) => {
                        const savedExp = data.expenses[i];
                        if (savedExp) {
                            if (savedExp.id) tr.setAttribute('data-row-id', savedExp.id);
                            if (savedExp.created_at) tr.setAttribute('data-created-at', savedExp.created_at);
                            if (savedExp.updated_at) tr.setAttribute('data-updated-at', savedExp.updated_at);
                        }
                    });
                }

                showToast('✓ ' + data.message, '#107c41');
            } else {
                showToast('Xəta: ' + (data.message || 'Məlumatları saxlamaq mümkün olmadı.'), '#dc3545');
            }
        })
        .catch(err => {
            saveBtn.disabled = false;
            if (btnText) btnText.textContent = 'Yadda Saxla (Ctrl+S)';
            console.error('Save error:', err);
            showToast('Serverlə əlaqə xətası baş verdi.', '#dc3545');
        });
    }

    function showToast(msg, bg = '#107c41') {
        const toast = document.getElementById('excelToast');
        if (!toast) return;
        toast.style.background = bg;
        const msgEl = document.getElementById('toastMessage');
        if (msgEl) msgEl.innerText = msg;
        const iconEl = document.getElementById('toastIcon');
        if (iconEl) iconEl.textContent = (bg === '#dc3545') ? '✕' : '✓';
        toast.style.display = 'flex';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }

    function initExcelTabs() {
        const tabsContainer = document.getElementById('excelTabsContainer');
        const activeTab = document.querySelector('.excel-tab.active');

        if (activeTab && tabsContainer) {
            setTimeout(() => {
                activeTab.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
            }, 150);
        }

        if (tabsContainer) {
            tabsContainer.addEventListener('wheel', function (e) {
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    tabsContainer.scrollLeft += e.deltaY;
                }
            }, { passive: false });

            let isDown = false;
            let startX = 0;
            let scrollLeft = 0;

            tabsContainer.addEventListener('mousedown', function (e) {
                if (e.button !== 0) return;
                isDown = true;
                tabsContainer.style.cursor = 'grabbing';
                startX = e.pageX - tabsContainer.offsetLeft;
                scrollLeft = tabsContainer.scrollLeft;
            });

            window.addEventListener('mouseup', function () {
                isDown = false;
                if (tabsContainer) tabsContainer.style.cursor = 'default';
            });

            tabsContainer.addEventListener('mousemove', function (e) {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - tabsContainer.offsetLeft;
                const walk = (x - startX) * 1.5;
                tabsContainer.scrollLeft = scrollLeft - walk;
            });
        }
    }

    /**
     * Xərc qeydiyyatı və created_at detalları modalını aç
     */
    function openExpenseDetails(el, event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }

        const tr = el.closest('tr');
        if (!tr) return;

        const rowId = tr.getAttribute('data-row-id') || '';
        const createdAt = tr.getAttribute('data-created-at') || '';
        const updatedAt = tr.getAttribute('data-updated-at') || '';
        const idx = tr.querySelector('.row-idx') ? tr.querySelector('.row-idx').innerText.trim() : '';

        // Xanaların dəyərlərini oxuyuruq
        const dateCell = tr.querySelector('[data-field="date"]');
        let dateVal = '';
        if (dateCell) {
            const m = dateCell.innerText.match(/\d{1,2}[.\/-]\d{1,2}[.\/-]\d{4}/);
            dateVal = m ? m[0] : dateCell.innerText.trim();
        }

        const gameCell = tr.querySelector('[data-field="game_name"]');
        const gameVal = gameCell ? gameCell.innerText.trim() : 'Ümumi';

        const titleCell = tr.querySelector('[data-field="title"]');
        const titleVal = titleCell ? titleCell.innerText.trim() : '';

        const noteCell = tr.querySelector('[data-field="note"]');
        const noteVal = noteCell ? noteCell.innerText.trim() : '';

        const cashCell = tr.querySelector('[data-field="amount_cash"]');
        const cashVal = parseFloat(cashCell ? cashCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;

        const cardCell = tr.querySelector('[data-field="amount_card"]');
        const cardVal = parseFloat(cardCell ? cardCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;
        const totalVal = (cashVal + cardVal).toFixed(2);

        const classCell = tr.querySelector('[data-field="classification"]');
        const classVal = classCell ? classCell.innerText.trim() : '';

        // Modala məlumatları yerləşdiririk
        const subtitleEl = document.getElementById('modalRowSubtitle');
        if (subtitleEl) {
            subtitleEl.innerText = `Sətir № ${idx || 1}` + (rowId ? ` • Qeyd ID: #${rowId}` : ' • Yeni sətir (Saxlanılmayıb)');
        }

        const createdAtCard = document.getElementById('modalCreatedAtCard');
        const createdAtValEl = document.getElementById('modalCreatedAtVal');
        const createdAtRelEl = document.getElementById('modalCreatedAtRelative');
        const unsavedNotice = document.getElementById('modalUnsavedNotice');

        if (createdAt) {
            if (createdAtCard) createdAtCard.style.display = 'flex';
            if (unsavedNotice) unsavedNotice.style.display = 'none';
            if (createdAtValEl) createdAtValEl.innerText = createdAt;
            if (createdAtRelEl) {
                createdAtRelEl.innerText = formatRelativeTime(createdAt);
            }
        } else if (!rowId) {
            // Hələ yadda saxlanılmamış yeni sətir
            if (createdAtCard) createdAtCard.style.display = 'none';
            if (unsavedNotice) unsavedNotice.style.display = 'flex';
        } else {
            // ID var, amma created_at boşdur
            if (createdAtCard) createdAtCard.style.display = 'flex';
            if (unsavedNotice) unsavedNotice.style.display = 'none';
            if (createdAtValEl) createdAtValEl.innerText = 'Qeyd olunmayıb';
            if (createdAtRelEl) createdAtRelEl.innerText = 'İlkin köhnə qeyd';
        }

        const updatedAtCard = document.getElementById('modalUpdatedAtCard');
        const updatedAtValEl = document.getElementById('modalUpdatedAtVal');
        if (updatedAt && updatedAt !== createdAt) {
            if (updatedAtCard) updatedAtCard.style.display = 'block';
            if (updatedAtValEl) updatedAtValEl.innerText = updatedAt;
        } else {
            if (updatedAtCard) updatedAtCard.style.display = 'none';
        }

        // Meta məlumatlar
        const metaDate = document.getElementById('modalMetaDate');
        if (metaDate) metaDate.innerText = dateVal || '—';

        const metaTotal = document.getElementById('modalMetaTotal');
        if (metaTotal) metaTotal.innerText = `${totalVal} ₼ (Nəğd: ${cashVal.toFixed(2)} | Kart: ${cardVal.toFixed(2)})`;

        const metaGame = document.getElementById('modalMetaGame');
        if (metaGame) metaGame.innerText = gameVal || 'Ümumi';

        const metaClass = document.getElementById('modalMetaClassification');
        if (metaClass) metaClass.innerText = classVal || '—';

        const metaTitle = document.getElementById('modalMetaTitle');
        if (metaTitle) metaTitle.innerText = titleVal || '—';

        const metaNoteWrap = document.getElementById('modalMetaNoteWrapper');
        const metaNote = document.getElementById('modalMetaNote');
        if (noteVal) {
            if (metaNoteWrap) metaNoteWrap.style.display = 'block';
            if (metaNote) metaNote.innerText = noteVal;
        } else {
            if (metaNoteWrap) metaNoteWrap.style.display = 'none';
        }

        // Kopyalama düyməsini sıfırla
        const copyBtnText = document.getElementById('copyTimestampText');
        if (copyBtnText) copyBtnText.innerText = 'Kopyala';

        // Modalı göstər
        const modal = document.getElementById('expenseDetailsModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    /**
     * Modalı bağla
     */
    function closeExpenseModal() {
        const modal = document.getElementById('expenseDetailsModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    /**
     * Arxa fona klik edildikdə bağla
     */
    function closeModalOnBackdrop(e) {
        if (e.target && e.target.id === 'expenseDetailsModal') {
            closeExpenseModal();
        }
    }

    /**
     * created_at vaxtını oxunaqlı nisbi vaxta çevir
     */
    function formatRelativeTime(dateStr) {
        try {
            const parts = dateStr.match(/^(\d{2})\.(\d{2})\.(\d{4})\s+(\d{2}):(\d{2})(?::(\d{2}))?$/);
            if (!parts) return 'Sistemdə ilkin qeydiyyat anı';
            const d = new Date(parts[3], parts[2] - 1, parts[1], parts[4], parts[5], parts[6] || 0);
            const now = new Date();
            const diffMs = now - d;
            const diffSec = Math.floor(diffMs / 1000);
            const diffMin = Math.floor(diffSec / 60);
            const diffHour = Math.floor(diffMin / 60);
            const diffDays = Math.floor(diffHour / 24);

            if (diffDays > 30) {
                return `${Math.floor(diffDays / 30)} ay əvvəl daxil edilib`;
            } else if (diffDays > 0) {
                return `${diffDays} gün əvvəl daxil edilib`;
            } else if (diffHour > 0) {
                return `${diffHour} saat əvvəl daxil edilib`;
            } else if (diffMin > 0) {
                return `${diffMin} dəqiqə əvvəl daxil edilib`;
            } else {
                return 'Bir az əvvəl daxil edilib';
            }
        } catch (e) {
            return 'Sistemdə ilkin qeydiyyat anı';
        }
    }

    /**
     * Yaradılma vaxtını kopyala
     */
    function copyModalTimestamp() {
        const valEl = document.getElementById('modalCreatedAtVal');
        if (!valEl) return;
        const text = valEl.innerText.trim();
        if (!text || text === '—') return;

        navigator.clipboard.writeText(text).then(() => {
            const btnText = document.getElementById('copyTimestampText');
            if (btnText) {
                btnText.innerText = '✓ Kopyalandı';
                setTimeout(() => { btnText.innerText = 'Kopyala'; }, 2000);
            }
        }).catch(() => {
            showToast('Vaxt kopyalandı: ' + text);
        });
    }
</script>
@endsection
