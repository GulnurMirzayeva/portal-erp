@extends('layouts.erp')

@section('title', 'Vahid İşçi Bazası & Kadrlar')
@section('page-title', 'VAHİD İŞÇİ BAZASI VƏ KADRLAR')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">HR / İşçilər</a></li>
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

    /* Success / Danger Alert */
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
    .att-alert-danger {
        background: #fef2f2;
        border: 1.5px solid #fecaca;
        border-radius: 16px;
        padding: 14px 20px;
        margin-bottom: 22px;
        color: #991b1b;
    }

    /* Stat Cards Grid */
    .stat-cards-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
        margin-bottom: 22px;
    }
    @media (max-width: 1200px) {
        .stat-cards-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .stat-cards-grid { grid-template-columns: 1fr; }
    }
    .stat-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
    }
    .stat-metric-title {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 4px;
    }
    .stat-metric-val {
        font-size: 24px;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
    }
    .stat-metric-subtitle {
        font-size: 11px;
        color: #94a3b8;
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
    .stat-icon-purple { background: #f5f3ff; color: #7c3aed; }

    /* Filter Panel */
    .filter-panel-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 18px 22px;
        margin-bottom: 22px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }
    .filter-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #e2e8f0;
    }
    .filter-panel-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-form-grid {
        display: grid;
        grid-template-columns: 2fr 1.3fr 1.3fr 1fr 1fr 1fr auto;
        gap: 10px;
        align-items: flex-end;
    }
    @media (max-width: 1300px) {
        .filter-form-grid { grid-template-columns: 1fr 1fr 1fr 1fr; }
    }
    @media (max-width: 768px) {
        .filter-form-grid { grid-template-columns: 1fr; }
    }
    .filter-field-col label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 5px;
    }
    .filter-ctrl {
        width: 100%;
        height: 38px;
        padding: 0 12px;
        font-size: 12.5px;
        font-weight: 600;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        outline: none;
        background: #ffffff;
        color: #0f172a;
        transition: all 0.15s ease;
    }
    .filter-ctrl:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.12);
    }
    .filter-select {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 14px 14px;
        padding-right: 30px;
        -webkit-appearance: none;
        appearance: none;
    }
    .filter-btn-group {
        display: flex;
        gap: 6px;
    }
    .filter-btn-submit {
        background: #107c41;
        color: #ffffff;
        border: none;
        height: 38px;
        padding: 0 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .filter-btn-submit:hover {
        background: #0b5e30;
    }
    .filter-btn-reset {
        background: #f1f5f9;
        color: #475569;
        border: 1.5px solid #e2e8f0;
        height: 38px;
        padding: 0 12px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 5px;
        text-decoration: none !important;
    }
    .filter-btn-reset:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Data Card & Table */
    .data-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }
    .data-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
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
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11.5px;
        font-weight: 700;
    }

    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }
    .table-modern th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 13px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .table-modern td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        vertical-align: middle;
        background: #ffffff;
    }
    .table-modern tbody tr:hover td {
        background: #f8fafc;
    }

    /* Worker Cell */
    .emp-avatar-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .emp-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        flex-shrink: 0;
    }
    .avatar-male { background: #eff6ff; color: #2563eb; border: 1.5px solid #bfdbfe; }
    .avatar-female { background: #fdf2f8; color: #db2777; border: 1.5px solid #fbcfe8; }

    .emp-name {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .emp-sub {
        font-size: 11.5px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Badges & Chips */
    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        color: #ffffff;
        margin-right: 4px;
        margin-bottom: 3px;
    }
    .role-badge-secondary {
        background: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1;
    }
    .branch-pill-primary {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        padding: 3px 10px;
        border-radius: 10px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .branch-pill-managed {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
        padding: 2px 8px;
        border-radius: 8px;
        font-size: 10.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin-top: 3px;
    }
    .tabel-preview-tag {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        color: #0f172a;
        padding: 3px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        display: inline-block;
    }

    /* Status & Intern Badges */
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
        cursor: pointer;
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
        cursor: pointer;
    }
    .status-badge-active .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #10b981; }
    .status-badge-inactive .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #ef4444; }

    .intern-badge-yes {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .intern-badge-no {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Action buttons */
    .worker-btn-action {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid transparent;
        transition: all 0.15s ease;
        text-decoration: none !important;
        cursor: pointer;
    }
    .emp-btn-edit { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; width: auto; padding: 0 10px; gap: 4px; font-size: 12px; font-weight: 700; }
    .emp-btn-edit:hover { background: #2563eb; color: #ffffff !important; }
    .emp-btn-del { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
    .emp-btn-del:hover { background: #dc2626; color: #ffffff; }

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
    }
    .att-btn-primary { background: #107c41; color: #ffffff !important; border-color: #0d6334; }
    .att-btn-primary:hover { background: #0b5e30; }
    .att-btn-outline { background: #ffffff; border-color: #cbd5e1; color: #334155 !important; }
    .att-btn-outline:hover { background: #f8fafc; border-color: #94a3b8; color: #0f172a !important; }
    .att-btn-sm { font-size: 12.5px; padding: 7px 14px; border-radius: 10px; }
</style>
@endpush

@section('content')
<div class="att-wrapper">

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="att-alert-success">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span><strong>Uğurlu!</strong> {{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" onclick="this.closest('.att-alert-success').remove()"></button>
        </div>
    @endif

    {{-- Danger Alert --}}
    @if($errors->any())
        <div class="att-alert-danger">
            <strong>Xəta:</strong> {{ $errors->first() }}
        </div>
    @endif

    {{-- Top Action & Info Bar --}}
    <div class="att-top-bar">
        <div>
            <h5 class="att-top-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Vahid İşçi Bazası & Kadr İdarəetməsi
            </h5>
            <div class="att-top-desc">
                Bütün filial və ofis heyəti, çoxlu vəzifələr, təhkimatlar və stajorluq statusu.
                <span class="att-pill-active">
                    <span class="att-pulse-dot"></span>
                    ERP Master Kadrlar
                </span>
                <span class="att-pill-info">
                    QR Davamiyyət ilə tam inteqrasiya
                </span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.create'))
                <form action="{{ route('employees.import') }}" method="POST" class="m-0" onsubmit="return confirm('Mövcud sayt və QR işçilərini ERP-yə idxal etmək istəyirsiniz?');">
                    @csrf
                    <button type="submit" class="att-btn att-btn-outline att-btn-sm" title="Saytdakı və QR cədvəlindəki işçiləri ERP-yə çək">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        İşçiləri İdxal Et
                    </button>
                </form>
                <a href="{{ route('employees.create') }}" class="att-btn att-btn-primary att-btn-sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Yeni İşçi Əlavə Et
                </a>
            @endif
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-cards-grid">
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Cəmi İşçi</div>
                <div class="stat-metric-val">{{ $stats['total'] }}</div>
                <div class="stat-metric-subtitle">ERP kadr bazası</div>
            </div>
            <div class="stat-icon-wrap stat-icon-slate">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
        </div>
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Aktiv İşçilər</div>
                <div class="stat-metric-val text-primary">{{ $stats['active'] }}</div>
                <div class="stat-metric-subtitle">Hazırda işləyənlər</div>
            </div>
            <div class="stat-icon-wrap stat-icon-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
            </div>
        </div>
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Daimi Heyət</div>
                <div class="stat-metric-val" style="color: #107c41;">{{ $stats['permanent'] }}</div>
                <div class="stat-metric-subtitle">Tam ştat işçilər</div>
            </div>
            <div class="stat-icon-wrap stat-icon-emerald">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
        </div>
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Stajorlar</div>
                <div class="stat-metric-val" style="color: #d97706;">{{ $stats['interns'] }}</div>
                <div class="stat-metric-subtitle">Staj keçənlər (0 xal)</div>
            </div>
            <div class="stat-icon-wrap stat-icon-amber">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
            </div>
        </div>
        <div class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Deaktiv / Arxiv</div>
                <div class="stat-metric-val text-danger">{{ $stats['inactive'] }}</div>
                <div class="stat-metric-subtitle">İşdən çıxmış / qeyri-aktiv</div>
            </div>
            <div class="stat-icon-wrap stat-icon-purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            </div>
        </div>
    </div>

    {{-- Süzgəc və Axtarış Paneli --}}
    <div class="filter-panel-card">
        <div class="filter-panel-header">
            <div class="filter-panel-title">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                <span>İşçi Süzgəci və Axtarış</span>
            </div>
        </div>

        <form method="GET" action="{{ route('employees.index') }}" class="filter-form-grid">
            <div class="filter-field-col">
                <label>Axtarış (Ad, Telefon, PIN)</label>
                <input type="text" name="search" class="filter-ctrl" placeholder="Məs: Əli, 050, 1234..." value="{{ $filters['search'] ?? '' }}">
            </div>

            <div class="filter-field-col">
                <label>Filial</label>
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
                <label>Vəzifə</label>
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
                <label>Stajorluq</label>
                <select name="is_intern" class="filter-ctrl filter-select">
                    <option value="all" {{ ($filters['is_intern'] ?? '') === 'all' || !isset($filters['is_intern']) ? 'selected' : '' }}>Hamısı</option>
                    <option value="0" {{ ($filters['is_intern'] ?? '') === '0' ? 'selected' : '' }}>Yalnız Daimi</option>
                    <option value="1" {{ ($filters['is_intern'] ?? '') === '1' ? 'selected' : '' }}>Yalnız Stajor</option>
                </select>
            </div>

            <div class="filter-field-col">
                <label>Status</label>
                <select name="status" class="filter-ctrl filter-select">
                    <option value="all" {{ ($filters['status'] ?? '') === 'all' ? 'selected' : '' }}>Hamısı</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Yalnız Aktiv</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Deaktiv</option>
                    <option value="dismissed" {{ ($filters['status'] ?? '') === 'dismissed' ? 'selected' : '' }}>İşdən çıxıb</option>
                </select>
            </div>

            <div class="filter-field-col">
                <label>Cinsiyyət</label>
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

    {{-- İşçilər Cədvəli --}}
    <div class="data-card-modern">
        <div class="data-card-header">
            <h6 class="data-table-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                İşçilərin Kadr Siyahısı
                <span class="data-count-tag">{{ $employees->total() }} işçi</span>
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>İşçi (Ad / Telefon / PIN)</th>
                        <th>Vəzifələr</th>
                        <th>Tabel Etiketi</th>
                        <th>Filiallar</th>
                        <th>Baxdığı Oyunlar</th>
                        <th>Stajorluq</th>
                        <th>Status</th>
                        <th style="width: 130px;">Əməliyyatlar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr>
                            <td class="text-muted fw-bold font-monospace" style="font-size: 11.5px;">
                                #{{ $emp->id }}
                            </td>
                            <td>
                                <div class="emp-avatar-cell">
                                    <div class="emp-avatar {{ $emp->gender === 'female' ? 'avatar-female' : 'avatar-male' }}" title="{{ $emp->gender_label }}">
                                        {{ mb_substr($emp->first_name ?: $emp->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="emp-name">
                                            {{ $emp->full_name }}
                                            @if($emp->gender === 'female')
                                                <span class="text-danger fw-bold" style="font-size: 11px;">(Qadın)</span>
                                            @endif
                                        </div>
                                        <div class="emp-sub">
                                            @if($emp->phone)
                                                <span>{{ $emp->phone }}</span> &bull;
                                            @endif
                                            <span>PIN: <code class="fw-bold">{{ $emp->pin_code ?: '1234' }}</code></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($emp->workerTypes->count() > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($emp->workerTypes as $wt)
                                            <span class="role-badge {{ $wt->pivot->is_primary ? '' : 'role-badge-secondary' }}" style="{{ $wt->pivot->is_primary ? 'background: ' . ($wt->badge_color ?: '#2563eb') . ';' : '' }}" title="{{ $wt->pivot->is_primary ? 'Əsas vəzifə' : 'Əlavə vəzifə' }}">
                                                {{ $wt->name }}
                                                @if($wt->pivot->is_primary)
                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" class="ms-1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">Vəzifə təyin edilməyib</span>
                                @endif
                            </td>
                            <td>
                                <span class="tabel-preview-tag" id="tabelLabel-{{ $emp->id }}">
                                    {{ $emp->tabel_name }} / {{ $emp->tabel_role_label }}
                                </span>
                            </td>
                            <td>
                                @if($emp->primaryBranch())
                                    <span class="branch-pill-primary">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                                        {{ $emp->primaryBranch()->name }}
                                    </span>
                                @else
                                    <span class="text-muted small">Mərkəzi Ofis</span>
                                @endif

                                @if($emp->managedBranches->count() > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($emp->managedBranches as $mb)
                                            <span class="branch-pill-managed" title="İdarə etdiyi filial">
                                                + {{ $mb->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($emp->games->count() > 0)
                                    <div class="small fw-semibold text-secondary">
                                        {{ $emp->games->pluck('game_name')->implode(', ') }}
                                    </div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.edit'))
                                    <button type="button"
                                            class="{{ $emp->is_intern ? 'intern-badge-yes' : 'intern-badge-no' }} btn-toggle-intern"
                                            data-id="{{ $emp->id }}">
                                        <span class="intern-label">{{ $emp->is_intern ? 'Stajor' : 'Daimi' }}</span>
                                    </button>
                                @else
                                    <span class="{{ $emp->is_intern ? 'intern-badge-yes' : 'intern-badge-no' }}" style="cursor: default;">
                                        {{ $emp->is_intern ? 'Stajor' : 'Daimi' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.edit'))
                                    <button type="button"
                                            class="{{ $emp->status === 'active' ? 'status-badge-active' : 'status-badge-inactive' }} btn-toggle-emp-status"
                                            data-id="{{ $emp->id }}">
                                        <span class="status-dot"></span>
                                        <span class="status-label">{{ $emp->status_label }}</span>
                                    </button>
                                @else
                                    <span class="{{ $emp->status === 'active' ? 'status-badge-active' : 'status-badge-inactive' }}" style="cursor: default;">
                                        <span class="status-dot"></span>
                                        <span>{{ $emp->status_label }}</span>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.edit'))
                                        <a href="{{ route('employees.edit', $emp->id) }}" class="worker-btn-action emp-btn-edit">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            <span>Redaktə</span>
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.delete'))
                                        <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" class="m-0" onsubmit="return confirm('«{{ $emp->full_name }}» adlı işçini arxivə göndərmək istədiyinizdən əminsiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="worker-btn-action emp-btn-del" title="Arxivə göndər">
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
                                    <h6 class="fw-bold text-dark mb-1">Heç bir işçi tapılmadı</h6>
                                    <p class="small mb-3">Mövcud işçiləri saytdan idxal edin və ya yeni işçi qeydiyyatı aparın.</p>
                                    @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.create'))
                                        <form action="{{ route('employees.import') }}" method="POST" class="d-inline-block">
                                            @csrf
                                            <button type="submit" class="att-btn att-btn-primary att-btn-sm">
                                                İşçiləri Saytdan İdxal Et
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $employees->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-toggle-emp-status').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        const self = this;
        const label = this.querySelector('.status-label');

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
            }
        });
    });
});

document.querySelectorAll('.btn-toggle-intern').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        const self = this;
        const label = this.querySelector('.intern-label');
        const tabelTag = document.getElementById(`tabelLabel-${id}`);

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
                if (tabelTag && data.tabel_role_label) {
                    const parts = tabelTag.textContent.split('/');
                    tabelTag.textContent = `${parts[0].trim()} / ${data.tabel_role_label}`;
                }
            } else if (data.message) {
                alert(data.message);
            }
        });
    });
});
</script>
@endpush
