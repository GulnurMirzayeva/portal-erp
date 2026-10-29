@extends('layouts.erp')

@section('title', 'İşçilər və PIN Kodlar — Davamiyyət')
@section('page-title', 'İŞÇİLƏR VƏ PIN KOD İDARƏETMƏSİ')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Davamiyyət</a></li>
    <li class="breadcrumb-item active">İşçilər & PIN Kodlar</li>
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
    .att-pill-pin {
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
    .att-pill-workers {
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
    .att-pill-active-count {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .att-pill-inactive-count {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

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

    /* 4-Column Aligned Grid */
    .filter-form-grid {
        display: grid;
        grid-template-columns: 1.4fr 1.1fr 1.1fr 190px;
        gap: 16px;
        align-items: flex-end;
    }
    @media (max-width: 1050px) {
        .filter-form-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    @media (max-width: 580px) {
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

    /* Action Buttons in Column 3 - EXACT 42px height */
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
        background: #f0fdf4;
        border-bottom: 1px solid #dcfce7;
        color: #15803d;
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
        min-width: 980px;
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
    .worker-id-sub {
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

    /* PIN Code Badge */
    .pin-code-badge {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        color: #0f172a;
        font-family: monospace;
        font-weight: 800;
        font-size: 14px;
        letter-spacing: 3px;
        padding: 4px 10px;
        border-radius: 9px;
        display: inline-block;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    /* Device Badges */
    .device-badge-active {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .device-badge-none {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Table Action Buttons */
    .worker-btn-action {
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        padding: 0 12px;
        cursor: pointer;
        transition: all 0.18s ease;
        text-decoration: none !important;
        box-sizing: border-box;
    }
    .worker-btn-pin {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        color: #334155;
    }
    .worker-btn-pin:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #0f172a;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        transform: translateY(-1px);
    }
    .worker-btn-reset-device {
        background: #fffbeb;
        border: 1.5px solid #fde68a;
        color: #b45309;
    }
    .worker-btn-reset-device:hover {
        background: #fef3c7;
        border-color: #f59e0b;
        color: #92400e;
        box-shadow: 0 2px 6px rgba(245, 158, 11, 0.15);
        transform: translateY(-1px);
    }

    /* Status Badges */
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
        gap: 6px;
    }
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
        gap: 6px;
    }
    .status-badge-active .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
    }
    .status-badge-inactive .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ef4444;
    }

    /* Custom Attendance Modal styles */
    .att-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(5px);
        z-index: 10050;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .att-modal-dialog {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        animation: attModalIn 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
    }
    @keyframes attModalIn {
        from { opacity: 0; transform: scale(0.95) translateY(-10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .att-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .att-modal-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .att-modal-close-btn {
        background: transparent;
        border: none;
        font-size: 24px;
        color: #94a3b8;
        cursor: pointer;
        line-height: 1;
        padding: 4px 8px;
        border-radius: 8px;
        transition: all 0.15s;
    }
    .att-modal-close-btn:hover {
        color: #0f172a;
        background: #f1f5f9;
    }
    .att-modal-body {
        padding: 24px 22px;
    }
    .att-modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 22px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
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
</style>
@endpush

@section('content')
<div class="att-wrapper">

    {{-- Top Action & Info Bar --}}
    <div class="att-top-bar">
        <div>
            <h5 class="att-top-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                İşçilər və Fərdi PIN Kod İdarəetməsi
            </h5>
            <div class="att-top-desc">
                Hər filial üzrə yalnız aktiv işçilər görünür. Statusu dəyişərək və ya sinxronizasiya edərək işçilərin QR siyahısını idarə edin.
                <span class="att-pill-pin">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    {{ $isPinRequired ? '4 Rəqəmli PIN Tələbi Aktiv' : 'PIN-siz Rejim' }}
                </span>
                <span class="att-pill-active-count">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    {{ $stats['active'] ?? 0 }} Aktiv İşçi
                </span>
                @if(($stats['inactive'] ?? 0) > 0)
                <span class="att-pill-inactive-count">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                    {{ $stats['inactive'] }} Deaktiv
                </span>
                @endif
                <span class="att-pill-workers">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    {{ $stats['total'] ?? $workers->total() }} Cəmi Baza
                </span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('attendance.sync'))
            <button type="button" id="btnSyncWorkers" class="att-btn att-btn-outline att-btn-sm" title="Vebsaytdan yeni filial və işçiləri sinxronizasiya et">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                Sinxron et
            </button>
            @endif
            <a href="{{ route('attendance.settings') }}" class="att-btn att-btn-outline att-btn-sm">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                Tənzimləmələr
            </a>
            <a href="{{ route('attendance.branches') }}" class="att-btn att-btn-outline att-btn-sm">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                Filiallar & QR-lar
            </a>
            <a href="{{ route('attendance.index') }}" class="att-btn att-btn-outline att-btn-sm">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                Davamiyyət Jurnalı
            </a>
        </div>
    </div>

    {{-- Süzgəc və Axtarış Paneli (Dəqiq Ölçülər və Bərabər Hizada Grid) --}}
    <div class="filter-panel-card">
        <div class="filter-panel-header">
            <div class="filter-panel-title">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                <span>Süzgəc və Axtarış Paneli</span>
            </div>
            @if($search || ($branchId && $branchId !== 'all') || ($status && $status !== 'active'))
                <span class="filter-active-pill">
                    <span class="filter-active-dot"></span>
                    Aktiv Süzgəc Tətbiq Edilib
                </span>
            @endif
        </div>

        <form method="GET" action="{{ route('attendance.workers') }}" class="filter-form-grid">
            {{-- 1. Axtarış (Ad və ya Telefon) --}}
            <div class="filter-field-col">
                <label class="filter-col-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Axtarış (Ad və ya Telefon)
                </label>
                <input type="text" name="search" class="filter-ctrl" placeholder="Məsələn: Elvin və ya 055..." value="{{ $search }}">
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
                        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 3. Status --}}
            <div class="filter-field-col">
                <label class="filter-col-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                    Status
                </label>
                <select name="status" class="filter-ctrl filter-select">
                    <option value="active" {{ ($status ?? 'active') == 'active' ? 'selected' : '' }}>Yalnız Aktivlər (Defolt)</option>
                    <option value="inactive" {{ ($status ?? '') == 'inactive' ? 'selected' : '' }}>Yalnız Deaktivlər</option>
                    <option value="all" {{ ($status ?? '') == 'all' ? 'selected' : '' }}>Bütün İşçilər (Hamısı)</option>
                </select>
            </div>

            {{-- 4. Əməliyyatlar (Axtar & Sıfırla - Vahid 42px Hündürlük) --}}
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
                    <a href="{{ route('attendance.workers') }}" class="filter-btn-reset" title="Axtarışı Sıfırla">
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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Qeydiyyatda Olan İşçilərin Siyahısı
                <span class="data-count-tag">{{ $workers->total() }} işçi</span>
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
                        <th>İşçinin Adı</th>
                        <th>Əlaqə Nömrəsi</th>
                        <th>Təhkim Olunduğu Filial</th>
                        <th>Status</th>
                        <th>4 Rəqəmli PIN Kod</th>
                        <th>Bağlanmış Cihaz</th>
                        <th style="width: 180px;">Əməliyyatlar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workers as $idx => $w)
                        <tr>
                            <td class="text-muted fw-bold" style="font-size: 12px;">#{{ $workers->firstItem() + $idx }}</td>

                            {{-- İşçi Adı & ID --}}
                            <td>
                                <div class="worker-cell">
                                    <div class="worker-avatar">
                                        {{ mb_substr($w->name ?: '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="worker-name">{{ $w->name }}</div>
                                        <div class="worker-id-sub">ID: #{{ $w->id }} (Portal ID: {{ $w->portal_worker_id }})</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Telefon --}}
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px; font-weight: 600; color: #334155; font-size: 13px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #64748b;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    {{ $w->phone ?: '—' }}
                                </div>
                            </td>

                            {{-- Filial --}}
                            <td>
                                <span class="branch-cell-pill">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                    {{ $w->branch?->name ?: 'Təyin edilməyib' }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="status-badge-{{ $w->is_active ? 'active' : 'inactive' }}" id="statusBadge_{{ $w->id }}">
                                    <span class="status-dot"></span>
                                    {{ $w->is_active ? 'Aktiv' : 'Deaktiv' }}
                                </span>
                            </td>

                            {{-- PIN Kod --}}
                            <td>
                                <span class="pin-code-badge" id="pinDisplay_{{ $w->id }}">
                                    {{ $w->pin_code ?: '----' }}
                                </span>
                            </td>

                            {{-- Cihaz Bağlaması --}}
                            <td>
                                @if($w->registered_device_uuid)
                                    <span class="device-badge-active" title="Cihaz UUID: {{ $w->registered_device_uuid }}">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                                        Cihaz Bağlıdır
                                    </span>
                                @else
                                    <span class="device-badge-none">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                                        Sərbəst (Cihaz yoxdur)
                                    </span>
                                @endif
                            </td>

                            {{-- Əməliyyatlar --}}
                            <td>
                                @if(auth()->user()->hasRole('Admin') || auth()->user()->can('attendance.manage-workers'))
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="worker-btn-action worker-btn-pin btn-change-pin"
                                        data-id="{{ $w->id }}"
                                        data-name="{{ $w->name }}"
                                        data-pin="{{ $w->pin_code }}"
                                        title="Fərdi 4 rəqəmli PIN kodu dəyiş">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        PIN Dəyiş
                                    </button>

                                    @if($w->registered_device_uuid)
                                        <button type="button" class="worker-btn-action worker-btn-reset-device btn-reset-device"
                                            data-id="{{ $w->id }}"
                                            data-name="{{ $w->name }}"
                                            title="İşçi yeni telefon alanda cihaz bağlamasını sıfırlayır">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                            <span>Sıfırla</span>
                                        </button>
                                    @endif
                                </div>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="text-center py-5 text-muted">
                                    <div class="mb-2">
                                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.8"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Axtarış üzrə heç bir işçi tapılmadı</h6>
                                    <p class="small text-muted mb-0">Ad və ya filial parametrini dəyişərək yenidən yoxlayın.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Səhifələmə --}}
        @if($workers->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #fafbfc;">
                <span class="small text-muted">
                    Göstərilir: <strong>{{ $workers->firstItem() }}</strong> – <strong>{{ $workers->lastItem() }}</strong> / Cəmi: <strong>{{ $workers->total() }}</strong> işçi
                </span>
                <div>
                    {{ $workers->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

{{-- PIN Dəyiş Modalı (Custom Overlay, 20px Radius) --}}
<div class="att-modal-backdrop" id="modalChangePin">
    <div class="att-modal-dialog">
        <div class="att-modal-header">
            <h6 class="att-modal-title" id="modalPinWorkerName">Fərdi PIN Kodu Təyin Et</h6>
            <button type="button" class="att-modal-close-btn" onclick="closeModal('modalChangePin')">&times;</button>
        </div>
        <form id="formUpdatePin">
            <div class="att-modal-body text-center">
                <input type="hidden" id="pinWorkerId">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 10px;">
                    4 Rəqəmli Yeni PIN Kod
                </label>
                <input type="text" maxlength="4" pattern="[0-9]{4}" class="form-control text-center font-monospace" id="newPinInput" required placeholder="0000" style="letter-spacing: 10px; font-size: 26px; font-weight: 800; padding: 12px 14px; width: 190px; margin: 0 auto; border: 2px solid #cbd5e1; border-radius: 14px;">
                <div style="font-size: 12px; color: #64748b; margin-top: 10px;">
                    Yalnız 4 rəqəm daxil edin (Məsələn: <strong>1234</strong>, <strong>7492</strong>)
                </div>
            </div>
            <div class="att-modal-footer">
                <button type="button" class="att-btn att-btn-outline" onclick="closeModal('modalChangePin')">Ləğv et</button>
                <button type="submit" class="att-btn att-btn-primary" id="btnSavePin">Təsdiqlə</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Modalın arxasına klik edəndə bağlanması
    document.querySelectorAll('.att-modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // ESC ilə bağlamaq
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.att-modal-backdrop').forEach(m => m.style.display = 'none');
            document.body.style.overflow = '';
        }
    });

    // PIN Dəyiş Açılışı
    document.querySelectorAll('.btn-change-pin').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('pinWorkerId').value = this.dataset.id;
            document.getElementById('modalPinWorkerName').textContent = this.dataset.name + ' — PIN Təyini';
            document.getElementById('newPinInput').value = this.dataset.pin || '';
            openModal('modalChangePin');
            setTimeout(() => document.getElementById('newPinInput').focus(), 150);
        });
    });

    // PIN Submit
    document.getElementById('formUpdatePin')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('pinWorkerId').value;
        const newPin = document.getElementById('newPinInput').value;
        const btn = document.getElementById('btnSavePin');

        if (newPin.length < 4) {
            alert('PIN kod 4 rəqəmli olmalıdır!');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Saxlanılır...';

        fetch(`/attendance/workers/${id}/pin`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ pin_code: newPin })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const el = document.getElementById('pinDisplay_' + id);
                if (el) el.textContent = data.pin_code;
                closeModal('modalChangePin');
                alert(data.message);
            } else {
                alert('Xəta baş verdi: ' + (data.message || ''));
            }
            btn.disabled = false;
            btn.textContent = 'Təsdiqlə';
        })
        .catch(err => {
            alert('Server xətası!');
            btn.disabled = false;
            btn.textContent = 'Təsdiqlə';
        });
    });

    // Cihaz sıfırlama
    document.querySelectorAll('.btn-reset-device').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            if (!confirm(`'${name}' üçün cihaz bağlamasını sıfırlamaq istəyirsiniz? İşçi növbəti dəfə istənilən telefondan qeydiyyat apara biləcək.`)) return;

            fetch(`/attendance/workers/${id}/reset-device`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message || 'Cihaz sıfırlandı!');
                location.reload();
            })
            .catch(e => alert('Xəta baş verdi!'));
        });
    });

    // Portal-dan Sinxron etmə
    document.getElementById('btnSyncWorkers')?.addEventListener('click', function() {
        if (!confirm('PortalWebsite-dan filiallar və işçilər sinxronizasiya edilsin?\n\nVebsaytda aktiv olan işçilər aktivləşəcək, arxivlənmiş/silinmiş işçilər isə avtomatik deaktiv ediləcək.')) return;
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
            alert('Sinxronizasiya xətası baş verdi!');
            btn.disabled = false;
            btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg> Sinxron et`;
        });
    });
</script>
@endpush
@endsection
