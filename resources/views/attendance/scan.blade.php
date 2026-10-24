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
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.35);
            color: #60a5fa;
        }

        .gps-banner.prompt {
            background: rgba(245, 158, 11, 0.14);
            border: 1px solid rgba(245, 158, 11, 0.4);
            color: #fbbf24;
        }

        .gps-banner.success {
            background: rgba(16, 185, 129, 0.14);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
        }

        .gps-banner.danger {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .gps-banner-text-box {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
        }

        .gps-banner-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: #ffffff;
            padding: 7px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            box-shadow: 0 2px 10px rgba(16, 185, 129, 0.4);
            animation: pulseBtn 1.8s infinite;
            transition: all 0.2s;
        }
        .gps-banner-btn:active {
            transform: scale(0.96);
        }

        /* GPS Birbaşa İcazə Modalı */
        .gps-prompt-card {
            max-width: 390px;
            padding: 30px 22px;
            text-align: center;
            background: #0f172a;
            border: 1px solid rgba(59, 130, 246, 0.35);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8), 0 0 40px rgba(37, 99, 235, 0.15);
            border-radius: 24px;
        }

        .gps-radar-box {
            position: relative;
            width: 84px;
            height: 84px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gps-radar-pin {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.5);
            z-index: 2;
            position: relative;
        }

        .gps-radar-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid rgba(59, 130, 246, 0.6);
            animation: radarRipple 2s ease-out infinite;
            pointer-events: none;
        }
        .gps-radar-ring.ring-delay {
            animation-delay: 1s;
        }

        @keyframes radarRipple {
            0% { transform: scale(0.6); opacity: 1; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        .btn-activate-gps {
            width: 100%;
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: #ffffff;
            padding: 15px 20px;
            border-radius: 16px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
            animation: pulseBtn 2s infinite;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-activate-gps:active {
            transform: scale(0.97);
        }
        .btn-activate-gps.loading {
            background: #2563eb;
            pointer-events: none;
            opacity: 0.85;
            animation: none;
        }

        .btn-guide-link {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 12px;
            margin-top: 14px;
            cursor: pointer;
            text-decoration: underline;
            transition: color 0.2s;
            display: inline-block;
        }
        .btn-guide-link:hover {
            color: #e2e8f0;
        }

        @keyframes pulseBtn {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* GPS Guide Modal Styles */
        .gps-guide-card {
            max-width: 440px;
            text-align: left;
            padding: 24px;
        }
        .gps-os-tabs {
            display: flex;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            padding: 4px;
            border-radius: 12px;
            gap: 6px;
            margin-bottom: 16px;
        }
        .gps-os-tab {
            flex: 1;
            padding: 9px 12px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 9px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .gps-os-tab.active {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .gps-guide-step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 13px;
            color: #e2e8f0;
            line-height: 1.45;
        }
        .gps-step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.25);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .gps-guide-note {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.45;
            margin-top: 14px;
        }
        .btn-android-intent {
            width: 100%;
            margin-top: 12px;
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.4);
            color: #93c5fd;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }
        .btn-android-intent:hover {
            background: rgba(59, 130, 246, 0.25);
        }
        .btn-check-gps-now {
            width: 100%;
            margin-top: 18px;
            background: #10b981;
            border: none;
            color: #ffffff;
            padding: 13px 20px;
            border-radius: 14px;
            font-size: 14.5px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.35);
            transition: all 0.2s;
        }
        .btn-check-gps-now:hover {
            background: #059669;
            transform: translateY(-1px);
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
        <div class="gps-banner-text-box">
            <div id="gpsText">📍 GPS məkanınız yoxlanılır...</div>
        </div>
        <button type="button" class="gps-banner-btn" id="gpsBannerBtn" style="display: none;" onclick="requestLocationExplicitly()">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
            <span>İcazə Ver</span>
        </button>
    </div>

    {{-- Workers Section --}}
    <div class="section-header">
        <span class="section-title">İşçini Seçin</span>
        <button type="button" class="toggle-btn" id="toggleWorkersBtn">Digər filialdan əvəzedici</button>
    </div>

    <div class="workers-grid" id="branchWorkersList">
        @forelse($workers as $w)
            @php
                $nextAct = $w->next_action;
                $initials = mb_substr($w->name, 0, 1);
            @endphp
            <div class="worker-card" data-worker-id="{{ $w->id }}" data-worker-name="{{ $w->name }}" data-next-action="{{ $nextAct }}">
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
            @endphp
            <div class="worker-card" data-worker-id="{{ $w->id }}" data-worker-name="{{ $w->name }}" data-next-action="{{ $nextAct }}">
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

{{-- GPS Birbaşa İcazə Modalı (Aktiv Alert) --}}
<div class="result-overlay" id="gpsPromptModal" style="display: none;">
    <div class="result-card gps-prompt-card">
        <div class="gps-radar-box">
            <div class="gps-radar-ring"></div>
            <div class="gps-radar-ring ring-delay"></div>
            <div class="gps-radar-pin">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
            </div>
        </div>

        <h3 class="result-title" style="margin-top: 14px; font-size: 19px;">Məkan İcazəsi Tələb Olunur</h3>
        <p class="result-desc" id="gpsPromptDesc" style="margin-bottom: 22px; font-size: 13.5px; color: #94a3b8;">
            Filialda olduğunuzu təsdiqləmək üçün zəhmət olmasa GPS məkan icazəsini aktivləşdirin.
        </p>

        <button type="button" class="btn-activate-gps" id="btnActivateGps" onclick="requestLocationExplicitly()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
            <span id="btnActivateGpsText">Məkanı Aktivləşdir (İcazə Ver)</span>
        </button>

        <div>
            <button type="button" class="btn-guide-link" onclick="openGpsGuide()">
                ⚙️ İcazə pəncərəsi açılmırsa bələdçiyə baxın
            </button>
        </div>
    </div>
</div>

{{-- GPS Ayarları və Kömək Modalı --}}
<div class="result-overlay" id="gpsGuideModal" style="display: none;">
    <div class="result-card gps-guide-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 16px; font-weight: 800; color: #fff;">
                <span style="font-size: 20px;">📍</span> Məkan (GPS) İcazəsi
            </div>
            <button type="button" onclick="closeGpsGuide()" style="background: rgba(255,255,255,0.1); border: none; color: #94a3b8; font-size: 20px; border-radius: 8px; width: 32px; height: 32px; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
        </div>

        {{-- Cihaz Seçim Tabları (iOS vs Android) --}}
        <div class="gps-os-tabs">
            <button type="button" class="gps-os-tab active" id="tabIosBtn" onclick="selectGpsOs('ios')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.62-.75 1.04-1.8 1.01-2.87-.96.04-2.12.64-2.8 1.43-.59.68-1.11 1.76-.97 2.8.03.01.08.01.12.01.99 0 2.02-.62 2.64-1.37z"/></svg>
                iPhone (iOS)
            </button>
            <button type="button" class="gps-os-tab" id="tabAndroidBtn" onclick="selectGpsOs('android')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.551 0 .9993.4482.9993.9993.0001.5511-.4483.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4116 13.8533 8.083 12 8.083c-1.8534 0-3.5902.3286-5.1368.8667L4.841 5.4467a.416.416 0 00-.5677-.1521.4157.4157 0 00-.152 5676l1.9972 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396"/></svg>
                Android
            </button>
        </div>

        {{-- iOS Təlimatları --}}
        <div id="guideIos" class="gps-guide-content">
            <div class="gps-guide-step">
                <div class="gps-step-num">1</div>
                <div class="gps-step-text">
                    Safari-də ünvan sətrindəki <strong>"aA"</strong> və ya <strong>"⚙️ / Gizlilik"</strong> işarəsinə vurun.
                </div>
            </div>
            <div class="gps-guide-step">
                <div class="gps-step-num">2</div>
                <div class="gps-step-text">
                    <strong>"Veb-sayt Tənzimləmələri" (Website Settings)</strong> bölməsinə daxil olun.
                </div>
            </div>
            <div class="gps-guide-step">
                <div class="gps-step-num">3</div>
                <div class="gps-step-text">
                    <strong>"Məkan" (Location) ➔ "İcazə Ver" (Allow)</strong> seçin.
                </div>
            </div>
            <div class="gps-guide-note">
                💡 <em>Əgər sistemdə bağlıdırsa:</em> Telefonun <strong>Ayarlar ➔ Məxfilik və Təhlükəsizlik ➔ Məkan Xidmətləri</strong>ni aktiv edin və Safari üçün icazə verin.
            </div>
        </div>

        {{-- Android Təlimatları --}}
        <div id="guideAndroid" class="gps-guide-content" style="display: none;">
            <div class="gps-guide-step">
                <div class="gps-step-num">1</div>
                <div class="gps-step-text">
                    Brauzerin ünvan sətrindəki <strong>Qıfıl (🔒)</strong> və ya <strong>Tənzimləmə</strong> işarəsinə vurun.
                </div>
            </div>
            <div class="gps-guide-step">
                <div class="gps-step-num">2</div>
                <div class="gps-step-text">
                    <strong>"İcazələr" (Permissions) ➔ "Məkan" (Location) ➔ "İcazə ver" (Allow)</strong> seçin.
                </div>
            </div>
            <div class="gps-guide-step">
                <div class="gps-step-num">3</div>
                <div class="gps-step-text">
                    Telefonun yuxarı pərdəsini aşağı çəkərək <strong>Məkan (GPS / Konum)</strong> xidmətinin açıq olduğundan əmin olun.
                </div>
            </div>
            <button type="button" class="btn-android-intent" onclick="tryOpenAndroidSettings()">
                ⚙️ Telefon Məkan Ayarlarını Açmağa Cəhd Et
            </button>
        </div>

        <button type="button" class="btn-check-gps-now" onclick="retryGpsFromModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
            Məkanı Aktiv Etdim, Yenidən Yoxla
        </button>
    </div>
</div>

<script>
    // Konfiqurasiya
    const branchToken = "{{ $branch->qr_token }}";
    const branchLat = {{ $branch->latitude ?: 'null' }};
    const branchLng = {{ $branch->longitude ?: 'null' }};
    const branchRadius = {{ $branch->radius_meters ?: 100 }};
    const isPinRequired = {{ $isPinRequired ? 'true' : 'false' }};
    const submitUrl = "{{ route('attendance.scan.submit', ['token' => $branch->qr_token]) }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let currentLat = null;
    let currentLng = null;
    let selectedWorkerId = null;
    let selectedWorkerName = null;
    let selectedNextAction = 'check_in';
    let enteredPin = "";
    let isSubmitting = false;

    // Device UUID (LocalStorage + Cookie)
    function getDeviceUuid() {
        let uuid = localStorage.getItem('pg_att_device_uuid');
        if (!uuid) {
            uuid = 'dev_' + (crypto.randomUUID ? crypto.randomUUID() : (Date.now().toString(36) + Math.random().toString(36).substr(2, 9)));
            localStorage.setItem('pg_att_device_uuid', uuid);
        }
        return uuid;
    }
    const deviceUuid = getDeviceUuid();

    // Device Model
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

    // Live Clock
    function updateClock() {
        const now = new Date();
        const days = ['Bazar', 'Bazar ertəsi', 'Çərşənbə axşamı', 'Çərşənbə', 'Cümə axşamı', 'Cümə', 'Şənbə'];
        const day = days[now.getDay()];
        const time = now.toLocaleTimeString('az-AZ', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('liveClock').textContent = day + ' • ' + time;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Haversine məsafə hesablayıcı (Brauzer daxilində dərhal indikator üçün)
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

    // Geolocation yoxlanışı və avtomatik aktivləşdirmə
    const gpsBanner = document.getElementById('gpsBanner');
    const gpsText = document.getElementById('gpsText');
    const gpsBannerBtn = document.getElementById('gpsBannerBtn');
    const gpsPromptModal = document.getElementById('gpsPromptModal');
    const gpsGuideModal = document.getElementById('gpsGuideModal');
    let isLocationChecking = false;

    // Hər girişdə yenidən sorğu göndərir (heç vaxt 'deaktiv' yadda saxlanılmır)
    function checkLocation() {
        if (!navigator.geolocation) {
            if (gpsBanner) {
                gpsBanner.className = 'gps-banner danger';
                gpsText.textContent = 'Brauzeriniz GPS məkan xidmətini dəstəkləmir.';
            }
            return;
        }

        if (currentLat && currentLng) return;
        if (isLocationChecking) return;
        isLocationChecking = true;

        if (gpsBanner) {
            gpsBanner.className = 'gps-banner loading';
            gpsText.textContent = '📍 GPS məkanınız yoxlanılır...';
            if (gpsBannerBtn) gpsBannerBtn.style.display = 'none';
        }

        // 1-ci mərhələ: Yüksək dəqiqlikli GPS sorğusu
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                isLocationChecking = false;
                onLocationSuccess(pos);
            },
            (err) => {
                // Əgər 1-ci cəhd gecikibsə, dərhal daha sürətli şəbəkə/Wi-Fi ilə 2-ci fallback cəhdi edirik
                navigator.geolocation.getCurrentPosition(
                    (posFallback) => {
                        isLocationChecking = false;
                        onLocationSuccess(posFallback);
                    },
                    (errFinal) => {
                        isLocationChecking = false;
                        onLocationFailed(errFinal);
                    },
                    { enableHighAccuracy: false, timeout: 8000, maximumAge: 60000 }
                );
            },
            { enableHighAccuracy: true, timeout: 14000, maximumAge: 0 }
        );
    }

    function onLocationSuccess(pos) {
        currentLat = pos.coords.latitude;
        currentLng = pos.coords.longitude;

        closeGpsPrompt();
        closeGpsGuide();

        if (branchLat && branchLng) {
            const dist = calculateDistance(currentLat, currentLng, branchLat, branchLng);
            if (dist <= branchRadius) {
                gpsBanner.className = 'gps-banner success';
                gpsText.textContent = '✓ Məkan təsdiqləndi! Filialdan ' + dist + 'm məsafədəsiniz (İcazə: ' + branchRadius + 'm).';
            } else {
                gpsBanner.className = 'gps-banner danger';
                gpsText.textContent = '⚠️ Filialdan kənardasınız! Məsafə: ' + dist + 'm (Maksimal icazə: ' + branchRadius + 'm).';
            }
        } else {
            gpsBanner.className = 'gps-banner success';
            gpsText.textContent = '✓ Məkan aktivdir.';
        }

        if (gpsBannerBtn) gpsBannerBtn.style.display = 'none';
        updateSubmitButtonState();
    }

    function onLocationFailed(err) {
        // Əsla başdan "deaktiv" kimi qalmır — həmişə aktiv "İcazə Ver" düyməsi ilə qalır
        gpsBanner.className = 'gps-banner prompt';
        gpsText.textContent = '📍 GPS icazəsi tələb olunur';
        if (gpsBannerBtn) {
            gpsBannerBtn.style.display = 'inline-flex';
            gpsBannerBtn.textContent = 'İcazə Ver';
        }

        openGpsPrompt(err && err.code === 1);
        updateSubmitButtonState();
    }

    // Birbaşa istifadəçi toxunuşu ilə icazə istəmək (bütün mobil brauzerlərdə native popup açır)
    window.requestLocationExplicitly = function() {
        const btn = document.getElementById('btnActivateGps');
        const btnText = document.getElementById('btnActivateGpsText');
        if (btn) btn.classList.add('loading');
        if (btnText) btnText.textContent = 'Məkan təsdiqlənir...';

        if (!navigator.geolocation) {
            alert('Brauzeriniz GPS dəstəkləmir.');
            if (btn) btn.classList.remove('loading');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                if (btn) btn.classList.remove('loading');
                if (btnText) btnText.textContent = 'Məkanı Aktivləşdir (İcazə Ver)';
                onLocationSuccess(pos);
            },
            (err) => {
                navigator.geolocation.getCurrentPosition(
                    (pos2) => {
                        if (btn) btn.classList.remove('loading');
                        if (btnText) btnText.textContent = 'Məkanı Aktivləşdir (İcazə Ver)';
                        onLocationSuccess(pos2);
                    },
                    (finalErr) => {
                        if (btn) btn.classList.remove('loading');
                        if (btnText) btnText.textContent = 'Yenidən Cəhd Et';
                        onLocationFailed(finalErr);
                    },
                    { enableHighAccuracy: false, timeout: 8000, maximumAge: 60000 }
                );
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    };

    function openGpsPrompt(isDenied = false) {
        if (currentLat && currentLng) return;
        const modal = document.getElementById('gpsPromptModal');
        const desc = document.getElementById('gpsPromptDesc');
        const btnText = document.getElementById('btnActivateGpsText');

        if (isDenied) {
            if (desc) desc.textContent = 'Məkan icazəsi verilməyib. Aşağıdakı düyməyə toxunaraq və ya ünvan sətrindəki işarədən "İcazə Ver" seçin.';
            if (btnText) btnText.textContent = 'İcazə Sorğusunu Təkrarla';
        } else {
            if (desc) desc.textContent = 'Filialda olduğunuzu təsdiqləmək üçün zəhmət olmasa GPS məkan icazəsini aktivləşdirin.';
            if (btnText) btnText.textContent = 'Məkanı Aktivləşdir (İcazə Ver)';
        }

        if (modal) modal.style.display = 'flex';
    }

    function closeGpsPrompt() {
        if (gpsPromptModal) gpsPromptModal.style.display = 'none';
    }

    // Kömək Modalı
    function openGpsGuide() {
        closeGpsPrompt();
        detectGpsOs();
        if (gpsGuideModal) gpsGuideModal.style.display = 'flex';
    }

    function closeGpsGuide() {
        if (gpsGuideModal) gpsGuideModal.style.display = 'none';
    }

    function retryGpsFromModal() {
        closeGpsGuide();
        requestLocationExplicitly();
    }

    function selectGpsOs(os) {
        const tabIos = document.getElementById('tabIosBtn');
        const tabAndroid = document.getElementById('tabAndroidBtn');
        const guideIos = document.getElementById('guideIos');
        const guideAndroid = document.getElementById('guideAndroid');

        if (os === 'ios') {
            if (tabIos) tabIos.classList.add('active');
            if (tabAndroid) tabAndroid.classList.remove('active');
            if (guideIos) guideIos.style.display = 'block';
            if (guideAndroid) guideAndroid.style.display = 'none';
        } else {
            if (tabAndroid) tabAndroid.classList.add('active');
            if (tabIos) tabIos.classList.remove('active');
            if (guideAndroid) guideAndroid.style.display = 'block';
            if (guideIos) guideIos.style.display = 'none';
        }
    }

    function detectGpsOs() {
        const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        selectGpsOs(isIos ? 'ios' : 'android');
    }

    function tryOpenAndroidSettings() {
        try {
            window.location.href = "intent:#Intent;action=android.settings.LOCATION_SOURCE_SETTINGS;end";
        } catch (e) {
            console.warn(e);
        }
    }

    // İstifadəçi tənzimləmələrdən qayıdanda avtomatik yenidən yoxlama
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

    if (navigator.permissions && navigator.permissions.query) {
        navigator.permissions.query({ name: 'geolocation' }).then(permissionStatus => {
            permissionStatus.onchange = () => {
                checkLocation();
            };
        }).catch(() => {});
    }

    // Səhifə açılan kimi dərhal yoxlayır və 1.2 saniyəyə məkan gəlməsə alert modalını təqdim edir
    checkLocation();
    setTimeout(() => {
        if (!currentLat || !currentLng) {
            openGpsPrompt();
        }
    }, 1200);

    // İşçi seçimi
    function attachWorkerListeners() {
        document.querySelectorAll('.worker-card').forEach(card => {
            card.addEventListener('click', function() {
                // Əgər hələ GPS təsdiqlənməyibsə, birbaşa icazə istəyir
                if (!currentLat || !currentLng) {
                    openGpsPrompt();
                    requestLocationExplicitly();
                }

                document.querySelectorAll('.worker-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');

                selectedWorkerId = this.getAttribute('data-worker-id');
                selectedWorkerName = this.getAttribute('data-worker-name');
                selectedNextAction = this.getAttribute('data-next-action') || 'check_in';

                if (isPinRequired) {
                    document.getElementById('pinBox').style.display = 'block';
                    clearPin();
                }

                updateSubmitButtonState();
            });
        });
    }
    attachWorkerListeners();

    // Digər filiallar toggle
    const toggleWorkersBtn = document.getElementById('toggleWorkersBtn');
    let showingAll = false;
    toggleWorkersBtn.addEventListener('click', () => {
        showingAll = !showingAll;
        document.getElementById('branchWorkersList').style.display = showingAll ? 'none' : 'flex';
        document.getElementById('allWorkersList').style.display = showingAll ? 'flex' : 'none';
        toggleWorkersBtn.textContent = showingAll ? 'Yalnız bu filialın işçiləri' : 'Digər filialdan əvəzedici';
    });

    // PIN funksiyaları
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

    // Düymə vəziyyəti
    const submitBtn = document.getElementById('submitBtn');
    const btnLabel = document.getElementById('btnLabel');

    function updateSubmitButtonState() {
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
            btnLabel.textContent = selectedWorkerName + ' • İŞƏ GİRİŞİ TƏSDİQLƏ';
        } else {
            submitBtn.className = 'btn-submit btn-out';
            btnLabel.textContent = selectedWorkerName + ' • İŞDƏN ÇIXIŞI TƏSDİQLƏ';
        }
    }

    // Submit handler
    submitBtn.addEventListener('click', function() {
        if (!selectedWorkerId || isSubmitting) return;

        if (!currentLat || !currentLng) {
            openGpsPrompt();
            requestLocationExplicitly();
            return;
        }

        isSubmitting = true;
        submitBtn.disabled = true;
        btnLabel.textContent = "Yoxlanılır və qeydə alınır...";

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
                // Kartdakı növbəti hərəkəti yeniləyirik
                const card = document.querySelector('.worker-card[data-worker-id="' + selectedWorkerId + '"]');
                if (card && data.next_action) {
                    card.setAttribute('data-next-action', data.next_action);
                    const tag = card.querySelector('.action-tag');
                    if (tag) {
                        tag.className = 'action-tag ' + (data.next_action === 'check_in' ? 'tag-in' : 'tag-out');
                        tag.textContent = data.next_action === 'check_in' ? 'GİRİŞ' : 'ÇIXIŞ';
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

    // Modal
    const resultModal = document.getElementById('resultModal');
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalDesc = document.getElementById('modalDesc');
    const modalCloseBtn = document.getElementById('modalCloseBtn');

    function showModal(isSuccess, message, extraData) {
        modalIcon.className = 'result-icon ' + (isSuccess ? 'success' : 'danger');
        modalIcon.textContent = isSuccess ? '✓' : '✕';
        modalTitle.textContent = isSuccess ? 'Uğurla Qeydə Alındı!' : 'Diqqət: Xəta!';
        
        let desc = message;
        if (isSuccess && extraData && extraData.time) {
            desc += '\nSaat: ' + extraData.time + ' (' + extraData.branch_name + ')';
        }
        modalDesc.textContent = desc;
        resultModal.style.display = 'flex';
    }

    modalCloseBtn.addEventListener('click', () => {
        resultModal.style.display = 'none';
    });
</script>

</body>
</html>
