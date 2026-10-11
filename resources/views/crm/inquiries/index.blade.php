@extends('layouts.erp')

@section('title', 'Müraciət Statistikası — CRM')
@section('page-title', 'MÜRACİƏT VƏ ZƏNG STATİSTİKASI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="{{ route('crm.index') }}">CRM</a></li>
    <li class="breadcrumb-item active">Müraciət Statistikası</li>
@endsection

@push('css')
<style>
    /* ==============================================================
       CRM INQUIRY STATISTICS (ACCORDION & EXCEL HYBRID DESIGN)
       ============================================================== */
    :root {
        --inq-radius: 14px;
        --inq-card-bg: #ffffff;
        --inq-border: #e2e8f0;
        --inq-primary: #107c41;
        --inq-primary-hover: #0b5e30;
        --inq-yellow-bg: #fef9c3;      /* Sarı rəngli sütunlar (Şəkildəki kimi) */
        --inq-yellow-border: #fef08a;
        --inq-pink-bg: #fee2e2;        /* TOTAL sətri (Şəkildəki çəhrayı highlighter kimi) */
        --inq-pink-border: #fca5a5;
        --inq-pink-text: #991b1b;
    }

    .inquiry-wrapper {
        padding-bottom: 30px;
    }

    /* ---------------- Top Action & Toolbar ---------------- */
    .inquiry-toolbar {
        background: #ffffff;
        border: 1px solid var(--inq-border);
        border-radius: var(--inq-radius);
        padding: 14px 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .inquiry-toolbar-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 15px;
        color: #1e293b;
        margin: 0;
    }

    .inquiry-badge-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #ecfdf5;
        color: var(--inq-primary);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .inquiry-actions-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-inq-primary {
        background: var(--inq-primary);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-inq-primary:hover {
        background: var(--inq-primary-hover);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.2);
    }

    .btn-inq-light {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #334155;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-inq-light:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    /* ---------------- Mini KPI Cards ---------------- */
    .inq-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .inq-kpi-card {
        background: #ffffff;
        border: 1px solid var(--inq-border);
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .inq-kpi-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin: 0 0 4px 0;
    }

    .inq-kpi-value {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.1;
    }

    /* ---------------- Accordion (Qarmon) Containers ---------------- */
    .year-accordion-box {
        margin-bottom: 18px;
        border: 1px solid var(--inq-border);
        border-radius: var(--inq-radius);
        background: #ffffff;
        box-shadow: 0 3px 12px rgba(0,0,0,0.03);
        overflow: hidden;
        transition: all 0.25s ease;
    }
    .year-accordion-box.is-collapsed {
        box-shadow: none;
    }

    .year-accordion-header {
        padding: 14px 20px;
        background: #f8fafc;
        border-bottom: 1px solid var(--inq-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        transition: background 0.2s ease;
    }
    .year-accordion-header:hover {
        background: #f1f5f9;
    }
    .year-accordion-box.is-collapsed .year-accordion-header {
        border-bottom: none;
    }

    .year-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .year-title-pill {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .year-badge-status {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 999px;
    }
    .status-active {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .status-completed {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .year-header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .year-total-indicator {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 4px 12px;
        border-radius: 8px;
    }

    .year-toggle-chevron {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        transition: transform 0.3s ease;
    }
    .year-accordion-box.is-collapsed .year-toggle-chevron {
        transform: rotate(-90deg);
    }

    .year-accordion-body {
        padding: 0;
        overflow-x: auto;
        display: block;
    }
    .year-accordion-box.is-collapsed .year-accordion-body {
        display: none;
    }

    /* ---------------- Physical Replica Table ---------------- */
    .inquiry-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-bottom: 0;
        font-family: inherit;
    }

    .inquiry-table th,
    .inquiry-table td {
        border: 1px solid #cbd5e1;
        padding: 8px 10px;
        vertical-align: middle;
        text-align: center;
        white-space: nowrap;
    }

    /* Başlıq sətri */
    .inquiry-table thead th {
        background: #f1f5f9;
        color: #1e293b;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 10px 8px;
    }

    .inquiry-table thead th.col-yellow {
        background: #fef08a; /* Şəkildəki sarı başlıqlar */
        color: #713f12;
    }

    .inquiry-table thead th.col-total {
        background: #fed7aa;
        color: #9a3412;
    }

    /* Xana daxilindəki rəqəmlər və redaktə olunan inputlar */
    .inq-cell-input {
        width: 100%;
        min-width: 65px;
        max-width: 90px;
        text-align: center;
        font-weight: 600;
        font-size: 13.5px;
        color: #0f172a;
        border: 1px solid transparent;
        background: transparent;
        padding: 4px 6px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .inq-cell-input:focus {
        border-color: #107c41;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.15);
        outline: none;
    }
    .inq-cell-input.is-dirty {
        background-color: #fef3c7 !important;
        border-color: #f59e0b !important;
        color: #b45309 !important;
    }
    .inq-cell-input:disabled,
    .inq-cell-input[readonly] {
        cursor: default;
        background: transparent !important;
        border-color: transparent !important;
        box-shadow: none !important;
        color: #1e293b;
        -moz-appearance: textfield;
    }
    .inq-cell-input:disabled::-webkit-outer-spin-button,
    .inq-cell-input:disabled::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .inquiry-table tbody td.col-yellow {
        background-color: #fefce8; /* Şəkildə sarı rənglənmiş sahələr */
    }

    .inquiry-table tbody tr:hover td {
        background-color: #f8fafc;
    }
    .inquiry-table tbody tr:hover td.col-yellow {
        background-color: #fef9c3;
    }

    .inq-month-label {
        font-weight: 700;
        color: #1e293b;
        text-align: left;
        min-width: 110px;
        padding-left: 14px;
    }

    .inq-row-total {
        font-weight: 800;
        font-size: 14px;
        color: #0f172a;
        background: #f8fafc;
    }

    /* ---------------- TOTAL SƏTRİ (Şəkildəki çəhrayı/qırmızı highlighter kimi) ---------------- */
    .inquiry-table tbody tr.row-year-total td {
        background-color: var(--inq-pink-bg) !important;
        border-top: 2px solid var(--inq-pink-border);
        border-bottom: 2px solid var(--inq-pink-border);
        color: var(--inq-pink-text);
        font-weight: 800;
        font-size: 14px;
        letter-spacing: 0.2px;
        padding: 10px 8px;
    }

    /* Əməliyyat düymələri */
    .btn-row-delete {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        border: 1px solid #fee2e2;
        background: #fff5f5;
        color: #ef4444;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-row-delete:hover {
        background: #ef4444;
        color: #ffffff;
        border-color: #dc2626;
        transform: scale(1.08);
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);
    }
</style>
@endpush

@section('content')
<div class="inquiry-wrapper">

    {{-- Üst Alətlər Paneli --}}
    <div class="inquiry-toolbar">
        <div class="d-flex align-items-center gap-3">
            <div class="inquiry-badge-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
            </div>
            <div>
                <h5 class="inquiry-toolbar-title">Müraciət və Zəng Statistikası (CRM)</h5>
                <span class="text-muted font-size-12">Zəng və sorğu təyinatlarının illər və aylar üzrə dinamik hesabatı</span>
            </div>
        </div>

        <div class="inquiry-actions-group">
            @can('crm.edit')
                <button type="button" class="btn-inq-primary" id="btnSaveAll" onclick="saveAllChanges()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Yadda Saxla (Ctrl+S)
                </button>
            @endcan
        </div>
    </div>

    {{-- Mini KPI Kartları --}}
    <div class="inq-kpi-grid">
        <div class="inq-kpi-card">
            <div>
                <p class="inq-kpi-label">Ümumi Sorğular (Cəmi)</p>
                <h4 class="inq-kpi-value text-primary" id="kpiGrandTotal">{{ number_format($overallTotals['total']) }}</h4>
            </div>
            <div class="inquiry-badge-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
            </div>
        </div>

        <div class="inq-kpi-card">
            <div>
                <p class="inq-kpi-label">Rezervasiya Sorğusu</p>
                <h4 class="inq-kpi-value text-success" id="kpiReservations">{{ number_format($overallTotals['reservations']) }}</h4>
            </div>
            <div class="inquiry-badge-icon" style="background:#f0fdf4; color:#16a34a;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
        </div>

        <div class="inq-kpi-card">
            <div>
                <p class="inq-kpi-label">Oyunlar üzrə Sorğular</p>
                <h4 class="inq-kpi-value text-info" id="kpiGames">{{ number_format($overallTotals['games']) }}</h4>
            </div>
            <div class="inquiry-badge-icon" style="background:#eff6ff; color:#2563eb;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            </div>
        </div>

        <div class="inq-kpi-card">
            <div>
                <p class="inq-kpi-label">Şikayətlər</p>
                <h4 class="inq-kpi-value text-danger" id="kpiComplaints">{{ number_format($overallTotals['complaints']) }}</h4>
            </div>
            <div class="inquiry-badge-icon" style="background:#fef2f2; color:#dc2626;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- İLLƏR ÜZRƏ QARMON (ACCORDION) VƏ CƏDVƏLLƏR --}}
    {{-- ============================================================== --}}
    @foreach($yearsData as $year => $yData)
        <div class="year-accordion-box {{ $yData['is_completed'] && !$yData['is_current'] ? 'is-collapsed' : '' }}" id="yearBox-{{ $year }}">
            {{-- Qarmon Başlığı --}}
            <div class="year-accordion-header" onclick="toggleYearAccordion({{ $year }})">
                <div class="year-header-left">
                    <span class="year-title-pill">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="text-primary"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        {{ $year }}-ci il Statistikası
                    </span>
                    @if($yData['is_current'])
                        <span class="year-badge-status status-active">Cari İl</span>
                    @elseif($yData['is_completed'])
                        <span class="year-badge-status status-completed">Tamamlanmış İl</span>
                    @endif
                    <span class="text-muted font-size-12">({{ $yData['months_count'] }} ay)</span>
                </div>

                <div class="year-header-right">
                    <div class="year-total-indicator">
                        Cəmi: <span class="year-total-val" id="yearTotal-{{ $year }}">{{ number_format($yData['totals']['total']) }}</span> müraciət
                    </div>
                    <div class="year-toggle-chevron">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </div>
            </div>

            {{-- Qarmon Gövdəsi (Cədvəl) --}}
            <div class="year-accordion-body">
                <table class="inquiry-table" id="table-{{ $year }}" data-year="{{ $year }}">
                    <thead>
                        <tr>
                            <th style="min-width: 140px; text-align: left; padding-left: 14px;">Ay</th>
                            <th class="col-yellow" title="Ləğv və İmtina">Ləğvetmə</th>
                            <th class="col-yellow" title="Endirim">Endirim</th>
                            <th class="col-yellow" title="Rezervasiya">Rezervasiya</th>
                            <th class="col-yellow" title="Məkan və Ünvan">Məkan / Ünvan</th>
                            <th>Oyunlar</th>
                            <th class="col-yellow" title="İş Qrafiki">İş Qrafiki</th>
                            <th class="col-yellow" title="Şikayətlər">Şikayətlər</th>
                            <th>Digər</th>
                            <th class="col-total" title="Cəm">Cəm</th>
                            @if($canDelete)
                                <th style="width: 50px;">Sil</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tbody-{{ $year }}">
                        @forelse($yData['months'] as $mRow)
                            <tr id="row-{{ $mRow['id'] }}" data-id="{{ $mRow['id'] }}" data-year="{{ $year }}" data-month="{{ $mRow['month'] }}">
                                <td class="inq-month-label">{{ $mRow['period_label'] }}</td>

                                {{-- Отмена --}}
                                <td class="col-yellow">
                                    <input type="number" min="0" class="inq-cell-input" data-col="cancellations"
                                        value="{{ $mRow['cancellations'] }}" {{ !$canEdit ? 'readonly disabled' : '' }}
                                        oninput="recalcRow(this)">
                                </td>

                                {{-- Скидка --}}
                                <td class="col-yellow">
                                    <input type="number" min="0" class="inq-cell-input" data-col="discounts"
                                        value="{{ $mRow['discounts'] }}" {{ !$canEdit ? 'readonly disabled' : '' }}
                                        oninput="recalcRow(this)">
                                </td>

                                {{-- Резервация --}}
                                <td class="col-yellow">
                                    <input type="number" min="0" class="inq-cell-input" data-col="reservations"
                                        value="{{ $mRow['reservations'] }}" {{ !$canEdit ? 'readonly disabled' : '' }}
                                        oninput="recalcRow(this)">
                                </td>

                                {{-- Местоположение --}}
                                <td class="col-yellow">
                                    <input type="number" min="0" class="inq-cell-input" data-col="location"
                                        value="{{ $mRow['location'] }}" {{ !$canEdit ? 'readonly disabled' : '' }}
                                        oninput="recalcRow(this)">
                                </td>

                                {{-- Игры --}}
                                <td>
                                    <input type="number" min="0" class="inq-cell-input" data-col="games"
                                        value="{{ $mRow['games'] }}" {{ !$canEdit ? 'readonly disabled' : '' }}
                                        oninput="recalcRow(this)">
                                </td>

                                {{-- График работы --}}
                                <td class="col-yellow">
                                    <input type="number" min="0" class="inq-cell-input" data-col="working_hours"
                                        value="{{ $mRow['working_hours'] }}" {{ !$canEdit ? 'readonly disabled' : '' }}
                                        oninput="recalcRow(this)">
                                </td>

                                {{-- Жалобы --}}
                                <td class="col-yellow">
                                    <input type="number" min="0" class="inq-cell-input" data-col="complaints"
                                        value="{{ $mRow['complaints'] }}" {{ !$canEdit ? 'readonly disabled' : '' }}
                                        oninput="recalcRow(this)">
                                </td>

                                {{-- Другое --}}
                                <td>
                                    <input type="number" min="0" class="inq-cell-input" data-col="other"
                                        value="{{ $mRow['other'] }}" {{ !$canEdit ? 'readonly disabled' : '' }}
                                        oninput="recalcRow(this)">
                                </td>

                                {{-- Общее (Cəmi) --}}
                                <td class="inq-row-total" data-col="total">{{ $mRow['total'] }}</td>

                                @if($canDelete)
                                    <td>
                                        <button type="button" class="btn-row-delete" title="Bu ayı sil" onclick="deleteRow({{ $mRow['id'] }}, '{{ $mRow['period_label'] }}')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr class="empty-year-row">
                                <td colspan="{{ $canDelete ? 11 : 10 }}" class="text-center text-muted py-4">
                                    Bu il üzrə hələ heç bir ay daxil edilməyib.
                                </td>
                            </tr>
                        @endforelse

                        {{-- ============================================================== --}}
                        {{-- TOTAL SƏTRİ (Şəkildəki çəhrayı/narıncı TOTAL kimi) --}}
                        {{-- ============================================================== --}}
                        <tr class="row-year-total" id="yearTotalRow-{{ $year }}">
                            <td style="text-align: left; padding-left: 14px; font-weight: 800;">YEKUN CƏM</td>
                            <td data-total-col="cancellations">{{ $yData['totals']['cancellations'] }}</td>
                            <td data-total-col="discounts">{{ $yData['totals']['discounts'] }}</td>
                            <td data-total-col="reservations">{{ $yData['totals']['reservations'] }}</td>
                            <td data-total-col="location">{{ $yData['totals']['location'] }}</td>
                            <td data-total-col="games">{{ $yData['totals']['games'] }}</td>
                            <td data-total-col="working_hours">{{ $yData['totals']['working_hours'] }}</td>
                            <td data-total-col="complaints">{{ $yData['totals']['complaints'] }}</td>
                            <td data-total-col="other">{{ $yData['totals']['other'] }}</td>
                            <td data-total-col="total">{{ $yData['totals']['total'] }}</td>
                            @if($canDelete)
                                <td>—</td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

</div>



@endsection

@push('scripts')
<script>
    // ---------------- Accordion Toggle ----------------
    function toggleYearAccordion(year) {
        const box = document.getElementById(`yearBox-${year}`);
        if (box) {
            box.classList.toggle('is-collapsed');
        }
    }

    // ---------------- Canlı Sətir və Cədvəl Hesablanması ----------------
    function recalcRow(input) {
        input.classList.add('is-dirty');
        const tr = input.closest('tr');
        if (!tr) return;

        let rowSum = 0;
        const numberInputs = tr.querySelectorAll('input[type="number"]');
        numberInputs.forEach(inp => {
            const val = parseInt(inp.value) || 0;
            rowSum += val;
        });

        const totalCell = tr.querySelector('[data-col="total"]');
        if (totalCell) {
            totalCell.textContent = rowSum;
        }

        const table = tr.closest('table');
        if (table) {
            recalcTableTotals(table);
        }
    }

    function recalcTableTotals(table) {
        const year = table.dataset.year;
        const cols = ['cancellations', 'discounts', 'reservations', 'location', 'games', 'working_hours', 'complaints', 'other'];
        const colTotals = {};
        cols.forEach(c => colTotals[c] = 0);
        let yearGrandTotal = 0;

        const dataRows = table.querySelectorAll('tbody tr:not(.row-year-total):not(.empty-year-row)');
        dataRows.forEach(tr => {
            cols.forEach(c => {
                const inp = tr.querySelector(`input[data-col="${c}"]`);
                if (inp) {
                    colTotals[c] += (parseInt(inp.value) || 0);
                }
            });

            const rowTotCell = tr.querySelector('[data-col="total"]');
            if (rowTotCell) {
                yearGrandTotal += (parseInt(rowTotCell.textContent) || 0);
            }
        });

        // Cədvəlin alt TOTAL sətrini yenilə
        const totalRow = document.getElementById(`yearTotalRow-${year}`);
        if (totalRow) {
            cols.forEach(c => {
                const cell = totalRow.querySelector(`[data-total-col="${c}"]`);
                if (cell) cell.textContent = colTotals[c];
            });
            const grandCell = totalRow.querySelector(`[data-total-col="total"]`);
            if (grandCell) grandCell.textContent = yearGrandTotal;
        }

        // Qarmon başlığındakı cəmi yenilə
        const headerTotal = document.getElementById(`yearTotal-${year}`);
        if (headerTotal) {
            headerTotal.textContent = yearGrandTotal.toLocaleString();
        }

        recalcGlobalKpis();
    }

    function recalcGlobalKpis() {
        let globalTotal = 0;
        let globalReservations = 0;
        let globalGames = 0;
        let globalComplaints = 0;

        document.querySelectorAll('.inquiry-table').forEach(table => {
            table.querySelectorAll('tbody tr:not(.row-year-total):not(.empty-year-row)').forEach(tr => {
                const tot = parseInt(tr.querySelector('[data-col="total"]')?.textContent) || 0;
                const res = parseInt(tr.querySelector('input[data-col="reservations"]')?.value) || 0;
                const gam = parseInt(tr.querySelector('input[data-col="games"]')?.value) || 0;
                const com = parseInt(tr.querySelector('input[data-col="complaints"]')?.value) || 0;

                globalTotal += tot;
                globalReservations += res;
                globalGames += gam;
                globalComplaints += com;
            });
        });

        const kpiTot = document.getElementById('kpiGrandTotal');
        const kpiRes = document.getElementById('kpiReservations');
        const kpiGam = document.getElementById('kpiGames');
        const kpiCom = document.getElementById('kpiComplaints');

        if (kpiTot) kpiTot.textContent = globalTotal.toLocaleString();
        if (kpiRes) kpiRes.textContent = globalReservations.toLocaleString();
        if (kpiGam) kpiGam.textContent = globalGames.toLocaleString();
        if (kpiCom) kpiCom.textContent = globalComplaints.toLocaleString();
    }

    // ---------------- Hamısını Yadda Saxla (Ctrl+S və ya Düymə) ----------------
    function saveAllChanges() {
        const rows = [];
        document.querySelectorAll('.inquiry-table tbody tr:not(.row-year-total):not(.empty-year-row)').forEach(tr => {
            const year = tr.dataset.year;
            const month = tr.dataset.month;
            const id = tr.dataset.id;
            const label = tr.querySelector('.inq-month-label')?.textContent.trim();

            const rowData = {
                id: id,
                year: parseInt(year),
                month: parseInt(month),
                period_label: label,
                cancellations: parseInt(tr.querySelector('input[data-col="cancellations"]')?.value) || 0,
                discounts: parseInt(tr.querySelector('input[data-col="discounts"]')?.value) || 0,
                reservations: parseInt(tr.querySelector('input[data-col="reservations"]')?.value) || 0,
                location: parseInt(tr.querySelector('input[data-col="location"]')?.value) || 0,
                games: parseInt(tr.querySelector('input[data-col="games"]')?.value) || 0,
                working_hours: parseInt(tr.querySelector('input[data-col="working_hours"]')?.value) || 0,
                complaints: parseInt(tr.querySelector('input[data-col="complaints"]')?.value) || 0,
                other: parseInt(tr.querySelector('input[data-col="other"]')?.value) || 0,
            };

            rows.push(rowData);
        });

        const btn = document.getElementById('btnSaveAll');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saxlanılır...`;
        }

        fetch("{{ route('crm.inquiries.save') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ rows: rows })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Yadda Saxla (Ctrl+S)`;
            }

            if (data.success) {
                document.querySelectorAll('.inq-cell-input.is-dirty').forEach(inp => inp.classList.remove('is-dirty'));
                showToastNotification(data.message || 'Məlumatlar uğurla saxlanıldı!', 'success');
            } else {
                showToastNotification(data.message || 'Xəta baş verdi', 'error');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `Yadda Saxla (Ctrl+S)`;
            }
            showToastNotification('Serverlə əlaqə xətası', 'error');
        });
    }

    // ---------------- Sətri Sil ----------------
    function deleteRow(id, label = '') {
        const confirmMsg = label ? `"${label}" statistika sətrini silmək istədiyinizdən əminsiniz?` : 'Bu ayın statistikasını silmək istədiyinizdən əminsiniz?';
        if (!confirm(confirmMsg)) return;

        fetch(`/crm/inquiries/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const tr = document.getElementById(`row-${id}`);
                if (tr) {
                    const table = tr.closest('table');
                    tr.style.opacity = '0';
                    tr.style.transform = 'scale(0.96)';
                    setTimeout(() => {
                        tr.remove();
                        if (table) recalcTableTotals(table);
                    }, 200);
                }
                showToastNotification(data.message || 'Sətir silindi!', 'success');
            } else {
                showToastNotification(data.message || 'Xəta baş verdi', 'error');
            }
        })
        .catch(err => {
            showToastNotification('Əlaqə xətası', 'error');
        });
    }



    // ---------------- Klaviatura Qısayolu (Ctrl+S / Cmd+S) ----------------
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
            e.preventDefault();
            @if($canEdit)
                saveAllChanges();
            @endif
        }
    });

    // ---------------- Toast Bildiriş ----------------
    function showToastNotification(message, type = 'success') {
        const toast = document.createElement('div');
        toast.style.position = 'fixed';
        toast.style.bottom = '24px';
        toast.style.right = '24px';
        toast.style.padding = '12px 20px';
        toast.style.borderRadius = '10px';
        toast.style.color = '#ffffff';
        toast.style.fontWeight = '600';
        toast.style.fontSize = '13.5px';
        toast.style.zIndex = '99999';
        toast.style.boxShadow = '0 6px 20px rgba(0,0,0,0.18)';
        toast.style.transition = 'all 0.3s ease';

        if (type === 'success') {
            toast.style.background = '#107c41';
        } else {
            toast.style.background = '#dc2626';
        }

        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
@endpush
