@extends('layouts.erp')

@section('title', 'Davamiyyət Tənzimləmələri — QR Davamiyyət')
@section('page-title', 'DAVAMİYYƏT VƏ TƏHLÜKƏSİZLİK TƏNZİMLƏMƏLƏRİ')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Davamiyyət</a></li>
    <li class="breadcrumb-item active">Tənzimləmələr</li>
@endsection

@push('css')
<style>
    .att-wrapper {
        padding-bottom: 50px;
        max-width: 960px;
        margin: 0 auto;
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
    }
    .att-top-desc {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    /* Quick Summary Strip */
    .config-summary-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 12px;
        margin-bottom: 22px;
    }
    .summary-chip {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }
    .summary-chip-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .summary-chip-icon.emerald { background: #ecfdf5; color: #107c41; }
    .summary-chip-icon.blue { background: #eff6ff; color: #2563eb; }
    .summary-chip-icon.rose { background: #fff1f2; color: #e11d48; }
    .summary-chip-icon.amber { background: #fffbeb; color: #d97706; }
    .summary-chip-label {
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        display: block;
    }
    .summary-chip-val {
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
    }

    /* Settings Card */
    .settings-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px 26px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        margin-bottom: 22px;
        transition: all 0.2s ease;
    }
    .settings-card-header {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 18px;
    }
    .settings-icon-badge {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .badge-emerald { background: #ecfdf5; color: #107c41; border: 1.5px solid #a7f3d0; }
    .badge-blue { background: #eff6ff; color: #2563eb; border: 1.5px solid #bfdbfe; }
    .badge-rose { background: #fff1f2; color: #e11d48; border: 1.5px solid #fecdd3; }
    .badge-amber { background: #fffbeb; color: #d97706; border: 1.5px solid #fde68a; }

    .settings-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
    }
    .settings-desc {
        font-size: 13px;
        color: #64748b;
        margin: 0;
        line-height: 1.45;
    }

    /* Option Cards */
    .option-card-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .option-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 18px;
        background: #ffffff;
        cursor: pointer;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }
    .option-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .option-card.active {
        border-color: #107c41;
        background: #f0fdf4;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.12);
    }

    /* Custom Radio Dot Indicator */
    .custom-radio-indicator {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
        transition: all 0.18s ease;
    }
    .option-card.active .custom-radio-indicator {
        border-color: #107c41;
        background: #107c41;
    }
    .custom-radio-indicator svg {
        opacity: 0;
        transform: scale(0.6);
        transition: all 0.18s ease;
        color: #ffffff;
    }
    .option-card.active .custom-radio-indicator svg {
        opacity: 1;
        transform: scale(1);
    }
    .option-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    /* Option Content & Badges */
    .opt-header-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
        flex-wrap: wrap;
    }
    .opt-title {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
    }
    .opt-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .opt-badge-recommended {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }
    .opt-badge-fast {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .opt-badge-neutral {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .opt-desc {
        font-size: 12.5px;
        color: #64748b;
        margin: 0;
        line-height: 1.45;
    }

    /* Modern Input Groups */
    .att-input-group {
        display: flex;
        align-items: center;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.15s ease;
    }
    .att-input-group:focus-within {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.15);
    }
    .att-input-icon {
        padding: 10px 14px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .att-input-field {
        flex: 1;
        border: none;
        outline: none;
        padding: 11px 0;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        background: transparent;
    }
    .att-input-addon {
        background: #f8fafc;
        border-left: 1px solid #e2e8f0;
        padding: 11px 16px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
    }

    /* Standard Input */
    .att-input-standard {
        width: 100%;
        padding: 11px 14px;
        font-size: 14px;
        font-weight: 700;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        outline: none;
        background: #ffffff;
        color: #0f172a;
        transition: all 0.15s ease;
        box-sizing: border-box;
    }
    .att-input-standard:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.15);
    }

    /* Preset Chips */
    .preset-chips-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .preset-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        margin-right: 4px;
    }
    .preset-chip {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .preset-chip:hover {
        background: #ffffff;
        border-color: #107c41;
        color: #107c41;
        transform: translateY(-1px);
    }
    .preset-chip.active {
        background: #ecfdf5;
        border-color: #107c41;
        color: #107c41;
    }

    /* Info Tip Box */
    .att-info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-top: 14px;
        font-size: 12.5px;
        color: #475569;
        line-height: 1.45;
    }
    .att-info-box-icon {
        color: #2563eb;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* Sub-section Box (Time & Tolerance) */
    .sub-fields-box {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        margin-top: 16px;
        transition: all 0.2s ease;
    }
    .sub-fields-box.dimmed {
        opacity: 0.55;
        background: #fcfcfc;
    }

    /* Buttons */
    .att-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 20px;
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

    /* Bottom Action Bar */
    .settings-bottom-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 18px 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
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
</style>
@endpush

@section('content')
<div class="att-wrapper">

    {{-- Top Action & Info Bar --}}
    <div class="att-top-bar">
        <div>
            <h5 class="att-top-title">Davamiyyət və Qlobal Qaydalar</h5>
            <div class="att-top-desc">
                Bütün filiallar üçün vahid geofence radiusu, PIN təhlükəsizliyi, cihaz məhdudiyyəti və gecikmə tənzimləmələri.
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('attendance.branches') }}" class="att-btn att-btn-outline">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Filiallar & QR-lar
            </a>
            <a href="{{ route('attendance.index') }}" class="att-btn att-btn-outline">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                Davamiyyət Jurnalı
            </a>
        </div>
    </div>

    {{-- Cari Konfiqurasiya İcmal Zolağı --}}
    <div class="config-summary-strip">
        <div class="summary-chip">
            <div class="summary-chip-icon emerald">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <div>
                <span class="summary-chip-label">PIN Rejimi</span>
                <span class="summary-chip-val" id="summaryPinText">
                    {{ ($settings['require_pin'] ?? '0') === '1' ? '4 Rəqəmli PIN Tələbi' : 'PIN-siz (Sürətli)' }}
                </span>
            </div>
        </div>
        <div class="summary-chip">
            <div class="summary-chip-icon blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
            </div>
            <div>
                <span class="summary-chip-label">Cihaz Qaydası</span>
                <span class="summary-chip-val" id="summaryDeviceText">
                    {{ ($settings['allow_device_reuse'] ?? '0') === '1' ? 'Məhdudiyyətsiz' : '1 Cihaz = 1 İşçi' }}
                </span>
            </div>
        </div>
        <div class="summary-chip">
            <div class="summary-chip-icon rose">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><line x1="2" y1="12" x2="22" y2="12"></line></svg>
            </div>
            <div>
                <span class="summary-chip-label">Vahid Geofence</span>
                <span class="summary-chip-val" id="summaryRadiusText">{{ $settings['default_radius_meters'] ?? '100' }} metr</span>
            </div>
        </div>
        <div class="summary-chip">
            <div class="summary-chip-icon amber">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div>
                <span class="summary-chip-label">Gecikmə İzləməsi</span>
                <span class="summary-chip-val" id="summaryLateText">
                    {{ ($settings['track_late_arrivals'] ?? '0') === '1' ? 'Aktiv (Dinamik)' : 'Deaktiv' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="att-alert-success">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <strong>Uğurlu!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" onclick="this.closest('.att-alert-success').remove()" aria-label="Bağla" style="cursor: pointer;"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('attendance.settings.update') }}" id="settingsForm">
        @csrf

        {{-- 1. PIN TƏLƏBİ SEÇİMİ --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-emerald">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                </div>
                <div>
                    <h5 class="settings-title">PIN Kod Tələbi (Aktiv / Deaktiv)</h5>
                    <p class="settings-desc">İşçilər filialdakı QR kodu oxudarkən siyahıdan adlarını seçdikdən sonra 4 rəqəmli fərdi PIN daxil etsinlər, yoxsa bir toxunuşla qeydiyyat aparsınlar?</p>
                </div>
            </div>

            <div class="option-card-group">
                {{-- PIN-siz Rejim --}}
                <label class="option-card {{ ($settings['require_pin'] ?? '0') === '0' ? 'active' : '' }}" onclick="selectOption(this)">
                    <input type="radio" name="require_pin" value="0" {{ ($settings['require_pin'] ?? '0') === '0' ? 'checked' : '' }}>
                    <div class="custom-radio-indicator">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-header-row">
                            <span class="opt-title">PIN-siz Rejim (Sürətli Giriş)</span>
                            <span class="opt-badge opt-badge-fast">Sürətli</span>
                        </div>
                        <p class="opt-desc">İşçi filialın QR kodunu oxudur, siyahıdan yalnız adını seçir və bir toxunuşla "İşə Giriş" və ya "İşdən Çıxış" edir. Əlavə PIN kodu daxil etməyə ehtiyac qalmır.</p>
                    </div>
                </label>

                {{-- 4 Rəqəmli PIN Tələbi --}}
                <label class="option-card {{ ($settings['require_pin'] ?? '0') === '1' ? 'active' : '' }}" onclick="selectOption(this)">
                    <input type="radio" name="require_pin" value="1" {{ ($settings['require_pin'] ?? '0') === '1' ? 'checked' : '' }}>
                    <div class="custom-radio-indicator">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-header-row">
                            <span class="opt-title">4 Rəqəmli PIN Tələb Olunsun</span>
                            <span class="opt-badge opt-badge-recommended">Yüksək Təhlükəsizlik</span>
                        </div>
                        <p class="opt-desc">İşçi adını seçdikdən sonra mütləq ona təyin edilmiş 4 rəqəmli fərdi PIN kodu yazmalıdır. Heç kim başqasının adını səhvən və ya qəsdən seçərək saxta qeydiyyat apara bilməz.</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- 2. CİHAZ LİMİTİ VƏ SAXTAKARLIQ ƏLEYHİNƏ QAYDA --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-blue">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                </div>
                <div>
                    <h5 class="settings-title">Cihaz Limiti & Saxtakarlıq Əleyhinə Qayda</h5>
                    <p class="settings-desc">Bir işçi digər iş yoldaşının yerinə öz telefonundan qeydiyyat vura bilərmi? (Smartfon Barmaq İzi Təhlili)</p>
                </div>
            </div>

            <div class="option-card-group">
                {{-- 1 Cihaz = 1 İşçi --}}
                <label class="option-card {{ ($settings['allow_device_reuse'] ?? '0') === '0' ? 'active' : '' }}" onclick="selectOption(this)">
                    <input type="radio" name="allow_device_reuse" value="0" {{ ($settings['allow_device_reuse'] ?? '0') === '0' ? 'checked' : '' }}>
                    <div class="custom-radio-indicator">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-header-row">
                            <span class="opt-title">1 Cihaz = 1 İşçi (Tək Cihaz Bağlaması)</span>
                            <span class="opt-badge opt-badge-recommended">Tövsiyə Olunan</span>
                        </div>
                        <p class="opt-desc">Hər bir telefondan eyni gün ərzində yalnız tək bir işçi qeydiyyat apara bilər. Bir nəfər başqa iş yoldaşının adından öz telefonundan vurmağa cəhd etdikdə sistem dərhal bloklayır və "Şübhəli Cəhdlər" jurnalına yazır.</p>
                    </div>
                </label>

                {{-- Cihaz Məhdudiyyətsiz --}}
                <label class="option-card {{ ($settings['allow_device_reuse'] ?? '0') === '1' ? 'active' : '' }}" onclick="selectOption(this)">
                    <input type="radio" name="allow_device_reuse" value="1" {{ ($settings['allow_device_reuse'] ?? '0') === '1' ? 'checked' : '' }}>
                    <div class="custom-radio-indicator">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-header-row">
                            <span class="opt-title">Cihaz Məhdudiyyəti Olmasın (Çoxsaylı İşçi)</span>
                            <span class="opt-badge opt-badge-neutral">Sərbəst</span>
                        </div>
                        <p class="opt-desc">Eyni telefondan və ya filialın ümumi planşetindən bir neçə işçi növbə ilə öz adlarını seçib vura bilər (Məsələn: Filialda ortaq ümumi tablet quraşdırıldıqda uyğundur).</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- 3. MƏKAN VƏ VAHİD GPS RADIUS PARAMETRİ --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-rose">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <div>
                    <h5 class="settings-title">Bütün Filiallar Üçün Vahid Məkan (GPS Radius) Parametri</h5>
                    <p class="settings-desc">Burada təyin etdiyiniz geofence radiusu istisnasız <strong>bütün filiallara</strong> şamil olunur. Filiallar bölməsində hər filial üçün tək-tək radius yazmağa ehtiyac qalmır.</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-7">
                    <label class="form-label small fw-bold text-dark mb-2">Vahid Filial Radiusu (Metrlə)</label>
                    <div class="att-input-group">
                        <div class="att-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><line x1="2" y1="12" x2="22" y2="12"></line></svg>
                        </div>
                        <input type="number" min="20" max="1000" step="5" id="radiusInput" name="default_radius_meters" class="att-input-field" value="{{ $settings['default_radius_meters'] ?? '100' }}" required>
                        <span class="att-input-addon">metr</span>
                    </div>

                    {{-- Tez Seçim Düymələri (Presets) --}}
                    <div class="preset-chips-row">
                        <span class="preset-label">Tez seçim:</span>
                        <span class="preset-chip {{ ($settings['default_radius_meters'] ?? '100') == 50 ? 'active' : '' }}" onclick="setRadiusPreset(50)">50 m</span>
                        <span class="preset-chip {{ ($settings['default_radius_meters'] ?? '100') == 100 ? 'active' : '' }}" onclick="setRadiusPreset(100)">100 m (Tövsiyə)</span>
                        <span class="preset-chip {{ ($settings['default_radius_meters'] ?? '100') == 200 ? 'active' : '' }}" onclick="setRadiusPreset(200)">200 m</span>
                        <span class="preset-chip {{ ($settings['default_radius_meters'] ?? '100') == 500 ? 'active' : '' }}" onclick="setRadiusPreset(500)">500 m</span>
                    </div>
                </div>
            </div>

            <div class="att-info-box">
                <div class="att-info-box-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                </div>
                <div>
                    <strong>Qeyd:</strong> Mobil cihazlarda GPS dəqiqliyi binaların içində və ticarət mərkəzlərində 15–40 metr dəyişə bilər. <strong>100 metr</strong> seçilməsi filial daxilində və giriş qapısında işçilərin problemsiz qeydiyyatı üçün ən optimal parametrdir. İşçi bu radiusdan kənarda olarsa cəhd bloklanır və jurnalda şübhəli kimi qeyd edilir.
                </div>
            </div>
        </div>

        {{-- 4. İŞ VAXTI VƏ GECİKMƏ PARAMETRLƏRİ --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-amber">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div>
                    <h5 class="settings-title">İş Qrafiki və Gecikmə İzləməsi (Aktiv / Deaktiv)</h5>
                    <p class="settings-desc">İşçilərin işə gecikib-gecikmədiyini ölçmək üçündür. Aktiv edildikdə filialın fərdi qrafikinə uyğun gecikmə dəqiqələri avtomatik hesablanır.</p>
                </div>
            </div>

            <div class="option-card-group">
                {{-- Gecikmə Deaktiv --}}
                <label class="option-card {{ ($settings['track_late_arrivals'] ?? '0') === '0' ? 'active' : '' }}" onclick="selectOption(this); toggleLateArrivalFields(false);">
                    <input type="radio" name="track_late_arrivals" value="0" {{ ($settings['track_late_arrivals'] ?? '0') === '0' ? 'checked' : '' }}>
                    <div class="custom-radio-indicator">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-header-row">
                            <span class="opt-title">Gecikmə İzləməsi DEAKTİV Olsun</span>
                            <span class="opt-badge opt-badge-neutral">Standart</span>
                        </div>
                        <p class="opt-desc">İşçilərin saat neçədə gəlməsindən asılı olmayaraq bütün girişlər standart qəbul edilir, heç bir gecikmə hesablanmır və cəza qaydası tətbiq olunmur.</p>
                    </div>
                </label>

                {{-- Gecikmə Aktiv --}}
                <label class="option-card {{ ($settings['track_late_arrivals'] ?? '0') === '1' ? 'active' : '' }}" onclick="selectOption(this); toggleLateArrivalFields(true);">
                    <input type="radio" name="track_late_arrivals" value="1" {{ ($settings['track_late_arrivals'] ?? '0') === '1' ? 'checked' : '' }}>
                    <div class="custom-radio-indicator">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-header-row">
                            <span class="opt-title">Gecikmə İzləməsi AKTİV Olsun (Dinamik Filial Qrafiki ilə)</span>
                            <span class="opt-badge opt-badge-recommended">Dinamik İzləmə</span>
                        </div>
                        <p class="opt-desc">Gecikmə avtomatik olaraq işçinin qeydiyyatdan keçdiyi <strong>filialın həftəiçi və ya həftəsonu qrafikinə</strong> əsasən hesablanır və Davamiyyət Jurnalında xüsusi bildirişlə (məs: "Gecikmə: 25 dəq") qeyd olunur.</p>
                    </div>
                </label>
            </div>

            {{-- Əlavə Tənzimləmə Qutusu (Saat və Tolerantlıq) --}}
            <div class="sub-fields-box {{ ($settings['track_late_arrivals'] ?? '0') === '0' ? 'dimmed' : '' }}" id="lateFieldsBox">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="me-1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            Defolt / Ehtiyat Başlama Saatı
                        </label>
                        <input type="time" name="work_start_time" class="att-input-standard" value="{{ $settings['work_start_time'] ?? '09:00' }}" required>
                        <div class="small text-muted mt-1" style="font-size: 11.5px;">Əsasən filialın öz iş qrafiki götürülür. Bu saat yalnız qrafiki təyin edilməmiş filiallar üçün ehtiyat (fallback) kimi istifadə edilir.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="me-1"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            Gecikmə Tolerantlığı (Dəqiqə)
                        </label>
                        <div class="att-input-group">
                            <input type="number" min="0" max="120" step="5" id="toleranceInput" name="late_tolerance_minutes" class="att-input-field ps-3" value="{{ $settings['late_tolerance_minutes'] ?? '15' }}" required>
                            <span class="att-input-addon">dəqiqə</span>
                        </div>
                        <div class="preset-chips-row mt-2">
                            <span class="preset-label">Presetlər:</span>
                            <span class="preset-chip {{ ($settings['late_tolerance_minutes'] ?? '15') == 5 ? 'active' : '' }}" onclick="setTolerancePreset(5)">5 dəq</span>
                            <span class="preset-chip {{ ($settings['late_tolerance_minutes'] ?? '15') == 10 ? 'active' : '' }}" onclick="setTolerancePreset(10)">10 dəq</span>
                            <span class="preset-chip {{ ($settings['late_tolerance_minutes'] ?? '15') == 15 ? 'active' : '' }}" onclick="setTolerancePreset(15)">15 dəq (Tövsiyə)</span>
                            <span class="preset-chip {{ ($settings['late_tolerance_minutes'] ?? '15') == 30 ? 'active' : '' }}" onclick="setTolerancePreset(30)">30 dəq</span>
                        </div>
                        <div class="small text-muted mt-1" style="font-size: 11.5px;">Məsələn: Filial saat 14:00-da açılırsa + 15 dəq = 14:15-dək gələnlər vaxtında gəlmiş hesab olunur.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Aşağı Təsdiq Paneli --}}
        <div class="settings-bottom-bar">
            <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 13px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Yadda saxlanılan parametrlər dərhal bütün filiallarda və QR skan ekranında qüvvəyə minir.</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('attendance.index') }}" class="att-btn att-btn-outline">
                    Ləğv et
                </a>
                <button type="submit" class="att-btn att-btn-primary shadow-sm" id="btnSubmitSettings">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Tənzimləmələri Yadda Saxla
                </button>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    // Radio kartlarını seçərkən animasiya və aktiv vəziyyət
    function selectOption(cardElement) {
        const group = cardElement.closest('.option-card-group');
        if (!group) return;

        group.querySelectorAll('.option-card').forEach(c => c.classList.remove('active'));
        cardElement.classList.add('active');

        const radio = cardElement.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            // İcmal zolağını dərhal vizual yeniləmək
            updateLiveSummary(radio.name, radio.value);
        }
    }

    // İcmal zolağındakı dəyərləri real-time yeniləmək
    function updateLiveSummary(name, value) {
        if (name === 'require_pin') {
            const el = document.getElementById('summaryPinText');
            if (el) el.textContent = value === '1' ? '4 Rəqəmli PIN Tələbi' : 'PIN-siz (Sürətli)';
        } else if (name === 'allow_device_reuse') {
            const el = document.getElementById('summaryDeviceText');
            if (el) el.textContent = value === '1' ? 'Məhdudiyyətsiz' : '1 Cihaz = 1 İşçi';
        } else if (name === 'track_late_arrivals') {
            const el = document.getElementById('summaryLateText');
            if (el) el.textContent = value === '1' ? 'Aktiv (Dinamik)' : 'Deaktiv';
        }
    }

    // Radius üçün Preset Düymələri
    function setRadiusPreset(val) {
        const input = document.getElementById('radiusInput');
        if (input) {
            input.value = val;
            const summary = document.getElementById('summaryRadiusText');
            if (summary) summary.textContent = val + ' metr';
        }

        // Preset çiplərinin aktiv sinifini yeniləmək
        event.target.closest('.preset-chips-row').querySelectorAll('.preset-chip').forEach(c => c.classList.remove('active'));
        event.target.classList.add('active');
    }

    // Tolerantlıq üçün Preset Düymələri
    function setTolerancePreset(val) {
        const input = document.getElementById('toleranceInput');
        if (input) {
            input.value = val;
        }

        event.target.closest('.preset-chips-row').querySelectorAll('.preset-chip').forEach(c => c.classList.remove('active'));
        event.target.classList.add('active');
    }

    // Radius inputu əllə yazıldıqda icmal zolağını yeniləmək
    document.getElementById('radiusInput')?.addEventListener('input', function() {
        const summary = document.getElementById('summaryRadiusText');
        if (summary) summary.textContent = (this.value || '0') + ' metr';
    });

    // Gecikmə sahələrini aktiv/deaktiv göstərmək
    function toggleLateArrivalFields(isActive) {
        const box = document.getElementById('lateFieldsBox');
        if (box) {
            if (isActive) {
                box.classList.remove('dimmed');
            } else {
                box.classList.add('dimmed');
            }
        }
    }

    // Yadda saxla düyməsinə klik zamanı feedback
    document.getElementById('settingsForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitSettings');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="spinner-border spinner-border-sm me-1"></svg> Saxlanılır...`;
        }
    });
</script>
@endpush
