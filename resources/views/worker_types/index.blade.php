@extends('layouts.erp')

@section('title', 'Dinamik İşçi Tipləri & Bonus Qaydaları')
@section('page-title', 'DİNAMİK İŞÇİ TİPLƏRİ VƏ BONUS QAYDALARI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">HR / İşçilər</a></li>
    <li class="breadcrumb-item active">İşçi Tipləri & Bonuslar</li>
@endsection

@push('css')
<style>
    .att-wrapper {
        padding-bottom: 40px;
        width: 100%;
    }

    /* Top Action & Info Bar */
    .att-top-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px 24px;
        margin-bottom: 22px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .att-top-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .att-top-desc {
        font-size: 13px;
        color: #64748b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .att-pill-active {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .att-pulse-dot {
        width: 7px;
        height: 7px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
    }
    .att-pill-info {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Success Alert */
    .att-alert-success {
        background: #ecfdf5;
        border: 1.5px solid #a7f3d0;
        border-radius: 16px;
        padding: 14px 20px;
        margin-bottom: 22px;
        color: #065f46;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.06);
    }

    /* Stat Cards Grid */
    .stat-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(215px, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }
    .stat-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 18px 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .stat-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .stat-metric-val {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.1;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .stat-metric-title {
        font-size: 12.5px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 2px;
    }
    .stat-metric-subtitle {
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
    }
    .stat-icon-wrap {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon-emerald { background: #ecfdf5; color: #107c41; border: 1px solid #a7f3d0; }
    .stat-icon-blue { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .stat-icon-amber { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }
    .stat-icon-purple { background: #faf5ff; color: #7c3aed; border: 1px solid #e9d5ff; }
    .stat-icon-slate { background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; }

    /* Filter Panel Card */
    .filter-panel-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px 24px;
        margin-bottom: 22px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }
    .filter-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #e2e8f0;
        gap: 12px;
        flex-wrap: wrap;
    }
    .filter-panel-title {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-active-pill {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .filter-active-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #2563eb;
    }
    .filter-form-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1.2fr 1fr 190px;
        gap: 16px;
        align-items: flex-end;
    }
    @media (max-width: 1150px) {
        .filter-form-grid { grid-template-columns: 1fr 1fr 1fr; }
    }
    @media (max-width: 680px) {
        .filter-form-grid { grid-template-columns: 1fr; }
    }
    .filter-field-col {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    .filter-col-label {
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        height: 18px;
        line-height: 18px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .filter-ctrl {
        height: 42px;
        width: 100%;
        padding: 0 14px;
        font-size: 13.5px;
        font-weight: 600;
        color: #0f172a;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        outline: none;
        transition: all 0.18s ease;
        box-sizing: border-box;
        line-height: 40px;
    }
    .filter-ctrl:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.12);
    }
    .filter-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 16px 16px;
        padding-right: 38px;
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .filter-btn-group {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        height: 42px;
    }
    .filter-btn-submit {
        flex: 1;
        height: 42px;
        background: #107c41;
        color: #ffffff !important;
        border: 1px solid #0d6334;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 2px 6px rgba(16, 124, 65, 0.18);
        padding: 0 12px;
        white-space: nowrap;
    }
    .filter-btn-submit:hover {
        background: #0b5e30;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.28);
    }
    .filter-btn-reset {
        height: 42px;
        background: #f8fafc;
        color: #475569 !important;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.18s ease;
        text-decoration: none !important;
        padding: 0 14px;
        white-space: nowrap;
    }
    .filter-btn-reset:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #0f172a !important;
    }

    /* Data Table Card */
    .data-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }
    .data-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #ffffff;
        flex-wrap: wrap;
    }
    .data-table-title {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .data-count-tag {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 2px 9px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
    }
    .mobile-scroll-hint {
        display: none;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 14px;
        background: #f0fdf4;
        border-bottom: 1px solid #dcfce7;
        color: #15803d;
        font-size: 12px;
        font-weight: 600;
    }
    @media (max-width: 991px) {
        .mobile-scroll-hint { display: flex; }
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-modern {
        width: 100%;
        min-width: 1080px;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-modern th {
        background: #f8fafc;
        padding: 13px 16px;
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 1.5px solid #e2e8f0;
        white-space: nowrap;
    }
    .table-modern td {
        padding: 14px 16px;
        font-size: 13px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        background: #ffffff;
    }
    .table-modern tbody tr:hover td {
        background-color: #fafbfc;
    }

    /* Role Cell */
    .wt-role-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .wt-code-avatar {
        min-width: 48px;
        height: 36px;
        padding: 0 10px;
        border-radius: 11px;
        font-family: monospace;
        font-weight: 800;
        font-size: 12.5px;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(15, 23, 42, 0.12);
    }
    .wt-role-name {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.25;
    }
    .wt-role-sub {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 2px;
    }

    /* Badges */
    .scope-pill {
        border-radius: 10px;
        padding: 5px 11px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .scope-branch { background: #eff6ff; color: #1d4ed8; border: 1.5px solid #bfdbfe; }
    .scope-office { background: #f8fafc; color: #475569; border: 1.5px solid #e2e8f0; }
    .scope-both { background: #f5f3ff; color: #6d28d9; border: 1.5px solid #ddd6fe; }

    .cat-pill-op { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; }
    .cat-pill-mgmt { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; }
    .cat-pill-sup { background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; }

    .rule-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        margin: 2px 3px 2px 0;
        white-space: nowrap;
    }
    .rule-chip-intern { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .rule-chip-game { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
    .rule-chip-tabel { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }

    .bonus-score-badge {
        background: #ecfdf5;
        color: #047857;
        border: 1.5px solid #a7f3d0;
        padding: 4px 11px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .bonus-game-sub {
        font-size: 11px;
        font-weight: 600;
        color: #2563eb;
        margin-top: 4px;
    }

    /* Status Toggle */
    .status-badge-active {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 4px 11px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    .status-badge-inactive {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 4px 11px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* Buttons */
    .att-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 13px;
        font-weight: 700;
        padding: 9px 18px;
        border-radius: 12px;
        cursor: pointer;
        border: 1.5px solid transparent;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
        box-sizing: border-box;
    }
    .att-btn-primary {
        background: #107c41;
        color: #ffffff !important;
        border-color: #0d6334;
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.22);
    }
    .att-btn-primary:hover {
        background: #0b5e30;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(16, 124, 65, 0.3);
    }
    .att-btn-outline {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #334155 !important;
    }
    .att-btn-outline:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a !important;
    }
    .att-btn-sm {
        padding: 7px 14px;
        font-size: 12.5px;
        border-radius: 10px;
    }

    .worker-btn-action {
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        padding: 0 11px;
        cursor: pointer;
        transition: all 0.18s ease;
        text-decoration: none !important;
    }
    .wt-btn-edit {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        color: #334155;
    }
    .wt-btn-edit:hover {
        background: #ffffff;
        border-color: #107c41;
        color: #107c41;
        transform: translateY(-1px);
    }
    .wt-btn-del {
        background: #fef2f2;
        border: 1.5px solid #fee2e2;
        color: #dc2626;
    }
    .wt-btn-del:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="att-wrapper">

    @if(session('success'))
        <div class="att-alert-success">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span><strong>Uğurlu!</strong> {{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" onclick="this.closest('.att-alert-success').remove()" aria-label="Bağla"></button>
        </div>
    @endif

    {{-- Top Action & Info Bar --}}
    <div class="att-top-bar">
        <div>
            <h5 class="att-top-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                Dinamik İşçi Tipləri & Vizual Bonus Qaydaları
            </h5>
            <div class="att-top-desc">
                Filial və Ofis vəzifələrini, stajor icazələrini, tabel sıralamasını və oyun/rol üzrə yarış xalı qaydalarını idarə edin.
                <span class="att-pill-active">
                    <span class="att-pulse-dot"></span>
                    ERP Master Mərkəzi
                </span>
                <span class="att-pill-info">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                    Stajor vizual xalı = 0
                </span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('worker-types.create'))
                <form action="{{ route('worker-types.seed-defaults') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="att-btn att-btn-outline att-btn-sm" title="Çatışmayan standart tipləri və ilkin xalları əlavə et">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                        Standart Tipləri Yüklə
                    </button>
                </form>
                <a href="{{ route('worker-types.create') }}" class="att-btn att-btn-primary att-btn-sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Yeni İşçi Tipi
                </a>
            @endif
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-cards-grid">
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Cəmi İşçi Tipi</div>
                <div class="stat-metric-val">{{ $stats['total'] }}</div>
                <div class="stat-metric-subtitle">Bütün sistem rolları</div>
            </div>
            <div class="stat-icon-wrap stat-icon-slate">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
        </div>
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Filial Vəzifələri</div>
                <div class="stat-metric-val text-primary">{{ $stats['branch'] }}</div>
                <div class="stat-metric-subtitle">Filiallarda işləyən tiplər</div>
            </div>
            <div class="stat-icon-wrap stat-icon-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
        </div>
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Bonus Gətirən</div>
                <div class="stat-metric-val" style="color: #107c41;">{{ $stats['bonus_eligible'] }}</div>
                <div class="stat-metric-subtitle">Satışda seçilən rollar</div>
            </div>
            <div class="stat-icon-wrap stat-icon-emerald">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            </div>
        </div>
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Stajor İcazəli</div>
                <div class="stat-metric-val" style="color: #ea580c;">{{ $stats['intern_eligible'] }}</div>
                <div class="stat-metric-subtitle">Stajor təyin edilə bilən</div>
            </div>
            <div class="stat-icon-wrap stat-icon-amber">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
            </div>
        </div>
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">İdarəedici Rol</div>
                <div class="stat-metric-val" style="color: #7c3aed;">{{ $stats['management'] }}</div>
                <div class="stat-metric-subtitle">Kurator, Admin, Sup...</div>
            </div>
            <div class="stat-icon-wrap stat-icon-purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
        </div>
    </div>

    {{-- Süzgəc və Axtarış Paneli --}}
    <div class="filter-panel-card">
        <div class="filter-panel-header">
            <div class="filter-panel-title">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                <span>Süzgəc və Axtarış Paneli</span>
            </div>
            @if($search || $scope !== 'all' || $category !== 'all' || $status !== 'all')
                <span class="filter-active-pill">
                    <span class="filter-active-dot"></span>
                    Aktiv Süzgəc Tətbiq Edilib
                </span>
            @endif
        </div>

        <form method="GET" action="{{ route('worker-types.index') }}" class="filter-form-grid">
            <div class="filter-field-col">
                <label class="filter-col-label">Axtarış (Ad / Kod)</label>
                <input type="text" name="search" class="filter-ctrl" placeholder="Məs: Aktyor, K, adm..." value="{{ $search }}">
            </div>

            <div class="filter-field-col">
                <label class="filter-col-label">İş Sahəsi</label>
                <select name="scope" class="filter-ctrl filter-select">
                    <option value="all" {{ $scope === 'all' ? 'selected' : '' }}>Bütün Sahələr</option>
                    <option value="branch" {{ $scope === 'branch' ? 'selected' : '' }}>Filial</option>
                    <option value="office" {{ $scope === 'office' ? 'selected' : '' }}>Ofis</option>
                </select>
            </div>

            <div class="filter-field-col">
                <label class="filter-col-label">Kateqoriya</label>
                <select name="category" class="filter-ctrl filter-select">
                    <option value="all" {{ $category === 'all' ? 'selected' : '' }}>Bütün Kateqoriyalar</option>
                    <option value="operational" {{ $category === 'operational' ? 'selected' : '' }}>Bonus Gətirən (Satış)</option>
                    <option value="management" {{ $category === 'management' ? 'selected' : '' }}>İdarəedici</option>
                    <option value="support" {{ $category === 'support' ? 'selected' : '' }}>Dəstək / İnzibati</option>
                </select>
            </div>

            <div class="filter-field-col">
                <label class="filter-col-label">Status</label>
                <select name="status" class="filter-ctrl filter-select">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Hamısı</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Yalnız Aktiv</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Yalnız Deaktiv</option>
                </select>
            </div>

            <div class="filter-field-col">
                <label class="filter-col-label">Əməliyyatlar</label>
                <div class="filter-btn-group">
                    <button type="submit" class="filter-btn-submit">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <span>Axtar</span>
                    </button>
                    <a href="{{ route('worker-types.index') }}" class="filter-btn-reset" title="Sıfırla">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                        <span>Sıfırla</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Cədvəl Kartı --}}
    <div class="data-card-modern">
        <div class="data-card-header">
            <h6 class="data-table-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><rect x="3" y="4" width="18" height="16" rx="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                İşçi Tipləri və Qaydalar Cədvəli
                <span class="data-count-tag">{{ $workerTypes->count() }} tip</span>
            </h6>
        </div>

        <div class="mobile-scroll-hint">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 8L22 12L18 16"></path><path d="M6 8L2 12L6 16"></path></svg>
            <span>Cədvəli tam görmək üçün sağa-sola sürüşdürə bilərsiniz</span>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 65px;">Sıra</th>
                        <th>Vəzifə & Qısa Kod</th>
                        <th>Sahə</th>
                        <th>Kateqoriya</th>
                        <th>Dinamik Qaydalar</th>
                        <th>Vizual Yarış Xalı</th>
                        <th>Defolt Ödəniş</th>
                        <th>Status</th>
                        <th style="width: 165px;">Əməliyyatlar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workerTypes as $wt)
                        <tr>
                            <td class="text-muted fw-bold font-monospace" style="font-size: 12px;">#{{ $wt->tabel_sort_order }}</td>
                            <td>
                                <div class="wt-role-cell">
                                    <span class="wt-code-avatar" style="background: {{ $wt->badge_color ?: '#2563eb' }};">
                                        {{ $wt->short_code }}
                                    </span>
                                    <div>
                                        <div class="wt-role-name">
                                            {{ $wt->name }}
                                            @if($wt->female_name)
                                                <span class="text-muted fw-semibold">/ {{ $wt->female_name }}</span>
                                            @endif
                                        </div>
                                        <div class="wt-role-sub">
                                            @if($wt->sale_slot_key)
                                                Satış qrupu: <code>{{ $wt->sale_slot_key }}</code>
                                            @elseif($wt->description)
                                                {{ \Illuminate\Support\Str::limit($wt->description, 48) }}
                                            @else
                                                Slug: <code>{{ $wt->slug }}</code>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $scopeClass = match($wt->scope) {
                                        'branch' => 'scope-branch',
                                        'office' => 'scope-office',
                                        default => 'scope-both',
                                    };
                                @endphp
                                <span class="scope-pill {{ $scopeClass }}">{{ $wt->scope_label }}</span>
                            </td>
                            <td>
                                @php
                                    $catClass = match($wt->category) {
                                        'operational' => 'cat-pill-op',
                                        'management' => 'cat-pill-mgmt',
                                        default => 'cat-pill-sup',
                                    };
                                @endphp
                                <span class="{{ $catClass }}">{{ $wt->category_label }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap">
                                    @if($wt->can_have_intern)
                                        <span class="rule-chip rule-chip-intern">Stajor: staj-{{ $wt->short_code }}</span>
                                    @endif
                                    @if($wt->requires_game)
                                        <span class="rule-chip rule-chip-game">Oyun tələb edir</span>
                                    @endif
                                    @if($wt->show_in_tabel)
                                        <span class="rule-chip rule-chip-tabel">Tabeldə görünür</span>
                                    @endif
                                    @if($wt->can_substitute)
                                        <span class="rule-chip">Əvəz gedir</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($wt->is_bonus_eligible)
                                    @if($wt->defaultBonusRule)
                                        <span class="bonus-score-badge">
                                            {{ number_format((float)$wt->defaultBonusRule->points, 2) }} xal
                                        </span>
                                    @else
                                        <span class="text-warning small fw-bold">Default xal yoxdur</span>
                                    @endif
                                    @if($wt->gameBonusRules->count() > 0)
                                        <div class="bonus-game-sub">
                                            +{{ $wt->gameBonusRules->count() }} oyuna özəl xal
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted small">Bonus gətirmir</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-secondary" style="font-size: 12.5px;">{{ $wt->pay_model_label }}</span>
                            </td>
                            <td>
                                @if(auth()->user()->hasRole('Admin') || auth()->user()->can('worker-types.edit'))
                                    <button type="button"
                                            class="{{ $wt->is_active ? 'status-badge-active' : 'status-badge-inactive' }} btn-toggle-wt"
                                            data-id="{{ $wt->id }}">
                                        <span class="status-dot"></span>
                                        <span class="status-text">{{ $wt->is_active ? 'Aktiv' : 'Deaktiv' }}</span>
                                    </button>
                                @else
                                    <span class="{{ $wt->is_active ? 'status-badge-active' : 'status-badge-inactive' }}" style="cursor: default;">
                                        <span class="status-dot"></span>
                                        <span>{{ $wt->is_active ? 'Aktiv' : 'Deaktiv' }}</span>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('worker-types.edit'))
                                        <a href="{{ route('worker-types.edit', $wt->id) }}" class="worker-btn-action wt-btn-edit">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            <span>Redaktə</span>
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('worker-types.delete'))
                                        <form action="{{ route('worker-types.destroy', $wt->id) }}" method="POST" class="m-0" onsubmit="return confirm('«{{ $wt->name }}» işçi tipini silmək istədiyinizdən əminsiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="worker-btn-action wt-btn-del" title="Sil">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="text-center py-5 text-muted">
                                    <h6 class="fw-bold text-dark mb-1">Heç bir işçi tipi tapılmadı</h6>
                                    <p class="small mb-0">Standart rolları yükləyin və ya yeni işçi tipi əlavə edin.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-toggle-wt').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        const self = this;
        const textSpan = this.querySelector('.status-text');

        fetch(`/hr/worker-types/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                self.className = (data.is_active ? 'status-badge-active' : 'status-badge-inactive') + ' btn-toggle-wt';
                if (textSpan) textSpan.textContent = data.is_active ? 'Aktiv' : 'Deaktiv';
            }
        });
    });
});
</script>
@endpush
