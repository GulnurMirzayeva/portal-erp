@extends('layouts.erp')

@section('title', 'Yeni Xərc Təsnifatı')
@section('page-title', 'YENİ TƏSNİFAT')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Xərclər</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expense-classifications.index') }}">Təsnifatlar</a></li>
    <li class="breadcrumb-item active">Yeni</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5" style="max-width: 520px;">

        <div class="card shadow-sm border" style="border-radius: 10px; border-color: #e2e8f0 !important;">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold text-dark mb-0 fs-6">Yeni Xərc Təsnifatı</h5>
                    <small class="text-muted" style="font-size: 12px;">Yeni kateqoriya əlavə edin</small>
                </div>
                <a href="{{ route('expense-classifications.index') }}" class="btn btn-sm btn-light border py-1 px-2.5" style="font-size: 12px;">
                    &larr; Geri
                </a>
            </div>

            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger py-2 px-3 mb-3 small" role="alert" style="border-radius: 6px;">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('expense-classifications.store') }}" method="POST">
                    @csrf

                    {{-- Təsnifat Adı --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small mb-1">
                            Təsnifat Adı <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               class="form-control form-control-sm @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Məs: Maaş, Arenda, Taksi, PPX..."
                               style="height: 36px; font-size: 13px;"
                               required
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Sıralama Nömrəsi --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small mb-1">
                            Sıralama Nömrəsi
                        </label>
                        <input type="number"
                               name="sort_order"
                               class="form-control form-control-sm @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order', $nextOrder) }}"
                               style="height: 36px; font-size: 13px;"
                               min="1">
                        <small class="text-muted" style="font-size: 11px;">Açılan siyahılarda görünmə sırası</small>
                    </div>

                    {{-- Status --}}
                    <div class="mb-4 pt-1">
                        <div class="form-check form-switch d-flex align-items-center gap-2">
                            <input class="form-check-input ms-0"
                                   type="checkbox"
                                   role="switch"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                   style="width: 34px; height: 18px; cursor: pointer;">
                            <label class="form-check-label small fw-semibold text-dark mb-0" for="is_active" style="cursor: pointer;">
                                Aktiv olsun (xərc seçimlərində görünsün)
                            </label>
                        </div>
                    </div>

                    {{-- Düymələr --}}
                    <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('expense-classifications.index') }}"
                           class="btn btn-sm btn-light border px-3"
                           style="height: 34px; font-size: 12.5px;">
                            İmtina
                        </a>
                        <button type="submit"
                                class="btn btn-sm fw-semibold text-white px-3 d-inline-flex align-items-center gap-1.5"
                                style="background-color: var(--erp-primary, #107c41); height: 34px; font-size: 12.5px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Əlavə Et</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
