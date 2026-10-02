@extends('layouts.erp')

@section('title', 'Elektron Qaimələr — ' . $selectedBranchName . ' (' . ($selectedMonthName ?? $filterMonth) . ')')
@section('page-title', 'ELEKTRON QAİMƏLƏR')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item active">Elektron Qaimələr</li>
@endsection

@section('content')
{{-- CSRF Token for AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* Excel Workbook Design System */
    .excel-container {
        background: #ffffff;
        border: 1px solid #d4d4d4;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* Green Excel Ribbon / Top Bar (Standalone action toolbar) */
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
        padding: 5px 12px;
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

    /* Excel Grid Table */
    .excel-table-wrapper {
        max-height: 750px;
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
        table-layout: auto;
    }

    /* Row & Column Headers */
    table.excel-table th,
    table.excel-table td {
        border-right: 1px solid #d4d4d4;
        border-bottom: 1px solid #d4d4d4;
        padding: 4px 8px;
        white-space: nowrap;
        user-select: text;
    }

    table.excel-table thead tr.cat-header th {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-align: center;
        padding: 7px 8px;
        color: #ffffff;
        position: sticky;
        top: 0;
        z-index: 15;
    }

    table.excel-table thead tr.col-letter-header th {
        background: #f8f9fa;
        color: #6c757d;
        font-size: 10px;
        font-weight: 600;
        text-align: center;
        padding: 3px 4px;
        position: sticky;
        top: 31px;
        z-index: 14;
        border-top: 1px solid #d4d4d4;
        border-bottom: 1px solid #d4d4d4;
    }

    table.excel-table thead tr.col-name-header th {
        background: #f1f3f5;
        color: #212529;
        font-size: 11px;
        font-weight: 700;
        text-align: center;
        padding: 7px 8px;
        position: sticky;
        top: 52px;
        z-index: 14;
        box-shadow: 0 1px 2px rgba(0,0,0,0.06);
    }


    /* Left row number header */
    td.row-idx-header {
        background: #f8f9fa;
        color: #6c757d;
        font-weight: 600;
        text-align: center;
        font-size: 11px;
        position: sticky;
        left: 0;
        z-index: 5;
        border-right: 2px solid #adb5bd !important;
        cursor: pointer;
        width: 38px;
        min-width: 38px;
        max-width: 38px;
        user-select: none;
    }

    table.excel-table tbody tr:hover td {
        background-color: #f5fbf7;
    }

    /* Editable cell styling */
    td.excel-cell {
        outline: none;
        cursor: cell;
        transition: background-color 0.1s;
    }

    td.excel-cell:focus {
        background-color: #ffffff !important;
        box-shadow: inset 0 0 0 2px #107c41;
        z-index: 3;
        position: relative;
    }

    td.excel-cell.is-dirty {
        background-color: #fff9db !important;
    }

    /* Numbers & Alignment */
    .align-right { text-align: right; }
    .align-center { text-align: center; }
    .align-left { text-align: left; }

    /* Footer Totals Row */
    table.excel-table tfoot tr td {
        background: #eaf5ea;
        color: #0b5e30;
        font-weight: 700;
        font-size: 12px;
        position: sticky;
        bottom: 0;
        z-index: 10;
        border-top: 2px solid #107c41;
        border-bottom: 2px solid #107c41;
    }

    /* Excel Sheet Tabs Bar at bottom */
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

    /* Branch selector tabs */
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
</style>

{{-- Toast Notification Box --}}
<div id="excelToast" style="display: none; position: fixed; top: 20px; right: 25px; z-index: 9999; background: #107c41; color: white; padding: 12px 20px; border-radius: 6px; box-shadow: 0 4px 14px rgba(0,0,0,0.18); font-size: 13px; font-weight: 600; align-items: center; gap: 8px;">
    <span id="toastIcon">✓</span>
    <span id="toastMessage">Uğurla yadda saxlanıldı!</span>
</div>

{{-- 1. Filial Selector Pills Bar (Excludes "Ofis") --}}
<div class="card mb-3 border-0 shadow-sm" style="border-radius: 8px;">
    <div class="card-body p-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
        {{-- Filial Buttons --}}
        <div class="d-flex flex-wrap gap-2 flex-grow-1 align-items-center" style="max-height: 120px; overflow-y: auto;">
            @foreach($branches as $b)
                @php
                    $isActive = ($filterBranch == $b['id']);
                @endphp
                <a href="{{ route('accounting.index', ['branch_id' => $b['id'], 'month' => $filterMonth]) }}"
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

{{-- 2. Excel Ribbon Toolbar (Dedicated Action Bar) --}}
<div class="excel-ribbon mb-3">
    <div class="excel-ribbon-title">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="9" y1="3" x2="9" y2="21"></line>
            <line x1="15" y1="3" x2="15" y2="21"></line>
            <line x1="3" y1="9" x2="21" y2="9"></line>
            <line x1="3" y1="15" x2="21" y2="15"></line>
        </svg>
        <span>{{ $selectedBranchName }} — Elektron Qaimə ({{ $selectedMonthName ?? $filterMonth }})</span>
    </div>

    <div class="excel-ribbon-actions">
        {{-- Save Edits Button --}}
        <button type="button" class="excel-btn excel-btn-save" id="saveSheetBtn" onclick="saveAccountingSheet()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            <span id="saveBtnText">Yadda Saxla (Ctrl+S)</span>
        </button>

        {{-- Add Row Button --}}
        <button type="button" class="excel-btn" onclick="addNewRow()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Sətir Əlavə Et</span>
        </button>


        {{-- Native Excel (.xlsx) Download --}}
        <a href="{{ route('accounting.export', ['branch_id' => $filterBranch, 'month' => $filterMonth, 'format' => 'excel']) }}"
           class="excel-btn"
           title="Tam formatlı standart Microsoft Excel (.xlsx) faylını yüklə">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Excel Yüklə (.xlsx)</span>
        </a>

        {{-- CSV Download --}}
        <a href="{{ route('accounting.export', ['branch_id' => $filterBranch, 'month' => $filterMonth, 'format' => 'csv']) }}"
           class="excel-btn font-size-11"
           title="CSV formatında yüklə">
            CSV
        </a>
    </div>
</div>

{{-- 3. Excel Spreadsheet Grid Container (Standalone Card) --}}
<div class="excel-container mb-4">
    {{-- Spreadsheet Grid --}}
    <div class="excel-table-wrapper" id="excelWrapper">
        <table class="excel-table" id="accountingExcelTable">
            <thead>
                {{-- 1. Category Headers --}}
                <tr class="cat-header">
                    <th style="background: #e9ecef; border-right: 2px solid #ced4da; width: 38px; border-bottom: 2px solid #ffffff;"></th>
                    <th colspan="9" style="background: #1e5631; border-right: 2px solid #ffffff; border-bottom: 2px solid #ffffff;">SATIŞ MƏLUMATLARI</th>
                    <th colspan="3" style="background: #3b4252; border-right: 2px solid #ffffff; border-bottom: 2px solid #ffffff;">RESEPŞN</th>
                    <th colspan="3" style="background: #2e3440; border-right: 2px solid #ffffff; border-bottom: 2px solid #ffffff;">HOSTES</th>
                    <th colspan="3" style="background: #0f5b99; border-right: 2px solid #ffffff; border-bottom: 2px solid #ffffff;">AKTYOR 1</th>
                    <th colspan="3" style="background: #0a4270; border-right: 2px solid #ffffff; border-bottom: 2px solid #ffffff;">AKTYOR 2</th>
                    <th colspan="3" style="background: #434c5e; border-right: 2px solid #ffffff; border-bottom: 2px solid #ffffff;">OPERATOR</th>
                    <th colspan="2" style="background: #996500; border-right: 2px solid #ffffff; border-bottom: 2px solid #ffffff; color: #ffffff;">⭐ PLUS-BİR AKTYOR</th>
                    <th colspan="2" style="background: #996500; border-right: 2px solid #ffffff; border-bottom: 2px solid #ffffff; color: #ffffff;">⭐ PLUS-BİR RESEPŞN</th>
                    <th colspan="4" style="background: #1b4332; border-bottom: 2px solid #ffffff;">MÜŞTƏRİ VƏ YEKUN</th>
                </tr>

                {{-- 2. Excel Column Letter Headers (A, B, C, D...) --}}
                <tr class="col-letter-header">
                    <th class="row-idx-header"></th>
                    <th>A</th><th>B</th><th>C</th><th>D</th><th>E</th><th>F</th><th>G</th><th>H</th><th>I</th>
                    <th>J</th><th>K</th><th>L</th>
                    <th>M</th><th>N</th><th>O</th>
                    <th>P</th><th>Q</th><th>R</th>
                    <th>S</th><th>T</th><th>U</th>
                    <th>V</th><th>W</th><th>X</th>
                    <th>Y</th><th>Z</th>
                    <th>AA</th><th>AB</th>
                    <th>AC</th><th>AD</th><th>AE</th><th>AF</th>
                </tr>

                {{-- 3. Column Name Headers --}}
                <tr class="col-name-header">
                    <th class="row-idx-header">#</th>
                    <th style="min-width: 45px;">№</th>
                    <th style="min-width: 95px;">Tarix</th>
                    <th style="min-width: 140px;">Oyun Adı</th>
                    <th style="min-width: 70px;">Saat</th>
                    <th style="min-width: 55px;">Say</th>
                    <th style="min-width: 75px;">Qiymət</th>
                    <th style="min-width: 85px;">Nağd (₼)</th>
                    <th style="min-width: 85px;">Terminal (₼)</th>
                    <th style="min-width: 130px;">Qeyd / Endirim</th>

                    {{-- Resepşn --}}
                    <th style="min-width: 130px;">İşçi</th>
                    <th style="min-width: 50px;">Say</th>
                    <th style="min-width: 60px;">00:00</th>

                    {{-- Hostes --}}
                    <th style="min-width: 130px;">İşçi</th>
                    <th style="min-width: 50px;">Say</th>
                    <th style="min-width: 60px;">00:00</th>

                    {{-- Aktyor 1 --}}
                    <th style="min-width: 130px;">İşçi</th>
                    <th style="min-width: 50px;">Say</th>
                    <th style="min-width: 60px;">00:00</th>

                    {{-- Aktyor 2 --}}
                    <th style="min-width: 130px;">İşçi</th>
                    <th style="min-width: 50px;">Say</th>
                    <th style="min-width: 60px;">00:00</th>

                    {{-- Operator --}}
                    <th style="min-width: 130px;">İşçi</th>
                    <th style="min-width: 50px;">Say</th>
                    <th style="min-width: 60px;">00:00</th>

                    {{-- PlusBir Aktyor --}}
                    <th style="min-width: 130px;">İşçi</th>
                    <th style="min-width: 50px;">Say</th>

                    {{-- PlusBir Resepşn --}}
                    <th style="min-width: 130px;">İşçi</th>
                    <th style="min-width: 50px;">Say</th>

                    {{-- Müştəri & Yekun --}}
                    <th style="min-width: 130px;">Müştəri</th>
                    <th style="min-width: 110px;">Telefon</th>
                    <th style="min-width: 140px;">Qeyd</th>
                    <th style="min-width: 100px; background: #c3e6cb; color: #155724;">Ümumi Gəlir (₼)</th>
                </tr>
            </thead>

            <tbody id="excelBody">
                @forelse($sales as $index => $row)
                    @php
                        $cash = (float)($row['cash_amount'] ?? 0);
                        $card = (float)($row['card_amount'] ?? 0);
                        $total = (float)($row['total_price'] ?? ($cash + $card));
                    @endphp
                    <tr data-row-index="{{ $index }}" data-sale-id="{{ $row['id'] ?? '' }}">
                        <td class="row-idx-header">{{ $index + 1 }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="row_number" data-col="A">{{ $row['row_number'] ?? ($index + 1) }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="date" data-col="B">{{ $row['date'] ?? '' }}</td>
                        <td class="excel-cell align-left font-weight-600 text-primary" contenteditable="true" data-field="game_name" data-col="C">{{ $row['game_name'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="time" data-col="D">{{ $row['time'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="player_count" data-col="E" oninput="recalcRow(this)">{{ $row['player_count'] ?? 0 }}</td>
                        <td class="excel-cell align-right" contenteditable="true" data-field="price_per_person" data-col="F" oninput="recalcRow(this)">{{ $row['price_per_person'] ? number_format($row['price_per_person'], 2, '.', '') : '0.00' }}</td>
                        <td class="excel-cell align-right font-weight-600 text-dark" contenteditable="true" data-field="cash_amount" data-col="G" oninput="recalcRow(this)">{{ number_format($cash, 2, '.', '') }}</td>
                        <td class="excel-cell align-right font-weight-600 text-dark" contenteditable="true" data-field="card_amount" data-col="H" oninput="recalcRow(this)">{{ number_format($card, 2, '.', '') }}</td>
                        <td class="excel-cell align-left" contenteditable="true" data-field="discount_note" data-col="I">{{ $row['discount_note'] ?? '' }}</td>

                        {{-- Resepşn --}}
                        <td class="excel-cell align-left" contenteditable="true" data-field="receptionist_name" data-col="J">{{ $row['receptionist_name'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="receptionist_count" data-col="K">{{ $row['receptionist_count'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="receptionist_bonus_00" data-col="L">{{ !empty($row['receptionist_bonus_00']) ? '1' : '0' }}</td>

                        {{-- Hostes --}}
                        <td class="excel-cell align-left" contenteditable="true" data-field="hostess_name" data-col="M">{{ $row['hostess_name'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="hostess_count" data-col="N">{{ $row['hostess_count'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="hostess_bonus_00" data-col="O">{{ !empty($row['hostess_bonus_00']) ? '1' : '0' }}</td>

                        {{-- Aktyor 1 --}}
                        <td class="excel-cell align-left" contenteditable="true" data-field="actor1_name" data-col="P">{{ $row['actor1_name'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="actor1_count" data-col="Q">{{ $row['actor1_count'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="actor1_bonus_00" data-col="R">{{ !empty($row['actor1_bonus_00']) ? '1' : '0' }}</td>

                        {{-- Aktyor 2 --}}
                        <td class="excel-cell align-left" contenteditable="true" data-field="actor2_name" data-col="S">{{ $row['actor2_name'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="actor2_count" data-col="T">{{ $row['actor2_count'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="actor2_bonus_00" data-col="U">{{ !empty($row['actor2_bonus_00']) ? '1' : '0' }}</td>

                        {{-- Operator --}}
                        <td class="excel-cell align-left" contenteditable="true" data-field="operator_name" data-col="V">{{ $row['operator_name'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="operator_count" data-col="W">{{ $row['operator_count'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="operator_bonus_00" data-col="X">{{ !empty($row['operator_bonus_00']) ? '1' : '0' }}</td>

                        {{-- PlusBir Aktyor --}}
                        <td class="excel-cell align-left" contenteditable="true" data-field="plus_one_actor" data-col="Y">{{ $row['plus_one_actor'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="plus_one_actor_count" data-col="Z">{{ $row['plus_one_actor_count'] ?? '' }}</td>

                        {{-- PlusBir Resepşn --}}
                        <td class="excel-cell align-left" contenteditable="true" data-field="plus_one_receptionist" data-col="AA">{{ $row['plus_one_receptionist'] ?? '' }}</td>
                        <td class="excel-cell align-center" contenteditable="true" data-field="plus_one_receptionist_count" data-col="AB">{{ $row['plus_one_receptionist_count'] ?? '' }}</td>

                        {{-- Müştəri & Yekun --}}
                        <td class="excel-cell align-left" contenteditable="true" data-field="customer_name" data-col="AC">{{ $row['customer_name'] ?? '' }}</td>
                        <td class="excel-cell align-left" contenteditable="true" data-field="customer_phone" data-col="AD">{{ $row['customer_phone'] ?? '' }}</td>
                        <td class="excel-cell align-left" contenteditable="true" data-field="note" data-col="AE">{{ $row['note'] ?? '' }}</td>
                        <td class="excel-cell align-right font-weight-700 text-success total-price-cell" contenteditable="true" data-field="total_price" data-col="AF" oninput="recalcSheetTotals()">{{ number_format($total, 2, '.', '') }}</td>
                    </tr>
                @empty
                    <tr id="emptyRow">
                        <td colspan="33" class="text-center py-5 text-muted">
                            <p class="font-size-14 mb-1">Bu filial üçün seçilmiş ayda heç bir satış tapılmadı.</p>
                            <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addNewRow()">
                                ➕ İlk Sətri Əlavə Et
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr id="totalRow">
                    <td class="row-idx-header">Σ</td>
                    <td colspan="4" class="align-right">CƏMİ:</td>
                    <td class="align-center font-weight-700" id="totalPlayersSum">{{ number_format($summary['total_players'] ?? 0) }}</td>
                    <td class="align-right">—</td>
                    <td class="align-right font-weight-700" id="totalCashSum">{{ number_format($summary['total_cash'] ?? 0, 2, '.', '') }} ₼</td>
                    <td class="align-right font-weight-700" id="totalCardSum">{{ number_format($summary['total_card'] ?? 0, 2, '.', '') }} ₼</td>
                    <td></td>
                    <td></td>
                    <td class="align-center font-weight-700" id="totalRecSum">{{ $summary['total_sales'] ?? 0 }}</td>
                    <td></td>
                    <td></td>
                    <td class="align-center font-weight-700" id="totalHostSum">{{ $summary['total_sales'] ?? 0 }}</td>
                    <td></td>
                    <td></td>
                    <td class="align-center font-weight-700" id="totalAct1Sum">{{ $summary['total_sales'] ?? 0 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="align-center font-weight-700" id="totalOpSum">{{ $summary['total_sales'] ?? 0 }}</td>
                    <td></td>
                    <td></td>
                    <td class="align-center font-weight-700" id="totalPlusActSum">{{ $summary['total_plus_one_bonuses'] ?? 0 }}</td>
                    <td></td>
                    <td class="align-center font-weight-700" id="totalPlusRecSum">{{ $summary['total_plus_one_bonuses'] ?? 0 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="align-right font-weight-700 font-size-13" id="totalRevenueSum" style="background: #107c41; color: #ffffff;">
                        {{ number_format($summary['total_revenue'] ?? 0, 2, '.', '') }} ₼
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Bottom Excel Sheet Bar (Excel Tabs for Months & Sticky Totals) --}}
    <div class="excel-sheets-bar">
        {{-- Left Area: Horizontally Scrollable Months Tabs --}}
        <div class="excel-tabs-nav-wrapper">
            {{-- Horizontally Scrollable Tabs Container --}}
            <div class="excel-tabs-scroll-container" id="excelTabsContainer">
                @foreach($availableMonths as $m)
                    @php
                        $isMonthActive = ($filterMonth === $m['key']);
                    @endphp
                    <a href="{{ route('accounting.index', ['branch_id' => $filterBranch, 'month' => $m['key']]) }}"
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

        {{-- Right Area: Pinned Summary Statistics (Never pushed off screen) --}}
        <div class="excel-sheets-summary">
            <span class="excel-stat-item">
                <span class="text-muted">Sətir sayı:</span>
                <strong id="rowCountDisplay">{{ count($sales) }}</strong>
            </span>
            <span class="excel-stat-divider">|</span>
            <span class="excel-stat-item">
                <span class="text-muted">Ümumi Dövriyyə:</span>
                <strong class="text-success font-size-13" id="bottomRevDisplay">{{ number_format($summary['total_revenue'] ?? 0, 2) }} ₼</strong>
            </span>
        </div>
    </div>
</div>

<script>
    let activeCell = null;
    let isDirty = false;
    const branchId = "{{ $filterBranch }}";
    const month = "{{ $filterMonth }}";

    document.addEventListener('DOMContentLoaded', function () {
        initCellEvents();
        initExcelTabs();

        // Keyboard Shortcut: Ctrl+S or Cmd+S to Save
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveAccountingSheet();
            }
        });

        // Warn before leaving if changes are unsaved
        window.addEventListener('beforeunload', function (e) {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = 'Yadda saxlanılmamış dəyişikliklər var!';
                return e.returnValue;
            }
        });

        document.querySelectorAll('.excel-tab, .branch-pill').forEach(link => {
            link.addEventListener('click', function (e) {
                if (isDirty) {
                    if (!confirm('Dəyişiklikləriniz hələ yadda saxlanılmayıb! Başqa aya və ya filiala keçmək istədiyinizdən əminsiniz?')) {
                        e.preventDefault();
                    }
                }
            });
        });
    });

    function initExcelTabs() {
        const tabsContainer = document.getElementById('excelTabsContainer');
        const activeTab = document.querySelector('.excel-tab.active');

        // Səhifə yüklənəndə aktiv ayı (cari ay ən sonda yerləşir) görünən vəziyyətə gətiririk
        if (activeTab && tabsContainer) {
            setTimeout(() => {
                activeTab.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
            }, 150);
        }

        if (tabsContainer) {
            // Siçan təkəri ilə sağa-sola üfüqi sürüşdürmə (wheel horizontal scroll)
            tabsContainer.addEventListener('wheel', function (e) {
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    tabsContainer.scrollLeft += e.deltaY;
                }
            }, { passive: false });

            // Siçanla tutub sürüşdürmə (drag-to-scroll)
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

            // Enter key moves down, Tab moves right
            cell.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const tr = this.closest('tr');
                    const nextTr = tr.nextElementSibling;
                    if (nextTr) {
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
        const btn = document.getElementById('saveSheetBtn');
        btn.classList.add('has-changes');
        document.getElementById('saveBtnText').innerText = '💾 Yadda Saxla *';
        const statusInd = document.getElementById('saveStatusIndicator');
        if (statusInd) {
            statusInd.className = 'badge bg-warning text-dark border';
            statusInd.innerText = 'Yadda saxlanılmamış dəyişikliklər var';
        }
    }

    function recalcRow(cell) {
        const tr = cell.closest('tr');
        const cashCell = tr.querySelector('[data-field="cash_amount"]');
        const cardCell = tr.querySelector('[data-field="card_amount"]');
        const totalCell = tr.querySelector('[data-field="total_price"]');

        const cash = parseFloat(cashCell ? cashCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;
        const card = parseFloat(cardCell ? cardCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;

        if (totalCell) {
            const sum = (cash + card).toFixed(2);
            totalCell.innerText = sum;
            totalCell.classList.add('is-dirty');
        }

        recalcSheetTotals();
    }

    function recalcSheetTotals() {
        let totalCash = 0;
        let totalCard = 0;
        let totalRevenue = 0;
        let totalPlayers = 0;
        let count = 0;

        const rows = document.querySelectorAll('#excelBody tr');
        rows.forEach(tr => {
            if (tr.id === 'emptyRow') return;
            count++;
            const cashVal = parseFloat(tr.querySelector('[data-field="cash_amount"]')?.innerText.replace(/[^0-9.-]/g, '') || 0);
            const cardVal = parseFloat(tr.querySelector('[data-field="card_amount"]')?.innerText.replace(/[^0-9.-]/g, '') || 0);
            const totVal = parseFloat(tr.querySelector('[data-field="total_price"]')?.innerText.replace(/[^0-9.-]/g, '') || 0);
            const plyVal = parseInt(tr.querySelector('[data-field="player_count"]')?.innerText.replace(/[^0-9.-]/g, '') || 0);

            totalCash += isNaN(cashVal) ? 0 : cashVal;
            totalCard += isNaN(cardVal) ? 0 : cardVal;
            totalRevenue += isNaN(totVal) ? 0 : totVal;
            totalPlayers += isNaN(plyVal) ? 0 : plyVal;
        });

        document.getElementById('totalCashSum').innerText = totalCash.toFixed(2) + ' ₼';
        document.getElementById('totalCardSum').innerText = totalCard.toFixed(2) + ' ₼';
        document.getElementById('totalRevenueSum').innerText = totalRevenue.toFixed(2) + ' ₼';
        document.getElementById('totalPlayersSum').innerText = totalPlayers;
        document.getElementById('rowCountDisplay').innerText = count;
        document.getElementById('bottomRevDisplay').innerText = totalRevenue.toFixed(2) + ' ₼';
    }

    function addNewRow() {
        const tbody = document.getElementById('excelBody');
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        const currentCount = tbody.querySelectorAll('tr').length;
        const newIdx = currentCount + 1;
        const tr = document.createElement('tr');
        tr.dataset.rowIndex = currentCount;
        tr.dataset.saleId = '';

        tr.innerHTML = `
            <td class="row-idx-header">${newIdx}</td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="row_number" data-col="A">${newIdx}</td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="date" data-col="B">${new Date().toISOString().slice(0, 10)}</td>
            <td class="excel-cell align-left font-weight-600 text-primary is-dirty" contenteditable="true" data-field="game_name" data-col="C">Yeni Oyun</td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="time" data-col="D">12:00</td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="player_count" data-col="E" oninput="recalcRow(this)">4</td>
            <td class="excel-cell align-right is-dirty" contenteditable="true" data-field="price_per_person" data-col="F" oninput="recalcRow(this)">15.00</td>
            <td class="excel-cell align-right font-weight-600 is-dirty" contenteditable="true" data-field="cash_amount" data-col="G" oninput="recalcRow(this)">60.00</td>
            <td class="excel-cell align-right font-weight-600 is-dirty" contenteditable="true" data-field="card_amount" data-col="H" oninput="recalcRow(this)">0.00</td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="discount_note" data-col="I"></td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="receptionist_name" data-col="J"></td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="receptionist_count" data-col="K">1</td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="receptionist_bonus_00" data-col="L">0</td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="hostess_name" data-col="M"></td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="hostess_count" data-col="N">1</td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="hostess_bonus_00" data-col="O">0</td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="actor1_name" data-col="P"></td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="actor1_count" data-col="Q">1</td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="actor1_bonus_00" data-col="R">0</td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="actor2_name" data-col="S"></td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="actor2_count" data-col="T">0</td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="actor2_bonus_00" data-col="U">0</td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="operator_name" data-col="V"></td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="operator_count" data-col="W">1</td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="operator_bonus_00" data-col="X">0</td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="plus_one_actor" data-col="Y"></td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="plus_one_actor_count" data-col="Z">0</td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="plus_one_receptionist" data-col="AA"></td>
            <td class="excel-cell align-center is-dirty" contenteditable="true" data-field="plus_one_receptionist_count" data-col="AB">0</td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="customer_name" data-col="AC">Müştəri</td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="customer_phone" data-col="AD"></td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="note" data-col="AE"></td>
            <td class="excel-cell align-right font-weight-700 text-success total-price-cell is-dirty" contenteditable="true" data-field="total_price" data-col="AF" oninput="recalcSheetTotals()">60.00</td>
        `;

        tbody.appendChild(tr);
        initCellEvents();
        markDirty();
        recalcSheetTotals();

        // Focus first cell of new row
        tr.querySelector('[data-field="game_name"]').focus();
    }

    function saveAccountingSheet() {
        const rowsData = [];
        const rows = document.querySelectorAll('#excelBody tr');

        rows.forEach((tr, idx) => {
            if (tr.id === 'emptyRow') return;
            const rowObj = {};
            rowObj['id'] = tr.dataset.saleId || '';
            const cells = tr.querySelectorAll('td.excel-cell');
            cells.forEach(c => {
                const field = c.dataset.field;
                if (field) {
                    let val = c.innerText.trim();
                    if (field === 'cash_amount' || field === 'card_amount' || field === 'price_per_person' || field === 'total_price') {
                        val = parseFloat(val.replace(/[^0-9.-]/g, '')) || 0;
                    } else if (field === 'player_count' || field === 'receptionist_count' || field === 'hostess_count' || field === 'actor1_count' || field === 'actor2_count' || field === 'operator_count' || field === 'plus_one_actor_count' || field === 'plus_one_receptionist_count') {
                        val = parseInt(val.replace(/[^0-9.-]/g, '')) || 0;
                    }
                    rowObj[field] = val;
                }
            });
            rowObj['row_number'] = idx + 1;
            rowsData.push(rowObj);
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const saveBtn = document.getElementById('saveSheetBtn');
        saveBtn.disabled = true;
        document.getElementById('saveBtnText').innerText = 'Bazada yenilənir...';

        fetch("{{ route('accounting.save') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                branch_id: branchId,
                month: month,
                sales: rowsData
            })
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            if (data.success) {
                isDirty = false;
                saveBtn.classList.remove('has-changes');
                document.getElementById('saveBtnText').innerText = 'Yadda Saxla (Ctrl+S)';
                const statusInd = document.getElementById('saveStatusIndicator');
                if (statusInd) {
                    statusInd.className = 'badge bg-success text-white';
                    statusInd.innerText = 'Bazada yeniləndi (' + data.saved_at + ')';
                }

                // Remove dirty classes
                document.querySelectorAll('.is-dirty').forEach(c => c.classList.remove('is-dirty'));

                showToast('✓ ' + data.message, '#107c41');
            } else {
                showToast('Xəta baş verdi: ' + (data.message || 'Məlumat saxlanıla bilmədi.'), '#dc3545');
            }
        })
        .catch(err => {
            saveBtn.disabled = false;
            document.getElementById('saveBtnText').innerText = 'Yadda Saxla (Ctrl+S)';
            showToast('Serverlə əlaqə xətası baş verdi.', '#dc3545');
        });
    }

    function showToast(msg, bg) {
        const toast = document.getElementById('excelToast');
        toast.style.background = bg;
        document.getElementById('toastMessage').innerText = msg;
        toast.style.display = 'flex';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3500);
    }
</script>
@endsection
