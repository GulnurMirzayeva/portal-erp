@extends('layouts.erp')

@section('title', 'Davamiyyət Jurnalı — QR Giriş-Çıxış')
@section('page-title', 'QR DAVAMİYYƏT JURNALI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item active">Davamiyyət Jurnalı</li>
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
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
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

    /* Stat Cards Grid */
    .stat-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }
    .stat-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 20px 22px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
    }
    .stat-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .stat-metric-val {
        font-size: 28px;
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
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon-blue { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .stat-icon-emerald { background: #ecfdf5; color: #107c41; border: 1px solid #a7f3d0; }
    .stat-icon-sky { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
    .stat-icon-rose { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }

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

    /* 5-Column Grid Layout */
    .filter-form-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr 1fr 1fr 190px;
        gap: 16px;
        align-items: flex-end;
    }
    @media (max-width: 1180px) {
        .filter-form-grid {
            grid-template-columns: 1fr 1fr 1fr;
        }
    }
    @media (max-width: 768px) {
        .filter-form-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    @media (max-width: 520px) {
        .filter-form-grid {
            grid-template-columns: 1fr;
        }
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

    /* Inputs - EXACT 42px height & 12px radius */
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

    /* Action Buttons in Column 5 - EXACT 42px height */
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
        text-decoration: none !important;
        box-sizing: border-box;
        padding: 0 12px;
        white-space: nowrap;
    }
    .filter-btn-submit:hover {
        background: #0b5e30;
        border-color: #084924;
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
        box-sizing: border-box;
        padding: 0 14px;
        white-space: nowrap;
    }
    .filter-btn-reset:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #0f172a !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
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

    /* Enterprise Table */
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-modern th {
        background: #f8fafc;
        padding: 13px 18px;
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 1.5px solid #e2e8f0;
        white-space: nowrap;
    }
    .table-modern td {
        padding: 14px 18px;
        font-size: 13px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        background: #ffffff;
    }
    .table-modern tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-modern tbody tr:hover td {
        background-color: #fafbfc;
    }

    /* Worker Cell */
    .worker-cell {
        display: flex;
        align-items: center;
        gap: 11px;
    }
    .worker-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ecfdf5;
        color: #047857;
        border: 1.5px solid #a7f3d0;
        font-weight: 800;
        font-size: 12.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .worker-name {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.25;
    }
    .worker-phone {
        font-size: 11.5px;
        color: #64748b;
    }

    /* Branch Pill */
    .branch-cell-pill {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 5px 11px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    /* Movement Badges (Giriş / Çıxış) */
    .badge-in {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        letter-spacing: 0.3px;
    }
    .badge-out {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        letter-spacing: 0.3px;
    }

    /* Late Badge */
    .badge-late-warning {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
    }

    /* Distance Badges */
    .badge-dist-ok {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        padding: 4px 9px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-dist-warn {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 4px 9px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Status Badges */
    .badge-status-ok {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-status-rej {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Buttons General */
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
        border-color: #084924;
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

    /* Empty State */
    .empty-state-wrap {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }
    .empty-icon-box {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }
</style>
@endpush

@section('content')
<div class="att-wrapper">

    {{-- Top Action & Info Bar --}}
    <div class="att-top-bar">
        <div>
            <h5 class="att-top-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Canlı Davamiyyət Jurnalı
            </h5>
            <div class="att-top-desc">
                İşçilərin filiallar üzrə GPS və cihaz təsdiqli canlı giriş-çıxış monitorinqi.
                <span class="att-pill-active">
                    <span class="att-pulse-dot"></span>
                    Canlı İzləmə Aktiv
                </span>
                <span class="att-pill-info">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    {{ $isPinRequired ? '4 Rəqəmli PIN Tələbi' : 'PIN-siz Rejim' }}
                </span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @can('attendance.export')
            <a href="{{ route('attendance.export', request()->all()) }}" class="att-btn att-btn-outline att-btn-sm" title="Jurnalı Excel sənədi kimi yüklə">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Excel İxrac
            </a>
            @endcan
            @can('attendance.sync')
            <button type="button" id="btnSyncWorkers" class="att-btn att-btn-outline att-btn-sm" title="Vebsaytdan yeni filial və işçiləri çək">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                Portal-dan Sinxron et
            </button>
            @endcan
            <a href="{{ route('attendance.branches') }}" class="att-btn att-btn-primary att-btn-sm">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                Filiallar & QR-lar
            </a>
        </div>
    </div>

    {{-- 4 Statistik Kart --}}
    <div class="stat-cards-grid">
        {{-- 1. Bugünkü Qeydiyyatlar --}}
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Bugünkü Qeydiyyatlar</div>
                <div class="stat-metric-val text-primary">{{ $stats['total_today'] }}</div>
                <div class="stat-metric-subtitle">Ümumi uğurlu giriş/çıxış</div>
            </div>
            <div class="stat-icon-wrap stat-icon-blue">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
            </div>
        </div>

        {{-- 2. Girişlər (Check-in) --}}
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">İşə Girişlər (Check-in)</div>
                <div class="stat-metric-val" style="color: #059669;">{{ $stats['check_ins_today'] }}</div>
                <div class="stat-metric-subtitle">İşə başlama qeydləri</div>
            </div>
            <div class="stat-icon-wrap stat-icon-emerald">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
            </div>
        </div>

        {{-- 3. Çıxışlar (Check-out) --}}
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">İşdən Çıxışlar (Check-out)</div>
                <div class="stat-metric-val" style="color: #0284c7;">{{ $stats['check_outs_today'] }}</div>
                <div class="stat-metric-subtitle">İş sonu qeydiyyatları</div>
            </div>
            <div class="stat-icon-wrap stat-icon-sky">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </div>
        </div>

        {{-- 4. Şübhəli / Yanlış Cəhd --}}
        <a href="{{ route('attendance.violations') }}" class="stat-card-modern" title="Şübhəli cəhdlər jurnalına keçid">
            <div>
                <div class="stat-metric-title">Şübhəli / Yanlış Cəhd</div>
                <div class="stat-metric-val text-danger">{{ $stats['violations_today'] }}</div>
                <div class="stat-metric-subtitle">Bugünkü bloklananlar →</div>
            </div>
            <div class="stat-icon-wrap stat-icon-rose">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </div>
        </a>
    </div>

    {{-- Süzgəc və Axtarış Paneli (Dəqiq Ölçülər və Bərabər Hizada Grid) --}}
    <div class="filter-panel-card">
        <div class="filter-panel-header">
            <div class="filter-panel-title">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                <span>Süzgəc və Axtarış Paneli</span>
            </div>
            @if(($selectedDate && $selectedDate !== 'all') || ($selectedBranchId && $selectedBranchId !== 'all') || ($selectedType && $selectedType !== 'all') || ($selectedStatus && $selectedStatus !== 'all'))
                <span class="filter-active-pill">
                    <span class="filter-active-dot"></span>
                    Aktiv Süzgəc Tətbiq Edilib
                </span>
            @endif
        </div>

        <form method="GET" action="{{ route('attendance.index') }}" class="filter-form-grid">
            {{-- 1. Tarix --}}
            <div class="filter-field-col">
                <label class="filter-col-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Tarix
                </label>
                <input type="date" name="date" class="filter-ctrl" value="{{ $selectedDate === 'all' ? '' : $selectedDate }}">
            </div>

            {{-- 2. Filial --}}
            <div class="filter-field-col">
                <label class="filter-col-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                    Filial
                </label>
                <select name="branch_id" class="filter-ctrl filter-select">
                    <option value="all">Bütün Filiallar</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $selectedBranchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 3. Hərəkət Növü --}}
            <div class="filter-field-col">
                <label class="filter-col-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                    Hərəkət Növü
                </label>
                <select name="type" class="filter-ctrl filter-select">
                    <option value="all" {{ $selectedType === 'all' ? 'selected' : '' }}>Bütün Hərəkətlər</option>
                    <option value="check_in" {{ $selectedType === 'check_in' ? 'selected' : '' }}>İşə Giriş</option>
                    <option value="check_out" {{ $selectedType === 'check_out' ? 'selected' : '' }}>İşdən Çıxış</option>
                </select>
            </div>

            {{-- 4. Status --}}
            <div class="filter-field-col">
                <label class="filter-col-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Status
                </label>
                <select name="status" class="filter-ctrl filter-select">
                    <option value="success" {{ $selectedStatus === 'success' ? 'selected' : '' }}>Yalnız Uğurlu</option>
                    <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>Bütün Cəhdlər</option>
                    <option value="rejected" {{ $selectedStatus === 'rejected' ? 'selected' : '' }}>Yalnız İmtina</option>
                </select>
            </div>

            {{-- 5. Əməliyyatlar (Axtar & Sıfırla - Vahid 42px Hündürlük) --}}
            <div class="filter-field-col">
                <label class="filter-col-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    Əməliyyatlar
                </label>
                <div class="filter-btn-group">
                    <button type="submit" class="filter-btn-submit">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <span>Axtar</span>
                    </button>
                    <a href="{{ route('attendance.index') }}" class="filter-btn-reset" title="Axtarışı Sıfırla">
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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Gündəlik Giriş-Çıxış Qeydiyyatları
                <span class="data-count-tag">{{ $logs->total() }} qeyd</span>
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 50px;">№</th>
                        <th>İşçi</th>
                        <th>Filial</th>
                        <th>Hərəkət Növü</th>
                        <th>Tarix və Saat</th>
                        <th>Filialdan Məsafə</th>
                        <th>Cihaz & IP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $idx => $log)
                        <tr>
                            <td class="text-muted fw-bold" style="font-size: 12px;">#{{ $logs->firstItem() + $idx }}</td>

                            {{-- İşçi --}}
                            <td>
                                <div class="worker-cell">
                                    <div class="worker-avatar">
                                        {{ mb_substr($log->worker_name ?: '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="worker-name">{{ $log->worker_name }}</div>
                                        <div class="worker-phone">{{ $log->worker?->phone ?: 'Əlaqə qeyd edilməyib' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Filial --}}
                            <td>
                                <span class="branch-cell-pill">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                    {{ $log->branch_name }}
                                </span>
                            </td>

                            {{-- Hərəkət Növü --}}
                            <td>
                                @if($log->type === 'check_in')
                                    <span class="badge-in">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                                        GİRİŞ
                                    </span>
                                @elseif($log->type === 'check_out')
                                    <span class="badge-out">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                        ÇIXIŞ
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Naməlum</span>
                                @endif
                            </td>

                            {{-- Saat & Tarix --}}
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 13.5px;">
                                    {{ $log->scanned_at ? $log->scanned_at->format('H:i:s') : '—' }}
                                </div>
                                <div class="small text-muted" style="font-size: 11.5px;">
                                    {{ $log->date ? $log->date->format('d.m.Y') : '—' }}
                                </div>
                                @if($log->late_minutes && $log->late_minutes > 0)
                                    <div>
                                        <span class="badge-late-warning">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            Gecikmə: {{ $log->late_minutes }} dəq
                                        </span>
                                    </div>
                                @endif
                            </td>

                            {{-- Məsafə --}}
                            <td>
                                @if($log->distance_meters !== null)
                                    @php
                                        $radius = $log->branch?->radius_meters ?: \App\Models\AttendanceSetting::get('default_radius_meters', 100);
                                        $isOk = $log->distance_meters <= $radius;
                                    @endphp
                                    <span class="{{ $isOk ? 'badge-dist-ok' : 'badge-dist-warn' }}">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                        {{ round($log->distance_meters) }} m
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            {{-- Cihaz & IP --}}
                            <td>
                                <div class="fw-semibold text-dark" style="font-size: 12.5px; display: flex; align-items: center; gap: 5px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #64748b;"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                                    {{ $log->device_model ?: 'Mobil Cihaz' }}
                                </div>
                                <div style="font-family: monospace; font-size: 11px; color: #64748b; margin-top: 2px;">
                                    IP: {{ $log->ip_address ?: '—' }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($log->status === 'success')
                                    <span class="badge-status-ok">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        Uğurlu
                                    </span>
                                @else
                                    <span class="badge-status-rej" title="{{ $log->rejection_details }}">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                        İmtina
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state-wrap">
                                    <div class="empty-icon-box">
                                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Seçilmiş kriteriyalar üzrə heç bir davamiyyət qeydi tapılmadı</h6>
                                    <p class="small text-muted mb-0">Filtri sıfırlayaraq və ya fərqli tarix seçərək yenidən yoxlayın.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Səhifələmə --}}
        @if($logs->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #fafbfc;">
                <span class="small text-muted">
                    Göstərilir: <strong>{{ $logs->firstItem() }}</strong> – <strong>{{ $logs->lastItem() }}</strong> / Cəmi: <strong>{{ $logs->total() }}</strong> qeyd
                </span>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.getElementById('btnSyncWorkers')?.addEventListener('click', function() {
        if (!confirm('PortalWebsite-dan yeni filiallar və işçilər sinxronizasiya edilsin?')) return;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="spinner-border spinner-border-sm me-1"></svg> Sinxron edilir...`;

        fetch("{{ route('attendance.workers.sync') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message || 'Tamamlandı!');
            location.reload();
        })
        .catch(e => {
            alert('Xəta baş verdi!');
            btn.disabled = false;
            btn.innerHTML = 'Portal-dan Sinxron et';
        });
    });
</script>
@endpush
