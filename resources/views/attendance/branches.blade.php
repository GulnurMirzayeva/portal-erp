@extends('layouts.erp')

@section('title', 'Filiallar və QR Kodlar — Davamiyyət')
@section('page-title', 'FİLİALLAR VƏ QR KOD İDARƏETMƏSİ (CRUD)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Davamiyyət</a></li>
    <li class="breadcrumb-item active">Filiallar & QR Kodlar</li>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
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
        margin-bottom: 24px;
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

    /* PURE CSS GRID (Bootstrap row/col əvəzinə, tam bərabər və geniş) */
    .branch-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
        gap: 24px;
        width: 100%;
    }

    /* Branch Card */
    .branch-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        min-height: 480px;
    }
    .branch-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px rgba(16, 124, 65, 0.09);
        border-color: #cbd5e1;
    }

    /* Card Header */
    .branch-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }
    .branch-title {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px 0;
        line-height: 1.3;
    }

    /* Badges */
    .att-badge-active {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        border-radius: 30px;
        padding: 4px 12px;
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
    .att-badge-inactive {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        border-radius: 30px;
        padding: 4px 12px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .att-badge-workers {
        background: #f8fafc;
        color: #334155;
        border: 1.5px solid #e2e8f0;
        border-radius: 30px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* QR Preview Box */
    .qr-preview-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 18px;
        text-align: center;
        margin: 14px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .qr-img-wrapper {
        background: #ffffff;
        border-radius: 14px;
        padding: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid #e2e8f0;
        display: inline-block;
    }
    .qr-preview-box img {
        width: 140px;
        height: 140px;
        display: block;
        border-radius: 8px;
    }
    .qr-token-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 3px 10px;
        font-family: monospace;
        font-size: 11px;
        color: #64748b;
        margin-top: 10px;
    }

    /* Schedule Box */
    .branch-schedule-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 12px 14px;
        margin-bottom: 16px;
    }
    .schedule-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12.5px;
        padding: 5px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .schedule-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .schedule-label {
        color: #64748b;
        font-weight: 600;
    }
    .schedule-val {
        color: #0f172a;
        font-weight: 700;
    }
    .badge-time-pill {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        padding: 2px 8px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
    }
    .map-link-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #0284c7;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        padding: 3px 8px;
        border-radius: 8px;
        transition: all 0.15s;
    }
    .map-link-pill:hover {
        background: #e0f2fe;
        color: #0369a1;
    }

    /* Buttons General */
    .att-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 13px;
        font-weight: 700;
        padding: 9px 16px;
        border-radius: 12px;
        cursor: pointer;
        border: 1.5px solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
        box-sizing: border-box;
    }
    .att-btn-primary {
        background: #107c41;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.22);
    }
    .att-btn-primary:hover {
        background: #0b5e30;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(16, 124, 65, 0.28);
    }
    .att-btn-outline {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #334155;
        border-radius: 12px;
    }
    .att-btn-outline:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    /* Card Action Footer */
    .card-actions-wrapper {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px dashed #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* Primary Download Button */
    .btn-qr-download {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        height: 40px;
        background: #107c41;
        color: #ffffff !important;
        font-size: 13px;
        font-weight: 700;
        border-radius: 12px;
        border: 1px solid #0d6334;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 2px 6px rgba(16, 124, 65, 0.18);
        box-sizing: border-box;
    }
    .btn-qr-download:hover {
        background: #0b5e30;
        border-color: #084924;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(16, 124, 65, 0.28);
    }
    .btn-qr-download:active {
        transform: translateY(0);
    }
    .badge-format-tag {
        background: rgba(255, 255, 255, 0.22);
        color: #ffffff;
        font-size: 10.5px;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }

    /* Secondary Action Row (Redaktə | Mobil Sınaq | Sil) */
    .card-secondary-row {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
    }

    .btn-sub-action {
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 11px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
        box-sizing: border-box;
    }

    /* Edit Button (Takes remaining space) */
    .btn-sub-edit {
        flex: 1;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        color: #334155;
        padding: 0 14px;
    }
    .btn-sub-edit:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #0f172a;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }

    /* Mobile Test Button */
    .btn-sub-mobile {
        width: 38px;
        height: 38px;
        padding: 0;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        color: #475569;
        flex-shrink: 0;
    }
    .btn-sub-mobile:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #2563eb;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15);
        transform: translateY(-1px);
    }

    /* Delete Button */
    .btn-sub-delete {
        width: 38px;
        height: 38px;
        padding: 0;
        background: #fef2f2;
        border: 1.5px solid #fee2e2;
        color: #ef4444;
        flex-shrink: 0;
    }
    .btn-sub-delete:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(220, 38, 38, 0.28);
        transform: translateY(-1px);
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
        overflow-y: auto;
    }
    .att-modal-dialog {
        background: #ffffff;
        border-radius: 22px;
        width: 100%;
        max-width: 680px;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.35);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        animation: attModalIn 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 32px);
        margin: auto;
    }
    .att-modal-dialog > form {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
        margin: 0;
    }
    @keyframes attModalIn {
        from { opacity: 0; transform: scale(0.95) translateY(-10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .att-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        flex-shrink: 0;
    }
    .att-modal-title {
        font-size: 17px;
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
        padding: 20px 24px;
        overflow-y: auto;
        flex: 1 1 auto;
        min-height: 0;
        overscroll-behavior: contain;
    }
    .att-modal-body::-webkit-scrollbar {
        width: 6px;
    }
    .att-modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .att-modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .att-modal-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .att-modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 24px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        flex-shrink: 0;
    }

    /* Modal Form Inputs */
    .att-form-group {
        margin-bottom: 16px;
    }
    .att-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }
    .att-input {
        width: 100%;
        padding: 11px 14px;
        font-size: 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        outline: none;
        background: #ffffff;
        color: #0f172a;
        transition: all 0.15s ease;
        box-sizing: border-box;
    }
    .att-input:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.15);
    }
    .att-input-sm {
        padding: 8px 12px;
        font-size: 13px;
        border-radius: 10px;
    }
    .att-help {
        font-size: 12px;
        color: #64748b;
        margin-top: 5px;
    }
    .att-modal-section-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 18px;
        margin-bottom: 16px;
    }
    .att-modal-section-title {
        font-size: 12px;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    /* Map Picker Component */
    .map-picker-box {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 10px;
        position: relative;
    }
    .map-picker-toolbar {
        background: #f8fafc;
        padding: 8px 12px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .map-search-input {
        flex: 1;
        min-width: 180px;
        padding: 7px 12px;
        font-size: 13px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        outline: none;
        background: #ffffff;
        transition: all 0.15s;
    }
    .map-search-input:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 2px rgba(16, 124, 65, 0.15);
    }
    .map-tool-btn {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        padding: 6px 12px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .map-tool-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #94a3b8;
    }
    .map-tool-btn-primary {
        background: #107c41;
        color: #ffffff;
        border-color: #0d6334;
    }
    .map-tool-btn-primary:hover {
        background: #0b5e30;
        color: #ffffff;
    }
    .map-canvas-container {
        width: 100%;
        height: 210px;
        position: relative;
        z-index: 1;
        background: #f1f5f9;
    }
    .map-picker-footer {
        background: #f8fafc;
        padding: 7px 12px;
        border-top: 1px solid #e2e8f0;
        font-size: 11.5px;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        flex-wrap: wrap;
    }
    .map-live-coords {
        font-family: monospace;
        font-weight: 700;
        color: #0f172a;
        background: #ffffff;
        padding: 2px 7px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        font-size: 11px;
    }
</style>
@endpush

@section('content')
<div class="att-wrapper">

    {{-- Top Action & Info Bar --}}
    <div class="att-top-bar">
        <div>
            <h5 class="att-top-title">Filiallar və QR Kodların İdarə Edilməsi</h5>
            <div class="att-top-desc">
                Yeni filial/QR yaradın, həftəiçi/həftəsonu qrafiklərini və GPS koordinatlarını tənzimləyin.
                <span class="att-pill-radius">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Vahid GPS Radiusu: {{ \App\Models\AttendanceSetting::get('default_radius_meters', '100') }} metr
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="att-btn att-btn-primary shadow-sm" id="btnOpenCreateBranch">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Yeni Filial / QR Əlavə Et
            </button>
            <a href="{{ route('attendance.index') }}" class="att-btn att-btn-outline">
                ← Davamiyyət Jurnalı
            </a>
        </div>
    </div>

    {{-- Filial Kartları Grid (Pure CSS Grid, Tam Ekran Genişliyi və Bərabər Qap) --}}
    <div class="branch-grid">
        @forelse($branches as $b)
            <div class="branch-card" id="branchCard_{{ $b->id }}">
                <div>
                    {{-- Başlıq & Status --}}
                    <div class="branch-header">
                        <div>
                            <h6 class="branch-title">{{ $b->name }}</h6>
                            @if($b->is_active)
                                <span class="att-badge-active">
                                    <span class="att-pulse-dot"></span>
                                    Aktiv
                                </span>
                            @else
                                <span class="att-badge-inactive">
                                    ✕ Deaktiv
                                </span>
                            @endif
                        </div>
                        <span class="att-badge-workers">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            {{ $b->workers_count }} İşçi
                        </span>
                    </div>

                    {{-- QR Preview Box --}}
                    <div class="qr-preview-box">
                        <div class="qr-img-wrapper">
                            <img src="{{ route('attendance.qr.image', ['token' => $b->qr_token]) }}" alt="{{ $b->name }} QR">
                        </div>
                        <span class="qr-token-pill">
                            Token: {{ substr($b->qr_token, 0, 10) }}...
                        </span>
                    </div>

                    {{-- Qrafik və GPS Məlumatları --}}
                    <div class="branch-schedule-box">
                        <div class="schedule-row">
                            <span class="schedule-label">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: #64748b;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                Həftəiçi:
                            </span>
                            @if($b->work_time_weekdays_start)
                                <span class="badge-time-pill">
                                    {{ substr($b->work_time_weekdays_start, 0, 5) }} – {{ substr($b->work_time_weekdays_end, 0, 5) }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </div>
                        <div class="schedule-row">
                            <span class="schedule-label">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: #64748b;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                Həftəsonu:
                            </span>
                            @if($b->work_time_weekend_start)
                                <span class="badge-time-pill">
                                    {{ substr($b->work_time_weekend_start, 0, 5) }} – {{ substr($b->work_time_weekend_end, 0, 5) }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </div>
                        <div class="schedule-row">
                            <span class="schedule-label">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: #64748b;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                GPS:
                            </span>
                            <span class="schedule-val font-monospace" style="font-size: 11.5px;">
                                {{ $b->latitude ? number_format($b->latitude, 3) : '—' }}, {{ $b->longitude ? number_format($b->longitude, 3) : '—' }}
                            </span>
                        </div>
                        @if($b->latitude && $b->longitude)
                        <div class="text-end pt-1">
                            <a href="https://www.google.com/maps?q={{ $b->latitude }},{{ $b->longitude }}" target="_blank" class="map-link-pill">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                Xəritədə bax →
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Əməliyyatlar (Səliqəli & Peşəkar Düymələr) --}}
                <div class="card-actions-wrapper">
                    {{-- 1. Əsas Düymə: Vektor QR Kodu Yüklə --}}
                    <a href="{{ route('attendance.qr.download', ['token' => $b->qr_token]) }}" class="btn-qr-download" title="Filialın sadə vektor QR kodunu yüklə">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>QR Kodu Yüklə</span>
                        <span class="badge-format-tag">SVG</span>
                    </a>

                    {{-- 2. Köməkçi Düymələr: Redaktə | Mobil Sınaq | Sil --}}
                    <div class="card-secondary-row">
                        <button type="button" class="btn-sub-action btn-sub-edit btn-edit-branch"
                            data-id="{{ $b->id }}"
                            data-name="{{ $b->name }}"
                            data-lat="{{ $b->latitude }}"
                            data-lng="{{ $b->longitude }}"
                            data-wd-start="{{ $b->work_time_weekdays_start ? substr($b->work_time_weekdays_start, 0, 5) : '' }}"
                            data-wd-end="{{ $b->work_time_weekdays_end ? substr($b->work_time_weekdays_end, 0, 5) : '' }}"
                            data-we-start="{{ $b->work_time_weekend_start ? substr($b->work_time_weekend_start, 0, 5) : '' }}"
                            data-we-end="{{ $b->work_time_weekend_end ? substr($b->work_time_weekend_end, 0, 5) : '' }}"
                            data-map="{{ $b->map_url }}"
                            data-active="{{ $b->is_active ? 1 : 0 }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            <span>Redaktə</span>
                        </button>

                        <a href="{{ route('attendance.scan', ['token' => $b->qr_token]) }}" target="_blank" class="btn-sub-action btn-sub-mobile" title="Mobil Ekranı Sınaqdan Keçir">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                        </a>

                        <button type="button" class="btn-sub-action btn-sub-delete btn-delete-branch"
                            data-id="{{ $b->id }}"
                            data-name="{{ $b->name }}"
                            title="Filialı və QR Kodu Sil">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #64748b;">
                Heç bir filial və QR kod tapılmadı. "Yeni Filial / QR Əlavə Et" düyməsindən istifadə edin.
            </div>
        @endforelse
    </div>

</div>

{{-- ============================================================== --}}
{{-- YENİ FİLİAL ƏLAVƏ ET MODALI                                    --}}
{{-- ============================================================== --}}
<div class="att-modal-backdrop" id="modalCreateBranch">
    <div class="att-modal-dialog">
        <div class="att-modal-header">
            <h5 class="att-modal-title">Yeni Filial və QR Kod Yarat</h5>
            <button type="button" class="att-modal-close-btn" onclick="closeModal('modalCreateBranch')">&times;</button>
        </div>
        <form id="formCreateBranch">
            <div class="att-modal-body">
                <div class="att-form-group">
                    <label class="att-label">Filialın Adı <span class="text-danger">*</span></label>
                    <input type="text" class="att-input" id="createName" required placeholder="Məsələn: Xırdalan Filialı və ya Yeni Ofis">
                </div>

                {{-- Dinamik İş Saatları --}}
                <div class="att-modal-section-box">
                    <div class="att-modal-section-title">
                        Filialın İş Qrafiki (Saatlar)
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Həftəiçi Başlama</label>
                            <input type="time" class="att-input att-input-sm" id="createWdStart" value="14:00">
                        </div>
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Həftəiçi Bitmə</label>
                            <input type="time" class="att-input att-input-sm" id="createWdEnd" value="00:00">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Həftəsonu Başlama</label>
                            <input type="time" class="att-input att-input-sm" id="createWeStart" value="12:00">
                        </div>
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Həftəsonu Bitmə</label>
                            <input type="time" class="att-input att-input-sm" id="createWeEnd" value="00:00">
                        </div>
                    </div>
                </div>

                {{-- Xəritə Seçimi & GPS Koordinatları --}}
                <div class="att-modal-section-box" style="padding: 14px 16px;">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="att-modal-section-title" style="margin-bottom: 0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="vertical-align: -2px; color: #107c41;"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
                            Google Maps İframe / Linki və ya Xəritədən Seçim
                        </div>
                        <button type="button" class="map-tool-btn" id="btnLocateCreate" title="Cihazın cari GPS koordinatlarını al">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
                            <span>📍 Cari Məkanım</span>
                        </button>
                    </div>

                    {{-- Əsas Ağıllı Giriş Xanası: Google Maps Iframe, Link və ya Ünvan --}}
                    <div class="mb-2">
                        <div class="d-flex gap-2">
                            <input type="text" class="map-search-input font-monospace" id="searchAddressCreate" style="font-size: 12.5px;" placeholder="Google Maps-dən <iframe src=...> və ya link yapışdırın (və ya ünvan yazın)...">
                            <button type="button" class="map-tool-btn map-tool-btn-primary" id="btnSearchCreate">Təyin Et / Axtar</button>
                        </div>
                        <div id="mapFeedbackCreate" style="font-size: 12px; margin-top: 5px; font-weight: 600; display: none;"></div>
                    </div>

                    <input type="hidden" id="createMapUrl">

                    {{-- İnteraktiv Xəritə --}}
                    <div class="map-picker-box">
                        <div id="mapCreate" class="map-canvas-container"></div>
                        <div class="map-picker-footer">
                            <span>💡 Link yapışdıra, xəritəyə klikləyə və ya pini çəkərək dəqiq yeri təyin edə bilərsiniz.</span>
                            <span class="map-live-coords" id="liveCoordsCreate">—</span>
                        </div>
                    </div>

                    {{-- Koordinat Inputları --}}
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Enlik (Latitude) <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="att-input att-input-sm font-monospace" id="createLat" required placeholder="40.3815">
                        </div>
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Uzunluq (Longitude) <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="att-input att-input-sm font-monospace" id="createLng" required placeholder="49.8383">
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 pt-1">
                    <input type="checkbox" id="createActive" checked style="width: 18px; height: 18px; accent-color: #107c41; cursor: pointer;">
                    <label for="createActive" style="font-size: 13.5px; font-weight: 700; color: #1e293b; cursor: pointer; margin: 0;">Filial aktivdir (QR dərhal işləsin)</label>
                </div>
            </div>
            <div class="att-modal-footer">
                <button type="button" class="att-btn att-btn-outline" onclick="closeModal('modalCreateBranch')">Ləğv et</button>
                <button type="submit" class="att-btn att-btn-primary" id="btnSaveNewBranch">Yarat və QR Təyin Et</button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================== --}}
{{-- REDAKTƏ MODALI                                                 --}}
{{-- ============================================================== --}}
<div class="att-modal-backdrop" id="modalEditBranch">
    <div class="att-modal-dialog">
        <div class="att-modal-header">
            <h5 class="att-modal-title" id="modalBranchTitle">Filial Məlumatlarını Redaktə Et</h5>
            <button type="button" class="att-modal-close-btn" onclick="closeModal('modalEditBranch')">&times;</button>
        </div>
        <form id="formUpdateBranch">
            <div class="att-modal-body">
                <input type="hidden" id="editBranchId">
                <div class="att-form-group">
                    <label class="att-label">Filialın Adı <span class="text-danger">*</span></label>
                    <input type="text" class="att-input" id="editName" required>
                </div>

                {{-- Dinamik İş Saatları --}}
                <div class="att-modal-section-box">
                    <div class="att-modal-section-title">
                        Filialın İş Qrafiki (Saatlar)
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Həftəiçi Başlama</label>
                            <input type="time" class="att-input att-input-sm" id="editWdStart">
                        </div>
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Həftəiçi Bitmə</label>
                            <input type="time" class="att-input att-input-sm" id="editWdEnd">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Həftəsonu Başlama</label>
                            <input type="time" class="att-input att-input-sm" id="editWeStart">
                        </div>
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Həftəsonu Bitmə</label>
                            <input type="time" class="att-input att-input-sm" id="editWeEnd">
                        </div>
                    </div>
                </div>

                {{-- Xəritə Seçimi & GPS Koordinatları (DB-dən yüklənən və redaktə edilən) --}}
                <div class="att-modal-section-box" style="padding: 14px 16px;">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="att-modal-section-title" style="margin-bottom: 0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="vertical-align: -2px; color: #107c41;"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
                            Mövcud Google Maps İframe / Linki və Xəritə Redaktəsi
                        </div>
                        <button type="button" class="map-tool-btn" id="btnLocateEdit" title="Cihazın cari GPS koordinatlarını al">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
                            <span>📍 Cari Məkanım</span>
                        </button>
                    </div>

                    {{-- Əsas Ağıllı Giriş Xanası: Google Maps Iframe, Link və ya Ünvan --}}
                    <div class="mb-2">
                        <div class="d-flex gap-2">
                            <input type="text" class="map-search-input font-monospace" id="searchAddressEdit" style="font-size: 12.5px;" placeholder="Yeni Google Maps <iframe src=...> və ya link yapışdırın (və ya ünvan yazın)...">
                            <button type="button" class="map-tool-btn map-tool-btn-primary" id="btnSearchEdit">Təyin Et / Axtar</button>
                        </div>
                        <div id="mapFeedbackEdit" style="font-size: 12px; margin-top: 5px; font-weight: 600; display: none;"></div>
                    </div>

                    <input type="hidden" id="editMapUrl">

                    {{-- İnteraktiv Xəritə --}}
                    <div class="map-picker-box">
                        <div id="mapEdit" class="map-canvas-container"></div>
                        <div class="map-picker-footer">
                            <span>💡 Mövcud filialın xəritəsidir. Link yapışdıra, xəritəyə klikləyə və ya pini çəkərək dəyişə bilərsiniz.</span>
                            <span class="map-live-coords" id="liveCoordsEdit">—</span>
                        </div>
                    </div>

                    {{-- Koordinat Inputları --}}
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Enlik (Latitude) <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="att-input att-input-sm font-monospace" id="editLat" required>
                        </div>
                        <div class="col-6">
                            <label class="att-label" style="font-size: 12px; margin-bottom: 4px;">Uzunluq (Longitude) <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="att-input att-input-sm font-monospace" id="editLng" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 pt-1">
                    <input type="checkbox" id="editActive" style="width: 18px; height: 18px; accent-color: #107c41; cursor: pointer;">
                    <label for="editActive" style="font-size: 13.5px; font-weight: 700; color: #1e293b; cursor: pointer; margin: 0;">Filial aktivdir (QR işləyir)</label>
                </div>
            </div>
            <div class="att-modal-footer">
                <button type="button" class="att-btn att-btn-outline" onclick="closeModal('modalEditBranch')">Ləğv et</button>
                <button type="submit" class="att-btn att-btn-primary" id="btnSaveBranch">Yadda Saxla</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    const BAKU_DEFAULT = [40.4093, 49.8671];
    let createMap = null;
    let createMarker = null;
    let editMap = null;
    let editMarker = null;

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

    // Modalın fonuna klik edəndə bağlanması
    document.querySelectorAll('.att-modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // ESC düyməsi ilə istənilən açıq modalı bağlamaq
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.att-modal-backdrop').forEach(m => {
                m.style.display = 'none';
            });
            document.body.style.overflow = '';
        }
    });

    // Google Maps Iframe / Link / Koordinat təmizləyici və ayırıcı funksiya
    function parseCoordinatesAndUrl(text) {
        if (!text || typeof text !== 'string') return null;
        let cleanUrl = text.trim();

        // 1. Əgər bütöv <iframe ... src="..."> yapışdırılıbsa, src daxilindəki URL-i çıxarırıq
        const iframeMatch = cleanUrl.match(/src=["']([^"']+)["']/i);
        if (iframeMatch) {
            cleanUrl = iframeMatch[1];
        }

        let lat = null, lng = null;

        // 2. Google Maps Embed linkindən parametrlər: !3d[enlik] və !2d[uzunluq]
        const mLat = cleanUrl.match(/!3d(-?[0-9.]+)/);
        const mLng = cleanUrl.match(/!2d(-?[0-9.]+)/);
        if (mLat && mLng) {
            lat = parseFloat(mLat[1]);
            lng = parseFloat(mLng[1]);
        }

        // 3. Google Maps query: ?q=lat,lng və ya @lat,lng və ya ll=lat,lng
        if (lat === null || lng === null) {
            const mQ = cleanUrl.match(/(?:@|[?&]q=|[?&]ll=)(-?[0-9.]+),(-?[0-9.]+)/);
            if (mQ) {
                lat = parseFloat(mQ[1]);
                lng = parseFloat(mQ[2]);
            }
        }

        // 4. Sadə rəqəmlər şəklində "40.3540700, 49.8096253"
        if (lat === null || lng === null) {
            const mPlain = cleanUrl.match(/(-?[0-9]{1,2}\.[0-9]+)[\s,]+(-?[0-9]{1,3}\.[0-9]+)/);
            if (mPlain) {
                lat = parseFloat(mPlain[1]);
                lng = parseFloat(mPlain[2]);
            }
        }

        if (lat !== null && lng !== null && !isNaN(lat) && !isNaN(lng)) {
            // Əgər təmiz URL yoxdursa amma koordinat tapılıbsa, standart Google Maps embed URL yaradırıq
            if (!cleanUrl.startsWith('http://') && !cleanUrl.startsWith('https://')) {
                cleanUrl = `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3039.0!2d${lng}!3d${lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z!5e0!3m2!1saz!2saz`;
            }
            return { lat, lng, cleanUrl };
        }
        return null;
    }

    // Ağıllı məkan emalı: istifadəçi iframe və ya link yapışdırdıqda dərhal oxuyur
    function processMapInput(rawInput, isEdit = false) {
        if (!rawInput || typeof rawInput !== 'string') return false;
        const text = rawInput.trim();
        if (!text) return false;

        const parsed = parseCoordinatesAndUrl(text);
        const feedbackEl = document.getElementById(isEdit ? 'mapFeedbackEdit' : 'mapFeedbackCreate');
        const inputEl = document.getElementById(isEdit ? 'searchAddressEdit' : 'searchAddressCreate');
        const hiddenUrlEl = document.getElementById(isEdit ? 'editMapUrl' : 'createMapUrl');

        if (parsed) {
            if (hiddenUrlEl) hiddenUrlEl.value = parsed.cleanUrl;

            // Əgər tam iframe yapışdırılmışdısa, input xanasını təmiz src linki ilə əvəzləyirik
            if (text.includes('<iframe') && parsed.cleanUrl && inputEl) {
                inputEl.value = parsed.cleanUrl;
            }

            if (isEdit) {
                setEditCoords(parsed.lat, parsed.lng, true, document.getElementById('editName').value || 'Filial');
            } else {
                setCreateCoords(parsed.lat, parsed.lng, true);
            }

            if (feedbackEl) {
                feedbackEl.style.display = 'block';
                feedbackEl.style.color = '#059669';
                feedbackEl.innerHTML = `✓ Google Maps koordinatları oxundu: Enlik: <code>${parsed.lat.toFixed(7)}</code>, Uzunluq: <code>${parsed.lng.toFixed(7)}</code> (Bazada bu embed link saxlanılacaq)`;
            }
            return true;
        }

        return false;
    }

    // Xəritə: Yeni Filial Yaradılması
    function initCreateMap() {
        if (createMap) return;
        createMap = L.map('mapCreate').setView(BAKU_DEFAULT, 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(createMap);

        createMarker = L.marker(BAKU_DEFAULT, { draggable: true }).addTo(createMap);

        createMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            setCreateCoords(pos.lat, pos.lng, false);
            updateGeneratedMapUrl(pos.lat, pos.lng, false);
        });

        createMap.on('click', function(e) {
            setCreateCoords(e.latlng.lat, e.latlng.lng, false);
            updateGeneratedMapUrl(e.latlng.lat, e.latlng.lng, false);
        });
    }

    function setCreateCoords(lat, lng, center = true) {
        const numLat = Number(lat).toFixed(7);
        const numLng = Number(lng).toFixed(7);
        document.getElementById('createLat').value = numLat;
        document.getElementById('createLng').value = numLng;
        document.getElementById('liveCoordsCreate').textContent = `${numLat}, ${numLng}`;
        if (createMarker) {
            createMarker.setLatLng([lat, lng]);
            createMarker.bindPopup(`<strong>Seçilmiş Nöqtə</strong><br>Enlik: ${numLat}<br>Uzunluq: ${numLng}`).openPopup();
        }
        if (center && createMap) {
            createMap.setView([lat, lng], 16);
        }
    }

    // Xəritə: Redaktə Modalı
    function initEditMap() {
        if (editMap) return;
        editMap = L.map('mapEdit').setView(BAKU_DEFAULT, 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(editMap);

        editMarker = L.marker(BAKU_DEFAULT, { draggable: true }).addTo(editMap);

        editMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            setEditCoords(pos.lat, pos.lng, false);
            updateGeneratedMapUrl(pos.lat, pos.lng, true);
        });

        editMap.on('click', function(e) {
            setEditCoords(e.latlng.lat, e.latlng.lng, false);
            updateGeneratedMapUrl(e.latlng.lat, e.latlng.lng, true);
        });
    }

    function setEditCoords(lat, lng, center = true, title = 'Filial') {
        const numLat = Number(lat).toFixed(7);
        const numLng = Number(lng).toFixed(7);
        document.getElementById('editLat').value = numLat;
        document.getElementById('editLng').value = numLng;
        document.getElementById('liveCoordsEdit').textContent = `${numLat}, ${numLng}`;
        if (editMarker) {
            editMarker.setLatLng([lat, lng]);
            editMarker.bindPopup(`<strong>${title}</strong><br>Enlik: ${numLat}<br>Uzunluq: ${numLng}`).openPopup();
        }
        if (center && editMap) {
            editMap.setView([lat, lng], 16);
        }
    }

    function updateGeneratedMapUrl(lat, lng, isEdit) {
        const url = `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3039.0!2d${lng}!3d${lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z!5e0!3m2!1saz!2saz`;
        const hiddenEl = document.getElementById(isEdit ? 'editMapUrl' : 'createMapUrl');
        if (hiddenEl) hiddenEl.value = url;
    }

    // Inputlardan xəritə markerini əl ilə yeniləmə
    ['createLat', 'createLng'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', function() {
            const lat = parseFloat(document.getElementById('createLat').value);
            const lng = parseFloat(document.getElementById('createLng').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                setCreateCoords(lat, lng, true);
                updateGeneratedMapUrl(lat, lng, false);
            }
        });
    });

    ['editLat', 'editLng'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', function() {
            const lat = parseFloat(document.getElementById('editLat').value);
            const lng = parseFloat(document.getElementById('editLng').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                setEditCoords(lat, lng, true, document.getElementById('editName').value || 'Filial');
                updateGeneratedMapUrl(lat, lng, true);
            }
        });
    });

    // Əsas axtarış / link yapışdırma xanalarına canli reaksiyalar (Paste və Input)
    ['searchAddressCreate', 'searchAddressEdit'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const isEdit = id === 'searchAddressEdit';

        el.addEventListener('paste', function(e) {
            setTimeout(() => {
                processMapInput(this.value, isEdit);
            }, 40);
        });

        el.addEventListener('input', function() {
            const v = this.value;
            if (v.includes('<iframe') || v.includes('http') || v.includes('!3d') || v.includes('maps.google')) {
                processMapInput(v, isEdit);
            }
        });
    });

    // Cari məkan təyini funksiyası
    function handleLocate(btn, callback, isEdit) {
        if (!navigator.geolocation) {
            alert('Brauzeriniz GPS məkan təyinini dəstəkləmir.');
            return;
        }
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Axtarılır...';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            pos => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                callback(pos.coords.latitude, pos.coords.longitude);
                updateGeneratedMapUrl(pos.coords.latitude, pos.coords.longitude, isEdit);
            },
            err => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('GPS koordinatları alınmadı: ' + err.message + '. Brauzerdə yerləşmə icazəsini yoxlayın.');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    document.getElementById('btnLocateCreate')?.addEventListener('click', function() {
        handleLocate(this, (lat, lng) => setCreateCoords(lat, lng, true), false);
    });

    document.getElementById('btnLocateEdit')?.addEventListener('click', function() {
        handleLocate(this, (lat, lng) => setEditCoords(lat, lng, true, document.getElementById('editName').value || 'Filial'), true);
    });

    // Ağıllı "Təyin Et / Axtar" düyməsi
    function handleSmartSubmit(isEdit) {
        const inputEl = document.getElementById(isEdit ? 'searchAddressEdit' : 'searchAddressCreate');
        const btnEl = document.getElementById(isEdit ? 'btnSearchEdit' : 'btnSearchCreate');
        const val = inputEl.value.trim();
        if (!val) return;

        // 1. Əvvəlcə link / iframe / koordinat kimi yoxlayırıq
        if (processMapInput(val, isEdit)) {
            return; // Uğurlu tapıldı və xəritə tənzimləndi, axtarış xətasına ehtiyac yoxdur!
        }

        // 2. Əgər link deyilsə, ünvan kimi OpenStreetMap Nominatim-də axtarırıq
        executeSearch(inputEl, btnEl, (lat, lng) => {
            if (isEdit) {
                setEditCoords(lat, lng, true, document.getElementById('editName').value || 'Filial');
            } else {
                setCreateCoords(lat, lng, true);
            }
            updateGeneratedMapUrl(lat, lng, isEdit);
            const feedbackEl = document.getElementById(isEdit ? 'mapFeedbackEdit' : 'mapFeedbackCreate');
            if (feedbackEl) {
                feedbackEl.style.display = 'block';
                feedbackEl.style.color = '#0284c7';
                feedbackEl.innerHTML = `✓ Ünvan tapıldı: Enlik: <code>${lat.toFixed(7)}</code>, Uzunluq: <code>${lng.toFixed(7)}</code>`;
            }
        });
    }

    // Ünvan axtarışı (Nominatim)
    async function executeSearch(inputEl, btnEl, callback) {
        const q = inputEl.value.trim();
        if (!q) return;

        const origBtn = btnEl.innerHTML;
        btnEl.innerHTML = '...';
        btnEl.disabled = true;

        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q + ', Baku, Azerbaijan')}&limit=1`);
            const data = await res.json();
            if (data && data.length > 0) {
                callback(parseFloat(data[0].lat), parseFloat(data[0].lon));
            } else {
                alert(`"${q}" üzrə ünvan tapılmadı. Xəritədə birbaşa klikləyə və ya Google Maps linki yapışdıra bilərsiniz.`);
            }
        } catch (e) {
            alert('Axtarış zamanı xəta baş verdi.');
        } finally {
            btnEl.innerHTML = origBtn;
            btnEl.disabled = false;
        }
    }

    document.getElementById('btnSearchCreate')?.addEventListener('click', () => handleSmartSubmit(false));
    document.getElementById('searchAddressCreate')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleSmartSubmit(false);
        }
    });

    document.getElementById('btnSearchEdit')?.addEventListener('click', () => handleSmartSubmit(true));
    document.getElementById('searchAddressEdit')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleSmartSubmit(true);
        }
    });

    // "Yeni Filial Əlavə Et" düyməsi
    document.getElementById('btnOpenCreateBranch')?.addEventListener('click', function() {
        document.getElementById('formCreateBranch').reset();
        document.getElementById('createActive').checked = true;
        document.getElementById('createWdStart').value = '14:00';
        document.getElementById('createWdEnd').value = '00:00';
        document.getElementById('createWeStart').value = '12:00';
        document.getElementById('createWeEnd').value = '00:00';
        if (document.getElementById('createMapUrl')) document.getElementById('createMapUrl').value = '';
        if (document.getElementById('searchAddressCreate')) document.getElementById('searchAddressCreate').value = '';
        const fb = document.getElementById('mapFeedbackCreate');
        if (fb) fb.style.display = 'none';

        openModal('modalCreateBranch');
        setTimeout(() => {
            initCreateMap();
            createMap.invalidateSize();
            setCreateCoords(BAKU_DEFAULT[0], BAKU_DEFAULT[1], true);
            updateGeneratedMapUrl(BAKU_DEFAULT[0], BAKU_DEFAULT[1], false);
            document.getElementById('createName')?.focus();
        }, 220);
    });

    // "Redaktə" düyməsi
    document.querySelectorAll('.btn-edit-branch').forEach(btn => {
        btn.addEventListener('click', function() {
            const bId = this.dataset.id;
            const bName = this.dataset.name || '';
            const bLat = parseFloat(this.dataset.lat);
            const bLng = parseFloat(this.dataset.lng);
            const bMap = this.dataset.map || '';

            document.getElementById('editBranchId').value = bId;
            document.getElementById('modalBranchTitle').textContent = bName + ' — Redaktə';
            document.getElementById('editName').value = bName;
            document.getElementById('editWdStart').value = this.dataset.wdStart || '';
            document.getElementById('editWdEnd').value = this.dataset.wdEnd || '';
            document.getElementById('editWeStart').value = this.dataset.weStart || '';
            document.getElementById('editWeEnd').value = this.dataset.weEnd || '';
            document.getElementById('editActive').checked = this.dataset.active === '1';

            if (document.getElementById('editMapUrl')) document.getElementById('editMapUrl').value = bMap;
            if (document.getElementById('searchAddressEdit')) document.getElementById('searchAddressEdit').value = bMap;

            const fb = document.getElementById('mapFeedbackEdit');
            if (fb) {
                if (bMap) {
                    fb.style.display = 'block';
                    fb.style.color = '#059669';
                    fb.innerHTML = `✓ Mövcud Google Maps linki bazadan yükləndi. Dəyişmək üçün yeni link yapışdıra bilərsiniz.`;
                } else {
                    fb.style.display = 'none';
                }
            }

            openModal('modalEditBranch');
            setTimeout(() => {
                initEditMap();
                editMap.invalidateSize();

                if (!isNaN(bLat) && !isNaN(bLng) && bLat !== 0 && bLng !== 0) {
                    setEditCoords(bLat, bLng, true, bName);
                } else {
                    setEditCoords(BAKU_DEFAULT[0], BAKU_DEFAULT[1], true, bName);
                }
                document.getElementById('editName')?.focus();
            }, 220);
        });
    });

    // Yeni Filial Yarat (Store Submit)
    document.getElementById('formCreateBranch')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveNewBranch');
        btn.disabled = true;
        btn.textContent = 'Yaradılır...';

        const payload = {
            name: document.getElementById('createName').value,
            latitude: document.getElementById('createLat').value,
            longitude: document.getElementById('createLng').value,
            map_url: document.getElementById('createMapUrl')?.value || document.getElementById('searchAddressCreate')?.value || null,
            work_time_weekdays_start: document.getElementById('createWdStart').value || null,
            work_time_weekdays_end: document.getElementById('createWdEnd').value || null,
            work_time_weekend_start: document.getElementById('createWeStart').value || null,
            work_time_weekend_end: document.getElementById('createWeEnd').value || null,
            is_active: document.getElementById('createActive').checked ? 1 : 0,
        };

        fetch("{{ route('attendance.branches.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeModal('modalCreateBranch');
                alert(data.message);
                location.reload();
            } else {
                alert('Xəta: ' + (data.message || 'Məlumat düzgün deyil'));
                btn.disabled = false;
                btn.textContent = 'Yarat və QR Təyin Et';
            }
        })
        .catch(err => {
            alert('Server xətası baş verdi.');
            btn.disabled = false;
            btn.textContent = 'Yarat və QR Təyin Et';
        });
    });

    // Filial Yenilə (Update Submit)
    document.getElementById('formUpdateBranch')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('editBranchId').value;
        const btn = document.getElementById('btnSaveBranch');
        btn.disabled = true;
        btn.textContent = 'Saxlanılır...';

        const payload = {
            name: document.getElementById('editName').value,
            latitude: document.getElementById('editLat').value,
            longitude: document.getElementById('editLng').value,
            map_url: document.getElementById('editMapUrl')?.value || document.getElementById('searchAddressEdit')?.value || null,
            work_time_weekdays_start: document.getElementById('editWdStart').value || null,
            work_time_weekdays_end: document.getElementById('editWdEnd').value || null,
            work_time_weekend_start: document.getElementById('editWeStart').value || null,
            work_time_weekend_end: document.getElementById('editWeEnd').value || null,
            is_active: document.getElementById('editActive').checked ? 1 : 0,
        };

        fetch(`/attendance/branches/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeModal('modalEditBranch');
                alert(data.message);
                location.reload();
            } else {
                alert('Xəta baş verdi: ' + (data.message || 'Məlumat düzgün deyil'));
                btn.disabled = false;
                btn.textContent = 'Yadda Saxla';
            }
        })
        .catch(err => {
            alert('Server xətası baş verdi.');
            btn.disabled = false;
            btn.textContent = 'Yadda Saxla';
        });
    });

    // Filial Sil (Delete)
    document.querySelectorAll('.btn-delete-branch').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            if (!confirm(`'${name}' filialını və ona aid QR kodu silmək istədiyinizdən əminsiniz?`)) {
                return;
            }

            fetch(`/attendance/branches/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    const el = document.getElementById('branchCard_' + id);
                    if (el) el.remove();
                    else location.reload();
                } else {
                    alert('Silinmədi: ' + (data.message || ''));
                }
            })
            .catch(e => alert('Server xətası baş verdi.'));
        });
    });
</script>
@endpush
@endsection
