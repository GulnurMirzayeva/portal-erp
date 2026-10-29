@extends('layouts.erp')

@section('title', 'Şübhəli və Yanlış Cəhdlər — Davamiyyət')
@section('page-title', 'ŞÜBHƏLİ VƏ YANLIŞ CƏHDLƏR JURNALI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Davamiyyət</a></li>
    <li class="breadcrumb-item active">Şübhəli Cəhdlər</li>
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
    .att-pill-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .att-pill-radius {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
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
    .stat-icon-rose { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
    .stat-icon-amber { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .stat-icon-blue { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .stat-icon-purple { background: #faf5ff; color: #9333ea; border: 1px solid #e9d5ff; }

    /* Filter Card */
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

    /* 4-Column Aligned Grid */
    .filter-form-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr 1.6fr 210px;
        gap: 16px;
        align-items: flex-end;
    }
    @media (max-width: 1080px) {
        .filter-form-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    @media (max-width: 640px) {
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

    /* Action Buttons in Column 4 - EXACT 42px height */
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

    /* Table Responsive Wrapper & Touch Scroll */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        position: relative;
        display: block;
    }
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .mobile-scroll-hint {
        display: none;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 14px;
        background: #fef2f2;
        border-bottom: 1px solid #fee2e2;
        color: #b91c1c;
        font-size: 12px;
        font-weight: 600;
    }
    @media (max-width: 991px) {
        .mobile-scroll-hint {
            display: flex;
        }
    }

    /* Enterprise Table */
    .table-modern {
        width: 100%;
        min-width: 1050px;
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
        white-space: nowrap;
    }
    .table-modern tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-modern tbody tr:hover td {
        background-color: #fafbfc;
    }

    /* Worker Initials Avatar */
    .worker-cell {
        display: flex;
        align-items: center;
        gap: 11px;
    }
    .worker-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #fef2f2;
        color: #dc2626;
        border: 1.5px solid #fecaca;
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
    .worker-id {
        font-size: 11.5px;
        font-family: monospace;
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

    /* Reason Badges */
    .badge-reason {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }
    .badge-reason-range {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-reason-device {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .badge-reason-pin {
        background: #faf5ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
    }
    .badge-reason-location {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .badge-reason-default {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    /* Distance Badges */
    .dist-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }
    .dist-badge-far {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .dist-badge-mid {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .dist-badge-none {
        color: #94a3b8;
        font-size: 12px;
    }

    /* Device & IP Cell */
    .device-title {
        font-size: 12.5px;
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .ip-mono {
        font-family: monospace;
        font-size: 11px;
        color: #64748b;
        margin-top: 2px;
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
        background: #ecfdf5;
        border: 1.5px solid #a7f3d0;
        color: #107c41;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        box-shadow: 0 8px 20px rgba(16, 124, 65, 0.12);
    }
</style>
@endpush

@section('content')
<div class="att-wrapper">

    {{-- Top Action & Info Bar --}}
    <div class="att-top-bar">
        <div>
            <h5 class="att-top-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Şübhəli və Yanlış Cəhdlər Jurnalı
            </h5>
            <div class="att-top-desc">
                Filialdan kənarda, yad cihazdan və ya yanlış PIN ilə qeydə alınmayan bütün bloklanmış cəhdlər.
                <span class="att-pill-danger">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    {{ $stats['total_violations'] }} Şübhəli Cəhd
                </span>
                <span class="att-pill-radius">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><line x1="2" y1="12" x2="22" y2="12"></line></svg>
                    Vahid Radius: {{ \App\Models\AttendanceSetting::get('default_radius_meters', '100') }} metr
                </span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('attendance.settings') }}" class="att-btn att-btn-outline att-btn-sm">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                Tənzimləmələr
            </a>
            <a href="{{ route('attendance.branches') }}" class="att-btn att-btn-outline att-btn-sm">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Filiallar & QR-lar
            </a>
            <a href="{{ route('attendance.index') }}" class="att-btn att-btn-outline att-btn-sm">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                Davamiyyət Jurnalı
            </a>
        </div>
    </div>

    {{-- Statistik Kartlar Qridi --}}
    <div class="stat-cards-grid">
        {{-- 1. Ümumi Şübhəli Cəhd --}}
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Ümumi Şübhəli Cəhd</div>
                <div class="stat-metric-val text-danger">{{ $stats['total_violations'] }}</div>
                <div class="stat-metric-subtitle">Bütün bloklanmış cəhdlər</div>
            </div>
            <div class="stat-icon-wrap stat-icon-rose">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
        </div>

        {{-- 2. Məsafə Xətası --}}
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Məsafə Xətası (Geofence)</div>
                <div class="stat-metric-val text-warning">{{ $stats['out_of_range_count'] }}</div>
                <div class="stat-metric-subtitle">Filialdan kənarda cəhd</div>
            </div>
            <div class="stat-icon-wrap stat-icon-amber">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            </div>
        </div>

        {{-- 3. Cihaz Limiti --}}
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Cihaz Limiti Pozuntusu</div>
                <div class="stat-metric-val text-primary">{{ $stats['device_reused_count'] }}</div>
                <div class="stat-metric-subtitle">1 telefonda 2-ci işçi</div>
            </div>
            <div class="stat-icon-wrap stat-icon-blue">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
            </div>
        </div>

        {{-- 4. Yanlış PIN --}}
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Yanlış PIN Cəhdi</div>
                <div class="stat-metric-val" style="color: #9333ea;">{{ $stats['wrong_pin_count'] }}</div>
                <div class="stat-metric-subtitle">Səhv fərdi kod</div>
            </div>
            <div class="stat-icon-wrap stat-icon-purple">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
        </div>
    </div>

    {{-- Süzgəc və Axtarış Paneli (Dəqiq Ölçülər və Bərabər Hizada Grid) --}}
    <div class="filter-panel-card">
        <div class="filter-panel-header">
            <div class="filter-panel-title">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                <span>Süzgəc və Axtarış Paneli</span>
            </div>
            @if($selectedDate || ($selectedBranchId && $selectedBranchId !== 'all') || ($selectedReason && $selectedReason !== 'all'))
                <span class="filter-active-pill">
                    <span class="filter-active-dot"></span>
                    Aktiv Süzgəc Tətbiq Edilib
                </span>
            @endif
        </div>

        <form method="GET" action="{{ route('attendance.violations') }}" class="filter-form-grid">
            {{-- 1. Tarix --}}
            <div class="filter-field-col">
                <label class="filter-col-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Tarix
                </label>
                <input type="date" name="date" class="filter-ctrl" value="{{ $selectedDate }}">
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

            {{-- 3. Pozuntu Növü --}}
            <div class="filter-field-col">
                <label class="filter-col-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    Pozuntu Səbəbi
                </label>
                <select name="reason" class="filter-ctrl filter-select">
                    <option value="all">Bütün Səbəblər</option>
                    <option value="out_of_range" {{ $selectedReason === 'out_of_range' ? 'selected' : '' }}>Məsafə Limiti Aşıldı (Filialdan kənarda)</option>
                    <option value="device_already_used" {{ $selectedReason === 'device_already_used' ? 'selected' : '' }}>Cihaz Limiti: Eyni telefondan 2-ci işçi</option>
                    <option value="wrong_pin" {{ $selectedReason === 'wrong_pin' ? 'selected' : '' }}>Yanlış PIN Kod</option>
                    <option value="location_permission_denied" {{ $selectedReason === 'location_permission_denied' ? 'selected' : '' }}>GPS Məkan İcazəsi Verilməyib</option>
                </select>
            </div>

            {{-- 4. Əməliyyatlar (Tətbiq Et & Sıfırla - Vahid 42px Hündürlük) --}}
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
                    <a href="{{ route('attendance.violations') }}" class="filter-btn-reset" title="Axtarışı Sıfırla">
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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Qeydə Alınmış Pozuntuların Siyahısı
                <span class="data-count-tag">{{ $violations->total() }} qeyd</span>
            </h6>
        </div>

        <div class="mobile-scroll-hint">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 8L22 12L18 16"></path><path d="M6 8L2 12L6 16"></path></svg>
            <span>Cədvəli tam görmək üçün sağa-sola sürüşdürə bilərsiniz</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 8L2 12L6 16"></path><path d="M18 8L22 12L18 16"></path></svg>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 50px;">№</th>
                        <th>Tarix və Saat</th>
                        <th>İşçi</th>
                        <th>Filial</th>
                        <th>Pozuntu Səbəbi</th>
                        <th>Məsafə (GPS)</th>
                        <th>Cihaz & IP</th>
                        <th>Təfsilat və İzah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($violations as $idx => $v)
                        <tr>
                            <td class="text-muted fw-bold" style="font-size: 12px;">#{{ $violations->firstItem() + $idx }}</td>

                            {{-- Tarix & Saat --}}
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 13.5px;">
                                    {{ $v->scanned_at ? $v->scanned_at->format('H:i:s') : '—' }}
                                </div>
                                <div class="small text-muted" style="font-size: 11.5px;">
                                    {{ $v->date ? $v->date->format('d.m.Y') : '—' }}
                                </div>
                            </td>

                            {{-- İşçi Məlumatı --}}
                            <td>
                                <div class="worker-cell">
                                    <div class="worker-avatar">
                                        {{ mb_substr($v->worker_name ?: '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="worker-name">{{ $v->worker_name ?: 'Seçilməyib' }}</div>
                                        <div class="worker-id">ID: #{{ $v->worker_id ?: '—' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Filial --}}
                            <td>
                                <span class="branch-cell-pill">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                    {{ $v->branch_name }}
                                </span>
                            </td>

                            {{-- Pozuntu Səbəbi --}}
                            <td>
                                @if($v->rejection_reason === 'out_of_range')
                                    <span class="badge-reason badge-reason-range">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><line x1="2" y1="12" x2="22" y2="12"></line></svg>
                                        Məsafə Limiti Aşıldı
                                    </span>
                                @elseif($v->rejection_reason === 'device_already_used')
                                    <span class="badge-reason badge-reason-device">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                                        Cihaz Limiti: 2-ci İşçi
                                    </span>
                                @elseif($v->rejection_reason === 'wrong_pin')
                                    <span class="badge-reason badge-reason-pin">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        Yanlış PIN Kod
                                    </span>
                                @elseif($v->rejection_reason === 'location_permission_denied')
                                    <span class="badge-reason badge-reason-location">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                                        GPS İcazəsi Yoxdur
                                    </span>
                                @else
                                    <span class="badge-reason badge-reason-default">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                        {{ $v->formatted_rejection_reason }}
                                    </span>
                                @endif
                            </td>

                            {{-- Məsafə --}}
                            <td>
                                @if($v->distance_meters !== null)
                                    @if($v->distance_meters >= 1000)
                                        <span class="dist-badge dist-badge-far">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                            {{ number_format($v->distance_meters / 1000, 1) }} km
                                        </span>
                                    @else
                                        <span class="dist-badge dist-badge-mid">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                            +{{ round($v->distance_meters) }} metr
                                        </span>
                                    @endif
                                @else
                                    <span class="dist-badge-none">GPS qeydə alınmayıb</span>
                                @endif
                            </td>

                            {{-- Cihaz & IP --}}
                            <td>
                                <div class="device-title">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #64748b;"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                                    {{ $v->device_model ?: 'Mobil Cihaz' }}
                                </div>
                                <div class="ip-mono">IP: {{ $v->ip_address ?: '—' }}</div>
                            </td>

                            {{-- Təfsilat və İzah --}}
                            <td>
                                <div style="max-width: 260px; font-size: 12px; color: #64748b; line-height: 1.4;">
                                    {{ $v->rejection_details ?: 'Əlavə izah qeyd olunmayıb.' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state-wrap">
                                    <div class="empty-icon-box">
                                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Heç bir şübhəli və ya yanlış cəhd tapılmadı</h6>
                                    <p class="small text-muted mb-0">Bütün işçilər qaydalara və məkan geofence radiusuna uyğun şəkildə qeydiyyatdan keçirlər.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Səhifələmə --}}
        @if($violations->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #fafbfc;">
                <span class="small text-muted">
                    Göstərilir: <strong>{{ $violations->firstItem() }}</strong> – <strong>{{ $violations->lastItem() }}</strong> / Cəmi: <strong>{{ $violations->total() }}</strong> qeyd
                </span>
                <div>
                    {{ $violations->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
