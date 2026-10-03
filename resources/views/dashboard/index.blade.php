@extends('layouts.erp')

@section('title', 'Dashboard')
@section('page-title', 'DASHBOARD & MALİYYƏ ANALİTİKASI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@push('css')
<style>
    /* ==============================================================
       SOFT & MODERN DASHBOARD DESIGN SYSTEM
       ============================================================== */
    :root {
        --dash-radius: 18px;
        --dash-card-bg: #ffffff;
        --dash-border: #edf2ee;
        --dash-shadow: 0 4px 18px rgba(18, 38, 63, 0.04);
        --dash-hover-shadow: 0 10px 28px rgba(16, 124, 65, 0.08);
    }

    .dashboard-wrapper {
        padding-bottom: 24px;
    }

    /* ---------------- Control Toolbar ---------------- */
    .dashboard-toolbar {
        background: #ffffff;
        border: 1px solid var(--dash-border);
        border-radius: 16px;
        padding: 14px 22px;
        box-shadow: var(--dash-shadow);
        margin-bottom: 24px;
    }

    .form-select-soft {
        border-radius: 12px;
        border: 1px solid #e2e8e4;
        background-color: #fafbfc;
        padding: 7px 32px 7px 14px;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    .form-select-soft:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.12);
        background-color: #ffffff;
    }

    .btn-soft-primary {
        background-color: #107c41;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 7px 16px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-soft-primary:hover {
        background-color: #0b5e30;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.25);
    }

    .btn-soft-light {
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 7px 14px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-soft-light:hover {
        background-color: #f1f5f9;
        color: #1e293b;
        transform: translateY(-1px);
    }
    .btn-soft-light:hover svg {
        transform: rotate(180deg);
    }
    .btn-soft-light svg {
        transition: transform 0.4s ease;
    }

    /* ---------------- Responsive 4 KPI Cards Grid ---------------- */
    .kpi-cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }
    @media (max-width: 1040px) {
        .kpi-cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 22px; /* Generous gap when wrapping to 2 rows */
        }
    }
    @media (max-width: 576px) {
        .kpi-cards-grid {
            grid-template-columns: 1fr;
            gap: 18px;
        }
    }

    .kpi-soft-card {
        background: var(--dash-card-bg);
        border: 1px solid var(--dash-border);
        border-radius: var(--dash-radius);
        padding: 20px 22px;
        box-shadow: var(--dash-shadow);
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }
    .kpi-soft-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--dash-hover-shadow);
        border-color: #d1fae5;
    }

    .kpi-soft-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .kpi-soft-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        margin: 0;
    }

    .kpi-soft-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
    }
    .kpi-soft-card:hover .kpi-soft-icon {
        transform: scale(1.08);
    }

    .kpi-soft-val {
        font-size: 25px;
        font-weight: 800;
        letter-spacing: -0.5px;
        line-height: 1.2;
        color: #1e293b;
        margin: 0 0 6px 0;
    }

    .kpi-soft-footer {
        font-size: 12px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
    }

    /* Ratio Progress Bar */
    .soft-ratio-wrap {
        margin: 10px 0 6px;
    }
    .soft-ratio-bar {
        height: 7px;
        border-radius: 999px;
        background: #f1f5f9;
        overflow: hidden;
        display: flex;
    }
    .soft-ratio-card { background: #0284c7; }
    .soft-ratio-cash { background: #f59e0b; }

    /* Pill Badges */
    .soft-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }
    .soft-pill-success { background: #ecfdf5; color: #059669; }
    .soft-pill-danger { background: #fef2f2; color: #dc2626; }
    .soft-pill-neutral { background: #f1f5f9; color: #475569; }

    /* ---------------- Analytics Container Cards ---------------- */
    .soft-box {
        background: #ffffff;
        border: 1px solid var(--dash-border);
        border-radius: var(--dash-radius);
        box-shadow: var(--dash-shadow);
        margin-bottom: 24px;
    }

    .soft-box-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafbfc;
        border-top-left-radius: var(--dash-radius);
        border-top-right-radius: var(--dash-radius);
    }

    .soft-box-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .soft-box-body {
        padding: 20px 22px;
    }

    /* ---------------- Branch Table ---------------- */
    .branch-clean-table {
        margin: 0;
    }
    .branch-clean-table th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #edf2ee;
        padding: 14px 16px;
        white-space: nowrap;
    }
    .branch-clean-table td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.15s ease;
    }
    .branch-clean-table tr:hover td {
        background-color: #fbfdfc;
    }

    .branch-icon-badge {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #f0fdf4;
        color: #16a34a;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .branch-name-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #1e293b;
    }

    /* Rank Medals */
    .rank-circle {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
    }
    .rank-gold { background: #fef3c7; color: #92400e; }
    .rank-silver { background: #f1f5f9; color: #334155; }
    .rank-bronze { background: #ffedd5; color: #9a3412; }
    .rank-other { background: #f8fafc; color: #64748b; }

    /* Progress pill for revenue share */
    .soft-share-bar {
        width: 100%;
        max-width: 110px;
        height: 6px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
    }
    .soft-share-fill {
        height: 100%;
        background: #107c41;
        border-radius: 999px;
    }

    /* User-friendly soft table 100% pill */
    .soft-table-percent {
        background: #e6f4ea;
        color: #137333;
        border: 1px solid #ceead6;
        font-weight: 700;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 999px;
        display: inline-block;
    }

    /* ---------------- Payment Method Cards (Under Donut) ---------------- */
    .pm-soft-badge-card {
        border-radius: 14px;
        padding: 13px 16px;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    .pm-soft-badge-card:hover {
        transform: translateY(-2px);
    }
    .pm-theme-terminal {
        background: #f0f9ff;
        border-color: #e0f2fe;
    }
    .pm-theme-cash {
        background: #fffbeb;
        border-color: #fef3c7;
    }
    .pm-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .pm-dot-blue { background: #0284c7; }
    .pm-dot-amber { background: #d97706; }

    .pm-type-name {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        white-space: nowrap;
    }
    .pm-rate-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .pm-pill-blue {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .pm-pill-amber {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .pm-type-val {
        font-size: 17px;
        font-weight: 800;
        letter-spacing: -0.3px;
    }

    /* ---------------- Top 5 Games Sleek Leaderboard ---------------- */
    .top-games-wrapper {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .top-game-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: 12px;
        background: #fafbfc;
        border: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }
    .top-game-row:hover {
        background: #ffffff;
        border-color: #d1fae5;
        box-shadow: 0 3px 10px rgba(16, 124, 65, 0.05);
        transform: translateX(2px);
    }

    .game-medal-pill {
        font-size: 11px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        flex-shrink: 0;
    }
    .medal-1 { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .medal-2 { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
    .medal-3 { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
    .medal-sub { background: #f8fafc; color: #64748b; border: 1px solid #edf2ee; }

    .game-clean-name {
        font-size: 13.5px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 2px 0;
    }
    .game-clean-sub {
        font-size: 12px;
        color: #64748b;
    }
    .game-dot {
        margin: 0 4px;
        opacity: 0.6;
    }
    .game-clean-price {
        font-size: 14.5px;
        font-weight: 800;
        color: #107c41;
        white-space: nowrap;
    }

    .game-mini-bar-track {
        width: 85px;
        height: 4px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
        margin-left: auto;
    }
    .game-mini-bar-fill {
        height: 100%;
        background: #107c41;
        border-radius: 999px;
    }

    /* Live pulse animation */
    .soft-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #107c41;
        display: inline-block;
        position: relative;
    }
    .soft-pulse-dot::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        border-radius: 50%;
        background-color: #107c41;
        animation: pulse 1.8s infinite ease-out;
    }
    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(3); opacity: 0; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">

    {{-- Top Filter & Control Toolbar --}}
    <div class="dashboard-toolbar d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="soft-pulse-dot"></span>
                <span class="font-size-14 font-weight-700 text-dark">
                    {{ $selectedMonthName }} — {{ $selectedBranchName }}
                </span>
            </div>
            <span class="badge {{ $connected ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }} font-size-11 px-2 py-1">
                {{ $connected ? '✓ Portal API Aktiv' : '⚠️ Lokal Rejim' }}
            </span>
        </div>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('dashboard') }}" id="filterForm" class="d-flex flex-wrap align-items-center gap-2 m-0">
            {{-- Month Filter --}}
            <div class="d-flex align-items-center gap-1">
                <label for="monthSelect" class="font-size-12 text-muted m-0 font-weight-600 d-none d-sm-inline">Dövr:</label>
                <select name="month" id="monthSelect" class="form-select form-select-soft font-weight-600" style="min-width: 165px;" onchange="this.form.submit()">
                    @foreach($availableMonths as $m)
                        <option value="{{ $m['key'] }}" {{ $filterMonth === $m['key'] ? 'selected' : '' }}>
                            {{ $m['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Branch Filter --}}
            <div class="d-flex align-items-center gap-1">
                <label for="branchSelect" class="font-size-12 text-muted m-0 font-weight-600 d-none d-sm-inline">Filial:</label>
                <select name="branch_id" id="branchSelect" class="form-select form-select-soft font-weight-500" style="min-width: 175px;" onchange="this.form.submit()">
                    <option value="all" {{ empty($filterBranch) ? 'selected' : '' }}>Bütün Filiallar</option>
                    @foreach($branches as $b)
                        <option value="{{ $b['id'] }}" {{ (string)$filterBranch === (string)$b['id'] ? 'selected' : '' }}>
                            {{ $b['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Refresh Button --}}
            <button type="submit" name="refresh" value="1" class="btn btn-soft-light d-inline-flex align-items-center gap-1 font-size-12" title="Məlumatları yenilə (keşi təmizlə)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                <span>Yenilə</span>
            </button>

            {{-- Shortcut to Accounting --}}
            <a href="{{ route('accounting.index', ['month' => $filterMonth, 'branch_id' => $filterBranch]) }}" class="btn btn-soft-primary d-inline-flex align-items-center gap-1 font-size-12 ms-sm-1">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                    <path d="M3 9h18"></path>
                    <path d="M9 21V9"></path>
                </svg>
                <span>Qaimələr</span>
            </a>
        </form>
    </div>

    {{-- TOP 4 EXECUTIVE KPI CARDS (ALL 4 SIDE-BY-SIDE ON DESKTOP, BALANCED GAP WHEN WRAPPED) --}}
    <div class="kpi-cards-grid">
        {{-- Card 1: Total Revenue (Ümumi Dövriyyə) --}}
        <div class="kpi-soft-card">
            <div>
                <div class="kpi-soft-header">
                    <p class="kpi-soft-label">Ümumi Dövriyyə</p>
                    <div class="kpi-soft-icon" style="background: #f0fdf4; color: #16a34a;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <line x1="12" x2="12" y1="2" y2="22"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                </div>
                <div class="kpi-soft-val text-success">
                    {{ number_format($kpi['total_revenue'], 2) }} <span class="font-size-18 font-weight-600">₼</span>
                </div>
            </div>
            <div class="kpi-soft-footer">
                @if($growth['has_prev'])
                    <span class="soft-pill {{ $growth['is_positive'] ? 'soft-pill-success' : 'soft-pill-danger' }}">
                        {{ $growth['is_positive'] ? '▲ +' : '▼ ' }}{{ $growth['percent'] }}%
                    </span>
                    <span>ötən aya nəzərən ({{ number_format($growth['prev_revenue'], 0) }} ₼)</span>
                @else
                    <span class="text-muted">Seçilmiş dövrün ümumi məbləği</span>
                @endif
            </div>
        </div>

        {{-- Card 2: Cash vs Terminal Breakdown (Clean & Spacious, No Duplicated Text) --}}
        <div class="kpi-soft-card">
            <div>
                <div class="kpi-soft-header">
                    <p class="kpi-soft-label">Ödəniş Balansı</p>
                    <div class="kpi-soft-icon" style="background: #f0f9ff; color: #0284c7;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                            <line x1="2" x2="22" y1="10" y2="10"></line>
                        </svg>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div>
                        <span class="font-size-11 text-muted d-block font-weight-600">KART ({{ $kpi['card_percent'] }}%)</span>
                        <span class="font-size-15 font-weight-800 text-info">{{ number_format($kpi['total_card'], 2) }} ₼</span>
                    </div>
                    <div class="text-end">
                        <span class="font-size-11 text-muted d-block font-weight-600">NAĞD ({{ $kpi['cash_percent'] }}%)</span>
                        <span class="font-size-15 font-weight-800 text-warning">{{ number_format($kpi['total_cash'], 2) }} ₼</span>
                    </div>
                </div>

                <div class="soft-ratio-wrap">
                    <div class="soft-ratio-bar">
                        <div class="soft-ratio-card" style="width: {{ $kpi['card_percent'] }}%;" title="Terminal: {{ $kpi['card_percent'] }}%"></div>
                        <div class="soft-ratio-cash" style="width: {{ $kpi['cash_percent'] }}%;" title="Nağd: {{ $kpi['cash_percent'] }}%"></div>
                    </div>
                </div>
            </div>

            <div class="kpi-soft-footer justify-content-between">
                <span class="soft-pill" style="background: #f0f9ff; color: #0284c7;">Terminal: {{ number_format($kpi['total_card'], 0) }} ₼</span>
                <span class="soft-pill" style="background: #fffbeb; color: #d97706;">Nağd: {{ number_format($kpi['total_cash'], 0) }} ₼</span>
            </div>
        </div>

        {{-- Card 3: Total Reservations --}}
        <div class="kpi-soft-card">
            <div>
                <div class="kpi-soft-header">
                    <p class="kpi-soft-label">Rezervasiyalar</p>
                    <div class="kpi-soft-icon" style="background: #f5f3ff; color: #7c3aed;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                            <line x1="16" x2="16" y1="2" y2="6"></line>
                            <line x1="8" x2="8" y1="2" y2="6"></line>
                            <line x1="3" x2="21" y1="10" y2="10"></line>
                        </svg>
                    </div>
                </div>
                <div class="kpi-soft-val text-dark">
                    {{ number_format($kpi['total_sales']) }} <span class="font-size-16 font-weight-600 text-muted">rezervasiya</span>
                </div>
            </div>
            <div class="kpi-soft-footer">
                <span class="badge bg-light text-dark border font-size-11">Orta Rezervasiya: {{ number_format($kpi['avg_ticket'], 2) }} ₼</span>
                <span>bir oyun üzrə</span>
            </div>
        </div>

        {{-- Card 4: Top Performing Branch --}}
        @php
            $topBranch = $branchStats[0] ?? null;
        @endphp
        <div class="kpi-soft-card">
            <div>
                <div class="kpi-soft-header">
                    <p class="kpi-soft-label">Lider Filial 🏆</p>
                    <div class="kpi-soft-icon" style="background: #fffbeb; color: #d97706;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </div>
                </div>
                @if($topBranch && $topBranch['revenue'] > 0)
                    <div class="kpi-soft-val font-size-20 text-truncate text-dark" title="{{ $topBranch['name'] }}">
                        {{ $topBranch['name'] }}
                    </div>
                    <div class="font-size-13 font-weight-700 text-success">
                        {{ number_format($topBranch['revenue'], 2) }} ₼
                        <span class="font-size-11 text-muted font-weight-500">({{ $topBranch['share_percent'] }}% gəlir payı)</span>
                    </div>
                @else
                    <div class="kpi-soft-val font-size-18 text-muted">Məlumat yoxdur</div>
                @endif
            </div>
            <div class="kpi-soft-footer">
                @if($topBranch && $topBranch['sales_count'] > 0)
                    <span>{{ $topBranch['sales_count'] }} rezervasiya &bull; {{ number_format($topBranch['players']) }} oyunçu</span>
                @else
                    <span>Dövr üzrə rezervasiya yoxdur</span>
                @endif
            </div>
        </div>
    </div>

    {{-- MAIN ANALYTICS ROW: REVENUE TREND + BRANCH COMPARISON CHART --}}
    <div class="row">
        {{-- Left: Daily Revenue Dynamics (Area Chart) --}}
        <div class="col-xl-8">
            <div class="soft-box">
                <div class="soft-box-header">
                    <div>
                        <h4 class="soft-box-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                <polyline points="17 6 23 6 23 12"></polyline>
                            </svg>
                            Gəlir və Rezervasiya Dinamikası (Günlər üzrə)
                        </h4>
                        <p class="text-muted font-size-12 mb-0 mt-1">
                            {{ $selectedMonthName }} ayı üzrə gündəlik ümumi gəlir, kart və nağd ödənişlərin axını
                        </p>
                    </div>

                    @if(!empty($dailyTrend['peak_day']))
                        <div class="text-end d-none d-md-block">
                            <span class="font-size-11 text-muted d-block">Pik Gün: <strong>{{ $dailyTrend['peak_day']['day'] }} {{ $selectedMonthName }}</strong></span>
                            <span class="badge bg-soft-success text-success font-size-11 font-weight-700">
                                {{ number_format($dailyTrend['peak_day']['revenue'], 2) }} ₼ ({{ $dailyTrend['peak_day']['sales_count'] }} rezervasiya)
                            </span>
                        </div>
                    @endif
                </div>
                <div class="soft-box-body">
                    <div id="dailyRevenueChart" style="min-height: 330px;"></div>
                </div>
            </div>
        </div>

        {{-- Right: Top Branches Revenue Share (Horizontal Bar Chart) --}}
        <div class="col-xl-4">
            <div class="soft-box">
                <div class="soft-box-header">
                    <div>
                        <h4 class="soft-box-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2.2">
                                <line x1="18" x2="18" y1="20" y2="10"></line>
                                <line x1="12" x2="12" y1="20" y2="4"></line>
                                <line x1="6" x2="6" y1="20" y2="14"></line>
                            </svg>
                            Filialların Gəlir Reytinqi
                        </h4>
                        <p class="text-muted font-size-12 mb-0 mt-1">Gəlir həcminə görə ən güclü filiallar</p>
                    </div>
                </div>
                <div class="soft-box-body p-2">
                    <div id="branchRankingChart" style="min-height: 345px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAILED BRANCH PERFORMANCE COMPARISON TABLE --}}
    <div class="row">
        <div class="col-12">
            <div class="soft-box">
                <div class="soft-box-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h4 class="soft-box-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2">
                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                <path d="M3 9h18"></path>
                                <path d="M9 21V9"></path>
                            </svg>
                            Filiallar üzrə Detallı Müqayisəli Maliyyə Cədvəli
                        </h4>
                        <p class="text-muted font-size-12 mb-0 mt-1">
                            Bütün filialların cari dövr üzrə dövriyyəsi, ödəniş formaları və gəlir payı
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border font-size-12 px-2 py-1">
                            {{ count($branchStats) }} Filial Analiz Edildi
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table branch-clean-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;" class="text-center">№</th>
                                <th>Filial</th>
                                <th class="text-center">Rezervasiyalar</th>
                                <th class="text-center">Oyunçular</th>
                                <th class="text-end">Nağd</th>
                                <th class="text-end">Terminal (Kart)</th>
                                <th class="text-end">Cəmi Gəlir</th>
                                <th style="width: 150px;">Gəlir Payı</th>
                                <th class="text-end">Orta Rezervasiya</th>
                                <th class="text-center" style="width: 100px;">Qaimə</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($branchStats as $bs)
                                <tr>
                                    {{-- Rank --}}
                                    <td class="text-center">
                                        @if($bs['rank'] == 1 && $bs['revenue'] > 0)
                                            <span class="rank-circle rank-gold" title="1-ci yer">🥇</span>
                                        @elseif($bs['rank'] == 2 && $bs['revenue'] > 0)
                                            <span class="rank-circle rank-silver" title="2-ci yer">🥈</span>
                                        @elseif($bs['rank'] == 3 && $bs['revenue'] > 0)
                                            <span class="rank-circle rank-bronze" title="3-cü yer">🥉</span>
                                        @else
                                            <span class="rank-circle rank-other">#{{ $bs['rank'] }}</span>
                                        @endif
                                    </td>

                                    {{-- Branch Name with Soft Store Icon (NO UGLY INITIAL CIRCLE, NO ID) --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="branch-icon-badge">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                                </svg>
                                            </div>
                                            <a href="{{ route('accounting.index', ['branch_id' => $bs['id'], 'month' => $filterMonth]) }}" class="branch-name-title hover-primary text-decoration-none">
                                                {{ $bs['name'] }}
                                            </a>
                                        </div>
                                    </td>

                                    {{-- Reservations Count --}}
                                    <td class="text-center font-weight-700">
                                        {{ number_format($bs['sales_count']) }}
                                    </td>

                                    {{-- Players Count --}}
                                    <td class="text-center text-muted font-weight-600">
                                        {{ number_format($bs['players']) }}
                                    </td>

                                    {{-- Cash Amount --}}
                                    <td class="text-end font-weight-700 text-warning">
                                        {{ number_format($bs['cash'], 2) }} ₼
                                    </td>

                                    {{-- Card Amount --}}
                                    <td class="text-end font-weight-700 text-info">
                                        {{ number_format($bs['card'], 2) }} ₼
                                    </td>

                                    {{-- Total Revenue --}}
                                    <td class="text-end font-weight-800 text-success font-size-14">
                                        {{ number_format($bs['revenue'], 2) }} ₼
                                    </td>

                                    {{-- Revenue Share % --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="font-size-11 font-weight-700 text-dark" style="min-width: 38px;">{{ $bs['share_percent'] }}%</span>
                                            <div class="soft-share-bar">
                                                <div class="soft-share-fill" style="width: {{ min(100, $bs['share_percent']) }}%;"></div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Average Ticket (1 rezervasiyadan orta gəlir) --}}
                                    <td class="text-end font-weight-600 text-muted">
                                        {{ number_format($bs['avg_price'], 2) }} ₼
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-center">
                                        <a href="{{ route('accounting.index', ['branch_id' => $bs['id'], 'month' => $filterMonth]) }}" class="btn btn-sm btn-light border py-1 px-2 font-size-11 rounded-3" title="Bu filialın elektron qaiməsinə keç">
                                            Qaimə
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        Bu dövr üzrə filial rezervasiyası tapılmadı.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(!empty($branchStats))
                            <tfoot class="border-top-2">
                                <tr style="background: #f8fafc; font-weight: 700; border-top: 2px solid #edf2ee;">
                                    <td colspan="2" class="text-end text-dark font-size-13 py-3">CƏMİ ŞƏBƏKƏ:</td>
                                    <td class="text-center text-dark font-weight-800">{{ number_format($kpi['total_sales']) }}</td>
                                    <td class="text-center text-dark font-weight-800">{{ number_format($kpi['total_players']) }}</td>
                                    <td class="text-end text-warning font-weight-800">{{ number_format($kpi['total_cash'], 2) }} ₼</td>
                                    <td class="text-end text-info font-weight-800">{{ number_format($kpi['total_card'], 2) }} ₼</td>
                                    <td class="text-end text-success font-size-15 font-weight-800">{{ number_format($kpi['total_revenue'], 2) }} ₼</td>
                                    <td>
                                        <span class="soft-table-percent">100.0%</span>
                                    </td>
                                    <td class="text-end text-dark">{{ number_format($kpi['avg_ticket'], 2) }} ₼</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- LOWER ROW: PAYMENT DONUT + TOP GAMES (SPACIOUS, USER-FRIENDLY & NO CLIPPING) --}}
    <div class="row g-3">
        {{-- Left: Payment Method Donut Chart --}}
        <div class="col-xl-5 col-lg-6">
            <div class="soft-box mb-0">
                <div class="soft-box-header">
                    <div>
                        <h4 class="soft-box-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 2a10 10 0 0 1 10 10h-10z"></path>
                            </svg>
                            Ödəniş Növləri Balansı
                        </h4>
                        <p class="text-muted font-size-12 mb-0 mt-1">Kart və nağd daxilolmaların nisbəti</p>
                    </div>
                </div>
                <div class="soft-box-body">
                    {{-- Donut Chart with clean center --}}
                    <div id="paymentDonutChart" style="min-height: 210px;"></div>

                    {{-- 2 Balanced Stat Pill Cards (No Colliding Text) --}}
                    <div class="row g-2 mt-2 pt-2">
                        <div class="col-6">
                            <div class="pm-soft-badge-card pm-theme-terminal">
                                <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="pm-dot pm-dot-blue"></span>
                                        <span class="pm-type-name">Terminal</span>
                                    </div>
                                    <span class="pm-rate-pill pm-pill-blue">{{ $kpi['card_percent'] }}%</span>
                                </div>
                                <div class="pm-type-val text-info">
                                    {{ number_format($kpi['total_card'], 2) }} <span class="font-size-13 font-weight-600">₼</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="pm-soft-badge-card pm-theme-cash">
                                <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="pm-dot pm-dot-amber"></span>
                                        <span class="pm-type-name">Nağd</span>
                                    </div>
                                    <span class="pm-rate-pill pm-pill-amber">{{ $kpi['cash_percent'] }}%</span>
                                </div>
                                <div class="pm-type-val text-warning">
                                    {{ number_format($kpi['total_cash'], 2) }} <span class="font-size-13 font-weight-600">₼</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Top 5 Games (Clean Compact Leaderboard - ALL 5 GAMES FULLY VISIBLE) --}}
        <div class="col-xl-7 col-lg-6">
            <div class="soft-box mb-0">
                <div class="soft-box-header d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="soft-box-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2.2">
                                <rect width="20" height="14" x="2" y="7" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                            Ən Çox Gəlir Gətirən TOP 5 Oyun
                        </h4>
                        <p class="text-muted font-size-12 mb-0 mt-1">Gəlir və rezervasiya sayına görə ən aktiv oyunlar</p>
                    </div>
                </div>
                <div class="soft-box-body">
                    <div class="top-games-wrapper">
                        @php
                            $maxGameRev = !empty($topGames[0]['revenue']) ? (float)$topGames[0]['revenue'] : 1.0;
                        @endphp
                        @forelse($topGames as $idx => $game)
                            @php
                                $gameRev = (float)($game['revenue'] ?? 0);
                                $gameBarPct = $maxGameRev > 0 ? min(100, max(15, round(($gameRev / $maxGameRev) * 100))) : 0;
                            @endphp
                            <div class="top-game-row">
                                {{-- Left: Rank & Details --}}
                                <div class="d-flex align-items-center gap-3">
                                    @if($idx === 0)
                                        <div class="game-medal-pill medal-1" title="1-ci yer">🥇 #1</div>
                                    @elseif($idx === 1)
                                        <div class="game-medal-pill medal-2" title="2-ci yer">🥈 #2</div>
                                    @elseif($idx === 2)
                                        <div class="game-medal-pill medal-3" title="3-cü yer">🥉 #3</div>
                                    @else
                                        <div class="game-medal-pill medal-sub">#{{ $idx + 1 }}</div>
                                    @endif

                                    <div>
                                        <h6 class="game-clean-name">{{ $game['name'] }}</h6>
                                        <div class="game-clean-sub">
                                            <span>{{ number_format($game['sales_count']) }} rezervasiya</span>
                                            <span class="game-dot">&bull;</span>
                                            <span>{{ number_format($game['players']) }} oyunçu</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: Revenue & Mini Progress Track --}}
                                <div class="text-end">
                                    <div class="game-clean-price">{{ number_format($game['revenue'], 2) }} ₼</div>
                                    <div class="game-mini-bar-track mt-1">
                                        <div class="game-mini-bar-fill" style="width: {{ $gameBarPct }}%;"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                Bu dövr üzrə oyun məlumatı tapılmadı.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- Load ApexCharts from CDN --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // 1. Günlük Gəlir Dinamikası (Area Chart)
    const dailyCategories = @json($dailyTrend['categories']);
    const dailyRevenue = @json($dailyTrend['revenue_series']);
    const dailyCash = @json($dailyTrend['cash_series']);
    const dailyCard = @json($dailyTrend['card_series']);

    const dailyChartOptions = {
        series: [
            {
                name: 'Cəmi Gəlir (₼)',
                data: dailyRevenue
            },
            {
                name: 'Terminal / Kart (₼)',
                data: dailyCard
            },
            {
                name: 'Nağd (₼)',
                data: dailyCash
            }
        ],
        chart: {
            type: 'area',
            height: 330,
            fontFamily: 'Inter, sans-serif',
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        colors: ['#107c41', '#0284c7', '#f59e0b'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: [3, 2, 2]
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.35,
                opacityTo: 0.05,
                stops: [0, 95, 100]
            }
        },
        xaxis: {
            categories: dailyCategories,
            title: {
                text: 'Ayın Günləri',
                style: { fontSize: '11px', color: '#94a3b8', fontWeight: 600 }
            },
            labels: {
                style: { colors: '#64748b', fontSize: '11px' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { colors: '#64748b', fontSize: '11px' },
                formatter: function (val) {
                    return val >= 1000 ? (val / 1000).toFixed(1) + 'k ₼' : val.toFixed(0) + ' ₼';
                }
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (val) {
                    return Number(val).toLocaleString('az-AZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₼';
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '12px',
            markers: { radius: 12 }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4
        }
    };

    const dailyChartEl = document.querySelector("#dailyRevenueChart");
    if (dailyChartEl) {
        const dailyChart = new ApexCharts(dailyChartEl, dailyChartOptions);
        dailyChart.render();
    }

    // 2. Filialların Gəlir Reytinqi (Horizontal Bar Chart)
    @php
        $topBranchStats = array_slice($branchStats, 0, 10);
        $chartBranchNames = array_map(fn($b) => mb_substr($b['name'], 0, 18), $topBranchStats);
        $chartBranchRevenues = array_map(fn($b) => round($b['revenue'], 2), $topBranchStats);
    @endphp

    const branchNames = @json(array_reverse($chartBranchNames));
    const branchRevenues = @json(array_reverse($chartBranchRevenues));

    const branchChartOptions = {
        series: [{
            name: 'Gəlir (₼)',
            data: branchRevenues
        }],
        chart: {
            type: 'bar',
            height: 345,
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                horizontal: true,
                barHeight: '60%',
                dataLabels: {
                    position: 'bottom'
                }
            }
        },
        colors: ['#107c41'],
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
                colors: ['#1e293b'],
                fontSize: '11px',
                fontWeight: 700
            },
            formatter: function (val) {
                return val > 0 ? Number(val).toLocaleString('az-AZ', { maximumFractionDigits: 0 }) + ' ₼' : '';
            },
            offsetX: 6
        },
        xaxis: {
            categories: branchNames,
            labels: {
                show: false
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { colors: '#334155', fontSize: '11px', fontWeight: 600 }
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return Number(val).toLocaleString('az-AZ', { minimumFractionDigits: 2 }) + ' ₼';
                }
            }
        }
    };

    const branchChartEl = document.querySelector("#branchRankingChart");
    if (branchChartEl) {
        const branchChart = new ApexCharts(branchChartEl, branchChartOptions);
        branchChart.render();
    }

    // 3. Ödəniş Metodu Donut Chart (Clean, centered total)
    const cashVal = {{ round($kpi['total_cash'], 2) }};
    const cardVal = {{ round($kpi['total_card'], 2) }};

    const donutOptions = {
        series: (cashVal === 0 && cardVal === 0) ? [1] : [cardVal, cashVal],
        labels: (cashVal === 0 && cardVal === 0) ? ['Məlumat yoxdur'] : ['Terminal (Kart)', 'Nağd'],
        chart: {
            type: 'donut',
            height: 210,
            fontFamily: 'Inter, sans-serif'
        },
        colors: (cashVal === 0 && cardVal === 0) ? ['#e2e8f0'] : ['#0284c7', '#f59e0b'],
        dataLabels: {
            enabled: false
        },
        legend: {
            show: false
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Cəmi Gəlir',
                            fontSize: '12px',
                            fontWeight: 600,
                            color: '#64748b',
                            formatter: function () {
                                const total = {{ round($kpi['total_revenue'], 2) }};
                                return Number(total).toLocaleString('az-AZ', { maximumFractionDigits: 0 }) + ' ₼';
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return Number(val).toLocaleString('az-AZ', { minimumFractionDigits: 2 }) + ' ₼';
                }
            }
        }
    };

    const donutChartEl = document.querySelector("#paymentDonutChart");
    if (donutChartEl) {
        const donutChart = new ApexCharts(donutChartEl, donutOptions);
        donutChart.render();
    }
});
</script>
@endpush