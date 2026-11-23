@push('css')
<style>
    .form-container {
        max-width: 900px;
        margin: 0 auto;
    }

    /* Settings Card */
    .settings-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 22px 24px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        margin-bottom: 20px;
    }
    .settings-card-header {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #e2e8f0;
    }
    .settings-icon-badge {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .badge-emerald { background: #ecfdf5; color: #107c41; border: 1.5px solid #a7f3d0; }
    .badge-blue { background: #eff6ff; color: #2563eb; border: 1.5px solid #bfdbfe; }
    .badge-amber { background: #fffbeb; color: #d97706; border: 1.5px solid #fde68a; }
    .badge-purple { background: #fdf2f8; color: #db2777; border: 1.5px solid #fbcfe8; }
    .badge-slate { background: #f8fafc; color: #475569; border: 1.5px solid #cbd5e1; }

    .settings-title {
        font-size: 15.5px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 3px 0;
    }
    .settings-desc {
        font-size: 12.5px;
        color: #64748b;
        margin: 0;
        line-height: 1.4;
    }

    /* Grids */
    .form-grid-1 {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    @media (max-width: 640px) {
        .form-grid-2 { grid-template-columns: 1fr; }
    }
    .form-grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 14px;
    }
    @media (max-width: 768px) {
        .form-grid-3 { grid-template-columns: 1fr; }
    }

    .form-field-group {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    .att-field-label {
        font-size: 11.5px;
        font-weight: 700;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .att-field-label .req {
        color: #ef4444;
    }

    .att-input-standard {
        width: 100%;
        height: 42px;
        padding: 0 13px;
        font-size: 13.5px;
        font-weight: 600;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        outline: none;
        background: #ffffff;
        color: #0f172a;
        transition: all 0.15s ease;
        box-sizing: border-box;
    }
    .att-input-standard:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.12);
    }
    .att-textarea-standard {
        width: 100%;
        padding: 10px 13px;
        font-size: 13px;
        font-weight: 500;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        outline: none;
        background: #ffffff;
        color: #0f172a;
        transition: all 0.15s ease;
        box-sizing: border-box;
    }
    .att-textarea-standard:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.12);
    }
    .att-select-standard {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 14px 14px;
        padding-right: 34px;
        cursor: pointer;
        -webkit-appearance: none;
        appearance: none;
    }

    /* Option Cards */
    .option-card-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .option-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 11px 14px;
        background: #ffffff;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.18s ease;
        position: relative;
        margin: 0;
        user-select: none;
    }
    .option-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .option-card.active {
        border-color: #107c41;
        background: #f0fdf4;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.1);
    }
    .option-card input[type="radio"],
    .option-card input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .custom-indicator {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        border: 2px solid #cbd5e1;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }
    .custom-radio { border-radius: 50%; }
    .option-card.active .custom-indicator {
        border-color: #107c41;
        background: #107c41;
    }
    .custom-indicator svg {
        opacity: 0;
        color: #ffffff;
    }
    .option-card.active .custom-indicator svg {
        opacity: 1;
    }
    .opt-title {
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
    }
    .opt-desc {
        font-size: 11px;
        color: #64748b;
        margin: 2px 0 0 0;
    }

    /* Role selection cards */
    .role-select-item {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 14px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: all 0.15s ease;
        margin-bottom: 8px;
    }
    .role-select-item:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .role-select-item.selected {
        border-color: #107c41;
        background: #f0fdf4;
    }
    .role-pill-badge {
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        color: #ffffff;
    }

    /* Scrollable checklist box */
    .compact-checklist-box {
        max-height: 200px;
        overflow-y: auto;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 6px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .compact-check-item {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 8px 10px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.12s ease;
        margin: 0;
    }
    .compact-check-item:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .compact-check-item input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        margin: 0;
    }
    .compact-check-item span {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
    }

    /* Info Tip Box */
    .att-info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 12.5px;
        color: #475569;
        line-height: 1.4;
        margin-bottom: 14px;
    }

    /* Bottom Action Bar */
    .settings-bottom-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 14px;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
    }
    .att-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 13px;
        font-weight: 700;
        height: 40px;
        padding: 0 18px;
        border-radius: 10px;
        cursor: pointer;
        border: 1.5px solid transparent;
        transition: all 0.15s ease;
        text-decoration: none !important;
        box-sizing: border-box;
    }
    .att-btn-primary {
        background: #107c41;
        color: #ffffff !important;
        border-color: #0d6334;
        box-shadow: 0 2px 6px rgba(16, 124, 65, 0.2);
    }
    .att-btn-primary:hover {
        background: #0b5e30;
    }
    .att-btn-outline {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #334155 !important;
    }
    .att-btn-outline:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a !important;
    }
</style>
@endpush

@if(isset($errors) && $errors->any())
    <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 14px; padding: 14px 18px; margin-bottom: 20px; color: #991b1b; font-size: 13.5px;">
        <div style="font-weight: 800; margin-bottom: 4px;">Düzəliş tələb olunur:</div>
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $selectedWorkerTypeIds = old('worker_type_ids', $employee->workerTypes->pluck('id')->all() ?? []);
    $primaryWorkerTypeId = old('primary_worker_type_id', $employee->primaryWorkerType()?->id);
    $selectedBranchIds = old('managed_branch_ids', $employee->managedBranches->pluck('id')->all() ?? []);
    $primaryBranchId = old('primary_branch_id', $employee->primaryBranch()?->id);
    $selectedGameIds = old('game_ids', $employee->games->pluck('portal_game_id')->all() ?? []);
    $selGender = old('gender', $employee->gender ?? 'male');
    $selIsIntern = old('is_intern', $employee->is_intern ?? false);
@endphp

<div class="form-container">

    {{-- 1. Şəxsi Məlumatlar & Cinsiyyət --}}
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-icon-badge badge-emerald">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <div>
                <h5 class="settings-title">1. Şəxsi Məlumatlar & Cinsiyyət</h5>
                <p class="settings-desc">İşçinin tam adı, cinsiyyəti (Aktyor / Aktrisa təyinatı üçün), telefon və QR PIN kodu.</p>
            </div>
        </div>

        <div class="form-grid-1">
            <div class="form-field-group">
                <label class="att-field-label">Tam Adı (Ad və Soyad) <span class="req">*</span></label>
                <input type="text" name="full_name" id="inpFullName" class="att-input-standard" value="{{ old('full_name', $employee->full_name) }}" placeholder="Məs: Əli Əliyev və ya Nərgiz Səlimova" required>
            </div>

            <div class="form-grid-3">
                <div class="form-field-group">
                    <label class="att-field-label">Ad</label>
                    <input type="text" name="first_name" id="inpFirstName" class="att-input-standard" value="{{ old('first_name', $employee->first_name) }}" placeholder="Əli">
                </div>
                <div class="form-field-group">
                    <label class="att-field-label">Soyad</label>
                    <input type="text" name="last_name" id="inpLastName" class="att-input-standard" value="{{ old('last_name', $employee->last_name) }}" placeholder="Əliyev">
                </div>
                <div class="form-field-group">
                    <label class="att-field-label">Tabeldə Qısa Ad</label>
                    <input type="text" name="tabel_display_name" id="inpTabelDisplayName" class="att-input-standard" value="{{ old('tabel_display_name', $employee->tabel_display_name) }}" placeholder="Boş qalsa soyad götürülər">
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field-group">
                    <label class="att-field-label">Telefon Nömrəsi</label>
                    <input type="text" name="phone" class="att-input-standard" value="{{ old('phone', $employee->phone) }}" placeholder="+994 50 123 45 67">
                </div>
                <div class="form-field-group">
                    <label class="att-field-label">QR Giriş PIN Kodu <span class="req">*</span></label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" name="pin_code" id="inpPinCode" class="att-input-standard" style="font-family: ui-monospace, monospace; letter-spacing: 2px; font-weight: 800;" value="{{ old('pin_code', $employee->pin_code ?: '1234') }}" maxlength="10" required>
                        <button type="button" class="att-btn att-btn-outline" style="height: 42px; padding: 0 14px;" onclick="generateRandomPin()" title="Təsadüfi PIN təyin et">
                            PIN
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field-group">
                    <label class="att-field-label">Cinsiyyət <span class="req">*</span></label>
                    <div class="option-card-grid">
                        <label class="option-card {{ $selGender === 'male' ? 'active' : '' }}" data-radio-gender>
                            <input type="radio" name="gender" value="male" {{ $selGender === 'male' ? 'checked' : '' }}>
                            <div class="custom-indicator custom-radio">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="opt-title">Kişi</div>
                                <div class="opt-desc">Aktyor, adm, res</div>
                            </div>
                        </label>
                        <label class="option-card {{ $selGender === 'female' ? 'active' : '' }}" data-radio-gender>
                            <input type="radio" name="gender" value="female" {{ $selGender === 'female' ? 'checked' : '' }}>
                            <div class="custom-indicator custom-radio">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="opt-title">Qadın</div>
                                <div class="opt-desc">Aktrisa (avto)</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-field-group">
                    <label class="att-field-label">İşə Qəbul Tarixi & Status</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <input type="date" name="hire_date" class="att-input-standard" value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d') ?: now()->toDateString()) }}">
                        <select name="status" class="att-input-standard att-select-standard">
                            <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Aktiv</option>
                            <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Deaktiv</option>
                            <option value="dismissed" {{ old('status', $employee->status) === 'dismissed' ? 'selected' : '' }}>İşdən çıxıb</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-field-group">
                <label class="option-card {{ old('requires_qr', $employee->requires_qr ?? true) ? 'active' : '' }}" data-check-card style="padding: 10px 14px;">
                    <input type="checkbox" name="requires_qr" value="1" {{ old('requires_qr', $employee->requires_qr ?? true) ? 'checked' : '' }}>
                    <div class="custom-indicator">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-title" style="font-size: 12.5px;">QR Davamiyyət Tələb Olunur</div>
                        <div class="opt-desc">Ofis və rəhbər heyət üçün söndürülə bilər</div>
                    </div>
                </label>
            </div>
        </div>
    </div>

    {{-- 2. Vəzifələr & Stajorluq --}}
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-icon-badge badge-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div>
                <h5 class="settings-title">2. Vəzifələr & Stajorluq Statusu</h5>
                <p class="settings-desc">İşçinin birdən çox vəzifəsi ola bilər. Əsas vəzifəni sağdakı radio ilə seçin.</p>
            </div>
        </div>

        {{-- Stajorluq Qutusu --}}
        <div style="margin-bottom: 14px;">
            <label class="option-card {{ $selIsIntern ? 'active' : '' }}" style="border-color: #fde68a; background: #fffbeb;" id="internCard" data-check-card>
                <input type="checkbox" name="is_intern" id="chkIsIntern" value="1" {{ $selIsIntern ? 'checked' : '' }}>
                <div class="custom-indicator" style="border-color: #d97706;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="3.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <div>
                    <div class="opt-title" style="color: #92400e;">Bu İşçi Stajordur (0 xal)</div>
                    <div class="opt-desc" style="color: #b45309;">Stajor olduqda yalnız stajor qəbul edən vəzifələr (Resepşn, Aktyor...) seçilə bilər.</div>
                </div>
            </label>
        </div>

        <div class="att-field-label" style="margin-bottom: 8px;">Vəzifələr Siyahısı:</div>

        <div>
            @foreach($workerTypes as $wt)
                @php
                    $isChecked = in_array($wt->id, $selectedWorkerTypeIds, true);
                    $isPrimary = ((int)$primaryWorkerTypeId === (int)$wt->id) || (empty($primaryWorkerTypeId) && $loop->first);
                @endphp
                <div class="role-select-item {{ $isChecked ? 'selected' : '' }}" data-role-row data-role-id="{{ $wt->id }}" data-can-intern="{{ $wt->can_have_intern ? '1' : '0' }}" data-requires-game="{{ $wt->requires_game ? '1' : '0' }}">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0; flex: 1;">
                        <input type="checkbox" name="worker_type_ids[]" value="{{ $wt->id }}" class="chk-role" style="width: 17px; height: 17px; cursor: pointer;" {{ $isChecked ? 'checked' : '' }}>
                        <div>
                            <span style="font-weight: 800; color: #0f172a; font-size: 13.5px;">{{ $wt->name }}</span>
                            @if($wt->female_name)
                                <span style="color: #64748b; font-size: 12px;">/ {{ $wt->female_name }}</span>
                            @endif
                            <span class="role-pill-badge" style="background: {{ $wt->badge_color ?: '#475569' }}; margin-left: 6px;">
                                {{ $wt->short_code }}
                            </span>
                            @if(!$wt->can_have_intern)
                                <span style="color: #dc2626; font-size: 11px; font-weight: 600; margin-left: 6px;">(Stajor olmur)</span>
                            @endif
                        </div>
                    </label>

                    <label class="primary-radio-wrap" style="display: {{ $isChecked ? 'flex' : 'none' }}; align-items: center; gap: 4px; cursor: pointer; margin: 0; font-size: 12px; font-weight: 700; color: #107c41;">
                        <input type="radio" name="primary_worker_type_id" value="{{ $wt->id }}" {{ $isPrimary ? 'checked' : '' }} style="cursor: pointer;">
                        <span>Əsas Rol ★</span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 3. Filial Təhkimatı --}}
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-icon-badge badge-amber">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <div>
                <h5 class="settings-title">3. Filial Təhkimatı</h5>
                <p class="settings-desc">Əsas filial + Kuratorlar üçün əlavə idarə etdiyi filialların siyahısı.</p>
            </div>
        </div>

        <div class="form-grid-1">
            <div class="form-field-group">
                <label class="att-field-label">Əsas Filial <span class="req">*</span></label>
                <select name="primary_branch_id" id="selPrimaryBranch" class="att-input-standard att-select-standard">
                    <option value="">— Filialsız (Mərkəzi Ofis) —</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ (int)$primaryBranchId === (int)$b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-field-group">
                <label class="att-field-label">Əlavə İdarə Etdiyi Filiallar (Kurator / Çoxlu filial üçün):</label>
                <div class="compact-checklist-box">
                    @foreach($branches as $b)
                        <label class="compact-check-item">
                            <input type="checkbox" name="managed_branch_ids[]" value="{{ $b->id }}" {{ in_array($b->id, $selectedBranchIds, true) ? 'checked' : '' }}>
                            <span>{{ $b->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Daimi Baxdığı Oyunlar --}}
    <div class="settings-card" id="gamesCard">
        <div class="settings-card-header">
            <div class="settings-icon-badge badge-purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
            </div>
            <div>
                <h5 class="settings-title">4. Baxdığı Daimi Oyunlar</h5>
                <p class="settings-desc">Aktyor və oyun rolları üçün daimi təhkim olunmuş 1 və ya bir neçə oyun.</p>
            </div>
        </div>

        <div class="att-info-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#db2777" stroke-width="2.2" style="flex-shrink: 0; margin-top: 1px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <div>
                Əvəzə getdiyi günlərdə oyunlar <strong>PortalWebsite satışlarından</strong> avtomatik gələcək. Burada isə işçinin daimi baxdığı əsas oyun(lar)ı seçirsiniz.
            </div>
        </div>

        @if(!empty($games))
            <div class="compact-checklist-box">
                @foreach($games as $g)
                    <label class="compact-check-item">
                        <input type="checkbox" name="game_ids[]" value="{{ $g['id'] }}" {{ in_array($g['id'], $selectedGameIds, true) ? 'checked' : '' }}>
                        <span><strong>{{ $g['name'] }}</strong> <small style="color: #64748b;">({{ $g['branch_name'] }})</small></span>
                    </label>
                @endforeach
            </div>
        @else
            <div style="text-align: center; color: #94a3b8; font-size: 12px; padding: 16px; border: 1px dashed #cbd5e1; border-radius: 10px;">
                Aktiv oyun tapılmadı.
            </div>
        @endif
    </div>

    {{-- 5. Qeydlər --}}
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-icon-badge badge-slate">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
            <div>
                <h5 class="settings-title">5. Əlavə Qeydlər</h5>
                <p class="settings-desc">İşçi haqqında daxili təlimat və ya xüsusi qeyd.</p>
            </div>
        </div>

        <textarea name="note" class="att-textarea-standard" rows="3" placeholder="İşçi haqqında daxili qeydlər...">{{ old('note', $employee->note) }}</textarea>
    </div>

</div>

@push('scripts')
<script>
function generateRandomPin() {
    const pin = Math.floor(1000 + Math.random() * 9000).toString();
    const inp = document.getElementById('inpPinCode');
    if (inp) inp.value = pin;
}

document.addEventListener('DOMContentLoaded', function () {
    const inpFullName = document.getElementById('inpFullName');
    const inpFirstName = document.getElementById('inpFirstName');
    const inpLastName = document.getElementById('inpLastName');
    const chkIsIntern = document.getElementById('chkIsIntern');
    const gamesCard = document.getElementById('gamesCard');

    function refreshOptionCardVisual(card, checked) {
        if (!card) return;
        card.classList.toggle('active', !!checked);
    }

    // Radio Gender
    document.querySelectorAll('.option-card[data-radio-gender] input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.option-card[data-radio-gender]').forEach(c => c.classList.remove('active'));
            this.closest('.option-card')?.classList.add('active');
        });
    });

    // Checkbox cards
    document.querySelectorAll('.option-card[data-check-card] input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function () {
            refreshOptionCardVisual(this.closest('.option-card'), this.checked);
            if (this.id === 'chkIsIntern') {
                syncInternRules();
            }
        });
    });

    // Role row toggles
    document.querySelectorAll('.chk-role').forEach(cb => {
        cb.addEventListener('change', function () {
            const row = this.closest('[data-role-row]');
            row.classList.toggle('selected', this.checked);
            const radioWrap = row.querySelector('.primary-radio-wrap');
            if (radioWrap) radioWrap.style.display = this.checked ? 'flex' : 'none';

            if (this.checked && !document.querySelector('input[name="primary_worker_type_id"]:checked')) {
                const r = row.querySelector('input[type="radio"]');
                if (r) r.checked = true;
            }

            syncGamesVisibility();
        });
    });

    function syncInternRules() {
        const isIntern = chkIsIntern?.checked || false;
        document.querySelectorAll('[data-role-row]').forEach(row => {
            const canIntern = row.dataset.canIntern === '1';
            const cb = row.querySelector('.chk-role');
            if (isIntern && !canIntern) {
                if (cb) {
                    cb.checked = false;
                    cb.disabled = true;
                }
                row.classList.remove('selected');
                row.style.opacity = '0.45';
                const radioWrap = row.querySelector('.primary-radio-wrap');
                if (radioWrap) radioWrap.style.display = 'none';
            } else {
                if (cb) cb.disabled = false;
                row.style.opacity = '1';
            }
        });
    }

    function syncGamesVisibility() {
        let requiresGame = false;
        document.querySelectorAll('[data-role-row]').forEach(row => {
            const cb = row.querySelector('.chk-role');
            if (cb && cb.checked && row.dataset.requiresGame === '1') {
                requiresGame = true;
            }
        });
        if (gamesCard) {
            gamesCard.style.display = requiresGame ? 'block' : 'none';
        }
    }

    inpFullName?.addEventListener('input', function () {
        const val = this.value.trim();
        if (val) {
            const parts = val.split(/\s+/);
            if (inpFirstName && !inpFirstName.value) inpFirstName.value = parts[0] || '';
            if (inpLastName && !inpLastName.value && parts.length > 1) inpLastName.value = parts.slice(1).join(' ');
        }
    });

    syncInternRules();
    syncGamesVisibility();
});
</script>
@endpush
