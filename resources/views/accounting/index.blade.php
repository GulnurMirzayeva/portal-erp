@extends('layouts.erp')

@section('title', 'Elektron Qaimələr')
@section('page-title', 'ELEKTRON QAİMƏLƏR')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item active">Elektron Qaimələr</li>
@endsection

@section('content')
{{-- Connection Warning --}}
@if(!$connected)
    <div class="card border-0 shadow-sm mb-4" style="background: #fff3cd; border-left: 4px solid #ffc107 !important;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="font-size-22 me-3">⚠️</span>
                    <div>
                        <h6 class="m-0 font-weight-700 text-dark font-size-14">PortalWebsite API Əlaqəsi Gözlənilir</h6>
                        <p class="m-0 font-size-12 text-muted">{{ $error ?? 'Əsas saytın API serverinə qoşulmaq mümkün olmadı.' }}</p>
                    </div>
                </div>
                <button type="button" onclick="location.reload()" class="btn btn-sm btn-warning font-size-12 font-weight-600">
                    🔄 Yenidən Yoxla
                </button>
            </div>
        </div>
    </div>
@endif

{{-- Branch Selector Tabs / Bar --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary font-size-12 px-2 py-1">
                    🏢 Filiallar
                </span>
                <span class="font-size-13 font-weight-600 text-dark">Qaiməsinə baxmaq istədiyiniz filialı seçin:</span>
            </div>
            <span class="font-size-12 text-muted">Cəmi: <strong>{{ count($branches) }}</strong> filial mövcuddur</span>
        </div>

        {{-- Filial Buttons / Pills --}}
        <div class="d-flex flex-wrap gap-2 pt-1" style="max-height: 140px; overflow-y: auto;">
            @foreach($branches as $b)
                @php
                    $isActive = ($filterBranch == $b['id']);
                @endphp
                <a href="{{ route('accounting.index', ['branch_id' => $b['id'], 'month' => $filterMonth]) }}"
                   class="btn btn-sm {{ $isActive ? 'btn-primary shadow-sm font-weight-700' : 'btn-light border text-dark font-weight-500' }} d-inline-flex align-items-center gap-2 px-3 py-2"
                   style="border-radius: 6px; transition: all 0.15s ease-in-out;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 21h18"></path>
                        <path d="M5 21V7l8-4v18"></path>
                        <path d="M19 21V11l-6-3"></path>
                        <line x1="9" x2="9" y1="9" y2="9"></line>
                        <line x1="9" x2="9" y1="13" y2="13"></line>
                        <line x1="9" x2="9" y1="17" y2="17"></line>
                    </svg>
                    <span>{{ $b['name'] }}</span>
                    @if($isActive)
                        <span class="badge bg-white text-primary px-1 font-size-10" style="line-height: 1;">Aktiv</span>
                    @endif
                </a>
            @endforeach

            {{-- All Branches Option --}}
            <a href="{{ route('accounting.index', ['branch_id' => 'all', 'month' => $filterMonth]) }}"
               class="btn btn-sm {{ $filterBranch === 'all' ? 'btn-dark shadow-sm font-weight-700' : 'btn-outline-secondary font-weight-500' }} d-inline-flex align-items-center gap-2 px-3 py-2 ms-auto"
               style="border-radius: 6px;">
                <span>🌐 Bütün Filiallar (Ümumi)</span>
            </a>
        </div>
    </div>
</div>

{{-- Document Header & Actions Bar --}}
<div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%); border-left: 4px solid #556ee6 !important;">
    <div class="card-body p-3">
        <div class="row align-items-center gy-3">
            {{-- Selected Branch Info --}}
            <div class="col-lg-6">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 46px; height: 46px; min-width: 46px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h5 class="m-0 font-weight-700 text-dark font-size-16">{{ $selectedBranchName }}</h5>
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-size-11">
                                Elektron Satış Qaiməsi
                            </span>
                        </div>
                        <p class="m-0 font-size-12 text-muted mt-1">
                            Dövr: <strong>{{ \Carbon\Carbon::parse($filterMonth . '-01')->translatedFormat('F Y') }}</strong>
                            &nbsp;•&nbsp; Satış sayı: <strong>{{ count($sales) }} ədəd</strong>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Month Filter & Download Buttons --}}
            <div class="col-lg-6">
                <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                    {{-- Quick Month Switcher --}}
                    <form method="GET" action="{{ route('accounting.index') }}" class="d-flex align-items-center gap-1">
                        <input type="hidden" name="branch_id" value="{{ $filterBranch }}">
                        <input type="month" name="month" value="{{ $filterMonth }}" onchange="this.form.submit()" class="form-control form-control-sm font-size-12" style="width: 145px; height: 35px; border-radius: 4px;">
                    </form>

                    {{-- Export to Excel (.xls) --}}
                    <a href="{{ route('accounting.export', array_merge(request()->all(), ['format' => 'excel', 'branch_id' => $filterBranch, 'month' => $filterMonth])) }}"
                       class="btn btn-success btn-sm font-size-12 font-weight-600 d-inline-flex align-items-center gap-1 shadow-sm"
                       style="height: 35px; border-radius: 4px;"
                       title="Bu filialın satış datasını rəsmi Excel cədvəli kimi yüklə">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="8" y1="13" x2="16" y2="17"></line>
                            <line x1="16" y1="13" x2="8" y2="17"></line>
                        </svg>
                        <span>Excel Yüklə (.xls)</span>
                    </a>

                    {{-- Export to CSV --}}
                    <a href="{{ route('accounting.export', array_merge(request()->all(), ['format' => 'csv', 'branch_id' => $filterBranch, 'month' => $filterMonth])) }}"
                       class="btn btn-light btn-sm border font-size-12 font-weight-500 d-inline-flex align-items-center gap-1"
                       style="height: 35px; border-radius: 4px;"
                       title="CSV formatında yüklə">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>CSV</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- KPI Summary Widgets for Selected Branch --}}
<div class="row g-3 mb-4">
    <div class="col-md-2 col-sm-4">
        <div class="card p-3 mb-0 h-100 border-start border-4 border-primary">
            <span class="font-size-12 text-muted font-weight-500">Filial Satış Sayı</span>
            <h4 class="font-size-20 font-weight-700 m-0 mt-1">{{ number_format($summary['total_sales'] ?? 0) }}</h4>
        </div>
    </div>
    <div class="col-md-2 col-sm-4">
        <div class="card p-3 mb-0 h-100 border-start border-4 border-success">
            <span class="font-size-12 text-muted font-weight-500">Filial Dövriyyəsi</span>
            <h4 class="font-size-20 font-weight-700 m-0 mt-1 text-success">{{ number_format($summary['total_revenue'] ?? 0, 2) }} ₼</h4>
        </div>
    </div>
    <div class="col-md-2 col-sm-4">
        <div class="card p-3 mb-0 h-100 border-start border-4 border-info">
            <span class="font-size-12 text-muted font-weight-500">Nağd Məbləğ</span>
            <h4 class="font-size-20 font-weight-700 m-0 mt-1">{{ number_format($summary['total_cash'] ?? 0, 2) }} ₼</h4>
        </div>
    </div>
    <div class="col-md-2 col-sm-4">
        <div class="card p-3 mb-0 h-100 border-start border-4 border-warning">
            <span class="font-size-12 text-muted font-weight-500">Posterminal (Kart)</span>
            <h4 class="font-size-20 font-weight-700 m-0 mt-1">{{ number_format($summary['total_card'] ?? 0, 2) }} ₼</h4>
        </div>
    </div>
    <div class="col-md-2 col-sm-4">
        <div class="card p-3 mb-0 h-100 border-start border-4 border-danger">
            <span class="font-size-12 text-muted font-weight-500">🌙 00:00 Bonusu</span>
            <h4 class="font-size-20 font-weight-700 m-0 mt-1 text-danger">{{ number_format($summary['total_midnight_bonuses'] ?? 0) }}</h4>
        </div>
    </div>
    <div class="col-md-2 col-sm-4">
        <div class="card p-3 mb-0 h-100 border-start border-4 border-primary">
            <span class="font-size-12 text-muted font-weight-500">⭐ PlusBir Bonusu</span>
            <h4 class="font-size-20 font-weight-700 m-0 mt-1 text-primary">{{ number_format($summary['total_plus_one_bonuses'] ?? 0) }}</h4>
        </div>
    </div>
</div>

{{-- Excel Spreadsheet Table View --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <span class="font-size-14 font-weight-700 text-dark">
                📊 {{ $selectedBranchName }} — Satış Cədvəli
            </span>
            <span class="badge bg-light text-muted border font-size-11">
                {{ count($sales) }} əməliyyat
            </span>
        </div>
        <small class="text-muted font-size-12">
            Excel standartında bütün sütunlar və işçi rolları üzrə bölgü
        </small>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 700px; overflow-x: auto; overflow-y: auto;">
            <table class="table table-bordered table-hover align-middle mb-0 font-size-12 text-nowrap accounting-table">
                <thead class="table-light sticky-top text-center" style="z-index: 10;">
                    <tr>
                        <th colspan="9" class="bg-primary text-white py-2">SATIŞ MƏLUMATLARI</th>
                        <th colspan="3" class="bg-secondary text-white py-2">RESEPŞN</th>
                        <th colspan="3" class="bg-dark text-white py-2">HOSTES</th>
                        <th colspan="3" class="bg-info text-white py-2">AKTYOR 1</th>
                        <th colspan="3" class="bg-info text-white py-2">AKTYOR 2</th>
                        <th colspan="3" class="bg-secondary text-white py-2">OPERATOR</th>
                        <th colspan="2" class="bg-warning text-dark py-2">⭐ PLUS-BİR AKTYOR</th>
                        <th colspan="2" class="bg-warning text-dark py-2">⭐ PLUS-BİR RESEPŞN</th>
                        <th colspan="4" class="bg-success text-white py-2">MÜŞTƏRİ VƏ YEKUN</th>
                    </tr>
                    <tr class="font-size-11 text-uppercase text-muted" style="background: #f8f9fa;">
                        {{-- 1-9 --}}
                        <th style="min-width: 45px;">№</th>
                        <th style="min-width: 90px;">Tarix</th>
                        <th style="min-width: 120px;">Oyun</th>
                        <th style="min-width: 65px;">Saat</th>
                        <th style="min-width: 50px;">Say</th>
                        <th style="min-width: 75px;">Qiymət</th>
                        <th style="min-width: 85px;">Nağd</th>
                        <th style="min-width: 85px;">Terminal</th>
                        <th style="min-width: 130px;">Qeyd / Endirim</th>

                        {{-- 10-12 Resepşn --}}
                        <th style="min-width: 130px;">İşçi</th>
                        <th style="min-width: 50px;">Sayı</th>
                        <th style="min-width: 75px;">00:00</th>

                        {{-- 13-15 Hostes --}}
                        <th style="min-width: 130px;">İşçi</th>
                        <th style="min-width: 50px;">Sayı</th>
                        <th style="min-width: 75px;">00:00</th>

                        {{-- 16-18 Aktyor 1 --}}
                        <th style="min-width: 130px;">İşçi</th>
                        <th style="min-width: 50px;">Sayı</th>
                        <th style="min-width: 75px;">00:00</th>

                        {{-- 19-21 Aktyor 2 --}}
                        <th style="min-width: 130px;">İşçi</th>
                        <th style="min-width: 50px;">Sayı</th>
                        <th style="min-width: 75px;">00:00</th>

                        {{-- 22-24 Operator --}}
                        <th style="min-width: 130px;">İşçi</th>
                        <th style="min-width: 50px;">Sayı</th>
                        <th style="min-width: 75px;">00:00</th>

                        {{-- 25-26 PlusBir Aktyor --}}
                        <th style="min-width: 130px;">İşçi</th>
                        <th style="min-width: 50px;">Sayı</th>

                        {{-- 27-28 PlusBir Resepşn --}}
                        <th style="min-width: 130px;">İşçi</th>
                        <th style="min-width: 50px;">Sayı</th>

                        {{-- 29-32 Müştəri & Yekun --}}
                        <th style="min-width: 140px;">Müştəri</th>
                        <th style="min-width: 120px;">Telefon</th>
                        <th style="min-width: 140px;">Rəy / Qeyd</th>
                        <th style="min-width: 95px;" class="bg-success-subtle text-dark font-weight-700">Ümumi Gəlir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $row)
                        <tr>
                            {{-- 1-9 --}}
                            <td class="text-center font-weight-600">{{ $row['row_number'] }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td class="font-weight-600 text-primary">{{ $row['game_name'] }}</td>
                            <td class="text-center">
                                <span class="badge {{ $row['is_midnight'] ? 'bg-danger' : 'bg-light text-dark' }}">
                                    {{ $row['time'] }}
                                </span>
                            </td>
                            <td class="text-center">{{ $row['player_count'] }}</td>
                            <td class="text-end">{{ $row['price_per_person'] ? number_format($row['price_per_person'], 2) . ' ₼' : '—' }}</td>
                            <td class="text-end font-weight-500">{{ $row['cash_amount'] > 0 ? number_format($row['cash_amount'], 2) . ' ₼' : '—' }}</td>
                            <td class="text-end font-weight-500">{{ $row['card_amount'] > 0 ? number_format($row['card_amount'], 2) . ' ₼' : '—' }}</td>
                            <td><small class="text-muted">{{ $row['discount_note'] ?: '—' }}</small></td>

                            {{-- 10-12 Resepşn --}}
                            <td>{{ $row['receptionist_name'] ?? '—' }}</td>
                            <td class="text-center">{{ $row['receptionist_count'] ?? '—' }}</td>
                            <td class="text-center">
                                @if($row['receptionist_bonus_00'])
                                    <span class="badge bg-danger">🌙 1</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- 13-15 Hostes --}}
                            <td>{{ $row['hostess_name'] ?? '—' }}</td>
                            <td class="text-center">{{ $row['hostess_count'] ?? '—' }}</td>
                            <td class="text-center">
                                @if($row['hostess_bonus_00'])
                                    <span class="badge bg-danger">🌙 1</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- 16-18 Aktyor 1 --}}
                            <td>{{ $row['actor1_name'] ?? '—' }}</td>
                            <td class="text-center">{{ $row['actor1_count'] ?? '—' }}</td>
                            <td class="text-center">
                                @if($row['actor1_bonus_00'])
                                    <span class="badge bg-danger">🌙 1</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- 19-21 Aktyor 2 --}}
                            <td>{{ $row['actor2_name'] ?? '—' }}</td>
                            <td class="text-center">{{ $row['actor2_count'] ?? '—' }}</td>
                            <td class="text-center">
                                @if($row['actor2_bonus_00'])
                                    <span class="badge bg-danger">🌙 1</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- 22-24 Operator --}}
                            <td>{{ $row['operator_name'] ?? '—' }}</td>
                            <td class="text-center">{{ $row['operator_count'] ?? '—' }}</td>
                            <td class="text-center">
                                @if($row['operator_bonus_00'])
                                    <span class="badge bg-danger">🌙 1</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- 25-26 PlusBir Aktyor --}}
                            <td>
                                @if($row['plus_one_actor'])
                                    <span class="badge bg-soft-warning text-dark font-weight-600">⭐ {{ $row['plus_one_actor'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $row['plus_one_actor_count'] ?? '—' }}</td>

                            {{-- 27-28 PlusBir Resepşn --}}
                            <td>
                                @if($row['plus_one_receptionist'])
                                    <span class="badge bg-soft-warning text-dark font-weight-600">⭐ {{ $row['plus_one_receptionist'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $row['plus_one_receptionist_count'] ?? '—' }}</td>

                            {{-- 29-32 Müştəri & Yekun --}}
                            <td>{{ $row['customer_name'] }}</td>
                            <td>{{ $row['customer_phone'] }}</td>
                            <td><small class="text-muted">{{ $row['note'] ?: '—' }}</small></td>
                            <td class="text-end font-weight-700 text-success bg-light">
                                {{ number_format($row['total_price'], 2) }} ₼
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="32" class="text-center py-5">
                                <div class="text-muted">
                                    <div class="mb-2">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="1.5">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="8" y1="12" x2="16" y2="12"></line>
                                        </svg>
                                    </div>
                                    <p class="font-size-15 mb-1 font-weight-600 text-dark">Bu filial üzrə seçilmiş ayda heç bir satış tapılmadı.</p>
                                    <small class="text-muted">Yuxarıdakı filial düymələrindən digər filialları seçə və ya hesabat ayını dəyişə bilərsiniz.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if(count($sales) > 0)
                <tfoot class="sticky-bottom" style="z-index: 9; background: #eef2f7; font-weight: bold; border-top: 2px solid #556ee6;">
                    <tr>
                        <td colspan="4" class="text-center py-2 text-primary font-size-13 font-weight-700">
                            CƏMİ ({{ count($sales) }} Satış)
                        </td>
                        <td class="text-center font-weight-700">{{ number_format($summary['total_players'] ?? 0) }}</td>
                        <td class="text-muted text-end">—</td>
                        <td class="text-end font-weight-700 text-dark">{{ number_format($summary['total_cash'] ?? 0, 2) }} ₼</td>
                        <td class="text-end font-weight-700 text-dark">{{ number_format($summary['total_card'] ?? 0, 2) }} ₼</td>
                        <td class="text-muted text-center">—</td>

                        {{-- Resepşn --}}
                        <td class="text-muted">—</td>
                        <td class="text-center font-weight-700">{{ $summary['total_sales'] ?? 0 }}</td>
                        <td class="text-center text-danger font-weight-700">{{ $summary['total_midnight_bonuses'] ?? 0 }}</td>

                        {{-- Hostes --}}
                        <td class="text-muted">—</td>
                        <td class="text-center font-weight-700">{{ $summary['total_sales'] ?? 0 }}</td>
                        <td class="text-center text-danger font-weight-700">{{ $summary['total_midnight_bonuses'] ?? 0 }}</td>

                        {{-- Aktyor 1 --}}
                        <td class="text-muted">—</td>
                        <td class="text-center font-weight-700">{{ $summary['total_sales'] ?? 0 }}</td>
                        <td class="text-center text-danger font-weight-700">{{ $summary['total_midnight_bonuses'] ?? 0 }}</td>

                        {{-- Aktyor 2 --}}
                        <td class="text-muted">—</td>
                        <td class="text-muted">—</td>
                        <td class="text-muted">—</td>

                        {{-- Operator --}}
                        <td class="text-muted">—</td>
                        <td class="text-center font-weight-700">{{ $summary['total_sales'] ?? 0 }}</td>
                        <td class="text-center text-danger font-weight-700">{{ $summary['total_midnight_bonuses'] ?? 0 }}</td>

                        {{-- PlusBir Aktyor --}}
                        <td class="text-muted">—</td>
                        <td class="text-center text-warning font-weight-700">{{ $summary['total_plus_one_bonuses'] ?? 0 }}</td>

                        {{-- PlusBir Resepşn --}}
                        <td class="text-muted">—</td>
                        <td class="text-center text-warning font-weight-700">{{ $summary['total_plus_one_bonuses'] ?? 0 }}</td>

                        {{-- Müştəri & Yekun --}}
                        <td class="text-muted">—</td>
                        <td class="text-muted">—</td>
                        <td class="text-muted">—</td>
                        <td class="text-end font-weight-700 font-size-14 text-success bg-white border-start border-success">
                            {{ number_format($summary['total_revenue'] ?? 0, 2) }} ₼
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
