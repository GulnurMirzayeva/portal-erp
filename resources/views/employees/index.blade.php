@extends('layouts.erp')

@section('title', 'Vahid İşçi Bazası & Kadrlar — Portal ERP')
@section('page-title', 'VAHİD İŞÇİ BAZASI VƏ KADRLAR')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">HR / Kadrlar</a></li>
    <li class="breadcrumb-item active">İşçilər Siyahısı</li>
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
    .att-top-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .att-top-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #ecfdf5;
        color: #059669;
        border: 1.5px solid #a7f3d0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .att-top-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
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
    .att-pill-tag {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .att-pulse-dot {
        width: 6px;
        height: 6px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.35);
    }
    .att-pill-count {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
    }

    /* Action Buttons in Top Bar */
    .att-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 13px;
        font-weight: 700;
        height: 42px;
        padding: 0 18px;
        border-radius: 12px;
        cursor: pointer;
        border: 1.5px solid transparent;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
        box-sizing: border-box;
        white-space: nowrap;
    }
    .att-btn-primary {
        background: #107c41;
        color: #ffffff !important;
        border-color: #0d6334;
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.22);
    }
    .att-btn-primary:hover {
        background: #0b5e30;
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
        transform: translateY(-1px);
    }

    /* Alerts */
    .att-alert-success {
        background: #ecfdf5;
        border: 1.5px solid #a7f3d0;
        border-radius: 14px;
        padding: 13px 18px;
        margin-bottom: 20px;
        color: #065f46;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 13.5px;
    }
    .att-alert-danger {
        background: #fef2f2;
        border: 1.5px solid #fecaca;
        border-radius: 14px;
        padding: 13px 18px;
        margin-bottom: 20px;
        color: #991b1b;
        font-size: 13.5px;
    }

    /* Stat Cards Grid */
    .stat-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }
    .stat-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
    }
    .stat-card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .stat-metric-title {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .stat-metric-val {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .stat-metric-subtitle {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 600;
        margin-top: 3px;
    }
    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon-slate { background: #f1f5f9; color: #475569; }
    .stat-icon-blue { background: #eff6ff; color: #2563eb; }
    .stat-icon-emerald { background: #ecfdf5; color: #107c41; }
    .stat-icon-amber { background: #fffbeb; color: #d97706; }
    .stat-icon-purple { background: #fdf2f8; color: #db2777; }

    /* Filter Panel Card */
    .filter-panel-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 18px 22px;
        margin-bottom: 22px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }
    .filter-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #e2e8f0;
        gap: 12px;
    }
    .filter-panel-title {
        font-size: 13.5px;
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
        padding: 2px 9px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .filter-active-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #2563eb;
    }

    /* Filter Form Grid - pure CSS grid */
    .filter-form-grid {
        display: grid;
        grid-template-columns: 2fr 1.3fr 1.3fr 1fr 1fr 1fr auto;
        gap: 10px;
        align-items: flex-end;
    }
    @media (max-width: 1200px) {
        .filter-form-grid { grid-template-columns: 1fr 1fr 1fr; }
    }
    @media (max-width: 768px) {
        .filter-form-grid { grid-template-columns: 1fr; }
    }
    .filter-field-col {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    .filter-col-label {
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .filter-ctrl {
        height: 40px;
        width: 100%;
        padding: 0 12px;
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        outline: none;
        transition: all 0.15s ease;
        box-sizing: border-box;
    }
    .filter-ctrl:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.12);
    }
    .filter-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 14px 14px;
        padding-right: 32px;
        cursor: pointer;
        -webkit-appearance: none;
        appearance: none;
    }
    .filter-btn-group {
        display: flex;
        align-items: center;
        gap: 6px;
        height: 40px;
    }
    .filter-btn-submit {
        height: 40px;
        background: #107c41;
        color: #ffffff !important;
        border: 1px solid #0d6334;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        padding: 0 14px;
        white-space: nowrap;
    }
    .filter-btn-submit:hover {
        background: #0b5e30;
    }
    .filter-btn-reset {
        height: 40px;
        background: #f8fafc;
        color: #475569 !important;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none !important;
        padding: 0 12px;
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
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }
    .data-card-header {
        padding: 16px 22px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #ffffff;
        flex-wrap: wrap;
    }
    .data-table-title {
        font-size: 14.5px;
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
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11.5px;
        font-weight: 700;
    }

    /* Table */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-modern {
        width: 100%;
        min-width: 960px;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-modern th {
        background: #f8fafc;
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
        white-space: nowrap;
    }
    .table-modern td {
        padding: 12px 16px;
        font-size: 13px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        background: #ffffff;
        white-space: nowrap;
    }
    .table-modern tbody tr:hover td {
        background-color: #fafbfc;
    }

    /* Worker Cell */
    .worker-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .worker-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 12.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .avatar-male {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1.5px solid #bfdbfe;
    }
    .avatar-female {
        background: #fdf2f8;
        color: #be185d;
        border: 1.5px solid #fbcfe8;
    }
    .worker-name {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.25;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .worker-sub {
        font-size: 11.5px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 2px;
    }
    .pin-badge {
        font-family: ui-monospace, SFMono-Regular, monospace;
        background: #f1f5f9;
        color: #0f172a;
        padding: 1px 5px;
        border-radius: 5px;
        border: 1px solid #cbd5e1;
        font-weight: 700;
        font-size: 11px;
    }

    /* Badges */
    .role-badge-primary {
        background: #ecfdf5;
        color: #065f46;
        border: 1.5px solid #a7f3d0;
        padding: 3px 8px;
        border-radius: 7px;
        font-size: 11.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .role-badge-secondary {
        background: #f8fafc;
        color: #475569;
        border: 1.5px solid #e2e8f0;
        padding: 2px 7px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .tabel-tag {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        color: #0f172a;
        padding: 3px 9px;
        border-radius: 8px;
        font-family: ui-monospace, SFMono-Regular, monospace;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
    }
    .branch-pill-main {
        background: #ecfdf5;
        color: #047857;
        border: 1.5px solid #a7f3d0;
        border-radius: 8px;
        padding: 3px 9px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .branch-pill-managed {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
        border-radius: 6px;
        padding: 1px 6px;
        font-size: 10.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin-top: 2px;
    }
    .game-badge {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
        border-radius: 6px;
        padding: 2px 7px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    /* Status & Intern Interactive Badges */
    .status-badge-active {
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
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .status-badge-active:hover { background: #d1fae5; }
    .status-badge-inactive {
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
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .status-badge-inactive:hover { background: #fee2e2; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
    .status-dot-active { background: #10b981; }
    .status-dot-inactive { background: #ef4444; }

    .intern-badge-yes {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
        padding: 4px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
    }
    .intern-badge-no {
        background: #f8fafc;
        color: #475569;
        border: 1.5px solid #e2e8f0;
        padding: 4px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
    }

    /* Table Actions */
    .action-btn-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .emp-btn-action {
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        padding: 0 10px;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none !important;
        box-sizing: border-box;
    }
    .emp-btn-edit {
        background: #eff6ff;
        color: #2563eb !important;
        border: 1.5px solid #bfdbfe;
    }
    .emp-btn-edit:hover {
        background: #2563eb;
        color: #ffffff !important;
    }
    .emp-btn-del {
        background: #fef2f2;
        color: #dc2626 !important;
        border: 1.5px solid #fecaca;
        padding: 0 8px;
    }
    .emp-btn-del:hover {
        background: #dc2626;
        color: #ffffff !important;
    }

    /* Custom Pagination */
    .custom-pagination-bar {
        padding: 14px 22px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafbfc;
        flex-wrap: wrap;
        gap: 12px;
    }
    .custom-pagination-info {
        font-size: 12.5px;
        color: #64748b;
        font-weight: 500;
    }
    .custom-pagination-info strong {
        color: #0f172a;
    }
    .custom-pagination-nav {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .custom-page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.15s ease;
    }
    .custom-page-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .custom-page-btn.active {
        background: #107c41;
        border-color: #0d6334;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(16, 124, 65, 0.2);
    }
    .custom-page-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f8fafc;
    }
    .custom-page-dots {
        color: #94a3b8;
        padding: 0 4px;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="att-wrapper">

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="att-alert-success">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span><strong>Uğurlu:</strong> {{ session('success') }}</span>
            </div>
            <span style="cursor: pointer; font-weight: bold; font-size: 16px;" onclick="this.closest('.att-alert-success').remove()">&times;</span>
        </div>
    @endif

    {{-- Danger Alert --}}
    @if(isset($errors) && $errors->any())
        <div class="att-alert-danger">
            <strong>Xəta:</strong> {{ $errors->first() }}
        </div>
    @endif

    {{-- Top Action & Info Bar --}}
    <div class="att-top-bar">
        <div class="att-top-left">
            <div class="att-top-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div>
                <h5 class="att-top-title">
                    Vahid İşçi Bazası & Kadr İdarəetməsi
                </h5>
                <div class="att-top-desc">
                    Bütün filial və ofis heyəti, çoxlu vəzifələr və stajorluq dərəcələri.
                    <span class="att-pill-tag">
                        <span class="att-pulse-dot"></span>
                        ERP Master Kadrlar
                    </span>
                    <span class="att-pill-count">
                        Cəmi {{ $employees->total() }} işçi
                    </span>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.create'))
                <form action="{{ route('employees.import') }}" method="POST" style="margin: 0;" onsubmit="return confirm('Mövcud sayt və QR cədvəlindəki işçiləri ERP-yə yenidən sinxronizasiya etmək istəyirsiniz?');">
                    @csrf
                    <button type="submit" class="att-btn att-btn-outline" title="Saytdakı və QR cədvəlindəki bütün işçiləri ERP-yə sinxron et">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        İşçiləri İdxal Et
                    </button>
                </form>
                <a href="{{ route('employees.create') }}" class="att-btn att-btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Yeni İşçi Əlavə Et
                </a>
            @endif
        </div>
    </div>

    {{-- Stat Cards Grid (5 Cards) --}}
    <div class="stat-cards-grid">
        <a href="{{ route('employees.index') }}" class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Cəmi İşçi</div>
                <div class="stat-metric-val">{{ $stats['total'] }}</div>
                <div class="stat-metric-subtitle">ERP kadr bazası</div>
            </div>
            <div class="stat-icon-wrap stat-icon-slate">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
        </a>

        <a href="{{ route('employees.index', array_merge(request()->except('page'), ['status' => 'active'])) }}" class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Aktiv İşçilər</div>
                <div class="stat-metric-val" style="color: #2563eb;">{{ $stats['active'] }}</div>
                <div class="stat-metric-subtitle">Hazırda işləyənlər</div>
            </div>
            <div class="stat-icon-wrap stat-icon-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
            </div>
        </a>

        <a href="{{ route('employees.index', array_merge(request()->except('page'), ['status' => 'active', 'is_intern' => '0'])) }}" class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Daimi Heyət</div>
                <div class="stat-metric-val" style="color: #107c41;">{{ $stats['permanent'] }}</div>
                <div class="stat-metric-subtitle">Tam ştat işçilər</div>
            </div>
            <div class="stat-icon-wrap stat-icon-emerald">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
        </a>

        <a href="{{ route('employees.index', array_merge(request()->except('page'), ['is_intern' => '1'])) }}" class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Stajorlar</div>
                <div class="stat-metric-val" style="color: #d97706;">{{ $stats['interns'] }}</div>
                <div class="stat-metric-subtitle">Staj keçənlər (0 xal)</div>
            </div>
            <div class="stat-icon-wrap stat-icon-amber">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
            </div>
        </a>

        <a href="{{ route('employees.index', array_merge(request()->except('page'), ['status' => 'inactive'])) }}" class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Deaktiv / Arxiv</div>
                <div class="stat-metric-val" style="color: #db2777;">{{ $stats['inactive'] }}</div>
                <div class="stat-metric-subtitle">İşdən çıxmış və ya arxiv</div>
            </div>
            <div class="stat-icon-wrap stat-icon-purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            </div>
        </a>
    </div>

    {{-- Filter Panel Card --}}
    @php
        $activeFiltersCount = collect($filters)->filter(function($val) {
            return !is_null($val) && $val !== '' && $val !== 'all';
        })->count();
    @endphp
    <div class="filter-panel-card">
        <div class="filter-panel-header">
            <div class="filter-panel-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                <span>İşçi Süzgəci və Ətraflı Axtarış</span>
            </div>
            @if($activeFiltersCount > 0)
                <span class="filter-active-pill">
                    <span class="filter-active-dot"></span>
                    {{ $activeFiltersCount }} filtr aktivdir
                </span>
            @endif
        </div>

        <form method="GET" action="{{ route('employees.index') }}" class="filter-form-grid">
            <div class="filter-field-col">
                <span class="filter-col-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Axtarış (Ad / Tel / PIN)
                </span>
                <input type="text" name="search" class="filter-ctrl" placeholder="Məs: Əli, 050, 2355..." value="{{ $filters['search'] ?? '' }}">
            </div>

            <div class="filter-field-col">
                <span class="filter-col-label">Filial</span>
                <select name="branch_id" class="filter-ctrl filter-select">
                    <option value="all">Bütün Filiallar</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ ($filters['branch_id'] ?? '') == $b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-field-col">
                <span class="filter-col-label">Vəzifə</span>
                <select name="worker_type_id" class="filter-ctrl filter-select">
                    <option value="all">Bütün Vəzifələr</option>
                    @foreach($workerTypes as $wt)
                        <option value="{{ $wt->id }}" {{ ($filters['worker_type_id'] ?? '') == $wt->id ? 'selected' : '' }}>
                            {{ $wt->name }} ({{ $wt->short_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-field-col">
                <span class="filter-col-label">Stajorluq</span>
                <select name="is_intern" class="filter-ctrl filter-select">
                    <option value="all" {{ ($filters['is_intern'] ?? '') === 'all' || !isset($filters['is_intern']) ? 'selected' : '' }}>Hamısı</option>
                    <option value="0" {{ ($filters['is_intern'] ?? '') === '0' ? 'selected' : '' }}>Yalnız Daimi</option>
                    <option value="1" {{ ($filters['is_intern'] ?? '') === '1' ? 'selected' : '' }}>Yalnız Stajor</option>
                </select>
            </div>

            <div class="filter-field-col">
                <span class="filter-col-label">Status</span>
                <select name="status" class="filter-ctrl filter-select">
                    <option value="all" {{ ($filters['status'] ?? '') === 'all' ? 'selected' : '' }}>Hamısı</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Yalnız Aktiv</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Deaktiv</option>
                    <option value="dismissed" {{ ($filters['status'] ?? '') === 'dismissed' ? 'selected' : '' }}>İşdən çıxıb</option>
                </select>
            </div>

            <div class="filter-field-col">
                <span class="filter-col-label">Cinsiyyət</span>
                <select name="gender" class="filter-ctrl filter-select">
                    <option value="all" {{ ($filters['gender'] ?? '') === 'all' ? 'selected' : '' }}>Hamısı</option>
                    <option value="male" {{ ($filters['gender'] ?? '') === 'male' ? 'selected' : '' }}>Kişi</option>
                    <option value="female" {{ ($filters['gender'] ?? '') === 'female' ? 'selected' : '' }}>Qadın</option>
                </select>
            </div>

            <div class="filter-btn-group">
                <button type="submit" class="filter-btn-submit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span>Axtar</span>
                </button>
                <a href="{{ route('employees.index') }}" class="filter-btn-reset" title="Sıfırla">
                    <span>Sıfırla</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Data Card & Table --}}
    <div class="data-card-modern">
        <div class="data-card-header">
            <h6 class="data-table-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                İşçilərin Kadr Reyestri
                <span class="data-count-tag">{{ $employees->total() }} işçi</span>
            </h6>
            <span style="font-size: 12px; color: #64748b;">Səhifə {{ $employees->currentPage() }} / {{ $employees->lastPage() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 45px; text-align: center;">#</th>
                        <th>İşçi (Ad / Əlaqə / PIN)</th>
                        <th>Vəzifələr (Rollar)</th>
                        <th>Tabel Kodu</th>
                        <th>Filial</th>
                        <th>Daimi Oyunlar</th>
                        <th style="text-align: center;">Stajorluq</th>
                        <th style="text-align: center;">Status</th>
                        <th style="width: 100px; text-align: center;">Əməliyyatlar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        @php
                            $genderClass = $emp->gender === 'female' ? 'avatar-female' : 'avatar-male';
                            $initial = mb_substr($emp->first_name ?: $emp->full_name, 0, 1);
                        @endphp
                        <tr>
                            <td style="text-align: center; color: #94a3b8; font-family: ui-monospace, monospace; font-size: 11px;">
                                #{{ $emp->id }}
                            </td>
                            <td>
                                <div class="worker-cell">
                                    <div class="worker-avatar {{ $genderClass }}">
                                        {{ $initial }}
                                    </div>
                                    <div>
                                        <div class="worker-name">
                                            <span>{{ $emp->full_name }}</span>
                                            @if($emp->gender === 'female')
                                                <span style="color: #be185d; font-size: 10.5px; font-weight: 700;">(Qadın)</span>
                                            @endif
                                        </div>
                                        <div class="worker-sub">
                                            @if($emp->phone)
                                                <span>{{ $emp->phone }}</span>
                                                <span style="color: #cbd5e1;">&bull;</span>
                                            @endif
                                            <span class="pin-badge">
                                                PIN: {{ $emp->pin_code ?: '1234' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($emp->workerTypes->count() > 0)
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px; align-items: center;">
                                        @foreach($emp->workerTypes as $wt)
                                            @if($wt->pivot->is_primary)
                                                <span class="role-badge-primary" title="Əsas vəzifə">
                                                    ★ {{ $wt->name }}
                                                </span>
                                            @else
                                                <span class="role-badge-secondary">
                                                    {{ $wt->name }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-size: 12px;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="tabel-tag" id="tabelLabel-{{ $emp->id }}">
                                    {{ $emp->tabel_name }} / <strong id="tabelRole-{{ $emp->id }}" style="color: #107c41;">{{ $emp->tabel_role_label }}</strong>
                                </span>
                            </td>
                            <td>
                                @if($emp->primaryBranch())
                                    <div>
                                        <span class="branch-pill-main">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                                            {{ $emp->primaryBranch()->name }}
                                        </span>
                                        @if($emp->managedBranches->count() > 0)
                                            <div style="display: flex; flex-wrap: wrap; gap: 3px;">
                                                @foreach($emp->managedBranches as $mb)
                                                    <span class="branch-pill-managed" title="Əlavə idarə etdiyi filial">
                                                        + {{ $mb->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span style="color: #64748b; font-size: 12px; font-weight: 600;">Mərkəzi Ofis</span>
                                @endif
                            </td>
                            <td>
                                @if($emp->games->count() > 0)
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                        @foreach($emp->games as $g)
                                            <span class="game-badge">
                                                {{ $g->game_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-size: 12px;">—</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.edit'))
                                    <button type="button"
                                            class="{{ $emp->is_intern ? 'intern-badge-yes' : 'intern-badge-no' }} btn-toggle-intern"
                                            data-id="{{ $emp->id }}"
                                            title="Stajorluq statusunu dəyiş">
                                        <span class="intern-label">{{ $emp->is_intern ? 'Stajor' : 'Daimi' }}</span>
                                    </button>
                                @else
                                    <span class="{{ $emp->is_intern ? 'intern-badge-yes' : 'intern-badge-no' }}" style="cursor: default;">
                                        <span class="intern-label">{{ $emp->is_intern ? 'Stajor' : 'Daimi' }}</span>
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.edit'))
                                    <button type="button"
                                            class="{{ $emp->status === 'active' ? 'status-badge-active' : 'status-badge-inactive' }} btn-toggle-emp-status"
                                            data-id="{{ $emp->id }}"
                                            title="İşçi statusunu dəyiş">
                                        <span class="status-dot {{ $emp->status === 'active' ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                        <span class="status-label">{{ $emp->status_label }}</span>
                                    </button>
                                @else
                                    <span class="{{ $emp->status === 'active' ? 'status-badge-active' : 'status-badge-inactive' }}" style="cursor: default;">
                                        <span class="status-dot {{ $emp->status === 'active' ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                        <span>{{ $emp->status_label }}</span>
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="action-btn-group">
                                    @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.edit'))
                                        <a href="{{ route('employees.edit', $emp->id) }}" class="emp-btn-action emp-btn-edit" title="Redaktə et">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            <span>Redaktə</span>
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.delete'))
                                        <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('«{{ $emp->full_name }}» adlı işçini arxivə göndərmək istədiyinizdən əminsiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="emp-btn-action emp-btn-del" title="Arxivləşdir">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px 20px; color: #64748b;">
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 4px;">Heç bir işçi tapılmadı</div>
                                <div style="font-size: 12px; margin-bottom: 12px;">Süzgəc parametrlərini sıfırlayın və ya yeni işçi əlavə edin.</div>
                                <a href="{{ route('employees.index') }}" class="att-btn att-btn-outline" style="height: 36px;">Süzgəci Sıfırla</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="custom-pagination-bar">
                <span class="custom-pagination-info">
                    Göstərilir: <strong>{{ $employees->firstItem() }} – {{ $employees->lastItem() }}</strong> / Cəmi: <strong>{{ $employees->total() }}</strong> işçi
                </span>
                <div class="custom-pagination-nav">
                    @if ($employees->onFirstPage())
                        <span class="custom-page-btn disabled">&laquo;</span>
                    @else
                        <a href="{{ $employees->previousPageUrl() }}" class="custom-page-btn">&laquo;</a>
                    @endif

                    @php
                        $start = max(1, $employees->currentPage() - 2);
                        $end = min($employees->lastPage(), $employees->currentPage() + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $employees->url(1) }}" class="custom-page-btn">1</a>
                        @if($start > 2)
                            <span class="custom-page-dots">&hellip;</span>
                        @endif
                    @endif

                    @for ($p = $start; $p <= $end; $p++)
                        @if ($p == $employees->currentPage())
                            <span class="custom-page-btn active">{{ $p }}</span>
                        @else
                            <a href="{{ $employees->url($p) }}" class="custom-page-btn">{{ $p }}</a>
                        @endif
                    @endfor

                    @if($end < $employees->lastPage())
                        @if($end < $employees->lastPage() - 1)
                            <span class="custom-page-dots">&hellip;</span>
                        @endif
                        <a href="{{ $employees->url($employees->lastPage()) }}" class="custom-page-btn">{{ $employees->lastPage() }}</a>
                    @endif

                    @if ($employees->hasMorePages())
                        <a href="{{ $employees->nextPageUrl() }}" class="custom-page-btn">&raquo;</a>
                    @else
                        <span class="custom-page-btn disabled">&raquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
// Status Toggle AJAX
document.querySelectorAll('.btn-toggle-emp-status').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const id = this.dataset.id;
        const self = this;
        const label = this.querySelector('.status-label');
        const dot = this.querySelector('.status-dot');

        fetch(`/hr/employees/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                self.className = (data.is_active ? 'status-badge-active' : 'status-badge-inactive') + ' btn-toggle-emp-status';
                if (label) label.textContent = data.label;
                if (dot) dot.className = 'status-dot ' + (data.is_active ? 'status-dot-active' : 'status-dot-inactive');
            }
        });
    });
});

// Intern Toggle AJAX
document.querySelectorAll('.btn-toggle-intern').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const id = this.dataset.id;
        const self = this;
        const label = this.querySelector('.intern-label');
        const tabelRole = document.getElementById(`tabelRole-${id}`);

        fetch(`/hr/employees/${id}/toggle-intern`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                self.className = (data.is_intern ? 'intern-badge-yes' : 'intern-badge-no') + ' btn-toggle-intern';
                if (label) label.textContent = data.label;
                if (tabelRole && data.tabel_role_label) {
                    tabelRole.textContent = data.tabel_role_label;
                }
            } else if (data.message) {
                alert(data.message);
            }
        });
    });
});
</script>
@endpush
