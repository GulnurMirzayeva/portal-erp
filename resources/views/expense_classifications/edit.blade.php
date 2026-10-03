@extends('layouts.erp')

@section('title', 'Təsnifata Düzəliş Et')
@section('page-title', 'TƏSNİFATA DÜZƏLİŞ ET')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Xərclər</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expense-classifications.index') }}">Təsnifatlar</a></li>
    <li class="breadcrumb-item active">Düzəliş et</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-6 col-lg-8 col-md-10">

        {{-- Geri Düyməsi və Başlıq --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('expense-classifications.index') }}"
                   class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 mb-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Təsnifatlar siyahısına qayıt</span>
                </a>
                <h4 class="fw-bold text-dark mb-1">Təsnifata Düzəliş Et: <span class="text-primary" style="color: var(--erp-primary, #107c41) !important;">{{ $classification->name }}</span></h4>
                <p class="text-muted small mb-0">Mövcud xərc kateqoriyasının adını və sıralamasını yeniləyin.</p>
            </div>
        </div>

        {{-- Xəta Mesajları --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="fw-bold mb-1">Zəhmət olmasa xətaları düzəldin:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Forma Kartı --}}
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('expense-classifications.update', $classification->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- 1. Təsnifat Adı --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">
                            Təsnifat Adı <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               class="form-control form-control-lg @error('name') is-invalid @enderror"
                               value="{{ old('name', $classification->name) }}"
                               placeholder="Məsələn: Maaş, Arenda, Taksi, Reklam, PPX..."
                               required
                               autofocus>
                        <small class="text-muted mt-1 d-block">
                            Bu ad filial adminləri xərc seçərkən və ERP xərc cədvəlində göstəriləcək.
                        </small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 2. Sıralama Nömrəsi --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">
                            Sıralama Nömrəsi (Sort Order)
                        </label>
                        <input type="number"
                               name="sort_order"
                               class="form-control @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order', $classification->sort_order) }}"
                               min="1">
                        <small class="text-muted mt-1 d-block">
                            Cədvəl və açılan siyahılarda təsnifatların hansı ardıcıllıqla düzüləcəyini təyin edir.
                        </small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 3. Status --}}
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input"
                                   type="checkbox"
                                   role="switch"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', $classification->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                                   style="width: 38px; height: 20px; cursor: pointer;">
                            <label class="form-check-label fw-bold text-dark ms-2" for="is_active" style="cursor: pointer;">
                                Aktiv olsun
                            </label>
                            <small class="text-muted d-block ms-2">
                                Qeyri-aktiv edilərsə, xərc daxil edərkən bu təsnifat seçim siyahısında görünməyəcək.
                            </small>
                        </div>
                    </div>

                    {{-- Düymələr: İmtina və Yadda Saxla --}}
                    <div class="d-flex align-items-center justify-content-end gap-3 pt-3 border-top">
                        <a href="{{ route('expense-classifications.index') }}"
                           class="btn btn-light px-4 py-2.5 fw-semibold border">
                            İmtina
                        </a>
                        <button type="submit"
                                class="btn btn-primary px-4 py-2.5 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm"
                                style="background-color: var(--erp-primary, #107c41); border-color: var(--erp-primary, #107c41);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            <span>Yadda Saxla</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
