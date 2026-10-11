@extends('layouts.erp')

@section('title', 'CRM Ümumi Statistika — Portal ERP')
@section('page-title', 'CRM ÜMUMİ STATİSTİKA')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="{{ route('crm.index') }}">CRM</a></li>
    <li class="breadcrumb-item active">Ümumi Statistika</li>
@endsection

@push('css')
<style>
    /* ==============================================================
       CRM OVERVIEW STATISTICS DESIGN SYSTEM
       ============================================================== */
    :root {
        --crm-radius: 14px;
        --crm-card-bg: #ffffff;
        --crm-border: #e2e8f0;
        --crm-primary: #107c41;
        --crm-primary-dark: #0b5e30;
        --crm-accent-green: #689f38;
        --crm-accent-orange: #e06b3a;
        --crm-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
        --crm-hover-shadow: 0 8px 24px rgba(15, 23, 42, 0.09);
    }

    .crm-wrapper {
        padding-bottom: 40px;
    }

    /* ---------------- Control Toolbar (Image 4 Design) ---------------- */
    .crm-filter-toolbar {
        background: #ffffff;
        border: 1px solid var(--crm-border);
        border-radius: var(--crm-radius);
        padding: 12px 20px;
        box-shadow: var(--crm-shadow);
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .crm-toolbar-status {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .crm-status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.2);
        display: inline-block;
    }

    .crm-status-text {
        font-weight: 700;
        font-size: 15px;
        color: #1e293b;
        letter-spacing: -0.2px;
    }

    .crm-api-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .crm-api-badge.badge-offline {
        background: #fef2f2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .crm-filter-controls {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .crm-filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .crm-filter-label {
        font-size: 13.5px;
        font-weight: 600;
        color: #475569;
        margin: 0;
        white-space: nowrap;
    }

    .form-select-crm {
        border-radius: 9px;
        border: 1px solid #cbd5e1;
        background-color: #ffffff;
        padding: 6px 30px 6px 12px;
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        transition: all 0.2s ease;
        cursor: pointer;
        outline: none;
    }
    .form-select-crm:focus {
        border-color: var(--crm-primary);
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.12);
    }

    .crm-btn-refresh {
        background-color: #ffffff;
        color: #334155;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        padding: 6px 16px;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .crm-btn-refresh:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .crm-btn-refresh:active {
        transform: translateY(0);
    }

    /* ---------------- Month Multi-Select Dropdown ---------------- */
    .crm-month-dropdown {
        position: relative;
    }

    .crm-month-trigger-btn {
        border-radius: 9px;
        border: 1px solid #cbd5e1;
        background-color: #ffffff;
        padding: 6px 14px;
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .crm-month-trigger-btn:hover {
        border-color: #94a3b8;
        background: #f8fafc;
    }

    .crm-month-menu {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        z-index: 1050;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.18);
        min-width: 330px;
        max-width: 380px;
        padding: 14px;
        display: none;
    }
    .crm-month-menu.show {
        display: block;
        animation: fadeInDown 0.15s ease-out;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .crm-presets-group {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .crm-preset-btn {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #334155;
        border-radius: 6px;
        padding: 4px 9px;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .crm-preset-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .crm-preset-btn.active {
        background: #107c41;
        color: #ffffff;
        border-color: #107c41;
    }

    .crm-month-list {
        max-height: 240px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .crm-month-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.15s;
        margin-bottom: 2px;
    }
    .crm-month-item:hover {
        background: #f8fafc;
    }
    .crm-month-item input[type="checkbox"] {
        cursor: pointer;
        width: 16px;
        height: 16px;
        accent-color: var(--crm-primary);
    }
    .crm-month-item label {
        margin: 0;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        flex: 1;
        padding-left: 8px;
    }

    .crm-month-menu-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 10px;
        margin-top: 10px;
        border-top: 1px solid #f1f5f9;
    }

    /* ---------------- KPI Cards Grid ---------------- */
    .crm-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .crm-kpi-card {
        background: #ffffff;
        border: 1px solid var(--crm-border);
        border-radius: var(--crm-radius);
        padding: 16px 20px;
        box-shadow: var(--crm-shadow);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .crm-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--crm-hover-shadow);
    }

    .crm-kpi-title {
        font-size: 12.5px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 4px;
    }

    .crm-kpi-val {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.2;
    }

    .crm-kpi-sub {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 4px;
    }

    .crm-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .crm-kpi-icon.green {
        background: #ecfdf5;
        color: #059669;
    }
    .crm-kpi-icon.blue {
        background: #eff6ff;
        color: #2563eb;
    }
    .crm-kpi-icon.orange {
        background: #fff7ed;
        color: #ea580c;
    }
    .crm-kpi-icon.purple {
        background: #faf5ff;
        color: #9333ea;
    }

    /* ---------------- Chart & Table Section Cards ---------------- */
    .crm-stat-section {
        background: #ffffff;
        border: 1px solid var(--crm-border);
        border-radius: var(--crm-radius);
        box-shadow: var(--crm-shadow);
        margin-bottom: 28px;
        overflow: hidden;
        transition: box-shadow 0.25s ease;
    }
    .crm-stat-section:hover {
        box-shadow: var(--crm-hover-shadow);
    }

    .crm-stat-header {
        padding: 18px 24px;
        background: #ffffff;
        border-bottom: 1px solid var(--crm-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .crm-stat-title-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .crm-stat-badge {
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.6px;
        padding: 5px 10px;
        border-radius: 6px;
        text-transform: uppercase;
    }
    .crm-stat-badge.green {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .crm-stat-badge.orange {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .crm-stat-title {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .crm-stat-subtitle {
        font-size: 12.5px;
        color: #64748b;
        margin: 2px 0 0 0;
    }

    .crm-stat-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .crm-btn-export {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        border-radius: 8px;
        padding: 5px 12px;
        font-size: 12.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .crm-btn-export:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .crm-chart-wrap {
        padding: 24px 20px 10px 20px;
        background: #ffffff;
    }

    /* ---------------- Tables ---------------- */
    .crm-table-container {
        padding: 0;
        overflow-x: auto;
        border-top: 1px solid #f1f5f9;
        background: #fafbfc;
    }

    .crm-stat-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
        font-size: 13px;
        text-align: center;
    }

    .crm-stat-table thead th {
        background: #f1f5f9;
        color: #334155;
        font-weight: 700;
        font-size: 12px;
        padding: 10px 14px;
        border-bottom: 2px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .crm-stat-table thead th:first-child {
        text-align: left;
        position: sticky;
        left: 0;
        background: #f1f5f9;
        z-index: 2;
    }
    .crm-stat-table thead th:last-child {
        border-right: none;
        background: #e2e8f0;
        color: #0f172a;
    }

    .crm-stat-table tbody td {
        padding: 11px 14px;
        color: #334155;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #f1f5f9;
        font-weight: 600;
        white-space: nowrap;
    }
    .crm-stat-table tbody td:first-child {
        text-align: left;
        font-weight: 700;
        color: #1e293b;
        background: #fafbfc;
        position: sticky;
        left: 0;
        z-index: 1;
    }
    .crm-stat-table tbody td:last-child {
        border-right: none;
        background: #f1f5f9;
        font-weight: 800;
        color: #0f172a;
    }

    .crm-stat-table tbody tr:hover td {
        background-color: #f8fafc;
    }
    .crm-stat-table tbody tr:hover td:first-child {
        background-color: #f1f5f9;
    }
    .crm-stat-table tbody tr:hover td:last-child {
        background-color: #e2e8f0;
    }

    .crm-table-badge-max {
        background: #ecfdf5;
        color: #047857;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        border: 1px solid #a7f3d0;
        display: inline-block;
    }

    .crm-table-badge-orange {
        background: #fff7ed;
        color: #c2410c;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        border: 1px solid #fed7aa;
        display: inline-block;
    }

    .crm-card-footer-info {
        padding: 12px 24px;
        background: #ffffff;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 12.5px;
        color: #64748b;
    }

    /* Print styling */
    @media print {
        .crm-filter-toolbar, .crm-stat-actions, .navbar-header, .vertical-menu {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
        .crm-stat-section {
            page-break-inside: avoid;
            margin-bottom: 30px;
        }
    }
</style>
@endpush

@section('content')
<div class="crm-wrapper">

    {{-- ============================================================== --}}
    {{-- TOP FILTER & CONTROL TOOLBAR (Image 4 replica)                --}}
    {{-- ============================================================== --}}
    <div class="crm-filter-toolbar">
        {{-- Left: Status bullet, current period & branch title, and API status badge --}}
        <div class="crm-toolbar-status">
            <span class="crm-status-dot"></span>
            <span class="crm-status-text">{{ $periodDisplay }} — {{ $selectedBranchName }}</span>

            @if($apiConnected)
                <span class="crm-api-badge" title="Portal.land API ilə canlı əlaqə aktivdir">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Portal API Aktiv
                </span>
            @else
                <span class="crm-api-badge badge-offline" title="API cavab vermədi və ya gecikdi">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    API Əlaqə Xətası
                </span>
            @endif
        </div>

        {{-- Right: Multi-month selector, Branch selector & Yenilə button --}}
        <form method="GET" action="{{ route('crm.index') }}" id="crmFilterForm" class="crm-filter-controls">
            {{-- Hidden refresh trigger --}}
            <input type="hidden" name="refresh" id="inputRefresh" value="0">

            {{-- Dövr / Ay Multi-Select Filter --}}
            <div class="crm-filter-group">
                <label class="crm-filter-label" for="monthMultiBtn">Dövr:</label>
                <div class="crm-month-dropdown" id="monthDropdownContainer">
                    <button type="button" class="crm-month-trigger-btn" id="monthMultiBtn" aria-expanded="false">
                        <span id="monthMultiLabelText">
                            @if(count($selectedMonths) === 1)
                                {{ $periodDisplay }}
                            @elseif(str_contains($periodDisplay, 'rüb'))
                                {{ $periodDisplay }}
                            @else
                                {{ count($selectedMonths) }} ay seçilib
                            @endif
                        </span>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    {{-- Dropdown Panel for Multi-month selection --}}
                    <div class="crm-month-menu" id="monthMultiMenu">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="font-size-12 fw-bold text-dark">Dövrü Seçin (Aylar)</span>
                            <div class="d-flex gap-2">
                                <a href="javascript:void(0);" id="btnSelectAllMonths" class="font-size-11 fw-semibold text-primary">Hamısı</a>
                                <span class="text-muted font-size-11">&bull;</span>
                                <a href="javascript:void(0);" id="btnClearMonths" class="font-size-11 fw-semibold text-danger">Təmizlə</a>
                            </div>
                        </div>

                        {{-- Quick Presets --}}
                        <div class="crm-presets-group">
                            <button type="button" class="crm-preset-btn" data-preset="current">Cari Ay</button>
                            <button type="button" class="crm-preset-btn" data-preset="q2_2026">2-ci Rüb</button>
                            <button type="button" class="crm-preset-btn" data-preset="q1_2026">1-ci Rüb</button>
                            <button type="button" class="crm-preset-btn" data-preset="q3_2026">3-cü Rüb</button>
                            <button type="button" class="crm-preset-btn" data-preset="all_2026">Bütün 2026</button>
                        </div>

                        {{-- Scrollable Checkboxes List --}}
                        <div class="crm-month-list" id="monthChecklist">
                            @foreach($availableMonths as $m)
                                <div class="crm-month-item">
                                    <input type="checkbox"
                                           name="months[]"
                                           id="m_cb_{{ $m['key'] }}"
                                           value="{{ $m['key'] }}"
                                           {{ in_array($m['key'], $selectedMonths) ? 'checked' : '' }}>
                                    <label for="m_cb_{{ $m['key'] }}">{{ $m['name'] }}</label>
                                </div>
                            @endforeach
                        </div>

                        {{-- Footer Actions --}}
                        <div class="crm-month-menu-footer">
                            <span class="font-size-12 fw-semibold text-muted" id="selectedCountBadge">
                                {{ count($selectedMonths) }} ay seçilib
                            </span>
                            <button type="submit" class="btn btn-sm btn-success px-3 fw-bold">
                                ✓ Tətbiq Et
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filial Selector --}}
            <div class="crm-filter-group">
                <label class="crm-filter-label" for="selectBranch">Filial:</label>
                <select name="branch_id" id="selectBranch" class="form-select-crm" onchange="submitFilterForm()">
                    <option value="all" {{ ($filterBranch === 'all' || empty($filterBranch)) ? 'selected' : '' }}>Bütün Filiallar</option>
                    @foreach($branches as $b)
                        <option value="{{ $b['id'] }}" {{ ((string)$filterBranch === (string)$b['id']) ? 'selected' : '' }}>
                            {{ $b['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Yenilə (Refresh) Button --}}
            <button type="button" class="crm-btn-refresh" id="btnRefreshData" onclick="forceRefreshData()" title="Məlumatları Portal API-dən yenidən çək">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                Yenilə
            </button>
        </form>
    </div>

    {{-- ============================================================== --}}
    {{-- KPI SUMMARY METRICS ROW                                       --}}
    {{-- ============================================================== --}}
    <div class="crm-kpi-grid">
        {{-- Card 1: Cəmi Komanda Sayı --}}
        <div class="crm-kpi-card">
            <div>
                <div class="crm-kpi-title">Cəmi Komanda Sayı</div>
                <div class="crm-kpi-val">{{ number_format($kpiSummary['total_teams'], 0, '.', ',') }}</div>
                <div class="crm-kpi-sub">Ümumi rezerv = komanda</div>
            </div>
            <div class="crm-kpi-icon green">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
        </div>

        {{-- Card 2: Cəmi Nəfər Sayı --}}
        <div class="crm-kpi-card">
            <div>
                <div class="crm-kpi-title">Cəmi Nəfər Sayı</div>
                <div class="crm-kpi-val">{{ number_format($kpiSummary['total_people'], 0, '.', ',') }}</div>
                <div class="crm-kpi-sub">Faktiki oyunçu sayı</div>
            </div>
            <div class="crm-kpi-icon blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <polyline points="16 11 18 13 22 9"></polyline>
                </svg>
            </div>
        </div>

        {{-- Card 3: Orta Komanda Heyəti --}}
        <div class="crm-kpi-card">
            <div>
                <div class="crm-kpi-title">Orta Komanda Heyəti</div>
                <div class="crm-kpi-val">{{ $kpiSummary['avg_team_size'] }} <span style="font-size: 15px; font-weight: 600; color: #64748b;">nəfər</span></div>
                <div class="crm-kpi-sub">Hər komandaya düşən iştirakçı</div>
            </div>
            <div class="crm-kpi-icon orange">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
        </div>

        {{-- Card 4: Pik Oyun Saatı --}}
        <div class="crm-kpi-card">
            <div>
                <div class="crm-kpi-title">Pik Oyun Saatı</div>
                <div class="crm-kpi-val">{{ $kpiSummary['peak_hour']['hour'] ?? '—' }}</div>
                <div class="crm-kpi-sub">{{ $kpiSummary['peak_hour']['count'] ?? 0 }} komanda ({{ $kpiSummary['peak_hour']['percentage'] ?? 0 }}%)</div>
            </div>
            <div class="crm-kpi-icon purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- STATİSTİKA 1: KOMANDA SAYI ÜZRƏ STATİSTİKA (Image 1 replica)  --}}
    {{-- ============================================================== --}}
    <div class="crm-stat-section" id="sectionTeamCount">
        <div class="crm-stat-header">
            <div class="crm-stat-title-group">
                <span class="crm-stat-badge green">CƏDVƏL 1</span>
                <div>
                    <h5 class="crm-stat-title">Komanda sayı üzrə statistika {{ $periodDisplay }}</h5>
                    <p class="crm-stat-subtitle">Faktiki iştirakçı sayına görə baş tutan komandaların (rezervlərin) sayı və payı</p>
                </div>
            </div>
            <div class="crm-stat-actions">
                <button type="button" class="crm-btn-export" onclick="exportTableToCSV('tableTeamCount', 'Komanda_Sayi_Statistika_{{ $periodDisplay }}.csv')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    İxrac (CSV)
                </button>
            </div>
        </div>

        {{-- ApexChart (Image 1 Bar Chart Replica) --}}
        <div class="crm-chart-wrap">
            <div id="chartTeamCount" style="min-height: 380px;"></div>
        </div>

        {{-- Numerical Breakdown Table --}}
        <div class="crm-table-container">
            <table class="crm-stat-table" id="tableTeamCount">
                <thead>
                    <tr>
                        <th style="min-width: 170px;">GÖSTƏRİCİ</th>
                        @foreach($teamStats['categories'] as $cat)
                            <th>{{ $cat }} nəfərlik</th>
                        @endforeach
                        <th style="min-width: 120px;">CƏMİ KOMANDA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Komanda sayı</strong></td>
                        @foreach($teamStats['categories'] as $cat)
                            <td>
                                @if($cat === $teamStats['top_size']['size'] && $teamStats['counts'][$cat] > 0)
                                    <span class="crm-table-badge-max">{{ number_format($teamStats['counts'][$cat], 0, '.', ',') }}</span>
                                @else
                                    {{ number_format($teamStats['counts'][$cat], 0, '.', ',') }}
                                @endif
                            </td>
                        @endforeach
                        <td><strong>{{ number_format($teamStats['total'], 0, '.', ',') }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Ümumi pay (%)</strong></td>
                        @foreach($teamStats['categories'] as $cat)
                            <td>{{ $teamStats['percentages'][$cat] }}%</td>
                        @endforeach
                        <td><strong>100.0%</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="crm-card-footer-info">
            <span>
                <strong>Ən populyar heyət:</strong> {{ $teamStats['top_size']['size'] }} nəfərlik komandalar
                ({{ number_format($teamStats['top_size']['count'], 0, '.', ',') }} komanda &bull; {{ $teamStats['top_size']['percentage'] }}%)
            </span>
            <span>Məlumat mənbəyi: Portal.land API Satışları</span>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- STATİSTİKA 2: ÜMUMİ NƏFƏR SAYI ÜZRƏ STATİSTİKA (Image 2 replica)--}}
    {{-- ============================================================== --}}
    <div class="crm-stat-section" id="sectionPeopleCount">
        <div class="crm-stat-header">
            <div class="crm-stat-title-group">
                <span class="crm-stat-badge green">CƏDVƏL 2</span>
                <div>
                    <h5 class="crm-stat-title">Ümumi nəfər sayı ({{ $periodDisplay }})</h5>
                    <p class="crm-stat-subtitle">Hər komanda kateqoriyası üzrə oynayan faktiki ümumi oyunçu / iştirakçı sayı</p>
                </div>
            </div>
            <div class="crm-stat-actions">
                <button type="button" class="crm-btn-export" onclick="exportTableToCSV('tablePeopleCount', 'Umumi_Nefer_Sayi_Statistika_{{ $periodDisplay }}.csv')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    İxrac (CSV)
                </button>
            </div>
        </div>

        {{-- ApexChart (Image 2 Bar Chart Replica) --}}
        <div class="crm-chart-wrap">
            <div id="chartPeopleCount" style="min-height: 380px;"></div>
        </div>

        {{-- Numerical Breakdown Table --}}
        <div class="crm-table-container">
            <table class="crm-stat-table" id="tablePeopleCount">
                <thead>
                    <tr>
                        <th style="min-width: 170px;">GÖSTƏRİCİ</th>
                        @foreach($peopleStats['categories'] as $cat)
                            <th>{{ $cat }} nəfərlik</th>
                        @endforeach
                        <th style="min-width: 120px;">CƏMİ NƏFƏR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Nəfər sayı</strong></td>
                        @foreach($peopleStats['categories'] as $cat)
                            <td>
                                @if($cat === $peopleStats['top_size']['size'] && $peopleStats['counts'][$cat] > 0)
                                    <span class="crm-table-badge-max">{{ number_format($peopleStats['counts'][$cat], 0, '.', ',') }}</span>
                                @else
                                    {{ number_format($peopleStats['counts'][$cat], 0, '.', ',') }}
                                @endif
                            </td>
                        @endforeach
                        <td><strong>{{ number_format($peopleStats['total'], 0, '.', ',') }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Ümumi pay (%)</strong></td>
                        @foreach($peopleStats['categories'] as $cat)
                            <td>{{ $peopleStats['percentages'][$cat] }}%</td>
                        @endforeach
                        <td><strong>100.0%</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="crm-card-footer-info">
            <span>
                <strong>Ən çox iştirakçı cəlb edən:</strong> {{ $peopleStats['top_size']['size'] }} nəfərlik komandalar
                ({{ number_format($peopleStats['top_size']['count'], 0, '.', ',') }} nəfər &bull; {{ $peopleStats['top_size']['percentage'] }}%)
            </span>
            <span>Məlumat mənbəyi: Portal.land API Satışları</span>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- STATİSTİKA 3: SAAT ÜZRƏ STATİSTİKA (Image 3 replica)           --}}
    {{-- ============================================================== --}}
    <div class="crm-stat-section" id="sectionHourlyCount">
        <div class="crm-stat-header">
            <div class="crm-stat-title-group">
                <span class="crm-stat-badge orange">CƏDVƏL 3</span>
                <div>
                    <h5 class="crm-stat-title">Saat üzrə statistika {{ $periodDisplay }}</h5>
                    <p class="crm-stat-subtitle">Qeyd olunan saatlar üzrə oynanılan komandaların bölgüsü və pik saatların analizi</p>
                </div>
            </div>
            <div class="crm-stat-actions">
                <button type="button" class="crm-btn-export" onclick="exportTableToCSV('tableHourlyCount', 'Saat_Uzre_Statistika_{{ $periodDisplay }}.csv')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    İxrac (CSV)
                </button>
            </div>
        </div>

        {{-- ApexChart (Image 3 Bar Chart Replica) --}}
        <div class="crm-chart-wrap">
            <div id="chartHourlyCount" style="min-height: 400px;"></div>
        </div>

        {{-- Numerical Breakdown Table --}}
        <div class="crm-table-container">
            <table class="crm-stat-table" id="tableHourlyCount">
                <thead>
                    <tr>
                        <th style="min-width: 170px;">GÖSTƏRİCİ / SAAT</th>
                        @foreach($hourStats['categories'] as $hr)
                            <th>{{ $hr }}</th>
                        @endforeach
                        <th style="min-width: 120px;">CƏMİ KOMANDA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Komanda sayı</strong></td>
                        @foreach($hourStats['categories'] as $hr)
                            <td>
                                @if($hr === $hourStats['peak']['hour'] && $hourStats['counts'][$hr] > 0)
                                    <span class="crm-table-badge-orange">{{ number_format($hourStats['counts'][$hr], 0, '.', ',') }}</span>
                                @else
                                    {{ number_format($hourStats['counts'][$hr], 0, '.', ',') }}
                                @endif
                            </td>
                        @endforeach
                        <td><strong>{{ number_format($hourStats['total'], 0, '.', ',') }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Ümumi pay (%)</strong></td>
                        @foreach($hourStats['categories'] as $hr)
                            <td>{{ $hourStats['percentages'][$hr] }}%</td>
                        @endforeach
                        <td><strong>100.0%</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="crm-card-footer-info">
            <span>
                <strong>Pik Saat:</strong> {{ $hourStats['peak']['hour'] }}
                ({{ number_format($hourStats['peak']['count'], 0, '.', ',') }} komanda &bull; {{ $hourStats['peak']['percentage'] }}%)
            </span>
            <span>Məlumat mənbəyi: Portal.land API Satışları</span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- Load ApexCharts Library --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ==============================================================
    // 1. MONTH MULTI-SELECT DROPDOWN INTERACTION
    // ==============================================================
    const monthTriggerBtn = document.getElementById('monthMultiBtn');
    const monthMenu = document.getElementById('monthMultiMenu');
    const monthCheckboxes = document.querySelectorAll('#monthChecklist input[type="checkbox"]');
    const countBadge = document.getElementById('selectedCountBadge');
    const labelText = document.getElementById('monthMultiLabelText');

    // Toggle dropdown open/close
    if (monthTriggerBtn && monthMenu) {
        monthTriggerBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            monthMenu.classList.toggle('show');
            monthTriggerBtn.setAttribute('aria-expanded', monthMenu.classList.contains('show'));
        });

        // Close when clicking outside
        document.addEventListener('click', function (e) {
            if (!monthMenu.contains(e.target) && !monthTriggerBtn.contains(e.target)) {
                monthMenu.classList.remove('show');
                monthTriggerBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Update count badge when checkboxes change
    function updateSelectedMonthsCount() {
        const checkedCount = document.querySelectorAll('#monthChecklist input[type="checkbox"]:checked').length;
        if (countBadge) {
            countBadge.innerText = checkedCount + ' ay seçilib';
        }
    }

    monthCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectedMonthsCount);
    });

    // Preset buttons handlers
    const presetButtons = document.querySelectorAll('.crm-preset-btn');
    presetButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const preset = this.getAttribute('data-preset');
            const currentYm = new Date().toISOString().slice(0, 7);

            // Clear all first
            monthCheckboxes.forEach(cb => cb.checked = false);

            if (preset === 'current') {
                const curCb = document.getElementById('m_cb_' + currentYm);
                if (curCb) curCb.checked = true;
            } else if (preset === 'q1_2026') {
                ['2026-01', '2026-02', '2026-03'].forEach(k => {
                    const el = document.getElementById('m_cb_' + k);
                    if (el) el.checked = true;
                });
            } else if (preset === 'q2_2026') {
                ['2026-04', '2026-05', '2026-06'].forEach(k => {
                    const el = document.getElementById('m_cb_' + k);
                    if (el) el.checked = true;
                });
            } else if (preset === 'q3_2026') {
                ['2026-07', '2026-08', '2026-09'].forEach(k => {
                    const el = document.getElementById('m_cb_' + k);
                    if (el) el.checked = true;
                });
            } else if (preset === 'all_2026') {
                monthCheckboxes.forEach(cb => {
                    if (cb.value.startsWith('2026-')) {
                        cb.checked = true;
                    }
                });
            }

            presetButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            updateSelectedMonthsCount();
        });
    });

    // Select all & Clear
    const btnSelectAll = document.getElementById('btnSelectAllMonths');
    const btnClear = document.getElementById('btnClearMonths');
    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function () {
            monthCheckboxes.forEach(cb => cb.checked = true);
            updateSelectedMonthsCount();
        });
    }
    if (btnClear) {
        btnClear.addEventListener('click', function () {
            monthCheckboxes.forEach(cb => cb.checked = false);
            updateSelectedMonthsCount();
        });
    }

    // ==============================================================
    // 2. CHART 1: KOMANDA SAYI ÜZRƏ STATİSTİKA (Image 1 replica)
    // ==============================================================
    const teamCats = @json($teamStats['categories']);
    const teamData = @json(array_values($teamStats['counts']));

    const optionsTeamChart = {
        series: [{
            name: 'Komanda sayı',
            data: teamData
        }],
        chart: {
            type: 'bar',
            height: 380,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            },
            fontFamily: 'Inter, sans-serif'
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '52%',
                dataLabels: {
                    position: 'top' // Display numeric value on top of bar
                }
            }
        },
        // 3D-styled Green look matching Image 1
        colors: ['#7fb756'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.35,
                gradientToColors: ['#5c9735'],
                inverseColors: false,
                opacityFrom: 0.95,
                opacityTo: 1,
                stops: [0, 90, 100]
            }
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['#b35528'] // Reddish-brown border for 3D bevel matching user printout
        },
        dataLabels: {
            enabled: true,
            offsetY: -22,
            style: {
                fontSize: '13px',
                fontWeight: 700,
                colors: ['#0f172a']
            },
            formatter: function (val) {
                return val > 0 ? val.toLocaleString() : '0';
            }
        },
        xaxis: {
            categories: teamCats,
            title: {
                text: 'Faktiki İştirakçı Sayı',
                style: {
                    fontSize: '13px',
                    fontWeight: 600,
                    color: '#64748b'
                }
            },
            labels: {
                style: {
                    fontSize: '13px',
                    fontWeight: 700,
                    colors: '#1e293b'
                }
            },
            axisBorder: { show: true, color: '#cbd5e1' },
            axisTicks: { show: true, color: '#cbd5e1' }
        },
        yaxis: {
            title: {
                text: 'Komanda sayı',
                style: {
                    fontSize: '13px',
                    fontWeight: 700,
                    color: '#334155'
                }
            },
            labels: {
                style: {
                    fontSize: '12px',
                    fontWeight: 500,
                    colors: '#64748b'
                },
                formatter: function (val) {
                    return Math.round(val).toLocaleString();
                }
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 3,
            yaxis: { lines: { show: true } }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toLocaleString() + ' komanda';
                }
            }
        }
    };

    const teamChartEl = document.querySelector("#chartTeamCount");
    if (teamChartEl) {
        const teamChart = new ApexCharts(teamChartEl, optionsTeamChart);
        teamChart.render();
    }

    // ==============================================================
    // 3. CHART 2: ÜMUMİ NƏFƏR SAYI ÜZRƏ STATİSTİKA (Image 2 replica)
    // ==============================================================
    const peopleCats = @json($peopleStats['categories']);
    const peopleData = @json(array_values($peopleStats['counts']));

    const optionsPeopleChart = {
        series: [{
            name: 'Nəfər sayı',
            data: peopleData
        }],
        chart: {
            type: 'bar',
            height: 380,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            },
            fontFamily: 'Inter, sans-serif'
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '52%',
                dataLabels: {
                    position: 'top' // Display numeric value on top of bar
                }
            }
        },
        // 3D-styled Green look matching Image 2
        colors: ['#7fb756'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.35,
                gradientToColors: ['#5c9735'],
                inverseColors: false,
                opacityFrom: 0.95,
                opacityTo: 1,
                stops: [0, 90, 100]
            }
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['#b35528'] // Reddish-brown border for 3D bevel matching user printout
        },
        dataLabels: {
            enabled: true,
            offsetY: -22,
            style: {
                fontSize: '13px',
                fontWeight: 700,
                colors: ['#0f172a']
            },
            formatter: function (val) {
                return val > 0 ? val.toLocaleString() : '0';
            }
        },
        xaxis: {
            categories: peopleCats,
            title: {
                text: 'Faktiki Oyunçu Sayı',
                style: {
                    fontSize: '13px',
                    fontWeight: 600,
                    color: '#64748b'
                }
            },
            labels: {
                style: {
                    fontSize: '13px',
                    fontWeight: 700,
                    colors: '#1e293b'
                }
            },
            axisBorder: { show: true, color: '#cbd5e1' },
            axisTicks: { show: true, color: '#cbd5e1' }
        },
        yaxis: {
            title: {
                text: 'Nəfər sayı',
                style: {
                    fontSize: '13px',
                    fontWeight: 700,
                    color: '#334155'
                }
            },
            labels: {
                style: {
                    fontSize: '12px',
                    fontWeight: 500,
                    colors: '#64748b'
                },
                formatter: function (val) {
                    return Math.round(val).toLocaleString();
                }
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 3,
            yaxis: { lines: { show: true } }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toLocaleString() + ' nəfər';
                }
            }
        }
    };

    const peopleChartEl = document.querySelector("#chartPeopleCount");
    if (peopleChartEl) {
        const peopleChart = new ApexCharts(peopleChartEl, optionsPeopleChart);
        peopleChart.render();
    }

    // ==============================================================
    // 4. CHART 3: SAAT ÜZRƏ STATİSTİKA (Image 3 replica)
    // ==============================================================
    const hourCats = @json($hourStats['categories']);
    const hourData = @json(array_values($hourStats['counts']));

    const optionsHourChart = {
        series: [{
            name: 'Komanda sayı',
            data: hourData
        }],
        chart: {
            type: 'bar',
            height: 400,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            },
            fontFamily: 'Inter, sans-serif'
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '55%',
                dataLabels: {
                    position: 'top'
                }
            }
        },
        // 3D-styled Orange/Terracotta look matching Image 3
        colors: ['#e06b3a'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.35,
                gradientToColors: ['#c45124'],
                inverseColors: false,
                opacityFrom: 0.95,
                opacityTo: 1,
                stops: [0, 90, 100]
            }
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['#9c3814'] // Darker terracotta border for 3D bevel matching Image 3
        },
        dataLabels: {
            enabled: true,
            offsetY: -22,
            style: {
                fontSize: '12.5px',
                fontWeight: 700,
                colors: ['#0f172a']
            },
            formatter: function (val) {
                return val > 0 ? val.toLocaleString() : '0';
            }
        },
        xaxis: {
            categories: hourCats,
            title: {
                text: 'Saat',
                style: {
                    fontSize: '13px',
                    fontWeight: 700,
                    color: '#334155'
                }
            },
            labels: {
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                    colors: '#1e293b'
                }
            },
            axisBorder: { show: true, color: '#cbd5e1' },
            axisTicks: { show: true, color: '#cbd5e1' }
        },
        yaxis: {
            title: {
                text: 'Komanda sayı',
                style: {
                    fontSize: '13px',
                    fontWeight: 700,
                    color: '#334155'
                }
            },
            labels: {
                style: {
                    fontSize: '12px',
                    fontWeight: 500,
                    colors: '#64748b'
                },
                formatter: function (val) {
                    return Math.round(val).toLocaleString();
                }
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 3,
            yaxis: { lines: { show: true } }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toLocaleString() + ' komanda';
                }
            }
        }
    };

    const hourChartEl = document.querySelector("#chartHourlyCount");
    if (hourChartEl) {
        const hourChart = new ApexCharts(hourChartEl, optionsHourChart);
        hourChart.render();
    }
});

// Helper: Submit filter form on change
function submitFilterForm() {
    const form = document.getElementById('crmFilterForm');
    if (form) {
        document.getElementById('inputRefresh').value = '0';
        form.submit();
    }
}

// Helper: Force live refresh from Portal API
function forceRefreshData() {
    const refreshBtn = document.getElementById('btnRefreshData');
    if (refreshBtn) {
        refreshBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width: 13px; height: 13px;"></span>
            Yenilənir...
        `;
        refreshBtn.disabled = true;
    }
    const form = document.getElementById('crmFilterForm');
    if (form) {
        document.getElementById('inputRefresh').value = '1';
        form.submit();
    }
}

// Helper: Export Table to CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');

    for (let i = 0; i < rows.length; i++) {
        let row = [];
        const cols = rows[i].querySelectorAll('th, td');
        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.trim().replace(/"/g, '""');
            row.push('"' + text + '"');
        }
        csv.push(row.join(','));
    }

    const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + encodeURIComponent(csv.join('\n'));
    const link = document.createElement('a');
    link.setAttribute('href', csvContent);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endpush
