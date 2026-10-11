@extends('layouts.erp')

@section('title', 'Trafik və Sosial Media — Portal ERP')
@section('page-title', 'TRAFİK VƏ SOSİAL MEDİA')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="{{ route('crm.index') }}">CRM</a></li>
    <li class="breadcrumb-item active">Trafik və Sosial Media</li>
@endsection

@push('css')
<style>
    /* ==============================================================
       TRAFFIC & SOCIAL MEDIA MODULE DESIGN SYSTEM
       ============================================================== */
    :root {
        --traffic-radius: 14px;
        --traffic-border: #e2e8f0;
        --traffic-primary: #107c41;
        --traffic-primary-dark: #0b5e30;
        --traffic-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
        --traffic-hover-shadow: 0 8px 24px rgba(15, 23, 42, 0.09);
    }

    .traffic-wrapper {
        padding-bottom: 50px;
    }

    /* ---------------- Control Toolbar ---------------- */
    .traffic-toolbar {
        background: #ffffff;
        border: 1px solid var(--traffic-border);
        border-radius: var(--traffic-radius);
        padding: 14px 22px;
        box-shadow: var(--traffic-shadow);
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .traffic-toolbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .traffic-status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.2);
        display: inline-block;
    }

    .traffic-title {
        font-weight: 700;
        font-size: 15.5px;
        color: #1e293b;
        margin: 0;
    }

    .traffic-badge-api {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .traffic-toolbar-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .form-select-traffic {
        border-radius: 9px;
        border: 1px solid #cbd5e1;
        background-color: #ffffff;
        padding: 7px 32px 7px 12px;
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        transition: all 0.2s ease;
        cursor: pointer;
        outline: none;
    }
    .form-select-traffic:focus {
        border-color: var(--traffic-primary);
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.12);
    }

    .btn-traffic-save {
        background-color: var(--traffic-primary);
        color: #ffffff;
        border: none;
        border-radius: 9px;
        padding: 7px 18px;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-traffic-save:hover {
        background-color: var(--traffic-primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.22);
    }

    .btn-traffic-refresh {
        background: #ffffff;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        padding: 7px 14px;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-traffic-refresh:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    /* ---------------- 6 Charts Grid (2 Columns x 3 Rows) ---------------- */
    .traffic-charts-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    @media (max-width: 991px) {
        .traffic-charts-grid {
            grid-template-columns: 1fr;
        }
    }

    .traffic-chart-card {
        background: #ffffff;
        border: 1px solid var(--traffic-border);
        border-radius: var(--traffic-radius);
        box-shadow: var(--traffic-shadow);
        padding: 18px 20px 14px 20px;
        transition: all 0.25s ease;
    }
    .traffic-chart-card:hover {
        box-shadow: var(--traffic-hover-shadow);
        border-color: #cbd5e1;
    }

    .traffic-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
    }

    .traffic-card-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
    }

    .traffic-card-sub {
        font-size: 13px;
        font-weight: 700;
        color: #2563eb;
        margin: 0;
    }

    .traffic-card-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
    }
    .traffic-card-badge.badge-api {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .traffic-card-badge.badge-manual {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .traffic-chart-container {
        min-height: 250px;
    }

    /* ---------------- Excel-style Daily Data Table ---------------- */
    .traffic-table-card {
        background: #ffffff;
        border: 1px solid var(--traffic-border);
        border-radius: var(--traffic-radius);
        box-shadow: var(--traffic-shadow);
        overflow: hidden;
    }

    .traffic-table-header {
        padding: 18px 22px;
        background: #ffffff;
        border-bottom: 1px solid var(--traffic-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .traffic-table-container {
        overflow-x: auto;
        max-height: 600px;
    }

    .excel-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    .excel-table thead th {
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 10px 12px;
        border-bottom: 2px solid #cbd5e1;
        border-right: 1px solid #e2e8f0;
        text-align: center;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .excel-table thead th:first-child {
        text-align: left;
        position: sticky;
        left: 0;
        z-index: 11;
        background: #f8fafc;
    }

    .excel-table tbody td {
        padding: 8px 12px;
        color: #1e293b;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #f1f5f9;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        background: #ffffff;
    }
    .excel-table tbody tr.weekend td {
        background-color: #fafbfc;
    }
    .excel-table tbody td:first-child {
        text-align: left;
        position: sticky;
        left: 0;
        z-index: 5;
        font-weight: 700;
        background: #ffffff;
        border-right: 2px solid #cbd5e1;
    }
    .excel-table tbody tr.weekend td:first-child {
        background-color: #fafbfc;
    }

    .excel-table tbody tr:hover td {
        background-color: #f1f5f9;
    }

    .excel-cell-edit {
        outline: none;
        cursor: text;
        transition: all 0.15s ease;
        border-radius: 4px;
        padding: 3px 6px;
    }
    .excel-cell-edit:hover {
        background-color: #f8fafc;
        box-shadow: inset 0 0 0 1px #cbd5e1;
    }
    .excel-cell-edit:focus {
        background-color: #ffffff;
        box-shadow: inset 0 0 0 2px var(--traffic-primary);
        font-weight: 700;
    }
    .excel-cell-edit.dirty {
        background-color: #fef9c3 !important;
        font-weight: 700;
    }

    .excel-table tfoot td {
        background: #e2e8f0;
        font-weight: 800;
        color: #0f172a;
        padding: 10px 12px;
        border-top: 2px solid #cbd5e1;
        border-right: 1px solid #cbd5e1;
        text-align: center;
        white-space: nowrap;
        position: sticky;
        bottom: 0;
        z-index: 9;
    }
    .excel-table tfoot td:first-child {
        text-align: left;
        position: sticky;
        left: 0;
        z-index: 10;
        background: #e2e8f0;
    }

    .badge-net-pos {
        color: #047857;
        font-weight: 700;
    }
    .badge-net-neg {
        color: #b91c1c;
        font-weight: 700;
    }
    .badge-net-zero {
        color: #64748b;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="traffic-wrapper">

    {{-- ============================================================== --}}
    {{-- TOP CONTROL TOOLBAR                                            --}}
    {{-- ============================================================== --}}
    <div class="traffic-toolbar">
        <div class="traffic-toolbar-left">
            <span class="traffic-status-dot"></span>
            <h5 class="traffic-title">{{ $periodDisplay }} — Trafik və Sosial Media Analitikası</h5>

            @if($apiConnected)
                <span class="traffic-badge-api" title="Portal.land API ilə canlı trafik əlaqəsi aktivdir">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Portal API Aktiv
                </span>
            @else
                <span class="traffic-card-badge badge-api" title="Portal API endpoint-i qoşulduqda sayt statistikaları avtomatik yenilənəcək">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    API Qəbulu Hazırdır
                </span>
            @endif
        </div>

        <div class="traffic-toolbar-controls">
            {{-- Month Filter Dropdown --}}
            <form method="GET" action="{{ route('crm.traffic.index') }}" class="d-flex align-items-center gap-2 m-0">
                <label class="font-size-13 fw-bold text-muted mb-0">Ay:</label>
                <select name="month" class="form-select-traffic" onchange="this.form.submit()">
                    @foreach($availableMonths as $m)
                        <option value="{{ $m['key'] }}" {{ ($filterMonth === $m['key']) ? 'selected' : '' }}>
                            {{ $m['name'] }}
                        </option>
                    @endforeach
                </select>
            </form>

            @can('crm.edit')
                <button type="button" class="btn-traffic-save" id="btnSaveAll" onclick="saveAllTrafficData()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Yadda Saxla
                </button>
            @endcan

            <a href="{{ route('crm.traffic.index', ['month' => $filterMonth]) }}" class="btn-traffic-refresh" title="Səhifəni yenilə">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                Yenilə
            </a>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- 6 ANALYTICS LINE CHARTS (2 COLUMNS x 3 ROWS)                   --}}
    {{-- ============================================================== --}}
    <div class="traffic-charts-grid">

        {{-- 1. İNSTAGRAM MESAJLARI --}}
        <div class="traffic-chart-card">
            <div class="traffic-card-header">
                <div>
                    <h6 class="traffic-card-title">İnstaqram mesajları</h6>
                    <p class="traffic-card-sub" id="headerTotalInstMsg">Ümumi mesaj sayı: {{ number_format($totals['instagram_messages']) }}</p>
                </div>
                <span class="traffic-card-badge badge-manual">CRM Daxiletmə</span>
            </div>
            <div id="chartInstagramMessages" class="traffic-chart-container"></div>
        </div>

        {{-- 2. İNSTAGRAM İZLƏYİCİLƏRİ --}}
        <div class="traffic-chart-card">
            <div class="traffic-card-header">
                <div>
                    <h6 class="traffic-card-title">İnstaqram izləyiciləri</h6>
                    <p class="traffic-card-sub" id="headerTotalInstFollowers">
                        Yeni: {{ number_format($totals['instagram_followers_new']) }} &bull;
                        Çıxan: {{ number_format($totals['instagram_followers_lost']) }} &bull;
                        Xalis: {{ ($totals['instagram_followers_net'] > 0 ? '+' : '') . number_format($totals['instagram_followers_net']) }}
                    </p>
                </div>
                <span class="traffic-card-badge badge-manual">CRM Daxiletmə</span>
            </div>
            <div id="chartInstagramFollowers" class="traffic-chart-container"></div>
        </div>

        {{-- 3. ZƏNGLƏR --}}
        <div class="traffic-chart-card">
            <div class="traffic-card-header">
                <div>
                    <h6 class="traffic-card-title">Zənglər</h6>
                    <p class="traffic-card-sub" id="headerTotalCalls">Ümumi zəng sayı: {{ number_format($totals['calls']) }}</p>
                </div>
                <span class="traffic-card-badge badge-manual">CRM Daxiletmə</span>
            </div>
            <div id="chartCalls" class="traffic-chart-container"></div>
        </div>

        {{-- 4. TİKTOK İZLƏYİCİLƏRİ --}}
        <div class="traffic-chart-card">
            <div class="traffic-card-header">
                <div>
                    <h6 class="traffic-card-title">TikTok izləyiciləri</h6>
                    <p class="traffic-card-sub" id="headerTotalTiktok">
                        Xalis izləyici dəyişimi: {{ ($totals['tiktok_followers_net'] > 0 ? '+' : '') . number_format($totals['tiktok_followers_net']) }}
                    </p>
                </div>
                <span class="traffic-card-badge badge-manual">CRM Daxiletmə</span>
            </div>
            <div id="chartTikTok" class="traffic-chart-container"></div>
        </div>

        {{-- 5. SAYT İSTİFADƏÇİLƏRİ (UNİKAL) --}}
        <div class="traffic-chart-card">
            <div class="traffic-card-header">
                <div>
                    <h6 class="traffic-card-title">Sayt istifadəçiləri</h6>
                    <p class="traffic-card-sub" id="headerTotalWebUsers">Ümumi istifadəçi sayı: {{ number_format($totals['website_users']) }}</p>
                </div>
                <span class="traffic-card-badge badge-api">Veb-sayt API</span>
            </div>
            <div id="chartWebsiteUsers" class="traffic-chart-container"></div>
        </div>

        {{-- 6. SAYTA DAXİLOLMALAR / ZİYARƏTLƏR --}}
        <div class="traffic-chart-card">
            <div class="traffic-card-header">
                <div>
                    <h6 class="traffic-card-title">Sayta daxilolmalar</h6>
                    <p class="traffic-card-sub" id="headerTotalWebVisits">Daxilolmaların ümumi sayı: {{ number_format($totals['website_visits']) }}</p>
                </div>
                <span class="traffic-card-badge badge-api">Veb-sayt API</span>
            </div>
            <div id="chartWebsiteVisits" class="traffic-chart-container"></div>
        </div>

    </div>

    {{-- ============================================================== --}}
    {{-- DAILY DATA ENTRY TABLE (EXCEL-STYLE FOR CRM)                   --}}
    {{-- ============================================================== --}}
    <div class="traffic-table-card">
        <div class="traffic-table-header">
            <div>
                <h6 class="font-size-16 fw-bold text-dark m-0">📅 {{ $periodDisplay }} — Gündəlik Göstəricilərin Qeydiyyatı</h6>
                <p class="text-muted font-size-12 m-0 mt-1">
                    Xanalara rəqəmləri daxil etdikdə yekun cəmlər və yuxarıdakı qrafiklər dərhal yenilənir.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportTableToCSV('dailyTrafficTable', 'Trafik_Sosial_Media_{{ $filterMonth }}.csv')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    CSV İxrac
                </button>
                @can('crm.edit')
                    <button type="button" class="btn btn-sm btn-success fw-bold px-3" onclick="saveAllTrafficData()">
                        💾 Bütün Sətirləri Saxla
                    </button>
                @endcan
            </div>
        </div>

        <div class="traffic-table-container">
            <table class="excel-table" id="dailyTrafficTable">
                <thead>
                    <tr>
                        <th style="min-width: 140px;">TARİX / GÜN</th>
                        <th style="min-width: 110px;" title="İnstaqram Direct Mesajları">İNSTAQRAM MESAJ</th>
                        <th style="min-width: 110px;" title="İnstaqram Yeni İzləyicilər">YENİ İZLƏYİCİ (+)</th>
                        <th style="min-width: 110px;" title="İnstaqram Çıxan İzləyicilər">ÇIXAN İZLƏYİCİ (-)</th>
                        <th style="min-width: 110px;" title="İnstaqram Xalis Artım (Yeni - Çıxan)">İNSTAQRAM NET</th>
                        <th style="min-width: 100px;" title="Daxil Olan Zənglər">ZƏNGLƏR</th>
                        <th style="min-width: 110px;" title="TikTok Xalis İzləyici Dəyişimi">TİKTOK İZLƏYİCİ</th>
                        <th style="min-width: 110px;" title="Sayt Unikal İstifadəçiləri">SAYT İSTİFADƏÇİ</th>
                        <th style="min-width: 110px;" title="Sayta Daxilolmalar / Ziyarətlər">SAYTA DAXİLOLMA</th>
                        <th style="min-width: 180px;">QEYD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyRows as $d => $row)
                        <tr data-day="{{ $d }}" data-date="{{ $row['date'] }}" class="{{ $row['is_weekend'] ? 'weekend' : '' }}">
                            {{-- Day / Date --}}
                            <td>
                                <strong>{{ $row['date_formatted'] }}</strong>
                                <span class="text-muted font-size-11">({{ $row['day_name'] }})</span>
                            </td>

                            {{-- 1. Instagram Messages --}}
                            <td class="excel-cell-edit"
                                contenteditable="{{ $canEdit ? 'true' : 'false' }}"
                                data-field="instagram_messages"
                                oninput="markRowDirty(this)"
                                onblur="onCellBlur(this)">{{ $row['instagram_messages'] }}</td>

                            {{-- 2. Instagram Followers New --}}
                            <td class="excel-cell-edit"
                                contenteditable="{{ $canEdit ? 'true' : 'false' }}"
                                data-field="instagram_followers_new"
                                oninput="markRowDirty(this); recalcRowNet(this);"
                                onblur="onCellBlur(this)">{{ $row['instagram_followers_new'] }}</td>

                            {{-- 2. Instagram Followers Lost --}}
                            <td class="excel-cell-edit"
                                contenteditable="{{ $canEdit ? 'true' : 'false' }}"
                                data-field="instagram_followers_lost"
                                oninput="markRowDirty(this); recalcRowNet(this);"
                                onblur="onCellBlur(this)">{{ $row['instagram_followers_lost'] }}</td>

                            {{-- 2. Instagram Followers Net (Calculated) --}}
                            <td data-field="instagram_followers_net" class="fw-bold {{ $row['instagram_followers_net'] > 0 ? 'badge-net-pos' : ($row['instagram_followers_net'] < 0 ? 'badge-net-neg' : 'badge-net-zero') }}">
                                {{ ($row['instagram_followers_net'] > 0 ? '+' : '') . $row['instagram_followers_net'] }}
                            </td>

                            {{-- 3. Calls --}}
                            <td class="excel-cell-edit"
                                contenteditable="{{ $canEdit ? 'true' : 'false' }}"
                                data-field="calls"
                                oninput="markRowDirty(this)"
                                onblur="onCellBlur(this)">{{ $row['calls'] }}</td>

                            {{-- 4. TikTok Followers Net --}}
                            <td class="excel-cell-edit fw-bold {{ $row['tiktok_followers_net'] > 0 ? 'badge-net-pos' : ($row['tiktok_followers_net'] < 0 ? 'badge-net-neg' : 'badge-net-zero') }}"
                                contenteditable="{{ $canEdit ? 'true' : 'false' }}"
                                data-field="tiktok_followers_net"
                                oninput="markRowDirty(this)"
                                onblur="onCellBlur(this)">{{ $row['tiktok_followers_net'] }}</td>

                            {{-- 5. Website Users --}}
                            <td class="excel-cell-edit"
                                contenteditable="{{ $canEdit ? 'true' : 'false' }}"
                                data-field="website_users"
                                oninput="markRowDirty(this)"
                                onblur="onCellBlur(this)">{{ $row['website_users'] }}</td>

                            {{-- 6. Website Visits --}}
                            <td class="excel-cell-edit"
                                contenteditable="{{ $canEdit ? 'true' : 'false' }}"
                                data-field="website_visits"
                                oninput="markRowDirty(this)"
                                onblur="onCellBlur(this)">{{ $row['website_visits'] }}</td>

                            {{-- Notes --}}
                            <td class="excel-cell-edit text-start"
                                contenteditable="{{ $canEdit ? 'true' : 'false' }}"
                                data-field="notes"
                                oninput="markRowDirty(this)"
                                onblur="onCellBlur(this)">{{ $row['notes'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>CƏMİ ({{ count($dailyRows) }} gün):</td>
                        <td id="foot_inst_msg">{{ number_format($totals['instagram_messages']) }}</td>
                        <td id="foot_inst_new">{{ number_format($totals['instagram_followers_new']) }}</td>
                        <td id="foot_inst_lost">{{ number_format($totals['instagram_followers_lost']) }}</td>
                        <td id="foot_inst_net" class="{{ $totals['instagram_followers_net'] > 0 ? 'badge-net-pos' : ($totals['instagram_followers_net'] < 0 ? 'badge-net-neg' : 'badge-net-zero') }}">
                            {{ ($totals['instagram_followers_net'] > 0 ? '+' : '') . number_format($totals['instagram_followers_net']) }}
                        </td>
                        <td id="foot_calls">{{ number_format($totals['calls']) }}</td>
                        <td id="foot_tiktok" class="{{ $totals['tiktok_followers_net'] > 0 ? 'badge-net-pos' : ($totals['tiktok_followers_net'] < 0 ? 'badge-net-neg' : 'badge-net-zero') }}">
                            {{ ($totals['tiktok_followers_net'] > 0 ? '+' : '') . number_format($totals['tiktok_followers_net']) }}
                        </td>
                        <td id="foot_web_users">{{ number_format($totals['website_users']) }}</td>
                        <td id="foot_web_visits">{{ number_format($totals['website_visits']) }}</td>
                        <td>—</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- Load ApexCharts Library --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
// ==============================================================
// 1. APEXCHARTS CONFIGURATION FOR 6 METRICS (Matching Photographs)
// ==============================================================
const chartDays = @json($chartData['days']);

// Shared chart options template
function createLineChartOptions(seriesName, data, lineColor, yTitle, isInteger = true) {
    return {
        series: [{
            name: seriesName,
            data: data
        }],
        chart: {
            type: 'line',
            height: 250,
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'Inter, sans-serif'
        },
        colors: [lineColor],
        stroke: {
            curve: 'straight',
            width: 2.2
        },
        markers: {
            size: 3.5,
            colors: [lineColor],
            strokeColors: '#ffffff',
            strokeWidth: 1.5,
            hover: { size: 6 }
        },
        dataLabels: {
            enabled: true,
            offsetY: -8,
            style: {
                fontSize: '11px',
                fontWeight: 700,
                colors: ['#334155']
            },
            formatter: function (val) {
                return val !== null && val !== undefined ? val.toLocaleString() : '';
            }
        },
        xaxis: {
            categories: chartDays,
            labels: {
                style: {
                    fontSize: '11px',
                    fontWeight: 600,
                    colors: '#64748b'
                }
            },
            axisBorder: { show: true, color: '#cbd5e1' },
            axisTicks: { show: true, color: '#cbd5e1' }
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px', colors: '#64748b' },
                formatter: function (val) {
                    return Math.round(val).toLocaleString();
                }
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 2,
            yaxis: { lines: { show: true } }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toLocaleString();
                }
            }
        }
    };
}

// 1. Instagram Messages Chart
let chartInstMsg = new ApexCharts(
    document.querySelector("#chartInstagramMessages"),
    createLineChartOptions('Mesaj sayı', @json($chartData['instagram_messages']), '#2563eb')
);
chartInstMsg.render();

// 2. Instagram Followers Chart (Net)
let chartInstFollowers = new ApexCharts(
    document.querySelector("#chartInstagramFollowers"),
    createLineChartOptions('İnstaqram Xalis', @json($chartData['instagram_followers_net']), '#0284c7')
);
chartInstFollowers.render();

// 3. Calls Chart
let chartCalls = new ApexCharts(
    document.querySelector("#chartCalls"),
    createLineChartOptions('Zəng sayı', @json($chartData['calls']), '#0d9488')
);
chartCalls.render();

// 4. TikTok Followers Chart
let chartTikTok = new ApexCharts(
    document.querySelector("#chartTikTok"),
    createLineChartOptions('TikTok Xalis', @json($chartData['tiktok_followers_net']), '#4f46e5')
);
chartTikTok.render();

// 5. Website Users Chart
let chartWebUsers = new ApexCharts(
    document.querySelector("#chartWebsiteUsers"),
    createLineChartOptions('İstifadəçi sayı', @json($chartData['website_users']), '#3b82f6')
);
chartWebUsers.render();

// 6. Website Visits Chart
let chartWebVisits = new ApexCharts(
    document.querySelector("#chartWebsiteVisits"),
    createLineChartOptions('Daxilolma sayı', @json($chartData['website_visits']), '#2563eb')
);
chartWebVisits.render();

// ==============================================================
// 2. EXCEL TABLE INLINE EDIT & RECALCULATIONS
// ==============================================================
function markRowDirty(el) {
    el.classList.add('dirty');
}

// Recalculate row's Instagram net on typing
function recalcRowNet(el) {
    const tr = el.closest('tr');
    if (!tr) return;

    const newCell = tr.querySelector('[data-field="instagram_followers_new"]');
    const lostCell = tr.querySelector('[data-field="instagram_followers_lost"]');
    const netCell = tr.querySelector('[data-field="instagram_followers_net"]');

    if (newCell && lostCell && netCell) {
        const newVal = parseInt(newCell.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const lostVal = parseInt(lostCell.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const netVal = newVal - lostVal;

        netCell.innerText = (netVal > 0 ? '+' : '') + netVal;
        netCell.className = 'fw-bold ' + (netVal > 0 ? 'badge-net-pos' : (netVal < 0 ? 'badge-net-neg' : 'badge-net-zero'));
    }

    recalcAllTotals();
}

// Recalculate footers and charts dynamically
function recalcAllTotals() {
    const rows = document.querySelectorAll('#dailyTrafficTable tbody tr');

    let totalMsg = 0, totalNew = 0, totalLost = 0, totalNet = 0;
    let totalCalls = 0, totalTiktok = 0, totalWebUsers = 0, totalWebVisits = 0;

    let arrMsg = [], arrNet = [], arrCalls = [], arrTiktok = [], arrWebUsers = [], arrWebVisits = [];

    rows.forEach(tr => {
        const msg = parseInt(tr.querySelector('[data-field="instagram_messages"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const nw = parseInt(tr.querySelector('[data-field="instagram_followers_new"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const lst = parseInt(tr.querySelector('[data-field="instagram_followers_lost"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const net = nw - lst;
        const cls = parseInt(tr.querySelector('[data-field="calls"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const ttk = parseInt(tr.querySelector('[data-field="tiktok_followers_net"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const usr = parseInt(tr.querySelector('[data-field="website_users"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const vst = parseInt(tr.querySelector('[data-field="website_visits"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);

        totalMsg += msg;
        totalNew += nw;
        totalLost += lst;
        totalNet += net;
        totalCalls += cls;
        totalTiktok += ttk;
        totalWebUsers += usr;
        totalWebVisits += vst;

        arrMsg.push(msg);
        arrNet.push(net);
        arrCalls.push(cls);
        arrTiktok.push(ttk);
        arrWebUsers.push(usr);
        arrWebVisits.push(vst);
    });

    // Update footers
    document.getElementById('foot_inst_msg').innerText = totalMsg.toLocaleString();
    document.getElementById('foot_inst_new').innerText = totalNew.toLocaleString();
    document.getElementById('foot_inst_lost').innerText = totalLost.toLocaleString();
    const footNet = document.getElementById('foot_inst_net');
    footNet.innerText = (totalNet > 0 ? '+' : '') + totalNet.toLocaleString();
    footNet.className = totalNet > 0 ? 'badge-net-pos' : (totalNet < 0 ? 'badge-net-neg' : 'badge-net-zero');

    document.getElementById('foot_calls').innerText = totalCalls.toLocaleString();
    const footTtk = document.getElementById('foot_tiktok');
    footTtk.innerText = (totalTiktok > 0 ? '+' : '') + totalTiktok.toLocaleString();
    footTtk.className = totalTiktok > 0 ? 'badge-net-pos' : (totalTiktok < 0 ? 'badge-net-neg' : 'badge-net-zero');

    document.getElementById('foot_web_users').innerText = totalWebUsers.toLocaleString();
    document.getElementById('foot_web_visits').innerText = totalWebVisits.toLocaleString();

    // Update card header labels
    document.getElementById('headerTotalInstMsg').innerText = 'Ümumi mesaj sayı: ' + totalMsg.toLocaleString();
    document.getElementById('headerTotalInstFollowers').innerText =
        'Yeni: ' + totalNew.toLocaleString() + ' • Çıxan: ' + totalLost.toLocaleString() + ' • Xalis: ' + (totalNet > 0 ? '+' : '') + totalNet.toLocaleString();
    document.getElementById('headerTotalCalls').innerText = 'Ümumi zəng sayı: ' + totalCalls.toLocaleString();
    document.getElementById('headerTotalTiktok').innerText = 'Xalis izləyici dəyişimi: ' + (totalTiktok > 0 ? '+' : '') + totalTiktok.toLocaleString();
    document.getElementById('headerTotalWebUsers').innerText = 'Ümumi istifadəçi sayı: ' + totalWebUsers.toLocaleString();
    document.getElementById('headerTotalWebVisits').innerText = 'Daxilolmaların ümumi sayı: ' + totalWebVisits.toLocaleString();

    // Live update charts
    chartInstMsg.updateSeries([{ data: arrMsg }]);
    chartInstFollowers.updateSeries([{ data: arrNet }]);
    chartCalls.updateSeries([{ data: arrCalls }]);
    chartTikTok.updateSeries([{ data: arrTiktok }]);
    chartWebUsers.updateSeries([{ data: arrWebUsers }]);
    chartWebVisits.updateSeries([{ data: arrWebVisits }]);
}

// Autosave single cell on blur
function onCellBlur(el) {
    if (!el.classList.contains('dirty')) return;

    recalcAllTotals();

    const tr = el.closest('tr');
    if (!tr) return;

    const dateStr = tr.getAttribute('data-date');
    const field = el.getAttribute('data-field');
    let val = el.innerText.trim();

    if (field !== 'notes') {
        val = parseInt(val.replace(/[^0-9-]/g, '') || '0', 10);
        el.innerText = val;
    }

    fetch("{{ route('crm.traffic.saveRow') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            date: dateStr,
            field: field,
            value: val
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            el.classList.remove('dirty');
        }
    })
    .catch(err => {
        console.error("Autosave error:", err);
    });
}

// Bulk Save All Traffic Data
function saveAllTrafficData() {
    const btn = document.getElementById('btnSaveAll');
    if (btn) {
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width: 13px; height: 13px;"></span>
            Saxlanılır...
        `;
        btn.disabled = true;
    }

    const rows = [];
    document.querySelectorAll('#dailyTrafficTable tbody tr').forEach(tr => {
        const day = parseInt(tr.getAttribute('data-day'), 10);
        const msg = parseInt(tr.querySelector('[data-field="instagram_messages"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const nw = parseInt(tr.querySelector('[data-field="instagram_followers_new"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const lst = parseInt(tr.querySelector('[data-field="instagram_followers_lost"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const cls = parseInt(tr.querySelector('[data-field="calls"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const ttk = parseInt(tr.querySelector('[data-field="tiktok_followers_net"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const usr = parseInt(tr.querySelector('[data-field="website_users"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const vst = parseInt(tr.querySelector('[data-field="website_visits"]')?.innerText.replace(/[^0-9-]/g, '') || '0', 10);
        const nts = tr.querySelector('[data-field="notes"]')?.innerText.trim() || '';

        rows.push({
            day: day,
            instagram_messages: msg,
            instagram_followers_new: nw,
            instagram_followers_lost: lst,
            calls: cls,
            tiktok_followers_net: ttk,
            website_users: usr,
            website_visits: vst,
            notes: nts
        });
    });

    fetch("{{ route('crm.traffic.save') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            month: "{{ $filterMonth }}",
            rows: rows
        })
    })
    .then(r => r.json())
    .then(data => {
        if (btn) {
            btn.innerHTML = `✓ Saxlanıldı`;
            btn.classList.replace('btn-traffic-save', 'btn-success');
            setTimeout(() => {
                btn.innerHTML = `
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Yadda Saxla
                `;
                btn.classList.replace('btn-success', 'btn-traffic-save');
                btn.disabled = false;
            }, 1500);
        }

        // Clear dirty classes
        document.querySelectorAll('#dailyTrafficTable .dirty').forEach(el => el.classList.remove('dirty'));
    })
    .catch(err => {
        alert("Xəta baş verdi: " . err);
        if (btn) {
            btn.innerHTML = `Yadda Saxla`;
            btn.disabled = false;
        }
    });
}

// Export Table to CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');

    for (let i = 0; i < rows.length; i++) {
        let row = [];
        const cols = rows[i].querySelectorAll('th, td');
        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.trim().replace(/"/g, '""');
            row.push('"' + text + '"');
        }
        csv.push(row.join(','));
    }

    const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + encodeURIComponent(csv.join('\n'));
    const link = document.createElement('a');
    link.setAttribute('href', csvContent);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endpush
