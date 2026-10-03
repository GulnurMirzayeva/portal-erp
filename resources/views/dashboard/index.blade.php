@extends('layouts.erp')

@section('title', 'Dashboard')
@section('page-title', 'DASHBOARD & MALİYYƏ ANALİTİKASI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@push('css')
<style>
    /* ---------------- Dashboard Custom Premium Styling ---------------- */
    .dashboard-toolbar {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 12px;
        padding: 14px 20px;
        box-shadow: 0 2px 6px rgba(18, 38, 63, 0.03);
        margin-bottom: 24px;
    }

    .kpi-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 14px;
        padding: 20px 22px;
        position: relative;
        overflow: hidden;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 2px 8px rgba(18, 38, 63, 0.03);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 22px rgba(16, 124, 65, 0.09);
        border-color: rgba(16, 124, 65, 0.3);
    }

    .kpi-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: transparent;
        transition: background 0.25s ease;
    }

    .kpi-card.kpi-primary::before { background: linear-gradient(90deg, #107c41, #34d399); }
    .kpi-card.kpi-info::before { background: linear-gradient(90deg, #0284c7, #38bdf8); }
    .kpi-card.kpi-warning::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .kpi-card.kpi-purple::before { background: linear-gradient(90deg, #8b5cf6, #c084fc); }
    .kpi-card.kpi-emerald::before { background: linear-gradient(90deg, #059669, #10b981); }

    .kpi-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
    }

    .kpi-card:hover .kpi-icon-wrap {
        transform: scale(1.08);
    }

    .kpi-val {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.5px;
        line-height: 1.2;
        margin: 8px 0 4px;
        color: #1e293b;
    }

    .kpi-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .kpi-sub {
        font-size: 12px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
    }

    /* Trend badges */
    .trend-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    .trend-pill.up {
        background: #ecfdf5;
        color: #059669;
    }
    .trend-pill.down {
        background: #fef2f2;
        color: #dc2626;
    }
    .trend-pill.neutral {
        background: #f1f5f9;
        color: #475569;
    }

    /* Ratio Progress Bar */
    .ratio-bar-wrap {
        margin-top: 10px;
    }
    .ratio-bar {
        height: 6px;
        border-radius: 6px;
        background: #e2e8f0;
        overflow: hidden;
        display: flex;
    }
    .ratio-bar-cash { background: #f59e0b; }
    .ratio-bar-card { background: #0284c7; }

    /* Chart Cards */
    .analytics-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(18, 38, 63, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .analytics-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafbfc;
    }

    .analytics-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .analytics-card-body {
        padding: 22px;
    }

    /* Rank Badges */
    .rank-badge {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
    }
    .rank-gold { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; border: 1px solid #fcd34d; }
    .rank-silver { background: linear-gradient(135deg, #f1f5f9, #e2e8f0); color: #334155; border: 1px solid #cbd5e1; }
    .rank-bronze { background: linear-gradient(135deg, #ffedd5, #fed7aa); color: #9a3412; border: 1px solid #fdba74; }
    .rank-default { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

    /* Custom Table */
    .branch-table {
        margin: 0;
    }
    .branch-table th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 14px;
        white-space: nowrap;
    }
    .branch-table td {
        padding: 13px 14px;
        vertical-align: middle;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s ease;
    }
    .branch-table tr:hover td {
        background-color: #fbfdfc;
    }

    .share-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        max-width: 130px;
    }
    .share-track {
        flex: 1;
        height: 6px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }
    .share-fill {
        height: 100%;
        background: #107c41;
        border-radius: 4px;
    }

    /* Live pulse animation */
    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #107c41;
        display: inline-block;
        position: relative;
    }
    .pulse-dot::after {
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

    .btn-refresh {
        transition: all 0.2s ease;
    }
    .btn-refresh:hover svg {
        transform: rotate(180deg);
    }
    .btn-refresh svg {
        transition: transform 0.4s ease;
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">

    {{-- Top Filter & Control Toolbar --}}
    <div class="dashboard-toolbar d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="pulse-dot"></span>
                <span class="font-size-13 font-weight-600 text-dark">
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
                <label for="monthSelect" class="font-size-12 text-muted m-0 font-weight-500 d-none d-sm-inline">Dövr:</label>
                <select name="month" id="monthSelect" class="form-select form-select-sm font-size-13 font-weight-600" style="min-width: 160px;" onchange="this.form.submit()">
                    @foreach($availableMonths as $m)
                        <option value="{{ $m['key'] }}" {{ $filterMonth === $m['key'] ? 'selected' : '' }}>
                            {{ $m['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Branch Filter --}}
            <div class="d-flex align-items-center gap-1">
                <label for="branchSelect" class="font-size-12 text-muted m-0 font-weight-500 d-none d-sm-inline">Filial:</label>
                <select name="branch_id" id="branchSelect" class="form-select form-select-sm font-size-13 font-weight-500" style="min-width: 175px;" onchange="this.form.submit()">
                    <option value="all" {{ empty($filterBranch) ? 'selected' : '' }}>Bütün Filiallar</option>
                    @foreach($branches as $b)
                        <option value="{{ $b['id'] }}" {{ (string)$filterBranch === (string)$b['id'] ? 'selected' : '' }}>
                            {{ $b['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Refresh Button --}}
            <button type="submit" name="refresh" value="1" class="btn btn-light btn-sm border btn-refresh d-inline-flex align-items-center gap-1 font-size-12" title="Məlumatları yenilə (keşi təmizlə)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                <span>Yenilə</span>
            </button>

            {{-- Shortcut to Accounting --}}
            <a href="{{ route('accounting.index', ['month' => $filterMonth, 'branch_id' => $filterBranch]) }}" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1 font-size-12 ms-sm-1">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                    <path d="M3 9h18"></path>
                    <path d="M9 21V9"></path>
                </svg>
                <span>Qaimələr</span>
            </a>
        </form>
    </div>

    {{-- TOP 5 EXECUTIVE KPI CARDS --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Revenue (Ümumi Dövriyyə) --}}
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-primary">
                <div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="kpi-label">Ümumi Dövriyyə</p>
                        <div class="kpi-icon-wrap bg-soft-success text-success">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" x2="12" y1="2" y2="22"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="kpi-val text-success">
                        {{ number_format($kpi['total_revenue'], 2) }} <span class="font-size-18 font-weight-600">₼</span>
                    </div>
                </div>
                <div class="kpi-sub">
                    @if($growth['has_prev'])
                        <span class="trend-pill {{ $growth['is_positive'] ? 'up' : 'down' }}">
                            {{ $growth['is_positive'] ? '▲ +' : '▼ ' }}{{ $growth['percent'] }}%
                        </span>
                        <span>ötən aya nəzərən ({{ number_format($growth['prev_revenue'], 0) }} ₼)</span>
                    @else
                        <span class="text-muted">Seçilmiş dövrün ümumi məbləği</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 2: Cash vs Terminal Breakdown --}}
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-info">
                <div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="kpi-label">Ödəniş Bölgüsü</p>
                        <div class="kpi-icon-wrap bg-soft-info text-info">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                                <line x1="2" x2="22" y1="10" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between mt-2">
                        <div>
                            <span class="font-size-11 text-muted d-block font-weight-600">TERMINAL ({{ $kpi['card_percent'] }}%)</span>
                            <span class="font-size-16 font-weight-700 text-info">{{ number_format($kpi['total_card'], 2) }} ₼</span>
                        </div>
                        <div class="text-end">
                            <span class="font-size-11 text-muted d-block font-weight-600">NAĞD ({{ $kpi['cash_percent'] }}%)</span>
                            <span class="font-size-16 font-weight-700 text-warning">{{ number_format($kpi['total_cash'], 2) }} ₼</span>
                        </div>
                    </div>
                    <div class="ratio-bar-wrap">
                        <div class="ratio-bar">
                            <div class="ratio-bar-card" style="width: {{ $kpi['card_percent'] }}%;" title="Terminal: {{ $kpi['card_percent'] }}%"></div>
                            <div class="ratio-bar-cash" style="width: {{ $kpi['cash_percent'] }}%;" title="Nağd: {{ $kpi['cash_percent'] }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="kpi-sub justify-content-between">
                    <span>Kart: {{ number_format($kpi['total_card'], 0) }} ₼</span>
                    <span>Nağd: {{ number_format($kpi['total_cash'], 0) }} ₼</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Sessions & Average Ticket --}}
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-purple">
                <div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="kpi-label">Satışlar (Seanslar)</p>
                        <div class="kpi-icon-wrap bg-soft-purple text-purple" style="background: #f5f3ff; color: #7c3aed;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="kpi-val text-dark">
                        {{ number_format($kpi['total_sales']) }} <span class="font-size-16 font-weight-500 text-muted">seans</span>
                    </div>
                </div>
                <div class="kpi-sub">
                    <span class="badge bg-light text-dark border font-size-11">Orta Qəbz: {{ number_format($kpi['avg_ticket'], 2) }} ₼</span>
                    <span>bir seans üzrə</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Top Performing Branch --}}
        @php
            $topBranch = $branchStats[0] ?? null;
        @endphp
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-emerald">
                <div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="kpi-label">Lider Filial 🏆</p>
                        <div class="kpi-icon-wrap bg-soft-warning text-warning" style="background: #fffbeb; color: #d97706;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                    </div>
                    @if($topBranch && $topBranch['revenue'] > 0)
                        <div class="kpi-val font-size-20 text-truncate text-dark" title="{{ $topBranch['name'] }}">
                            {{ $topBranch['name'] }}
                        </div>
                        <div class="font-size-13 font-weight-700 text-success">
                            {{ number_format($topBranch['revenue'], 2) }} ₼
                            <span class="font-size-11 text-muted font-weight-500">({{ $topBranch['share_percent'] }}% şəbəkə payı)</span>
                        </div>
                    @else
                        <div class="kpi-val font-size-18 text-muted">Məlumat yoxdur</div>
                    @endif
                </div>
                <div class="kpi-sub">
                    @if($topBranch && $topBranch['sales_count'] > 0)
                        <span>{{ $topBranch['sales_count'] }} seans &bull; {{ number_format($topBranch['players']) }} qonaq</span>
                    @else
                        <span>Dövr üçün satış qeydə alınmayıb</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN ANALYTICS ROW: REVENUE TREND + BRANCH COMPARISON CHART --}}
    <div class="row">
        {{-- Left: Daily Revenue Dynamics (Area Chart) --}}
        <div class="col-xl-8">
            <div class="analytics-card">
                <div class="analytics-card-header">
                    <div>
                        <h4 class="analytics-card-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                <polyline points="17 6 23 6 23 12"></polyline>
                            </svg>
                            Gəlir və Satış Dinamikası (Günlər üzrə)
                        </h4>
                        <p class="text-muted font-size-12 mb-0 mt-1">
                            {{ $selectedMonthName }} ayı üzrə gündəlik ümumi gəlir, kart və nağd daxilolmalar
                        </p>
                    </div>

                    @if(!empty($dailyTrend['peak_day']))
                        <div class="text-end d-none d-md-block">
                            <span class="font-size-11 text-muted d-block">Pik Gün: <strong>{{ $dailyTrend['peak_day']['day'] }} {{ $selectedMonthName }}</strong></span>
                            <span class="badge bg-soft-success text-success font-size-11 font-weight-700">
                                {{ number_format($dailyTrend['peak_day']['revenue'], 2) }} ₼ ({{ $dailyTrend['peak_day']['sales_count'] }} seans)
                            </span>
                        </div>
                    @endif
                </div>
                <div class="analytics-card-body">
                    <div id="dailyRevenueChart" style="min-height: 330px;"></div>
                </div>
            </div>
        </div>

        {{-- Right: Top Branches Revenue Share (Horizontal Bar Chart) --}}
        <div class="col-xl-4">
            <div class="analytics-card">
                <div class="analytics-card-header">
                    <div>
                        <h4 class="analytics-card-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2.2">
                                <line x1="18" x2="18" y1="20" y2="10"></line>
                                <line x1="12" x2="12" y1="20" y2="4"></line>
                                <line x1="6" x2="6" y1="20" y2="14"></line>
                            </svg>
                            Filialların Dövriyyə Reytinqi
                        </h4>
                        <p class="text-muted font-size-12 mb-0 mt-1">Gəlir həcminə görə ən güclü filiallar</p>
                    </div>
                </div>
                <div class="analytics-card-body p-2">
                    <div id="branchRankingChart" style="min-height: 345px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAILED BRANCH PERFORMANCE COMPARISON TABLE --}}
    <div class="row">
        <div class="col-12">
            <div class="analytics-card">
                <div class="analytics-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h4 class="analytics-card-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2">
                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                <path d="M3 9h18"></path>
                                <path d="M9 21V9"></path>
                            </svg>
                            Filiallar üzrə Detallı Müqayisəli Maliyyə Cədvəli
                        </h4>
                        <p class="text-muted font-size-12 mb-0 mt-1">
                            Bütün filialların cari dövr üzrə dövriyyəsi, ödəniş formaları və şəbəkə payı
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border font-size-12 px-2 py-1">
                            {{ count($branchStats) }} Filial Analiz Edildi
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table branch-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;" class="text-center">Reytinq</th>
                                <th>Filial Adı</th>
                                <th class="text-center">Seans Sayı</th>
                                <th class="text-center">Cəmi Oyunçu</th>
                                <th class="text-end">Nağd</th>
                                <th class="text-end">Terminal (Kart)</th>
                                <th class="text-end">Ümumi Dövriyyə</th>
                                <th style="width: 160px;">Şəbəkə Payı</th>
                                <th class="text-end">Orta Seans</th>
                                <th class="text-center" style="width: 100px;">Əməliyyat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($branchStats as $bs)
                                <tr>
                                    {{-- Rank --}}
                                    <td class="text-center">
                                        @if($bs['rank'] == 1 && $bs['revenue'] > 0)
                                            <span class="rank-badge rank-gold" title="1-ci yer">🥇</span>
                                        @elseif($bs['rank'] == 2 && $bs['revenue'] > 0)
                                            <span class="rank-badge rank-silver" title="2-ci yer">🥈</span>
                                        @elseif($bs['rank'] == 3 && $bs['revenue'] > 0)
                                            <span class="rank-badge rank-bronze" title="3-cü yer">🥉</span>
                                        @else
                                            <span class="rank-badge rank-default">#{{ $bs['rank'] }}</span>
                                        @endif
                                    </td>

                                    {{-- Branch Name --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xs rounded-circle bg-light border d-flex align-items-center justify-content-center text-primary font-weight-700 font-size-11">
                                                {{ mb_substr($bs['name'], 0, 1) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('accounting.index', ['branch_id' => $bs['id'], 'month' => $filterMonth]) }}" class="text-dark font-weight-700 hover-primary font-size-13 text-decoration-none">
                                                    {{ $bs['name'] }}
                                                </a>
                                                <span class="text-muted font-size-11 d-block">ID: #{{ $bs['id'] }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Sessions Count --}}
                                    <td class="text-center font-weight-600">
                                        {{ number_format($bs['sales_count']) }}
                                    </td>

                                    {{-- Players Count --}}
                                    <td class="text-center text-muted font-weight-500">
                                        {{ number_format($bs['players']) }}
                                    </td>

                                    {{-- Cash Amount --}}
                                    <td class="text-end font-weight-600 text-warning">
                                        {{ number_format($bs['cash'], 2) }} ₼
                                    </td>

                                    {{-- Card Amount --}}
                                    <td class="text-end font-weight-600 text-info">
                                        {{ number_format($bs['card'], 2) }} ₼
                                    </td>

                                    {{-- Total Revenue --}}
                                    <td class="text-end font-weight-800 text-success font-size-14">
                                        {{ number_format($bs['revenue'], 2) }} ₼
                                    </td>

                                    {{-- Network Share % --}}
                                    <td>
                                        <div class="share-pill">
                                            <span class="font-size-11 font-weight-700 text-dark" style="min-width: 38px;">{{ $bs['share_percent'] }}%</span>
                                            <div class="share-track">
                                                <div class="share-fill" style="width: {{ min(100, $bs['share_percent']) }}%;"></div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Average Ticket --}}
                                    <td class="text-end font-weight-600 text-muted">
                                        {{ number_format($bs['avg_price'], 2) }} ₼
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-center">
                                        <a href="{{ route('accounting.index', ['branch_id' => $bs['id'], 'month' => $filterMonth]) }}" class="btn btn-outline-primary btn-sm py-1 px-2 font-size-11" title="Bu filialın elektron qaiməsinə bax">
                                            Qaimə
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        Bu dövr üzrə filial satışı tapılmadı.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(!empty($branchStats))
                            <tfoot class="border-top-2">
                                <tr style="background: #f1f8f3; font-weight: 700;">
                                    <td colspan="2" class="text-end text-dark font-size-13 py-3">CƏMİ ŞƏBƏKƏ:</td>
                                    <td class="text-center text-dark">{{ number_format($kpi['total_sales']) }}</td>
                                    <td class="text-center text-dark">{{ number_format($kpi['total_players']) }}</td>
                                    <td class="text-end text-warning">{{ number_format($kpi['total_cash'], 2) }} ₼</td>
                                    <td class="text-end text-info">{{ number_format($kpi['total_card'], 2) }} ₼</td>
                                    <td class="text-end text-success font-size-15">{{ number_format($kpi['total_revenue'], 2) }} ₼</td>
                                    <td>
                                        <span class="badge bg-success font-size-11">100.0%</span>
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

    {{-- LOWER ROW: PAYMENT DONUT + TOP GAMES --}}
    <div class="row">
        {{-- Payment Donut Chart --}}
        <div class="col-xl-4 col-md-6">
            <div class="analytics-card">
                <div class="analytics-card-header">
                    <h4 class="analytics-card-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 2a10 10 0 0 1 10 10h-10z"></path>
                        </svg>
                        Ödəniş Metodu Bölgüsü
                    </h4>
                </div>
                <div class="analytics-card-body text-center">
                    <div id="paymentDonutChart" style="min-height: 250px;"></div>
                    <div class="d-flex justify-content-around mt-3 pt-2 border-top">
                        <div>
                            <span class="font-size-11 text-muted d-block">Nağd Daxilolma</span>
                            <span class="font-size-14 font-weight-700 text-warning">{{ number_format($kpi['total_cash'], 2) }} ₼</span>
                        </div>
                        <div class="border-end"></div>
                        <div>
                            <span class="font-size-11 text-muted d-block">Terminal (Kart)</span>
                            <span class="font-size-14 font-weight-700 text-info">{{ number_format($kpi['total_card'], 2) }} ₼</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top 5 Games by Revenue --}}
        <div class="col-xl-8 col-md-6">
            <div class="analytics-card">
                <div class="analytics-card-header d-flex align-items-center justify-content-between">
                    <h4 class="analytics-card-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2.2">
                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                        Ən Çox Gəlir Gətirən TOP 5 Oyun
                    </h4>
                    <span class="text-muted font-size-11">Seçilmiş dövrün göstəricisi</span>
                </div>
                <div class="analytics-card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 font-size-11 text-uppercase text-muted"># Oyun Adı</th>
                                    <th class="text-center font-size-11 text-uppercase text-muted">Seans Sayı</th>
                                    <th class="text-center font-size-11 text-uppercase text-muted">Oyunçu Sayı</th>
                                    <th class="text-end pe-4 font-size-11 text-uppercase text-muted">Gəlir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topGames as $idx => $game)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-light border text-dark font-size-11 font-weight-700">#{{ $idx + 1 }}</span>
                                                <span class="font-weight-700 text-dark">{{ $game['name'] }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-600">{{ number_format($game['sales_count']) }}</td>
                                        <td class="text-center text-muted font-weight-500">{{ number_format($game['players']) }}</td>
                                        <td class="text-end pe-4 font-weight-800 text-success">{{ number_format($game['revenue'], 2) }} ₼</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">Məlumat tapılmadı</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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

    // 2. Filialların Dövriyyə Reytinqi (Horizontal Bar Chart)
    @php
        $topBranchStats = array_slice($branchStats, 0, 10);
        $chartBranchNames = array_map(fn($b) => mb_substr($b['name'], 0, 18), $topBranchStats);
        $chartBranchRevenues = array_map(fn($b) => round($b['revenue'], 2), $topBranchStats);
    @endphp

    const branchNames = @json(array_reverse($chartBranchNames));
    const branchRevenues = @json(array_reverse($chartBranchRevenues));

    const branchChartOptions = {
        series: [{
            name: 'Dövriyyə (₼)',
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
                borderRadius: 5,
                horizontal: true,
                barHeight: '62%',
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

    // 3. Ödəniş Metodu Donut Chart
    const cashVal = {{ round($kpi['total_cash'], 2) }};
    const cardVal = {{ round($kpi['total_card'], 2) }};

    const donutOptions = {
        series: (cashVal === 0 && cardVal === 0) ? [1] : [cardVal, cashVal],
        labels: (cashVal === 0 && cardVal === 0) ? ['Məlumat yoxdur'] : ['Terminal (Kart)', 'Nağd'],
        chart: {
            type: 'donut',
            height: 250,
            fontFamily: 'Inter, sans-serif'
        },
        colors: (cashVal === 0 && cardVal === 0) ? ['#e2e8f0'] : ['#0284c7', '#f59e0b'],
        dataLabels: {
            enabled: (cashVal > 0 || cardVal > 0),
            formatter: function (val) {
                return val.toFixed(1) + '%';
            }
        },
        legend: {
            position: 'bottom',
            fontSize: '12px'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Cəmi',
                            fontSize: '12px',
                            fontWeight: 600,
                            color: '#64748b',
                            formatter: function (w) {
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