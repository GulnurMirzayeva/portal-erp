@extends('layouts.erp')

@section('title', 'CRM Statistika — Portal ERP')
@section('page-title', 'CRM STATİSTİKA')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">CRM</a></li>
    <li class="breadcrumb-item active">Statistika</li>
@endsection

@push('css')
<style>
    /* ==============================================================
       CRM STATISTICS MODULE DESIGN SYSTEM
       ============================================================== */
    :root {
        --crm-radius: 16px;
        --crm-card-bg: #ffffff;
        --crm-border: #e8ecf1;
        --crm-shadow: 0 4px 18px rgba(18, 38, 63, 0.04);
        --crm-hover-shadow: 0 8px 24px rgba(16, 124, 65, 0.08);
        --crm-primary: #107c41;
        --crm-primary-dark: #0b5e30;
        --crm-accent: #2563eb;
    }

    .crm-wrapper {
        padding-bottom: 30px;
    }

    /* ---------------- Control Toolbar ---------------- */
    .crm-toolbar {
        background: #ffffff;
        border: 1px solid var(--crm-border);
        border-radius: var(--crm-radius);
        padding: 14px 22px;
        box-shadow: var(--crm-shadow);
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .crm-toolbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .crm-toolbar-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 15px;
        color: #1e293b;
        margin: 0;
    }

    .crm-toolbar-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #e8f5e9;
        color: var(--crm-primary);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .crm-toolbar-form {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .form-select-crm {
        border-radius: 11px;
        border: 1px solid #dcdfe4;
        background-color: #fafbfc;
        padding: 7px 32px 7px 14px;
        font-size: 13.5px;
        font-weight: 500;
        color: #334155;
        transition: all 0.2s ease;
    }
    .form-select-crm:focus {
        border-color: var(--crm-primary);
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.12);
        background-color: #ffffff;
        outline: none;
    }

    .btn-crm-primary {
        background-color: var(--crm-primary);
        color: #ffffff;
        border: none;
        border-radius: 11px;
        padding: 7px 18px;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-crm-primary:hover {
        background-color: var(--crm-primary-dark);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.22);
    }

    /* ---------------- Notice Banner ---------------- */
    .crm-notice-banner {
        background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        border: 1px solid #bbf7d0;
        border-radius: var(--crm-radius);
        padding: 16px 22px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: 0 2px 10px rgba(16, 124, 65, 0.04);
    }

    .crm-notice-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #107c41;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .crm-notice-content h6 {
        font-size: 14.5px;
        font-weight: 700;
        color: #14532d;
        margin: 0 0 4px 0;
    }

    .crm-notice-content p {
        font-size: 13px;
        color: #475569;
        margin: 0;
        line-height: 1.5;
    }

    /* ---------------- Table Cards ---------------- */
    .crm-card {
        background: var(--crm-card-bg);
        border: 1px solid var(--crm-border);
        border-radius: var(--crm-radius);
        box-shadow: var(--crm-shadow);
        transition: all 0.25s ease;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .crm-card:hover {
        box-shadow: var(--crm-hover-shadow);
        border-color: #cbd5e1;
    }

    .crm-card-header {
        padding: 16px 22px;
        background: #ffffff;
        border-bottom: 1px solid var(--crm-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .crm-card-title-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .crm-card-badge {
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        background: #e8f5e9;
        color: var(--crm-primary);
        border: 1px solid #c8e6c9;
        letter-spacing: 0.3px;
    }

    .crm-card-badge.badge-blue {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .crm-card-badge.badge-purple {
        background: #faf5ff;
        color: #7e22ce;
        border-color: #e9d5ff;
    }

    .crm-card-badge.badge-amber {
        background: #fffbeb;
        color: #b45309;
        border-color: #fde68a;
    }

    .crm-card-badge.badge-teal {
        background: #f0fdfa;
        color: #0f766e;
        border-color: #99f6e4;
    }

    .crm-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .crm-card-subtitle {
        font-size: 12px;
        color: #64748b;
        margin: 0;
    }

    .crm-card-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-card-action {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-card-action:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    /* ---------------- Tables Styling ---------------- */
    .crm-table-container {
        padding: 0;
        overflow-x: auto;
    }

    .crm-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    .crm-table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 18px;
        border-bottom: 1px solid var(--crm-border);
        white-space: nowrap;
    }

    .crm-table tbody td {
        padding: 14px 18px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        white-space: nowrap;
    }

    .crm-table tbody tr:hover td {
        background: #fbfcfd;
    }

    .crm-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ---------------- Empty State Placeholder ---------------- */
    .crm-placeholder-box {
        padding: 38px 20px;
        text-align: center;
        background: #fafbfc;
        border-radius: 0 0 var(--crm-radius) var(--crm-radius);
    }

    .crm-placeholder-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }

    .crm-placeholder-text {
        font-size: 13.5px;
        font-weight: 600;
        color: #64748b;
        margin: 0 0 4px 0;
    }

    .crm-placeholder-sub {
        font-size: 12px;
        color: #94a3b8;
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="crm-wrapper">

    {{-- Top Filter & Control Toolbar --}}
    <div class="crm-toolbar">
        <div class="crm-toolbar-left">
            <div class="crm-toolbar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div>
                <h5 class="crm-toolbar-title">CRM Analitika və Statistika Paneli</h5>
                <span class="text-muted font-size-12">{{ $selectedBranchName }} &bull; {{ $filterMonth }}</span>
            </div>
        </div>

        <form method="GET" action="{{ route('crm.index') }}" class="crm-toolbar-form">
            {{-- Branch Selector --}}
            <select name="branch_id" class="form-select-crm" onchange="this.form.submit()">
                <option value="all" {{ ($filterBranch === 'all' || empty($filterBranch)) ? 'selected' : '' }}>Bütün Filiallar</option>
                @foreach($branches as $b)
                    <option value="{{ $b['id'] }}" {{ ((string)$filterBranch === (string)$b['id']) ? 'selected' : '' }}>
                        {{ $b['name'] }}
                    </option>
                @endforeach
            </select>

            {{-- Month Selector --}}
            <select name="month" class="form-select-crm" onchange="this.form.submit()">
                @foreach($availableMonths as $m)
                    <option value="{{ $m['key'] }}" {{ ($filterMonth === $m['key']) ? 'selected' : '' }}>
                        {{ $m['name'] }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn-crm-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                Yenilə
            </button>
        </form>
    </div>

    {{-- Ready Information Banner --}}
    <div class="crm-notice-banner">
        <div class="d-flex align-items-center gap-3">
            <div class="crm-notice-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            </div>
            <div class="crm-notice-content">
                <h6>5 Statistika Cədvəli Strukturu Hazırdır</h6>
                <p>Sidebar bölməsi, route, kontroller və cədvəl strukturları quruldu. Hansı dataların necə çəkiləcəyini və hər cədvəldə hansı sütunların göstəriləcəyini bildirdikdə, birbaşa doldurulacaq.</p>
            </div>
        </div>
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill font-size-12 fw-semibold">
            Status: Hazırdır
        </span>
    </div>

    {{-- ============================================================== --}}
    {{-- TABLE 1: Statistika Cədvəli 1 --}}
    {{-- ============================================================== --}}
    <div class="crm-card" id="crm-table-1">
        <div class="crm-card-header">
            <div class="crm-card-title-group">
                <span class="crm-card-badge">CƏDVƏL 1</span>
                <div>
                    <h6 class="crm-card-title">Statistika Cədvəli 1</h6>
                    <p class="crm-card-subtitle">Birinci statistika bloku və göstəriciləri</p>
                </div>
            </div>
            <div class="crm-card-actions">
                <button type="button" class="btn-card-action">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    İxrac
                </button>
            </div>
        </div>
        <div class="crm-table-container">
            <div class="crm-placeholder-box">
                <div class="crm-placeholder-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                </div>
                <div class="crm-placeholder-text">Cədvəl 1 üçün məlumat strukturu gözlənilir</div>
                <div class="crm-placeholder-sub">Sütunlar və data mənbəyi təyin olunan kimi burada əks olunacaq</div>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- TABLE 2: Statistika Cədvəli 2 --}}
    {{-- ============================================================== --}}
    <div class="crm-card" id="crm-table-2">
        <div class="crm-card-header">
            <div class="crm-card-title-group">
                <span class="crm-card-badge badge-blue">CƏDVƏL 2</span>
                <div>
                    <h6 class="crm-card-title">Statistika Cədvəli 2</h6>
                    <p class="crm-card-subtitle">İkinci statistika bloku və göstəriciləri</p>
                </div>
            </div>
            <div class="crm-card-actions">
                <button type="button" class="btn-card-action">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    İxrac
                </button>
            </div>
        </div>
        <div class="crm-table-container">
            <div class="crm-placeholder-box">
                <div class="crm-placeholder-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                </div>
                <div class="crm-placeholder-text">Cədvəl 2 üçün məlumat strukturu gözlənilir</div>
                <div class="crm-placeholder-sub">Sütunlar və data mənbəyi təyin olunan kimi burada əks olunacaq</div>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- TABLE 3: Statistika Cədvəli 3 --}}
    {{-- ============================================================== --}}
    <div class="crm-card" id="crm-table-3">
        <div class="crm-card-header">
            <div class="crm-card-title-group">
                <span class="crm-card-badge badge-purple">CƏDVƏL 3</span>
                <div>
                    <h6 class="crm-card-title">Statistika Cədvəli 3</h6>
                    <p class="crm-card-subtitle">Üçüncü statistika bloku və göstəriciləri</p>
                </div>
            </div>
            <div class="crm-card-actions">
                <button type="button" class="btn-card-action">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    İxrac
                </button>
            </div>
        </div>
        <div class="crm-table-container">
            <div class="crm-placeholder-box">
                <div class="crm-placeholder-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                </div>
                <div class="crm-placeholder-text">Cədvəl 3 üçün məlumat strukturu gözlənilir</div>
                <div class="crm-placeholder-sub">Sütunlar və data mənbəyi təyin olunan kimi burada əks olunacaq</div>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- TABLE 4: Statistika Cədvəli 4 --}}
    {{-- ============================================================== --}}
    <div class="crm-card" id="crm-table-4">
        <div class="crm-card-header">
            <div class="crm-card-title-group">
                <span class="crm-card-badge badge-amber">CƏDVƏL 4</span>
                <div>
                    <h6 class="crm-card-title">Statistika Cədvəli 4</h6>
                    <p class="crm-card-subtitle">Dördüncü statistika bloku və göstəriciləri</p>
                </div>
            </div>
            <div class="crm-card-actions">
                <button type="button" class="btn-card-action">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    İxrac
                </button>
            </div>
        </div>
        <div class="crm-table-container">
            <div class="crm-placeholder-box">
                <div class="crm-placeholder-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                </div>
                <div class="crm-placeholder-text">Cədvəl 4 üçün məlumat strukturu gözlənilir</div>
                <div class="crm-placeholder-sub">Sütunlar və data mənbəyi təyin olunan kimi burada əks olunacaq</div>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- TABLE 5: Statistika Cədvəli 5 --}}
    {{-- ============================================================== --}}
    <div class="crm-card" id="crm-table-5">
        <div class="crm-card-header">
            <div class="crm-card-title-group">
                <span class="crm-card-badge badge-teal">CƏDVƏL 5</span>
                <div>
                    <h6 class="crm-card-title">Statistika Cədvəli 5</h6>
                    <p class="crm-card-subtitle">Beşinci statistika bloku və göstəriciləri</p>
                </div>
            </div>
            <div class="crm-card-actions">
                <button type="button" class="btn-card-action">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    İxrac
                </button>
            </div>
        </div>
        <div class="crm-table-container">
            <div class="crm-placeholder-box">
                <div class="crm-placeholder-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                </div>
                <div class="crm-placeholder-text">Cədvəl 5 üçün məlumat strukturu gözlənilir</div>
                <div class="crm-placeholder-sub">Sütunlar və data mənbəyi təyin olunan kimi burada əks olunacaq</div>
            </div>
        </div>
    </div>

</div>
@endsection
