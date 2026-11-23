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
        border-radius: 20px;
        padding: 22px 26px;
        margin-bottom: 22px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }
    .att-top-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .att-top-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        color: #059669;
        border: 1.5px solid #a7f3d0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(16, 124, 65, 0.08);
    }
    .att-top-title {
        font-size: 19px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.3px;
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
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.35);
        animation: attPulse 2s infinite ease-in-out;
    }
    @keyframes attPulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.3); opacity: 0.6; }
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
        gap: 6px;
    }

    /* Action Buttons in Top Bar */
    .att-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        height: 42px;
        padding: 0 20px;
        border-radius: 12px;
        cursor: pointer;
        border: 1.5px solid transparent;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
        box-sizing: border-box;
        white-space: nowrap;
    }
    .att-btn-primary {
        background: linear-gradient(135deg, #107c41 0%, #0d6334 100%);
        color: #ffffff !important;
        border-color: #0d6334;
        box-shadow: 0 4px 14px rgba(16, 124, 65, 0.25);
    }
    .att-btn-primary:hover {
        background: linear-gradient(135deg, #0d6334 0%, #084924 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(16, 124, 65, 0.35);
    }
    .att-btn-outline {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #334155 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
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
        gap: 16px;
        margin-bottom: 22px;
    }
    @media (max-width: 1280px) {
        .stat-cards-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .stat-cards-grid { grid-template-columns: 1fr; }
    }
    .stat-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 20px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
        position: relative;
        overflow: hidden;
    }
    .stat-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
    }
    .stat-metric-title {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .stat-metric-val {
        font-size: 28px;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }
    .stat-metric-subtitle {
        font-size: 11.5px;
        color: #94a3b8;
        font-weight: 600;
        margin-top: 4px;
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
    .stat-icon-slate { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .stat-icon-blue { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .stat-icon-emerald { background: #ecfdf5; color: #107c41; border: 1px solid #a7f3d0; }
    .stat-icon-amber { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .stat-icon-purple { background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }

    /* Filter Panel Card */
    .filter-panel-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 20px 24px;
        margin-bottom: 22px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
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

    /* Filter Form Grid - 42px aligned */
    .filter-form-grid {
        display: grid;
        grid-template-columns: 2fr 1.3fr 1.3fr 1fr 1fr 1fr auto;
        gap: 12px;
        align-items: flex-end;
    }
    @media (max-width: 1380px) {
        .filter-form-grid { grid-template-columns: 1.5fr 1fr 1fr 1fr; }
    }
    @media (max-width: 880px) {
        .filter-form-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 580px) {
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
        margin-bottom: 7px;
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
        font-size: 13px;
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
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.14);
    }
    .filter-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px 16px;
        padding-right: 36px;
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .filter-btn-group {
        display: flex;
        align-items: center;
        gap: 8px;
        height: 42px;
    }
    .filter-btn-submit {
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
        padding: 0 16px;
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

    /* Data Card Modern */
    .data-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }
    .data-card-header {
        padding: 18px 24px;
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
        gap: 10px;
    }
    .data-count-tag {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
    }

    /* Responsive Table Wrap */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        position: relative;
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
    @media (max-width: 1024px) {
        .mobile-scroll-hint { display: flex; }
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
        padding: 14px 18px;
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
        gap: 12px;
    }
    .worker-avatar {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        cursor: pointer;
        transition: transform 0.15s ease;
    }
    .worker-avatar:hover {
        transform: scale(1.08);
    }
    .avatar-male {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #1d4ed8;
        border: 1.5px solid #bfdbfe;
    }
    .avatar-female {
        background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
        color: #be185d;
        border: 1.5px solid #fbcfe8;
    }
    .worker-name {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .worker-sub {
        font-size: 11.5px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 3px;
    }
    .pin-badge {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, monospace;
        background: #f1f5f9;
        color: #0f172a;
        padding: 1px 6px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 1px;
    }

    /* Role Badges */
    .role-badge-primary {
        background: #ecfdf5;
        color: #065f46;
        border: 1.5px solid #a7f3d0;
        padding: 4px 10px;
        border-radius: 9px;
        font-size: 11.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 1px 3px rgba(16, 124, 65, 0.08);
    }
    .role-badge-secondary {
        background: #f8fafc;
        color: #475569;
        border: 1.5px solid #e2e8f0;
        padding: 3px 9px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Tabel Etiketi */
    .tabel-tag {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        color: #0f172a;
        padding: 4px 11px;
        border-radius: 10px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .tabel-tag .tabel-name-part {
        color: #0f172a;
    }
    .tabel-tag .tabel-divider {
        color: #94a3b8;
    }
    .tabel-tag .tabel-code-part {
        color: #107c41;
        font-weight: 800;
    }

    /* Filial Pill */
    .branch-pill-main {
        background: #ecfdf5;
        color: #047857;
        border: 1.5px solid #a7f3d0;
        border-radius: 10px;
        padding: 4px 11px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .branch-pill-managed {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
        border-radius: 8px;
        padding: 2px 7px;
        font-size: 10.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Game badges */
    .game-badge {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
        border-radius: 8px;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Status & Intern Badges with interactive state */
    .status-badge-active {
        background: #ecfdf5;
        color: #059669;
        border: 1.5px solid #a7f3d0;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.18s ease;
    }
    .status-badge-active:hover {
        background: #d1fae5;
        transform: translateY(-1px);
    }
    .status-badge-inactive {
        background: #fef2f2;
        color: #dc2626;
        border: 1.5px solid #fecaca;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.18s ease;
    }
    .status-badge-inactive:hover {
        background: #fee2e2;
        transform: translateY(-1px);
    }
    .status-dot-active {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
    }
    .status-dot-inactive {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #ef4444;
    }

    .intern-badge-yes {
        background: #fffbeb;
        color: #b45309;
        border: 1.5px solid #fde68a;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.18s ease;
    }
    .intern-badge-yes:hover {
        background: #fef3c7;
        transform: translateY(-1px);
    }
    .intern-badge-no {
        background: #f8fafc;
        color: #334155;
        border: 1.5px solid #e2e8f0;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.18s ease;
    }
    .intern-badge-no:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }

    /* Action Buttons in Table */
    .action-btn-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .emp-btn-action {
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
    .emp-btn-edit {
        background: #eff6ff;
        color: #2563eb !important;
        border: 1.5px solid #bfdbfe;
    }
    .emp-btn-edit:hover {
        background: #2563eb;
        color: #ffffff !important;
        border-color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    }
    .emp-btn-idcard {
        background: #f8fafc;
        color: #475569 !important;
        border: 1.5px solid #e2e8f0;
        padding: 0 9px;
    }
    .emp-btn-idcard:hover {
        background: #ffffff;
        color: #0f172a !important;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }
    .emp-btn-del {
        background: #fef2f2;
        color: #dc2626 !important;
        border: 1.5px solid #fecaca;
        padding: 0 9px;
    }
    .emp-btn-del:hover {
        background: #dc2626;
        color: #ffffff !important;
        border-color: #b91c1c;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
    }

    /* ID Card Preview Modal */
    .id-card-mockup {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    }
    .id-card-header {
        background: linear-gradient(135deg, #107c41 0%, #0d6334 100%);
        color: #ffffff;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .id-card-body {
        padding: 24px;
    }

    /* Floating Toast */
    .att-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #0f172a;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 9999;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .att-toast.show {
        transform: translateY(0);
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="att-wrapper">

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="att-alert-success">
            <div class="d-flex align-items-center gap-3">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span><strong>Uğurlu!</strong> {{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" onclick="this.closest('.att-alert-success').remove()"></button>
        </div>
    @endif

    {{-- Danger Alert --}}
    @if(isset($errors) && $errors->any())
        <div class="att-alert-danger">
            <div class="d-flex align-items-center gap-2 mb-1">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <strong>Xəta baş verdi:</strong>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Top Action & Info Bar --}}
    <div class="att-top-bar">
        <div class="att-top-left">
            <div class="att-top-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div>
                <h5 class="att-top-title">
                    Vahid İşçi Bazası & Kadr İdarəetməsi
                    <span class="badge bg-light text-dark border px-2 py-1 fs-6" style="border-radius: 8px;">{{ $employees->total() }} işçi</span>
                </h5>
                <div class="att-top-desc">
                    Bütün filial və ofis heyəti, çoxlu vəzifələr, filial təhkimatları və stajorluq dərəcələri.
                    <span class="att-pill-active">
                        <span class="att-pulse-dot"></span>
                        ERP Master Kadrlar
                    </span>
                    <span class="att-pill-info">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        QR Davamiyyət ilə tam inteqrasiya
                    </span>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.create'))
                <form action="{{ route('employees.import') }}" method="POST" class="m-0" onsubmit="return confirm('Mövcud sayt və QR cədvəlindəki işçiləri ERP-yə yenidən sinxronizasiya etmək istəyirsiniz?');">
                    @csrf
                    <button type="submit" class="att-btn att-btn-outline" title="Saytdakı və QR cədvəlindəki bütün işçiləri ERP-yə sinxron et">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        İşçiləri İdxal Et
                    </button>
                </form>
                <a href="{{ route('employees.create') }}" class="att-btn att-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
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
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
        </a>

        <a href="{{ route('employees.index', array_merge(request()->except('page'), ['status' => 'active'])) }}" class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Aktiv İşçilər</div>
                <div class="stat-metric-val" style="color: #2563eb;">{{ $stats['active'] }}</div>
                <div class="stat-metric-subtitle">Hazırda işləyənlər</div>
            </div>
            <div class="stat-icon-wrap stat-icon-blue">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
            </div>
        </a>

        <a href="{{ route('employees.index', array_merge(request()->except('page'), ['status' => 'active', 'is_intern' => '0'])) }}" class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Daimi Heyət</div>
                <div class="stat-metric-val" style="color: #107c41;">{{ $stats['permanent'] }}</div>
                <div class="stat-metric-subtitle">Tam ştat işçilər</div>
            </div>
            <div class="stat-icon-wrap stat-icon-emerald">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
        </a>

        <a href="{{ route('employees.index', array_merge(request()->except('page'), ['is_intern' => '1'])) }}" class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Stajorlar</div>
                <div class="stat-metric-val" style="color: #d97706;">{{ $stats['interns'] }}</div>
                <div class="stat-metric-subtitle">Staj keçənlər (0 xal)</div>
            </div>
            <div class="stat-icon-wrap stat-icon-amber">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
            </div>
        </a>

        <a href="{{ route('employees.index', array_merge(request()->except('page'), ['status' => 'inactive'])) }}" class="stat-card-modern">
            <div>
                <div class="stat-metric-title">Deaktiv / Arxiv</div>
                <div class="stat-metric-val" style="color: #db2777;">{{ $stats['inactive'] }}</div>
                <div class="stat-metric-subtitle">İşdən çıxmış və ya arxiv</div>
            </div>
            <div class="stat-icon-wrap stat-icon-purple">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
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
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Axtarış (Ad, Telefon, PIN)
                </span>
                <input type="text" name="search" class="filter-ctrl" placeholder="Məs: Əli, 050, 2355..." value="{{ $filters['search'] ?? '' }}">
            </div>

            <div class="filter-field-col">
                <span class="filter-col-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                    Filial
                </span>
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
                <span class="filter-col-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    Vəzifə
                </span>
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
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span>Axtar</span>
                </button>
                <a href="{{ route('employees.index') }}" class="filter-btn-reset" title="Bütün filtrləri sıfırla">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                    <span>Sıfırla</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Data Card & Table --}}
    <div class="data-card-modern">
        <div class="data-card-header">
            <h6 class="data-table-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                İşçilərin Kadr Reyestri
                <span class="data-count-tag">{{ $employees->total() }} işçi</span>
            </h6>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Səhifə {{ $employees->currentPage() }} / {{ $employees->lastPage() }}</span>
            </div>
        </div>

        <div class="mobile-scroll-hint">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            <span>Cədvəli tam görmək üçün sağa sürüşdürün &rarr;</span>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 55px; text-align: center;">#</th>
                        <th>İşçi (Ad / Əlaqə / PIN)</th>
                        <th>Vəzifələr (Rollar)</th>
                        <th>Tabel Kodu & Etiketi</th>
                        <th>Filiallar</th>
                        <th>Daimi Oyunlar</th>
                        <th>Stajorluq</th>
                        <th>Status</th>
                        <th style="width: 140px; text-align: center;">Əməliyyatlar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        @php
                            $primaryRole = $emp->primaryWorkerType();
                            $genderColorClass = $emp->gender === 'female' ? 'avatar-female' : 'avatar-male';
                            $initial = mb_substr($emp->first_name ?: $emp->full_name, 0, 1);
                        @endphp
                        <tr>
                            <td style="text-align: center;">
                                <span class="text-muted fw-bold font-monospace" style="font-size: 11.5px;">
                                    #{{ $emp->id }}
                                </span>
                            </td>
                            <td>
                                <div class="worker-cell">
                                    <div class="worker-avatar {{ $genderColorClass }}"
                                         title="{{ $emp->gender_label }} — ID Kartını Aç"
                                         onclick="showEmployeeModal({{ json_encode([
                                             'id' => $emp->id,
                                             'full_name' => $emp->full_name,
                                             'gender_label' => $emp->gender_label,
                                             'phone' => $emp->phone,
                                             'pin_code' => $emp->pin_code ?: '1234',
                                             'tabel_name' => $emp->tabel_name,
                                             'tabel_role' => $emp->tabel_role_label,
                                             'branch_name' => optional($emp->primaryBranch())->name ?: 'Mərkəzi Ofis',
                                             'is_intern' => (bool)$emp->is_intern,
                                             'status_label' => $emp->status_label,
                                             'hire_date' => optional($emp->hire_date)->format('d.m.Y') ?: '—',
                                             'games' => $emp->games->pluck('game_name')->all(),
                                             'roles' => $emp->workerTypes->pluck('name')->all(),
                                         ]) }})">
                                        {{ $initial }}
                                    </div>
                                    <div>
                                        <div class="worker-name">
                                            <span>{{ $emp->full_name }}</span>
                                            @if($emp->gender === 'female')
                                                <span class="badge bg-light text-danger border border-danger-subtle px-1 py-0" style="font-size: 10px; font-weight: 700;">Qadın</span>
                                            @endif
                                        </div>
                                        <div class="worker-sub">
                                            @if($emp->phone)
                                                <span class="d-inline-flex align-items-center gap-1">
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                                    {{ $emp->phone }}
                                                </span>
                                                <span class="text-muted">&bull;</span>
                                            @endif
                                            <span class="pin-badge" title="QR Giriş PIN Kodu">
                                                PIN: {{ $emp->pin_code ?: '1234' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($emp->workerTypes->count() > 0)
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        @foreach($emp->workerTypes as $wt)
                                            @if($wt->pivot->is_primary)
                                                <span class="role-badge-primary" title="Əsas vəzifə">
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="#f59e0b" stroke="#d97706" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                    {{ $wt->name }}
                                                </span>
                                            @else
                                                <span class="role-badge-secondary" title="Əlavə rol">
                                                    {{ $wt->name }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Vəzifə təyin edilməyib</span>
                                @endif
                            </td>
                            <td>
                                <span class="tabel-tag" id="tabelLabel-{{ $emp->id }}">
                                    <span class="tabel-name-part">{{ $emp->tabel_name }}</span>
                                    <span class="tabel-divider">/</span>
                                    <span class="tabel-code-part" id="tabelRole-{{ $emp->id }}">{{ $emp->tabel_role_label }}</span>
                                </span>
                            </td>
                            <td>
                                @if($emp->primaryBranch())
                                    <div class="d-flex flex-column gap-1 align-items-start">
                                        <span class="branch-pill-main">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                                            {{ $emp->primaryBranch()->name }}
                                        </span>
                                        @if($emp->managedBranches->count() > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($emp->managedBranches as $mb)
                                                    <span class="branch-pill-managed" title="Əlavə idarə etdiyi filial">
                                                        + {{ $mb->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small fw-semibold">Mərkəzi Ofis</span>
                                @endif
                            </td>
                            <td>
                                @if($emp->games->count() > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($emp->games as $g)
                                            <span class="game-badge">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                                {{ $g->game_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.edit'))
                                    <button type="button"
                                            class="{{ $emp->is_intern ? 'intern-badge-yes' : 'intern-badge-no' }} btn-toggle-intern"
                                            data-id="{{ $emp->id }}"
                                            title="Statusu dəyişmək üçün klikləyin">
                                        @if($emp->is_intern)
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                                        @else
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                        @endif
                                        <span class="intern-label">{{ $emp->is_intern ? 'Stajor' : 'Daimi' }}</span>
                                    </button>
                                @else
                                    <span class="{{ $emp->is_intern ? 'intern-badge-yes' : 'intern-badge-no' }}" style="cursor: default;">
                                        <span class="intern-label">{{ $emp->is_intern ? 'Stajor' : 'Daimi' }}</span>
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.edit'))
                                    <button type="button"
                                            class="{{ $emp->status === 'active' ? 'status-badge-active' : 'status-badge-inactive' }} btn-toggle-emp-status"
                                            data-id="{{ $emp->id }}"
                                            title="Statusu dəyişmək üçün klikləyin">
                                        <span class="{{ $emp->status === 'active' ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                        <span class="status-label">{{ $emp->status_label }}</span>
                                    </button>
                                @else
                                    <span class="{{ $emp->status === 'active' ? 'status-badge-active' : 'status-badge-inactive' }}" style="cursor: default;">
                                        <span class="{{ $emp->status === 'active' ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                        <span>{{ $emp->status_label }}</span>
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="action-btn-group">
                                    <button type="button"
                                            class="emp-btn-action emp-btn-idcard"
                                            title="İşçi Profil & ID Kartı"
                                            onclick="showEmployeeModal({{ json_encode([
                                                'id' => $emp->id,
                                                'full_name' => $emp->full_name,
                                                'gender_label' => $emp->gender_label,
                                                'phone' => $emp->phone,
                                                'pin_code' => $emp->pin_code ?: '1234',
                                                'tabel_name' => $emp->tabel_name,
                                                'tabel_role' => $emp->tabel_role_label,
                                                'branch_name' => optional($emp->primaryBranch())->name ?: 'Mərkəzi Ofis',
                                                'is_intern' => (bool)$emp->is_intern,
                                                'status_label' => $emp->status_label,
                                                'hire_date' => optional($emp->hire_date)->format('d.m.Y') ?: '—',
                                                'games' => $emp->games->pluck('game_name')->all(),
                                                'roles' => $emp->workerTypes->pluck('name')->all(),
                                            ]) }})">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    </button>
                                    @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.edit'))
                                        <a href="{{ route('employees.edit', $emp->id) }}" class="emp-btn-action emp-btn-edit" title="Məlumatları redaktə et">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            <span>Redaktə</span>
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.delete'))
                                        <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" class="m-0" onsubmit="return confirm('«{{ $emp->full_name }}» adlı işçini arxivə göndərmək istədiyinizdən əminsiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="emp-btn-action emp-btn-del" title="İşçini arxivləşdir">
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
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <div class="d-inline-flex p-3 rounded-circle bg-light border">
                                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Axtarışa uyğun işçi tapılmadı</h6>
                                    <p class="text-muted small mb-3">Süzgəc parametrlərini dəyişin və ya mövcud işçiləri saytdan idxal edin.</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('employees.index') }}" class="att-btn att-btn-outline" style="height: 38px;">Süzgəci Sıfırla</a>
                                        @if(auth()->user()->hasAnyRole(['Admin', 'HR']) || auth()->user()->can('employees.create'))
                                            <form action="{{ route('employees.import') }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="att-btn att-btn-primary" style="height: 38px;">
                                                    İşçiləri Yenidən İdxal Et
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Cəmi <strong>{{ $employees->total() }}</strong> qeyddən <strong>{{ $employees->firstItem() }} - {{ $employees->lastItem() }}</strong> arası göstərilir
                </div>
                <div>
                    {{ $employees->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

{{-- ID Card Quick View Modal --}}
<div class="modal fade" id="employeeIdCardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #107c41 0%, #0d6334 100%); border: none; padding: 20px 24px;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="modalEmpName">İşçi Kartı</h6>
                        <span class="small opacity-75" id="modalEmpGender">PORTAL GAMES ERP</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Bağla"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 bg-white">
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div id="modalAvatar" class="worker-avatar avatar-male" style="width: 56px; height: 56px; font-size: 20px;">A</div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1" id="modalEmpFullName">Tam Ad</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" id="modalStatusBadge">Aktiv</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1" id="modalInternBadge">Daimi</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 small">
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Tabeldə Qısa Ad:</span>
                            <span class="fw-bold text-dark font-monospace" id="modalTabelName">Əliyev</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Tabel Rol Kodu:</span>
                            <span class="fw-bold text-success font-monospace" id="modalTabelRole">akt</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Əsas Filial:</span>
                            <span class="fw-bold text-dark" id="modalBranch">Filial</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">QR Giriş PIN:</span>
                            <span class="pin-badge" id="modalPin">1234</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Telefon:</span>
                            <span class="fw-bold text-dark" id="modalPhone">—</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">İşə Qəbul:</span>
                            <span class="fw-bold text-dark" id="modalHireDate">—</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block mb-1">Vəzifələr:</span>
                            <div id="modalRoles" class="d-flex flex-wrap gap-1"></div>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block mb-1">Daimi Oyunlar:</span>
                            <div id="modalGames" class="d-flex flex-wrap gap-1"></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="#" id="modalEditLink" class="att-btn att-btn-primary" style="height: 38px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        Redaktə Səhifəsi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="attToast" class="att-toast">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
    <span id="attToastText">Uğurla icra edildi!</span>
</div>
@endsection

@push('scripts')
<script>
function showToast(message) {
    const toast = document.getElementById('attToast');
    const text = document.getElementById('attToastText');
    if (!toast || !text) return;
    text.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2500);
}

function showEmployeeModal(emp) {
    const modalEl = document.getElementById('employeeIdCardModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;

    document.getElementById('modalEmpName').textContent = emp.full_name;
    document.getElementById('modalEmpFullName').textContent = emp.full_name;
    document.getElementById('modalEmpGender').textContent = emp.gender_label || 'Portal ERP';
    document.getElementById('modalTabelName').textContent = emp.tabel_name;
    document.getElementById('modalTabelRole').textContent = emp.tabel_role;
    document.getElementById('modalBranch').textContent = emp.branch_name;
    document.getElementById('modalPin').textContent = emp.pin_code;
    document.getElementById('modalPhone').textContent = emp.phone || 'Qeyd edilməyib';
    document.getElementById('modalHireDate').textContent = emp.hire_date;

    const av = document.getElementById('modalAvatar');
    av.textContent = (emp.full_name || 'A').substring(0, 1);
    av.className = 'worker-avatar ' + (emp.gender_label === 'Qadın' ? 'avatar-female' : 'avatar-male');

    const statusBadge = document.getElementById('modalStatusBadge');
    statusBadge.textContent = emp.status_label;

    const internBadge = document.getElementById('modalInternBadge');
    internBadge.textContent = emp.is_intern ? 'Stajor' : 'Daimi Heyət';

    const rolesContainer = document.getElementById('modalRoles');
    rolesContainer.innerHTML = '';
    (emp.roles || []).forEach(r => {
        const span = document.createElement('span');
        span.className = 'badge bg-light text-dark border px-2 py-1';
        span.textContent = r;
        rolesContainer.appendChild(span);
    });

    const gamesContainer = document.getElementById('modalGames');
    gamesContainer.innerHTML = '';
    if ((emp.games || []).length > 0) {
        emp.games.forEach(g => {
            const span = document.createElement('span');
            span.className = 'badge bg-purple text-dark border px-2 py-1';
            span.style.background = '#f5f3ff';
            span.style.color = '#6d28d9';
            span.textContent = g;
            gamesContainer.appendChild(span);
        });
    } else {
        gamesContainer.innerHTML = '<span class="text-muted small">—</span>';
    }

    document.getElementById('modalEditLink').href = `/hr/employees/${emp.id}/edit`;

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// Status Toggle AJAX
document.querySelectorAll('.btn-toggle-emp-status').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const id = this.dataset.id;
        const self = this;
        const label = this.querySelector('.status-label');
        const dot = this.querySelector('span:first-child');

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
                if (dot) dot.className = data.is_active ? 'status-dot-active' : 'status-dot-inactive';
                showToast(`İşçi statusu «${data.label}» olaraq dəyişdirildi.`);
            }
        })
        .catch(() => showToast('Status dəyişdirilərkən xəta baş verdi!'));
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
                showToast(`Stajorluq statusu «${data.label}» olaraq yeniləndi.`);
            } else if (data.message) {
                alert(data.message);
            }
        })
        .catch(() => showToast('Stajorluq dəyişdirilərkən xəta baş verdi!'));
    });
});
</script>
@endpush
