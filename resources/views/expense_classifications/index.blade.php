@extends('layouts.erp')

@section('title', 'Xərc Təsnifatları')
@section('page-title', 'XƏRC TƏSNİFATLARI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Xərclər</a></li>
    <li class="breadcrumb-item active">Təsnifatlar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-all me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary p-2 fs-6">
                        <i class="mdi mdi-format-list-bulleted-type"></i>
                    </span>
                    <div>
                        <h5 class="card-title mb-0">Xərc Təsnifatı Siyahısı</h5>
                        <small class="text-muted">Mühasibatlıq xərclərinin kateqoriyalarını idarə edin</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="GET" action="{{ route('expense-classifications.index') }}" class="d-flex align-items-center gap-1">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Axtarış..." style="min-width: 180px;">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                            @if($search)
                                <a href="{{ route('expense-classifications.index') }}" class="btn btn-outline-danger" title="Sıfırla">
                                    <i class="mdi mdi-close"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="mdi mdi-plus fs-5"></i> Yeni Təsnifat
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;" class="text-center">№</th>
                                <th>Təsnifat Adı</th>
                                <th style="width: 120px;" class="text-center">Sıralama</th>
                                <th style="width: 140px;" class="text-center">Status</th>
                                <th style="width: 160px;" class="text-end pe-4">Əməliyyatlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classifications as $index => $item)
                                <tr>
                                    <td class="text-center fw-medium text-muted">
                                        {{ $classifications->firstItem() + $index }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="avatar-xs d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-primary fw-bold">
                                                {{ mb_substr($item->name, 0, 1) }}
                                            </span>
                                            <span class="fw-semibold text-dark">{{ $item->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center text-muted">
                                        {{ $item->sort_order }}
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                                   data-id="{{ $item->id }}"
                                                   {{ $item->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label small ms-1 text-muted status-label-{{ $item->id }}">
                                                {{ $item->is_active ? 'Aktiv' : 'Deaktiv' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-outline-primary btn-sm edit-btn"
                                                    data-id="{{ $item->id }}"
                                                    data-name="{{ $item->name }}"
                                                    data-sort="{{ $item->sort_order }}"
                                                    data-active="{{ $item->is_active ? '1' : '0' }}"
                                                    title="Redaktə et">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <form action="{{ route('expense-classifications.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu təsnifatı silmək istədiyinizdən əminsiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Sil">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="mdi mdi-information-outline fs-3 d-block mb-1"></i>
                                        Heç bir xərc təsnifatı tapılmadı.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($classifications->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
                    {{ $classifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('expense-classifications.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Yeni Xərc Təsnifatı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Təsnifat Adı <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Məsələn: Maaş, Arenda, Taksi..." required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sıralama Nömrəsi</label>
                    <input type="number" name="sort_order" class="form-control" placeholder="0" value="{{ ($classifications->total() ?? 0) + 1 }}">
                    <small class="text-muted">Cədvəl dropdown-da görünmə ardıcıllığı</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">İmtina</button>
                <button type="submit" class="btn btn-primary">Əlavə Et</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Təsnifatı Redaktə Et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Təsnifat Adı <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sıralama Nömrəsi</label>
                    <input type="number" name="sort_order" id="editSort" class="form-control">
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editActive">
                        <label class="form-check-label fw-semibold" for="editActive">Aktiv olsun</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">İmtina</button>
                <button type="submit" class="btn btn-primary">Yadda Saxla</button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Edit modal trigger
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        const editForm = document.getElementById('editForm');
        const editName = document.getElementById('editName');
        const editSort = document.getElementById('editSort');
        const editActive = document.getElementById('editActive');

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const sort = this.getAttribute('data-sort');
                const active = this.getAttribute('data-active') === '1';

                editForm.action = `/expense-classifications/${id}`;
                editName.value = name;
                editSort.value = sort;
                editActive.checked = active;

                editModal.show();
            });
        });

        // Status switch AJAX toggle
        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function () {
                const id = this.getAttribute('data-id');
                const label = document.querySelector(`.status-label-${id}`);

                fetch(`/expense-classifications/${id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        label.textContent = data.is_active ? 'Aktiv' : 'Deaktiv';
                    }
                })
                .catch(err => {
                    console.error('Status toggle failed:', err);
                });
            });
        });
    });
</script>
@endpush
@endsection
