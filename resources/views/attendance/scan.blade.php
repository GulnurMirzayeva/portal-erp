<!doctype html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $branch->name }} | İşə Giriş-Çıxış</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-base: #0b0f19;
            --bg-card: rgba(18, 24, 38, 0.85);
            --bg-card-hover: rgba(28, 36, 56, 0.95);
            --border-color: rgba(255, 255, 255, 0.08);
            --primary: #3b82f6;
            --primary-glow: rgba(59, 130, 246, 0.35);
            --success: #10b981;
            --success-glow: rgba(16, 185, 129, 0.35);
            --danger: #ef4444;
            --danger-glow: rgba(239, 68, 68, 0.35);
            --warning: #f59e0b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-base);
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.12) 0px, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            padding: 16px;
        }

        .container {
            width: 100%;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding-bottom: 30px;
        }

        /* Header */
        .header-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(59, 130, 246, 0.12);
            color: #60a5fa;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 30px;
            border: 1px solid rgba(59, 130, 246, 0.25);
            margin-bottom: 12px;
        }

        .branch-title {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .clock-display {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* GPS Status Banner */
        .gps-banner {
            border-radius: 16px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .gps-banner.loading {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fbbf24;
        }

        .gps-banner.success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .gps-banner.danger {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .pulse-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 10px currentColor;
            animation: pulse 1.5s infinite;
            flex-shrink: 0;
        }

        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.7; }
        }

        /* Section Title */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 6px;
            padding: 0 4px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
        }

        .toggle-btn {
            background: none;
            border: none;
            color: #60a5fa;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
        }

        /* Workers List */
        .workers-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 380px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .workers-grid::-webkit-scrollbar {
            width: 5px;
        }
        .workers-grid::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }

        .worker-card {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: 16px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .worker-card:hover {
            background: var(--bg-card-hover);
            border-color: rgba(59, 130, 246, 0.4);
        }

        .worker-card.selected {
            background: rgba(59, 130, 246, 0.15);
            border-color: #3b82f6;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.25);
        }

        .worker-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-initial {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .worker-name {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .worker-phone {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .action-tag {
            font-size: 11px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tag-in {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .tag-out {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* PIN Pad Box */
        .pin-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 18px;
            text-align: center;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pin-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .pin-dots {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .pin-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.25);
            transition: all 0.2s;
        }

        .pin-dot.filled {
            background: #3b82f6;
            border-color: #3b82f6;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
            transform: scale(1.15);
        }

        .keypad-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            max-width: 280px;
            margin: 0 auto;
        }

        .key-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            padding: 12px 0;
            cursor: pointer;
            transition: all 0.15s;
        }

        .key-btn:active {
            background: rgba(59, 130, 246, 0.3);
            transform: scale(0.95);
        }

        /* Action Buttons */
        .submit-section {
            margin-top: 4px;
        }

        .btn-submit {
            width: 100%;
            padding: 16px 20px;
            border-radius: 16px;
            border: none;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            transition: all 0.2s;
        }

        .btn-submit.btn-in {
            background: linear-gradient(135deg, #059669, #10b981);
            box-shadow: 0 8px 25px var(--success-glow);
        }

        .btn-submit.btn-out {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            box-shadow: 0 8px 25px var(--danger-glow);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            filter: grayscale(1);
        }

        /* Result Modal / Overlay */
        .result-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 999;
        }

        .result-card {
            background: #131c2e;
            border: 1.5px solid var(--border-color);
            border-radius: 24px;
            padding: 28px 24px;
            text-align: center;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6);
            animation: scaleIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes scaleIn {
            from { transform: scale(0.85); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .result-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
        }

        .result-icon.success {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 2px solid #10b981;
        }

        .result-icon.danger {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 2px solid #ef4444;
        }

        .result-title {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
        }

        .result-desc {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .btn-close-modal {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
        }

        /* Recognized Worker Card */
        .recognized-card {
            background: linear-gradient(145deg, rgba(18, 26, 44, 0.95), rgba(12, 17, 30, 0.98));
            backdrop-filter: blur(14px);
            border: 1.5px solid rgba(59, 130, 246, 0.35);
            border-radius: 22px;
            padding: 22px 20px;
            box-shadow: 0 14px 40px rgba(0, 0, 0, 0.5), 0 0 30px rgba(59, 130, 246, 0.15);
            display: flex;
            flex-direction: column;
            gap: 16px;
            animation: fadeInSlide 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInSlide {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .rec-header-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .rec-substitute-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #fbbf24;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 20px;
        }

        .rec-profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 12px;
        }

        .rec-avatar {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
            margin: 0 auto;
        }

        .rec-details {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .rec-greeting {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .rec-name {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.25;
            margin-top: 4px;
            text-align: center;
            word-break: break-word;
        }

        .rec-branch-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 12px;
            color: #93c5fd;
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.25);
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
        }

        .rec-notice-banner {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.28);
            border-radius: 14px;
            padding: 10px 14px;
            font-size: 12px;
            line-height: 1.45;
            color: #fde68a;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .rec-mismatch-banner {
            background: rgba(239, 68, 68, 0.12);
            border: 1.5px solid rgba(239, 68, 68, 0.35);
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            text-align: left;
            animation: fadeInSlide 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .rec-mismatch-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #f87171;
            line-height: 1.3;
        }

        .rec-mismatch-desc {
            font-size: 12px;
            color: #e2e8f0;
            margin-top: 3px;
            line-height: 1.45;
        }

        .rec-notice-banner svg {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .rec-status-indicator {
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            font-weight: 600;
        }

        .rec-status-indicator.ready-in {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .rec-status-indicator.ready-out {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .rec-status-title {
            font-weight: 700;
            font-size: 14px;
            color: #fff;
        }

        .rec-status-desc {
            font-size: 12px;
            opacity: 0.85;
            margin-top: 2px;
        }
    </style>
</head>
<body>

<div class="container">
    {{-- Header --}}
    <div class="header-card">
        <h1 class="branch-title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            {{ $branch->name }}
        </h1>
        <div class="clock-display" id="liveClock">Yüklənir...</div>
    </div>

    {{-- GPS Status Banner --}}
    <div class="gps-banner loading" id="gpsBanner">
        <div class="pulse-dot"></div>
        <span id="gpsText">GPS məkanınız yoxlanılır...</span>
    </div>

    {{-- Recognized Worker Card (Cihaz tanındıqda avtomatik görünür) --}}
    <div class="recognized-card" id="recognizedCard" style="display: none;">
        <div class="rec-header-row" id="recHeaderRow" style="display: none;">
            <div class="rec-substitute-badge" id="recSubstituteBadge" style="display: none;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                <span>Əvəzedici İşçi</span>
            </div>
        </div>

        <div class="rec-profile">
            <div class="rec-avatar" id="recAvatar">--</div>
            <div class="rec-details">
                <div class="rec-greeting">Xoş gəlmisiniz,</div>
                <div class="rec-name" id="recName">Yüklənir...</div>
                <div class="rec-branch-pill" id="recBranchPill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span id="recBranchName">Əsas Filial</span>
                </div>
            </div>
        </div>

        {{-- Fərqli filialda açıq giriş xəbərdarlıq banneri --}}
        <div class="rec-mismatch-banner" id="recMismatchBanner" style="display: none;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.2" style="flex-shrink:0; margin-top:2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div>
                <div class="rec-mismatch-title">Fərqli Filialda Açıq Girişiniz Var!</div>
                <div class="rec-mismatch-desc" id="recMismatchText">
                    Siz hal-hazırda başqa filialda işə daxil olmusunuz. İşdən çıxışı yalnız giriş etdiyiniz filialda təsdiqləyə bilərsiniz.
                </div>
            </div>
        </div>

        <div class="rec-notice-banner" id="recSubstituteNotice" style="display: none;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div>
                Hazırda <strong>{{ $branch->name }}</strong> filialındasınız. Davamiyyətiniz bu filiala əvəzedici olaraq yazılacaq.
            </div>
        </div>

        <div class="rec-status-indicator ready-in" id="recStatusIndicator">
            <div class="pulse-dot" id="recStatusDot"></div>
            <div>
                <div class="rec-status-title" id="recStatusTitle">İşə giriş üçün hazırdır</div>
                <div class="rec-status-desc" id="recStatusDesc">Girişi qeydə almaq üçün aşağıdakı düyməni sıxın</div>
            </div>
        </div>
    </div>

    {{-- Workers Section (Yalnız cihaz ilk dəfə təhkim olunmamış halda görünür) --}}
    <div id="workersSelectionSection">
        <div class="section-header">
            <span class="section-title">İşçini Seçin</span>
            <button type="button" class="toggle-btn" id="toggleWorkersBtn">Digər filialdan əvəzedici</button>
        </div>

        <div class="workers-grid" id="branchWorkersList">
            @forelse($workers as $w)
                @php
                    $nextAct = $w->next_action;
                    $initials = mb_substr($w->name, 0, 1);
                    $checkInLog = $w->currentCheckInLog();
                    $activeBranchId = $checkInLog?->branch_id;
                    $activeBranchName = $checkInLog?->branch_name;
                @endphp
                <div class="worker-card" data-worker-id="{{ $w->id }}" data-worker-name="{{ $w->name }}" data-next-action="{{ $nextAct }}" data-active-branch-id="{{ $activeBranchId }}" data-active-branch-name="{{ $activeBranchName }}">
                    <div class="worker-info">
                        <div class="avatar-initial">{{ $initials }}</div>
                        <div>
                            <div class="worker-name">{{ $w->name }}</div>
                            <div class="worker-phone">{{ $w->phone ?: 'Əlaqə nömrəsi yoxdur' }}</div>
                        </div>
                    </div>
                    <div>
                        <span class="action-tag {{ $nextAct === 'check_in' ? 'tag-in' : 'tag-out' }}">
                            {{ $nextAct === 'check_in' ? 'GİRİŞ' : 'ÇIXIŞ' }}
                        </span>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 14px;">
                    Bu filiala təhkim edilmiş işçi tapılmadı. "Digər filialdan əvəzedici" düyməsindən istifadə edin.
                </div>
            @endforelse
        </div>

        <div class="workers-grid" id="allWorkersList" style="display: none;">
            @foreach($allWorkers as $w)
                @php
                    $nextAct = $w->next_action;
                    $initials = mb_substr($w->name, 0, 1);
                    $checkInLog = $w->currentCheckInLog();
                    $activeBranchId = $checkInLog?->branch_id;
                    $activeBranchName = $checkInLog?->branch_name;
                @endphp
                <div class="worker-card" data-worker-id="{{ $w->id }}" data-worker-name="{{ $w->name }}" data-next-action="{{ $nextAct }}" data-active-branch-id="{{ $activeBranchId }}" data-active-branch-name="{{ $activeBranchName }}">
                    <div class="worker-info">
                        <div class="avatar-initial">{{ $initials }}</div>
                        <div>
                            <div class="worker-name">{{ $w->name }}</div>
                            <div class="worker-phone">{{ $w->branch?->name ?: 'Ümumi' }}</div>
                        </div>
                    </div>
                    <div>
                        <span class="action-tag {{ $nextAct === 'check_in' ? 'tag-in' : 'tag-out' }}">
                            {{ $nextAct === 'check_in' ? 'GİRİŞ' : 'ÇIXIŞ' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- PIN Pad Box (Görünür əgər PIN tələb olunursa) --}}
    @if($isPinRequired)
    <div class="pin-container" id="pinBox">
        <div class="pin-title">4 Rəqəmli Fərdi PIN Kodunuz</div>
        <div class="pin-dots">
            <div class="pin-dot" id="dot1"></div>
            <div class="pin-dot" id="dot2"></div>
            <div class="pin-dot" id="dot3"></div>
            <div class="pin-dot" id="dot4"></div>
        </div>
        <div class="keypad-grid">
            <button type="button" class="key-btn" onclick="pressKey('1')">1</button>
            <button type="button" class="key-btn" onclick="pressKey('2')">2</button>
            <button type="button" class="key-btn" onclick="pressKey('3')">3</button>
            <button type="button" class="key-btn" onclick="pressKey('4')">4</button>
            <button type="button" class="key-btn" onclick="pressKey('5')">5</button>
            <button type="button" class="key-btn" onclick="pressKey('6')">6</button>
            <button type="button" class="key-btn" onclick="pressKey('7')">7</button>
            <button type="button" class="key-btn" onclick="pressKey('8')">8</button>
            <button type="button" class="key-btn" onclick="pressKey('9')">9</button>
            <button type="button" class="key-btn" style="color: #f87171;" onclick="clearPin()">C</button>
            <button type="button" class="key-btn" onclick="pressKey('0')">0</button>
            <button type="button" class="key-btn" onclick="backspacePin()">⌫</button>
        </div>
    </div>
    @endif

    {{-- Action Button --}}
    <div class="submit-section">
        <button type="button" class="btn-submit btn-in" id="submitBtn" disabled>
            <span id="btnIcon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </span>
            <span id="btnLabel">İşçini Seçin</span>
        </button>
    </div>
</div>

{{-- Result Modal --}}
<div class="result-overlay" id="resultModal">
    <div class="result-card">
        <div class="result-icon success" id="modalIcon">✓</div>
        <h3 class="result-title" id="modalTitle">Uğurlu!</h3>
        <p class="result-desc" id="modalDesc">Məlumat qeydə alındı.</p>
        <button type="button" class="btn-close-modal" id="modalCloseBtn">Bağla</button>
    </div>
</div>

<script>
    // 1. Konfiqurasiya və Məlumatlar
    const branchToken = "{{ $branch->qr_token }}";
    const currentBranchId = {{ $branch->id }};
    const currentBranchName = "{{ addslashes($branch->name) }}";
    const branchLat = {{ $branch->latitude ?: 'null' }};
    const branchLng = {{ $branch->longitude ?: 'null' }};
    const branchRadius = {{ $allowedRadius ?? ($branch->radius_meters ?: 100) }};
    const isPinRequired = {{ $isPinRequired ? 'true' : 'false' }};
    const submitUrl = "{{ route('attendance.scan.submit', ['token' => $branch->qr_token]) }}";
    const lookupUrl = "{{ route('attendance.scan.lookup-device', ['token' => $branch->qr_token]) }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const boundWorkers = @json($boundWorkers ?? []);
    let recognizedWorker = @json($recognizedWorker ?? null);

    // 2. DOM Elementləri (Ən başda təyin edilir)
    const submitBtn = document.getElementById('submitBtn');
    const btnLabel = document.getElementById('btnLabel');
    const gpsBanner = document.getElementById('gpsBanner');
    const gpsText = document.getElementById('gpsText');
    const pinBox = document.getElementById('pinBox');
    const recognizedCard = document.getElementById('recognizedCard');
    const workersSelectionSection = document.getElementById('workersSelectionSection');
    const resultModal = document.getElementById('resultModal');
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalDesc = document.getElementById('modalDesc');
    const modalCloseBtn = document.getElementById('modalCloseBtn');

    // 3. Cari Vəziyyət
    let currentLat = null;
    let currentLng = null;
    let selectedWorkerId = null;
    let selectedWorkerName = null;
    let selectedNextAction = 'check_in';
    let selectedWorkerActiveBranchId = null;
    let selectedWorkerActiveBranchName = null;
    let enteredPin = "";
    let isSubmitting = false;

    // 4. Device UUID
    function getDeviceUuid() {
        let uuid = localStorage.getItem('pg_att_device_uuid');
        if (!uuid) {
            uuid = 'dev_' + (crypto.randomUUID ? crypto.randomUUID() : (Date.now().toString(36) + Math.random().toString(36).substr(2, 9)));
            localStorage.setItem('pg_att_device_uuid', uuid);
        }
        document.cookie = "pg_att_device_uuid=" + encodeURIComponent(uuid) + "; path=/; max-age=" + (365*24*60*60) + "; SameSite=Lax";
        return uuid;
    }
    const deviceUuid = getDeviceUuid();

    // 5. Device Model
    function getDeviceModel() {
        const ua = navigator.userAgent;
        if (/iPhone/i.test(ua)) return 'Apple iPhone';
        if (/iPad/i.test(ua)) return 'Apple iPad';
        if (/Android/i.test(ua)) {
            const match = ua.match(/Android\s([0-9.]+);?\s?([^;]+)/);
            return match ? ('Android (' + match[2] + ')') : 'Android Cihaz';
        }
        return 'Web Brauzer';
    }

    // 6. Düymə vəziyyəti yeniləyici
    function updateSubmitButtonState() {
        if (!submitBtn || !btnLabel) return;

        // Fərqli filial yoxlanışı (İşçi başqa filialda giriş edibsə, bu filialdan çıxış edə bilməz)
        const activeBranchId = recognizedWorker ? recognizedWorker.active_checkin_branch_id : selectedWorkerActiveBranchId;
        const activeBranchName = recognizedWorker ? recognizedWorker.active_checkin_branch_name : selectedWorkerActiveBranchName;

        if (activeBranchId && String(activeBranchId) !== String(currentBranchId)) {
            submitBtn.disabled = true;
            submitBtn.className = 'btn-submit';
            submitBtn.style.opacity = '0.55';
            submitBtn.style.cursor = 'not-allowed';
            btnLabel.textContent = 'ÇIXIŞ YALNIZ GİRİŞ FİLİALINDA MÜMKÜNDÜR';
            return;
        }

        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';

        if (!selectedWorkerId) {
            submitBtn.disabled = true;
            btnLabel.textContent = "İşçini Seçin";
            return;
        }

        if (isPinRequired && enteredPin.length < 4) {
            submitBtn.disabled = true;
            btnLabel.textContent = "4 Rəqəmli PIN Kodu Daxil Edin";
            return;
        }

        submitBtn.disabled = false;
        if (selectedNextAction === 'check_in') {
            submitBtn.className = 'btn-submit btn-in';
            btnLabel.textContent = 'İŞƏ GİRİŞİ TƏSDİQLƏ';
        } else {
            submitBtn.className = 'btn-submit btn-out';
            btnLabel.textContent = 'İŞDƏN ÇIXIŞI TƏSDİQLƏ';
        }
    }

    // 7. Recognized Card Status UI
    function updateRecognizedStatusUI(action) {
        const indicator = document.getElementById('recStatusIndicator');
        const dot = document.getElementById('recStatusDot');
        const title = document.getElementById('recStatusTitle');
        const desc = document.getElementById('recStatusDesc');

        if (!indicator) return;

        if (action === 'check_in') {
            indicator.className = 'rec-status-indicator ready-in';
            if (dot) {
                dot.style.background = '#10b981';
                dot.style.boxShadow = '0 0 10px #10b981';
            }
            if (title) title.textContent = 'İşə Giriş Gözlənilir';
            if (desc) desc.textContent = 'İş gününə başlamaq üçün aşağıdakı düyməni sıxın';
        } else {
            indicator.className = 'rec-status-indicator ready-out';
            if (dot) {
                dot.style.background = '#ef4444';
                dot.style.boxShadow = '0 0 10px #ef4444';
            }
            if (title) title.textContent = 'Hal-hazırda İşdəsiniz';
            if (desc) desc.textContent = 'İş gününü tamamlamaq üçün çıxışı təsdiqləyin';
        }
    }

    // 8. Tanınmış İşçi Tətbiqi
    function applyRecognizedWorker(worker) {
        if (!worker) return;
        recognizedWorker = worker;
        selectedWorkerId = worker.id;
        selectedWorkerName = worker.name;
        selectedNextAction = worker.next_action || 'check_in';

        // Profil detalları
        const nameEl = document.getElementById('recName');
        if (nameEl) nameEl.textContent = worker.name;

        const initials = worker.name ? worker.name.trim().charAt(0) : 'U';
        const avatarEl = document.getElementById('recAvatar');
        if (avatarEl) avatarEl.textContent = initials;

        const branchEl = document.getElementById('recBranchName');
        if (branchEl) {
            branchEl.textContent = worker.branch_name ? ('Əsas filial: ' + worker.branch_name) : 'Əsas filial: Təyin edilməyib';
        }

        // Əvəzedici yoxlanışı
        const isSubstitute = worker.branch_id && String(worker.branch_id) !== String(currentBranchId);
        const subRow = document.getElementById('recHeaderRow');
        if (subRow) subRow.style.display = isSubstitute ? 'flex' : 'none';
        const subBadge = document.getElementById('recSubstituteBadge');
        if (subBadge) subBadge.style.display = isSubstitute ? 'inline-flex' : 'none';

        const subNotice = document.getElementById('recSubstituteNotice');
        if (subNotice) subNotice.style.display = isSubstitute ? 'flex' : 'none';

        // Fərqli filialda açıq giriş yoxlanışı
        const hasBranchMismatch = worker.active_checkin_branch_id && String(worker.active_checkin_branch_id) !== String(currentBranchId);
        const mismatchBanner = document.getElementById('recMismatchBanner');
        const mismatchText = document.getElementById('recMismatchText');
        const statusIndicator = document.getElementById('recStatusIndicator');

        if (hasBranchMismatch) {
            if (mismatchBanner) mismatchBanner.style.display = 'flex';
            if (mismatchText) {
                mismatchText.innerHTML = 'Siz hal-hazırda <strong>' + (worker.active_checkin_branch_name || 'başqa') + '</strong> filialında işə giriş etmisiniz. İşdən çıxışı yalnız giriş etdiyiniz <strong>' + (worker.active_checkin_branch_name || '') + '</strong> filialında təsdiqləyə bilərsiniz!';
            }
            if (statusIndicator) statusIndicator.style.display = 'none';

            setTimeout(() => {
                showModal(false, 'Diqqət: Siz hal-hazırda "' + (worker.active_checkin_branch_name || 'başqa') + '" filialında işə giriş etmisiniz!\n\nİşdən çıxışı yalnız giriş etdiyiniz filialın (' + (worker.active_checkin_branch_name || '') + ') QR kodunu oxudaraq təsdiqləyə bilərsiniz.');
            }, 300);
        } else {
            if (mismatchBanner) mismatchBanner.style.display = 'none';
            if (statusIndicator) statusIndicator.style.display = 'flex';
        }

        // Status indikatoru
        updateRecognizedStatusUI(selectedNextAction);

        // Kartları göstər / gizlət
        if (recognizedCard) recognizedCard.style.display = 'flex';
        if (workersSelectionSection) workersSelectionSection.style.display = 'none';

        if (isPinRequired) {
            if (pinBox) pinBox.style.display = 'block';
            clearPin();
        } else {
            if (pinBox) pinBox.style.display = 'none';
        }

        updateSubmitButtonState();
    }

    // 9. Cihaz Tanıma Yoxlanışı
    function checkAutoRecognition() {
        if (recognizedWorker) {
            applyRecognizedWorker(recognizedWorker);
            return;
        }

        if (boundWorkers && boundWorkers[deviceUuid]) {
            applyRecognizedWorker(boundWorkers[deviceUuid]);
            return;
        }

        if (deviceUuid) {
            fetch(lookupUrl + '?device_uuid=' + encodeURIComponent(deviceUuid))
                .then(res => res.json())
                .then(data => {
                    if (data.found && data.worker) {
                        applyRecognizedWorker(data.worker);
                    }
                })
                .catch(() => {});
        }
    }

    // 10. Live Clock
    function updateClock() {
        const now = new Date();
        const days = ['Bazar', 'Bazar ertəsi', 'Çərşənbə axşamı', 'Çərşənbə', 'Cümə axşamı', 'Cümə', 'Şənbə'];
        const day = days[now.getDay()];
        const time = now.toLocaleTimeString('az-AZ', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const clockEl = document.getElementById('liveClock');
        if (clockEl) clockEl.textContent = day + ' • ' + time;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 11. Haversine məsafə hesablayıcı
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return Math.round(R * c);
    }

    // 12. GPS Yoxlanışı
    function checkLocation() {
        if (!navigator.geolocation) {
            if (gpsBanner) gpsBanner.className = 'gps-banner danger';
            if (gpsText) gpsText.textContent = 'Brauzeriniz GPS məkan xidmətini dəstəkləmir.';
            return;
        }

        if (gpsBanner) gpsBanner.className = 'gps-banner loading';
        if (gpsText) gpsText.textContent = 'GPS məkanınız yoxlanılır...';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                currentLat = pos.coords.latitude;
                currentLng = pos.coords.longitude;

                if (branchLat && branchLng) {
                    const dist = calculateDistance(currentLat, currentLng, branchLat, branchLng);
                    if (dist <= branchRadius) {
                        if (gpsBanner) gpsBanner.className = 'gps-banner success';
                        if (gpsText) gpsText.textContent = 'Məkan təsdiqləndi! Filialdan ' + dist + 'm məsafədəsiniz (İcazə: ' + branchRadius + 'm).';
                    } else {
                        if (gpsBanner) gpsBanner.className = 'gps-banner danger';
                        if (gpsText) gpsText.textContent = 'Filialdan kənardasınız! Məsafə: ' + dist + 'm (Maksimal icazə: ' + branchRadius + 'm).';
                    }
                } else {
                    if (gpsBanner) gpsBanner.className = 'gps-banner success';
                    if (gpsText) gpsText.textContent = 'Məkan aktivdir.';
                }
                updateSubmitButtonState();
            },
            (err) => {
                if (gpsBanner) gpsBanner.className = 'gps-banner danger';
                if (gpsText) {
                    if (err.code === 1) {
                        gpsText.textContent = 'Məkan icazəsi verilmədi! Zəhmət olmasa ekrandakı icazəni təsdiqləyin.';
                    } else {
                        gpsText.textContent = 'Məkan koordinatları müəyyən edilmədi. GPS-i açıb yenidən cəhd edin.';
                    }
                }
                updateSubmitButtonState();
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    }

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible' && (!currentLat || !currentLng)) {
            checkLocation();
        }
    });

    window.addEventListener('focus', () => {
        if (!currentLat || !currentLng) {
            checkLocation();
        }
    });

    // 13. Manual İşçi siyahısı seçimi (ilk dəfə üçün)
    function attachWorkerListeners() {
        document.querySelectorAll('.worker-card').forEach(card => {
            card.addEventListener('click', function() {
                if (!currentLat || !currentLng) {
                    checkLocation();
                }

                document.querySelectorAll('.worker-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');

                selectedWorkerId = this.getAttribute('data-worker-id');
                selectedWorkerName = this.getAttribute('data-worker-name');
                selectedNextAction = this.getAttribute('data-next-action') || 'check_in';
                selectedWorkerActiveBranchId = this.getAttribute('data-active-branch-id') || null;
                selectedWorkerActiveBranchName = this.getAttribute('data-active-branch-name') || null;

                if (selectedWorkerActiveBranchId && String(selectedWorkerActiveBranchId) !== String(currentBranchId)) {
                    showModal(false, 'Diqqət: ' + selectedWorkerName + ' hal-hazırda "' + selectedWorkerActiveBranchName + '" filialında işə giriş edib!\n\nİşdən çıxışı yalnız giriş etdiyiniz filialın (' + selectedWorkerActiveBranchName + ') QR kodunu oxudaraq təsdiqləyə bilərsiniz.');
                }

                if (isPinRequired) {
                    if (pinBox) pinBox.style.display = 'block';
                    clearPin();
                }

                updateSubmitButtonState();
            });
        });
    }

    const toggleWorkersBtn = document.getElementById('toggleWorkersBtn');
    if (toggleWorkersBtn) {
        let showingAll = false;
        toggleWorkersBtn.addEventListener('click', () => {
            showingAll = !showingAll;
            const branchList = document.getElementById('branchWorkersList');
            const allList = document.getElementById('allWorkersList');
            if (branchList) branchList.style.display = showingAll ? 'none' : 'flex';
            if (allList) allList.style.display = showingAll ? 'flex' : 'none';
            toggleWorkersBtn.textContent = showingAll ? 'Yalnız bu filialın işçiləri' : 'Digər filialdan əvəzedici';
        });
    }

    // 14. PIN Kod funksiyaları
    function updatePinDots() {
        for (let i = 1; i <= 4; i++) {
            const dot = document.getElementById('dot' + i);
            if (dot) {
                if (enteredPin.length >= i) dot.classList.add('filled');
                else dot.classList.remove('filled');
            }
        }
        updateSubmitButtonState();
    }

    window.pressKey = function(digit) {
        if (enteredPin.length < 4) {
            enteredPin += digit;
            updatePinDots();
        }
    };

    window.backspacePin = function() {
        if (enteredPin.length > 0) {
            enteredPin = enteredPin.slice(0, -1);
            updatePinDots();
        }
    };

    window.clearPin = function() {
        enteredPin = "";
        updatePinDots();
    };

    // 15. Submit Handler
    if (submitBtn) {
        submitBtn.addEventListener('click', function() {
            if (!selectedWorkerId || isSubmitting) return;

            // Fərqli filial bloklaması
            const activeBranchId = recognizedWorker ? recognizedWorker.active_checkin_branch_id : selectedWorkerActiveBranchId;
            const activeBranchName = recognizedWorker ? recognizedWorker.active_checkin_branch_name : selectedWorkerActiveBranchName;

            if (activeBranchId && String(activeBranchId) !== String(currentBranchId)) {
                showModal(false, 'Diqqət: Siz hal-hazırda "' + (activeBranchName || 'başqa') + '" filialında işə giriş etmisiniz!\n\nİşdən çıxışı yalnız giriş etdiyiniz filialda (' + (activeBranchName || '') + ') qeydə ala bilərsiniz.');
                return;
            }

            if (!currentLat || !currentLng) {
                alert('Davamiyyəti təsdiqləmək üçün zəhmət olmasa ekrandakı məkan (GPS) icazəsini təsdiqləyin.');
                checkLocation();
                return;
            }

            isSubmitting = true;
            submitBtn.disabled = true;
            if (btnLabel) btnLabel.textContent = "Yoxlanılır və qeydə alınır...";

            const payload = {
                worker_id: selectedWorkerId,
                pin_code: enteredPin,
                latitude: currentLat,
                longitude: currentLng,
                device_uuid: deviceUuid,
                device_model: getDeviceModel(),
                type: selectedNextAction
            };

            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(async response => {
                const data = await response.json();
                return { ok: response.ok, data };
            })
            .then(({ ok, data }) => {
                isSubmitting = false;
                showModal(ok, data.message || (ok ? 'Uğurlu!' : 'Xəta baş verdi!'), data);
                if (ok) {
                    const newNextAction = data.next_action || (selectedNextAction === 'check_in' ? 'check_out' : 'check_in');

                    if (!recognizedWorker || String(recognizedWorker.id) !== String(selectedWorkerId)) {
                        recognizedWorker = {
                            id: selectedWorkerId,
                            name: selectedWorkerName,
                            branch_id: data.branch_id || currentBranchId,
                            branch_name: data.branch_name || '',
                            next_action: newNextAction,
                            active_checkin_branch_id: (newNextAction === 'check_out' ? currentBranchId : null),
                            active_checkin_branch_name: (newNextAction === 'check_out' ? currentBranchName : null)
                        };
                        boundWorkers[deviceUuid] = recognizedWorker;
                        document.cookie = "pg_att_device_uuid=" + encodeURIComponent(deviceUuid) + "; path=/; max-age=" + (365*24*60*60) + "; SameSite=Lax";
                    } else {
                        recognizedWorker.next_action = newNextAction;
                        recognizedWorker.active_checkin_branch_id = (newNextAction === 'check_out' ? currentBranchId : null);
                        recognizedWorker.active_checkin_branch_name = (newNextAction === 'check_out' ? currentBranchName : null);
                    }

                    selectedNextAction = newNextAction;
                    selectedWorkerActiveBranchId = recognizedWorker.active_checkin_branch_id;
                    selectedWorkerActiveBranchName = recognizedWorker.active_checkin_branch_name;

                    // Tanınmış kart vəziyyətini yeniləyirik
                    applyRecognizedWorker(recognizedWorker);

                    // Siyahıdakı manual kartı da yeniləyirik
                    const card = document.querySelector('.worker-card[data-worker-id="' + selectedWorkerId + '"]');
                    if (card) {
                        card.setAttribute('data-next-action', newNextAction);
                        card.setAttribute('data-active-branch-id', recognizedWorker.active_checkin_branch_id || '');
                        card.setAttribute('data-active-branch-name', recognizedWorker.active_checkin_branch_name || '');
                        const tag = card.querySelector('.action-tag');
                        if (tag) {
                            tag.className = 'action-tag ' + (newNextAction === 'check_in' ? 'tag-in' : 'tag-out');
                            tag.textContent = newNextAction === 'check_in' ? 'GİRİŞ' : 'ÇIXIŞ';
                        }
                    }
                    clearPin();
                } else {
                    submitBtn.disabled = false;
                    updateSubmitButtonState();
                }
            })
            .catch(err => {
                isSubmitting = false;
                submitBtn.disabled = false;
                updateSubmitButtonState();
                showModal(false, 'Şəbəkə və ya server xətası baş verdi. Zəhmət olmasa yenidən cəhd edin.');
            });
        });
    }

    // 16. Modal
    function showModal(isSuccess, message, extraData) {
        if (!resultModal) return;
        if (modalIcon) {
            modalIcon.className = 'result-icon ' + (isSuccess ? 'success' : 'danger');
            modalIcon.textContent = isSuccess ? '✓' : '✕';
        }
        if (modalTitle) {
            modalTitle.textContent = isSuccess ? 'Uğurla Qeydə Alındı!' : 'Diqqət: Xəta!';
        }
        
        let desc = message;
        if (isSuccess && extraData && extraData.time) {
            desc += '\nSaat: ' + extraData.time + ' (' + (extraData.branch_name || '') + ')';
        }
        if (modalDesc) modalDesc.textContent = desc;
        resultModal.style.display = 'flex';
    }

    if (modalCloseBtn && resultModal) {
        modalCloseBtn.addEventListener('click', () => {
            resultModal.style.display = 'none';
        });
    }

    // 17. İcra Başlanğıcı
    checkLocation();
    attachWorkerListeners();
    checkAutoRecognition();
</script>

</body>
</html>
