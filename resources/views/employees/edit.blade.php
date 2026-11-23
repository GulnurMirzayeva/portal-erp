@extends('layouts.erp')

@section('title', 'İşçiyə Düzəliş: ' . $employee->full_name)
@section('page-title', 'İŞÇİ KARTINA DÜZƏLİŞ')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">İşçilər</a></li>
    <li class="breadcrumb-item active">{{ $employee->full_name }}</li>
@endsection

@section('content')
<div class="att-wrapper">
    <div class="att-top-bar">
        <div>
            <h5 class="att-top-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                {{ $employee->full_name }} — İşçi Kartı
            </h5>
            <p class="att-top-desc">Vəzifələr, filiallar, oyunlar, stajorluq statusu və QR tənzimləmələri.</p>
        </div>
        <a href="{{ route('employees.index') }}" class="att-btn att-btn-outline">
            ← İşçilər Siyahısına Qayıt
        </a>
    </div>

    <form action="{{ route('employees.update', $employee->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('employees._form')

        <div class="settings-bottom-bar">
            <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 13px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Dəyişikliklər dərhal ERP tabelinə və QR giriş parametrlərinə tətbiq olunacaq.</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('employees.index') }}" class="att-btn att-btn-outline">İmtina</a>
                <button type="submit" class="att-btn att-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Dəyişiklikləri Yadda Saxla
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
