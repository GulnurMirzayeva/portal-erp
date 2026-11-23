@extends('layouts.erp')

@section('title', 'Yeni İşçi Tipi')
@section('page-title', 'YENİ İŞÇİ TİPİ YARAT')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="{{ route('worker-types.index') }}">İşçi Tipləri</a></li>
    <li class="breadcrumb-item active">Yeni Tip</li>
@endsection

@section('content')
<div class="att-wrapper">
    <div class="att-top-bar">
        <div>
            <h5 class="att-top-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Yeni Dinamik İşçi Tipi
            </h5>
            <p class="att-top-desc">Vəzifənin davranış qaydalarını, tabel kodunu və vizual yarış xalını təyin edin.</p>
        </div>
        <a href="{{ route('worker-types.index') }}" class="att-btn att-btn-outline">
            ← İşçi Tiplərinə Qayıt
        </a>
    </div>

    <form action="{{ route('worker-types.store') }}" method="POST">
        @csrf
        @include('worker_types._form')

        <div class="settings-bottom-bar">
            <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 13px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Yadda saxlandıqdan sonra yeni tip dərhal ERP-də və API cavablarında aktivləşəcək.</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('worker-types.index') }}" class="att-btn att-btn-outline">İmtina</a>
                <button type="submit" class="att-btn att-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    İşçi Tipini Yadda Saxla
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
