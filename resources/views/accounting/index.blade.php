@extends('layouts.erp')

@section('title', 'Mühasibatlıq Hesabatı (32 Sütun)')
@section('page-title', 'MÜHASİBATLIQ HESABATI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item active">32 Sütunlu Cədvəl</li>
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

{{-- Filters Card --}}
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('accounting.index') }}" class="row align-items-end g-3">
            {{-- Month Filter --}}
            <div class="col-md-3">
                <label class="form-label font-size-12 font-weight-600 text-muted mb-1">Hesabat Ayı</label>
                <input type="month" name="month" value="{{ $filterMonth }}" class="form-control font-size-13" style="height: 38px; border-radius: 4px; border: 1px solid #ced4da;">
            </div>

            {{-- Branch Filter --}}
            <div class="col-md-3">
                <label class="form-label font-size-12 font-weight-600 text-muted mb-1">Filial</label>
                <select name="branch_id" class="form-control font-size-13" style="height: 38px; border-radius: 4px; border: 1px solid #ced4da;">
                    <option value="">Bütün Filiallar</option>
                    @foreach($branches as $b)
                        <option value="{{ $b['id'] }}" @selected($filterBranch == $b['id'])>{{ $b['name'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="col-md-6 d-flex align-items-center justify-content-end gap-2">
                <button type="submit" class="btn btn-primary" style="height: 38px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Filtrlə
                </button>

                <a href="{{ route('accounting.index') }}" class="btn btn-light" style="height: 38px;">
                    Sıfırla
                </a>

                <a href="{{ route('accounting.export', request()->all()) }}" class="btn btn-success ms-auto" style="height: 38px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    CSV İxrac
                </a>
            </div>
        </form>
    </div>
</div>

{{-- KPI Summary Widgets --}}
<div class="row g-3 mb-4">
    <div class="col-md-2 col-sm-4">
        <div class="card p-3 mb-0 h-100 border-start border-4 border-primary">
            <span class="font-size-12 text-muted font-weight-500">Satış Sayı</span>
            <h4 class="font-size-20 font-weight-700 m-0 mt-1">{{ number_format($summary['total_sales'] ?? 0) }}</h4>
        </div>
    </div>
    <div class="col-md-2 col-sm-4">
        <div class="card p-3 mb-0 h-100 border-start border-4 border-success">
            <span class="font-size-12 text-muted font-weight-500">Ümumi Dövriyyə</span>
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

{{-- 32 Column Full Accounting Table --}}
<div class="card">
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
                                    <p class="font-size-16 mb-1">Seçilmiş dövr və filtr üçün heç bir satış tapılmadı.</p>
                                    <small>Yuxarıdakı filtrdən ayı və ya filialı dəyişərək axtarın.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
