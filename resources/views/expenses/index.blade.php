@extends('layouts.erp')

@section('title', 'Xərclər — ' . $selectedBranchName . ' (' . ($selectedMonthName ?? $filterMonth) . ')')
@section('page-title', 'XƏRCLƏR CƏDVƏLİ')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item active">Xərclər</li>

{{-- Sətir Şəkilləri İdarəetmə Modalı --}}
<div id="rowImagesModal" class="expense-modal-backdrop" style="display: none;" onclick="closeRowImagesModalOnBackdrop(event)">
    <div class="expense-modal-dialog images-modal-dialog" role="dialog" aria-modal="true">
        <div class="expense-modal-header">
            <h4 class="expense-modal-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.5">
                    <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                    <circle cx="9" cy="9" r="2"></circle>
                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>
                </svg>
                <span id="rowImagesModalTitle">Xərc Şəkilləri (Çek / Qəbz)</span>
            </h4>
            <button type="button" class="expense-modal-close" onclick="closeRowImagesModal()" title="Bağla (Esc)">&times;</button>
        </div>
        <div class="expense-modal-body" style="padding: 16px; text-align: left;">
            {{-- Yükləmə Ziyası --}}
            <div class="upload-drop-zone mb-3" id="dropZone" onclick="document.getElementById('rowImageFileInput').click()">
                <input type="file" id="rowImageFileInput" multiple accept="image/*" style="display: none;" onchange="handleModalFiles(this.files)">
                <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <span style="font-size: 13px; font-weight: 600; color: #1e293b;">+ Şəkil əlavə etmək üçün klikləyin və ya faylları bura atın</span>
                    <span style="font-size: 11px; color: #64748b;">Birdən çox şəkil seçə bilərsiniz (JPG, PNG, WEBP). Ən azı 1 şəkil mütləqdir.</span>
                </div>
            </div>

            {{-- Yüklənir İndikatoru --}}
            <div id="uploadLoadingSpinner" style="display: none; text-align: center; padding: 10px; font-size: 12px; font-weight: 600; color: #107c41;">
                ⏳ Şəkillər yüklənir, zəhmət olmasa gözləyin...
            </div>

            {{-- Mövcud Şəkillər Qutusu --}}
            <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-size: 12px; font-weight: 700; color: #334155;">Əlavə edilmiş şəkillər:</span>
                    <span id="modalImageCountText" style="font-size: 11px; font-weight: 600; color: #64748b;">0 şəkil</span>
                </div>
                <div class="thumb-grid" id="modalThumbsGrid"></div>
                <p id="noImagesWarningNotice" class="text-danger small mt-2 mb-0" style="display: none;">
                    ⚠️ Diqqət: Bu xərc üçün ən azı 1 şəkil əlavə edilməlidir!
                </p>
            </div>
        </div>
        <div class="expense-modal-footer d-flex justify-content-between align-items-center">
            <span style="font-size: 11px; color: #64748b;">Şəkillər dərhal yaddaşa yazılır</span>
            <button type="button" class="expense-modal-btn-close" onclick="closeRowImagesModal()">Tamam</button>
        </div>
    </div>
</div>

{{-- Böyük Ölçülü Şəkil Baxış Lightbox --}}
<div id="fullImageViewerModal" class="expense-modal-backdrop" style="display: none; z-index: 10060;" onclick="closeFullImageViewer()">
    <div style="max-width: 90vw; max-height: 90vh; position: relative;">
        <img id="fullImageViewerImg" src="" style="max-width: 90vw; max-height: 90vh; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); object-fit: contain;">
        <button type="button" style="position: absolute; top: -12px; right: -12px; background: white; border: none; border-radius: 50%; width: 30px; height: 30px; font-size: 18px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.3);" onclick="closeFullImageViewer()">&times;</button>
    </div>
</div>

<script>
    let activeImagesTr = null;

    function openRowImagesModal(cell, event) {
        if (event) event.stopPropagation();
        const tr = cell.closest('tr');
        if (!tr) return;
        activeImagesTr = tr;

        const rowNum = tr.querySelector('.row-idx') ? tr.querySelector('.row-idx').textContent.trim() : '';
        const titleEl = document.getElementById('rowImagesModalTitle');
        if (titleEl) titleEl.textContent = `Sətir #${rowNum} — Xərc Şəkilləri (Çek / Qəbz)`;

        renderModalImages();

        const modal = document.getElementById('rowImagesModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeRowImagesModal() {
        const modal = document.getElementById('rowImagesModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
        if (activeImagesTr) {
            updateRowImageBadge(activeImagesTr);
        }
        activeImagesTr = null;
    }

    function closeRowImagesModalOnBackdrop(e) {
        if (e.target && e.target.id === 'rowImagesModal') {
            closeRowImagesModal();
        }
    }

    function renderModalImages() {
        if (!activeImagesTr) return;
        let images = [];
        try {
            images = JSON.parse(activeImagesTr.getAttribute('data-images') || '[]');
        } catch(e) {
            images = [];
        }
        if (!Array.isArray(images)) images = [];

        const grid = document.getElementById('modalThumbsGrid');
        const countText = document.getElementById('modalImageCountText');
        const warning = document.getElementById('noImagesWarningNotice');

        if (countText) countText.textContent = `${images.length} şəkil`;
        if (warning) warning.style.display = (images.length === 0) ? 'block' : 'none';

        if (!grid) return;
        grid.innerHTML = '';

        if (images.length === 0) {
            grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #94a3b8; font-size: 12px; padding: 15px;">Hələ heç bir şəkil əlavə edilməyib. Yuxarıdakı sahəyə klikləyərək şəkil seçin.</div>';
            return;
        }

        images.forEach((img, idx) => {
            const card = document.createElement('div');
            card.className = 'thumb-card';
            const imgUrl = (img.startsWith('http://') || img.startsWith('https://')) ? img : ('https://portal.land/storage/' + img.replace(/^\/+/, ''));
            card.innerHTML = `
                <img src="${imgUrl}" alt="Çek #${idx+1}" onclick="viewFullImage('${imgUrl}')" title="Böyütmək üçün klikləyin">
                <button type="button" class="thumb-del" onclick="deleteModalImage(${idx})" title="Sil">&times;</button>
            `;
            grid.appendChild(card);
        });
    }

    function handleModalFiles(files) {
        if (!files || files.length === 0 || !activeImagesTr) return;

        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append(`images[${i}]`, files[i]);
        }

        const spinner = document.getElementById('uploadLoadingSpinner');
        if (spinner) spinner.style.display = 'block';

        fetch("{{ route('expenses.uploadImages') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            if (spinner) spinner.style.display = 'none';
            if (data.success && Array.isArray(data.paths)) {
                let currentImgs = [];
                try {
                    currentImgs = JSON.parse(activeImagesTr.getAttribute('data-images') || '[]');
                } catch(e) {
                    currentImgs = [];
                }
                const newImgs = currentImgs.concat(data.paths);
                activeImagesTr.setAttribute('data-images', JSON.stringify(newImgs));
                markDirty();
                renderModalImages();
                updateRowImageBadge(activeImagesTr);
            } else {
                alert('Şəkil yüklənmə xətası: ' + (data.message || 'Gözlənilməz xəta baş verdi.'));
            }
        })
        .catch(err => {
            if (spinner) spinner.style.display = 'none';
            alert('Şəkil yüklənərkən xəta baş verdi: ' + err.message);
        });

        document.getElementById('rowImageFileInput').value = '';
    }

    function deleteModalImage(index) {
        if (!activeImagesTr) return;
        let images = [];
        try {
            images = JSON.parse(activeImagesTr.getAttribute('data-images') || '[]');
        } catch(e) {
            images = [];
        }
        if (index >= 0 && index < images.length) {
            images.splice(index, 1);
            activeImagesTr.setAttribute('data-images', JSON.stringify(images));
            markDirty();
            renderModalImages();
            updateRowImageBadge(activeImagesTr);
        }
    }

    function updateRowImageBadge(tr) {
        if (!tr) return;
        let images = [];
        try {
            images = JSON.parse(tr.getAttribute('data-images') || '[]');
        } catch(e) {
            images = [];
        }
        const cell = tr.querySelector('.images-cell');
        if (!cell) return;

        if (images.length > 0) {
            cell.innerHTML = `
                <span class="badge-images has-images" title="${images.length} şəkil əlavə edilib. Klikləyərək bax və ya yenisini əlavə et.">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="3" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                    <span class="badge-images-text">${images.length} şəkil</span>
                </span>
            `;
        } else {
            cell.innerHTML = `
                <span class="badge-images no-images" title="Xərc üçün ən azı 1 şəkil əlavə edilməlidir!">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span class="badge-images-text">Şəkil yoxdur ⚠️</span>
                </span>
            `;
        }
    }

    function viewFullImage(url) {
        const modal = document.getElementById('fullImageViewerModal');
        const img = document.getElementById('fullImageViewerImg');
        if (img && modal) {
            img.src = url;
            modal.style.display = 'flex';
        }
    }

    function closeFullImageViewer() {
        const modal = document.getElementById('fullImageViewerModal');
        if (modal) modal.style.display = 'none';
    }

    // Drag and drop for upload drop zone
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('dropZone');
        if (dropZone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.add('dragover');
                }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.remove('dragover');
                }, false);
            });
            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleModalFiles(files);
            }, false);
        }
    });
</script>

@endsection

@section('content')
{{-- CSRF Token for AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* Excel Workbook Container */
    .excel-container {
        background: #ffffff;
        border: 1px solid #d4d4d4;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* Green Excel Ribbon / Action Toolbar */
    .excel-ribbon {
        background: #107c41;
        color: #ffffff;
        padding: 10px 18px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .excel-ribbon-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.2px;
    }

    .excel-ribbon-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .excel-btn {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
        font-size: 12px;
        font-weight: 500;
        padding: 6px 14px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
    }

    .excel-btn:hover {
        background: rgba(255, 255, 255, 0.28);
        color: #ffffff;
        border-color: #ffffff;
    }

    .excel-btn-save {
        background: #ffffff;
        color: #107c41 !important;
        font-weight: 700;
        border: 1px solid #ffffff;
    }

    .excel-btn-save:hover {
        background: #f0fdf4;
        color: #0b5e30 !important;
    }

    .excel-btn-save.has-changes {
        background: #ffecb5;
        color: #856404 !important;
        border-color: #ffeeba;
        animation: pulse-btn 1.5s infinite;
    }

    @keyframes pulse-btn {
        0% { transform: scale(1); }
        50% { transform: scale(1.03); }
        100% { transform: scale(1); }
    }

    /* Branch selector tabs (Top Filiallar bar) */
    .branch-pill {
        border-radius: 5px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #dcdcdc;
        background: #ffffff;
        color: #495057;
        transition: all 0.15s ease;
    }

    .branch-pill:hover {
        background: #eef7ee;
        border-color: #107c41;
        color: #107c41;
    }

    .branch-pill.active {
        background: #107c41;
        color: #ffffff;
        border-color: #107c41;
        box-shadow: 0 2px 6px rgba(16, 124, 65, 0.25);
    }

    /* Top Banner Row (Exact Match from User's Screenshot: Lime-Green banner) */
    .excel-banner-row {
        background: #d9f286;
        border-bottom: 2px solid #b8d966;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .excel-banner-title {
        font-size: 17px;
        font-weight: 700;
        color: #1a2e05;
        flex: 1;
        text-align: center;
        letter-spacing: 0.3px;
    }

    .excel-banner-total {
        font-size: 20px;
        font-weight: 800;
        color: #1a2e05;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    /* Table Grid Styling */
    .excel-table-wrapper {
        overflow-x: auto;
        max-height: calc(100vh - 280px);
        background: #ffffff;
    }

    table.excel-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        font-size: 13px;
        color: #212529;
        table-layout: fixed;
        margin-bottom: 0;
    }

    table.excel-table th,
    table.excel-table td {
        border-right: 1px solid #d4d4d4;
        border-bottom: 1px solid #d4d4d4;
        padding: 4px 8px;
        white-space: nowrap;
        vertical-align: middle;
    }

    /* Pink Header: Exact match to Screenshot */
    table.excel-table thead tr.pink-header th {
        background: #e39bb5;
        color: #3b0818;
        font-weight: 700;
        font-size: 13px;
        text-align: center;
        padding: 8px 10px;
        position: sticky;
        top: 0;
        z-index: 15;
        border-bottom: 2px solid #c97f9b;
        letter-spacing: 0.2px;
    }

    table.excel-table tbody tr:hover td {
        background-color: #fafbfc;
    }

    /* Excel Editable Cells */
    td.excel-cell {
        cursor: cell;
        transition: background-color 0.1s ease;
        outline: none;
    }

    td.excel-cell:focus {
        background-color: #ffffff !important;
        box-shadow: inset 0 0 0 2px #107c41;
        z-index: 3;
        position: relative;
    }

    td.excel-cell.is-dirty,
    td.is-dirty {
        background-color: #fff9db !important;
    }

    tr.col-letter-header th {
        background: #f1f3f4;
        color: #5f6368;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        padding: 3px 0;
        border-bottom: 1px solid #d4d4d4;
        border-right: 1px solid #e0e0e0;
    }

    .align-right { text-align: right; }
    .align-center { text-align: center; }
    .align-left { text-align: left; }

    /* Footer Totals Row */
    table.excel-table tfoot tr td {
        background: #f4f6f8;
        color: #1a2e05;
        font-weight: 700;
        font-size: 13px;
        position: sticky;
        bottom: 0;
        z-index: 10;
        border-top: 2px solid #b8d966;
        border-bottom: 2px solid #b8d966;
        padding: 8px 10px;
    }

    .row-idx {
        background: #f8f9fa;
        color: #6c757d;
        font-weight: 600;
        text-align: center;
        font-size: 12px;
        user-select: none;
    }

    /* Bottom Excel Sheet Tabs Bar (Aylar aşağıda) */
    .excel-sheets-bar {
        background: #f1f3f4;
        border-top: 1px solid #d4d4d4;
        padding: 4px 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 12px;
        position: relative;
    }

    .excel-tabs-nav-wrapper {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1 1 auto;
        min-width: 0;
        overflow: hidden;
    }

    .excel-tabs-scroll-container {
        display: flex;
        align-items: center;
        gap: 3px;
        overflow-x: auto;
        white-space: nowrap;
        scroll-behavior: smooth;
        flex: 1 1 auto;
        min-width: 0;
        padding: 3px 4px 6px 4px;
        scrollbar-width: thin;
        scrollbar-color: #b0b8c0 #edf0f2;
    }

    .excel-tabs-scroll-container::-webkit-scrollbar {
        height: 6px;
    }

    .excel-tabs-scroll-container::-webkit-scrollbar-track {
        background: #edf0f2;
        border-radius: 3px;
    }

    .excel-tabs-scroll-container::-webkit-scrollbar-thumb {
        background: #b0b8c0;
        border-radius: 3px;
    }

    .excel-tabs-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #107c41;
    }

    .excel-tab {
        background: #e9ecef;
        color: #495057;
        font-weight: 500;
        padding: 5px 13px;
        border-radius: 4px 4px 0 0;
        border: 1px solid #ced4da;
        border-bottom: 1px solid #ced4da;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        flex-shrink: 0;
        user-select: none;
        transition: all 0.15s ease;
        font-size: 12px;
    }

    .excel-tab:hover {
        background: #f8f9fa;
        color: #107c41;
        border-color: #adb5bd;
        text-decoration: none;
    }

    .excel-tab.active {
        background: #ffffff;
        color: #107c41;
        font-weight: 700;
        border-color: #ced4da #ced4da transparent #ced4da;
        border-bottom: 3px solid #107c41;
        box-shadow: 0 -1px 3px rgba(0,0,0,0.04);
        position: relative;
        z-index: 2;
    }

    .excel-sheets-summary {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        white-space: nowrap;
        padding-left: 12px;
        border-left: 1px solid #d4d4d4;
        font-size: 12px;
    }

    .excel-stat-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .excel-stat-divider {
        color: #ced4da;
        font-weight: 300;
    }

    .row-actions-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .history-row-btn {
        opacity: 0.35;
        color: #107c41;
        cursor: pointer;
        padding: 3px 4px;
        border-radius: 4px;
        transition: all 0.15s ease;
        border: none;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    tr:hover .history-row-btn {
        opacity: 0.85;
    }

    .history-row-btn:hover {
        opacity: 1 !important;
        background: #eaf5ea;
        color: #0b5e30;
        transform: scale(1.15);
    }

    .del-row-btn {
        opacity: 0.3;
        color: #dc3545;
        cursor: pointer;
        padding: 3px 4px;
        border-radius: 4px;
        transition: all 0.15s ease;
        border: none;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    tr:hover .del-row-btn {
        opacity: 0.85;
    }

    .del-row-btn:hover {
        opacity: 1 !important;
        background: #ffe3e3;
        transform: scale(1.15);
    }

    /* Tarix xanası və created_at saat indikatoru */
    .date-cell {
        position: relative;
        padding-right: 22px !important;
    }

    .cell-created-indicator {
        position: absolute;
        right: 4px;
        top: 50%;
        transform: translateY(-50%);
        width: 17px;
        height: 17px;
        border-radius: 4px;
        border: none;
        background: transparent;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        transition: all 0.15s ease;
        opacity: 0.45;
        user-select: none;
    }

    .date-cell:hover .cell-created-indicator,
    tr:hover .cell-created-indicator {
        opacity: 1;
        color: #107c41;
    }

    .cell-created-indicator:hover {
        background: #eaf5ea;
        color: #0b5e30 !important;
        transform: translateY(-50%) scale(1.15);
    }

    /* Modal Backdrop & Dialog (Balaca Modal) */
    .expense-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 10050;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        animation: modalFadeIn 0.15s ease-out;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .expense-modal-dialog {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 350px;
        overflow: hidden;
        animation: modalSlideUp 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
    }

    @keyframes modalSlideUp {
        from { opacity: 0; transform: scale(0.96) translateY(8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .expense-modal-header {
        padding: 12px 16px;
        border-bottom: 1px solid #eef2f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafbfc;
    }

    .expense-modal-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .expense-modal-close {
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .expense-modal-close:hover {
        background: #f1f5f9;
        color: #334155;
    }

    .expense-modal-body {
        padding: 22px 16px;
        text-align: center;
    }

    .created-at-display-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 14px 12px;
    }

    .created-at-val {
        font-size: 19px;
        font-weight: 800;
        color: #064e3b;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        letter-spacing: 0.2px;
        display: block;
    }

    .unsaved-notice-text {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        border-radius: 8px;
        padding: 12px;
        font-size: 13px;
        font-weight: 500;
    }

    .expense-modal-footer {
        padding: 10px 16px;
        background: #fafbfc;
        border-top: 1px solid #eef2f6;
        display: flex;
        justify-content: flex-end;
    }

    .expense-modal-btn-close {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .expense-modal-btn-close:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    /* Expense Image Badges & Modal */
    .badge-images {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
        user-select: none;
        line-height: 1.3;
    }
    .badge-images.has-images {
        background: #e6f4ea;
        color: #137333;
        border: 1px solid #ceead6;
    }
    .badge-images.has-images:hover {
        background: #ceead6;
        color: #0d5423;
        transform: scale(1.05);
    }
    .badge-images.no-images {
        background: #fce8e6;
        color: #c5221f;
        border: 1px solid #fad2cf;
        animation: pulse-border 2s infinite;
    }
    .badge-images.no-images:hover {
        background: #fad2cf;
        color: #a51d1a;
        transform: scale(1.05);
    }
    @keyframes pulse-border {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { box-shadow: 0 0 0 4px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    .images-modal-dialog {
        max-width: 580px !important;
    }
    .upload-drop-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .upload-drop-zone:hover, .upload-drop-zone.dragover {
        border-color: #107c41;
        background: #f0fdf4;
    }
    .thumb-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 8px;
        max-height: 220px;
        overflow-y: auto;
        padding: 4px;
    }
    .thumb-card {
        position: relative;
        aspect-ratio: 1;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #000;
    }
    .thumb-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: zoom-in;
    }
    .thumb-card .thumb-del {
        position: absolute;
        top: 2px;
        right: 2px;
        background: rgba(220, 38, 38, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        cursor: pointer;
    }

</style>

{{-- Toast Notification Box --}}
<div id="excelToast" style="display: none; position: fixed; top: 20px; right: 25px; z-index: 9999; background: #107c41; color: white; padding: 12px 20px; border-radius: 6px; box-shadow: 0 4px 14px rgba(0,0,0,0.18); font-size: 13px; font-weight: 600; align-items: center; gap: 8px;">
    <span id="toastIcon">✓</span>
    <span id="toastMessage">Uğurla yadda saxlanıldı!</span>
</div>

{{-- 1. Filial Selector Pills Bar (YUXARIDA - Filiallar) --}}
<div class="card mb-3 border-0 shadow-sm" style="border-radius: 8px;">
    <div class="card-body p-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex flex-wrap gap-2 flex-grow-1 align-items-center">
            @foreach($branches as $b)
                @php
                    $isActive = ($filterBranch == $b['id']);
                @endphp
                <a href="{{ route('expenses.index', ['branch_id' => $b['id'], 'month' => $filterMonth]) }}"
                   class="branch-pill {{ $isActive ? 'active' : '' }}"
                   title="{{ $b['name'] }}">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 21h18"></path>
                        <path d="M5 21V7l8-4v18"></path>
                        <path d="M19 21V11l-6-3"></path>
                    </svg>
                    <span>{{ $b['name'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- 2. Excel Ribbon Toolbar (ORTADA - Action Bar) --}}
<div class="excel-ribbon mb-3">
    <div class="excel-ribbon-title">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
            <path d="M3 9h18"></path>
            <path d="M3 15h18"></path>
            <path d="M9 3v18"></path>
            <path d="M15 3v18"></path>
        </svg>
        <span>{{ $selectedBranchName }} — Xərclər ({{ $selectedMonthName ?? $filterMonth }})</span>

        @if($hasCustomEdits && $lastSavedAt)
            <span id="saveStatusIndicator" class="badge bg-success text-white ms-2">
                Bazada yeniləndi ({{ $lastSavedAt }})
            </span>
        @else
            <span id="saveStatusIndicator" class="badge bg-secondary text-white ms-2">
                İlkin məlumatlar
            </span>
        @endif
    </div>

    <div class="excel-ribbon-actions">
        {{-- Yadda Saxla Button --}}
        <button type="button" class="excel-btn excel-btn-save" id="saveBtn" onclick="saveAllExpenses()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            <span id="saveBtnText">Yadda Saxla (Ctrl+S)</span>
        </button>

        {{-- Sətir Əlavə Et Button --}}
        <button type="button" class="excel-btn" id="addRowBtn" onclick="addNewRow()" title="Yeni xərc sətri əlavə et">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>+ Sətir Əlavə Et</span>
        </button>

        {{-- Excel Export Button --}}
        <a href="{{ route('expenses.export', ['branch_id' => $filterBranch, 'month' => $filterMonth, 'format' => 'xlsx']) }}" class="excel-btn" title="Excel (.xlsx) faylı kimi yüklə">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Excel Yüklə (.xlsx)</span>
        </a>
    </div>
</div>

{{-- 3. Excel Sheet Container (CƏDVƏL) --}}
<div class="excel-container">

    {{-- Top Banner: Lime Green Banner with Branch Name & Grand Total --}}
    <div class="excel-banner-row">
        <div style="width: 140px;"></div>
        <div class="excel-banner-title">
            Xərclər {{ $selectedBranchName }}
        </div>
        <div class="excel-banner-total" id="bannerGrandTotal">
            ₼ {{ number_format($summary['grand_total'] ?? 0, 2, ',', ' ') }}
        </div>
    </div>

    {{-- Table Grid --}}
    <div class="excel-table-wrapper" id="excelTableWrapper">
        <table class="excel-table" id="expensesTable">
            <colgroup>
                <col style="width: 44px;">  {{-- № --}}
                <col style="width: 115px;"> {{-- Tarix --}}
                <col style="width: 160px;"> {{-- Oyun --}}
                <col style="width: 180px;"> {{-- Qeyd --}}
                <col style="width: 200px;"> {{-- Kim tərəfindən --}}
                <col style="width: 120px;"> {{-- Məbləğ nəğd --}}
                <col style="width: 120px;"> {{-- Məbləğ nəğdsiz --}}
                <col style="width: 160px;"> {{-- Təsnifat --}}
                <col style="width: 120px;"> {{-- Cəmi Məbləğ --}}
                <col style="width: 130px;"> {{-- Şəkillər --}}
                <col style="width: 60px;">  {{-- Əməliyyat --}}
            </colgroup>
            <thead>
                {{-- Pink Mauve Header --}}
                <tr class="pink-header">
                    <th>№</th>
                    <th>Tarix</th>
                    <th>Oyun</th>
                    <th>Qeyd</th>
                    <th>Kim tərəfindən</th>
                    <th>Məbləğ nəğd</th>
                    <th>Məbləğ nəğdsiz</th>
                    <th>Təsnifat</th>
                    <th>Cəmi Məbləğ</th>
                    <th>Şəkillər (Çek)</th>
                    <th></th>
                </tr>
                {{-- Column Letter Headers (A, B, C, D...) --}}
                <tr class="col-letter-header">
                    <th>#</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>E</th>
                    <th>F</th>
                    <th>G</th>
                    <th>H</th>
                    <th>I</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="expensesTableBody">
                @forelse($expenses as $idx => $row)
                    @php
                        $cash = (float)($row['amount_cash'] ?? 0);
                        $card = (float)($row['amount_card'] ?? 0);
                        $total = (float)($row['total_amount'] ?? ($cash + $card));
                        $rawDateVal = $row['raw_date'] ?? $row['date'] ?? '';
                        try {
                            if (preg_match('/^(\d{1,2})[.\/-](\d{1,2})[.\/-](\d{4})$/', trim($rawDateVal), $dm)) {
                                $displayDate = sprintf('%02d.%02d.%04d', (int)$dm[1], (int)$dm[2], (int)$dm[3]);
                            } elseif (!empty($rawDateVal)) {
                                $displayDate = \Carbon\Carbon::parse($rawDateVal)->format('d.m.Y');
                            } else {
                                $displayDate = '';
                            }
                        } catch (\Exception $e) {
                            $displayDate = $rawDateVal;
                        }
                    @endphp
                    @php
                        $rImgs = $row['images'] ?? [];
                        if (is_string($rImgs)) $rImgs = json_decode($rImgs, true) ?: [];
                        $rImgs = is_array($rImgs) ? array_values(array_filter($rImgs)) : [];
                        $imgCount = count($rImgs);
                    @endphp
                    <tr data-row-id="{{ $row['id'] ?? '' }}"
                        data-created-at="{{ $row['created_at'] ?? '' }}"
                        data-updated-at="{{ $row['updated_at'] ?? '' }}"
                        data-raw-created-at="{{ $row['raw_created_at'] ?? '' }}"
                        data-images="{{ json_encode($rImgs) }}">
                        <td class="row-idx align-center" style="background: #f8f9fa; color: #6c757d; font-weight: 600;">{{ $idx + 1 }}</td>
                        <td class="excel-cell align-center date-cell" contenteditable="true" data-field="date" data-col="A" title="Tarixi dəyişmək üçün klikləyin. Yaradılma vaxtına baxmaq üçün saat ikonuna klikləyin.">
                            {{ $displayDate }}
                            <button type="button" class="cell-created-indicator" contenteditable="false" onclick="openExpenseDetails(this, event)" title="Yaradılma vaxtına bax (created_at)">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </button>
                        </td>
                        <td class="excel-cell align-left font-weight-600 text-primary" contenteditable="true" data-field="game_name" data-col="B">{{ $row['game_name'] ?? 'Ümumi' }}</td>
                        <td class="excel-cell align-left font-weight-500" contenteditable="true" data-field="title" data-col="C">{{ $row['title'] ?? '' }}</td>
                        <td class="excel-cell align-left" contenteditable="true" data-field="note" data-col="D">{{ $row['note'] ?? '' }}</td>
                        <td class="excel-cell align-right font-weight-600 text-dark" contenteditable="true" data-field="amount_cash" data-col="E" oninput="recalcRow(this)">{{ $cash > 0 ? number_format($cash, 2, '.', '') : '0.00' }}</td>
                        <td class="excel-cell align-right font-weight-600 text-dark" contenteditable="true" data-field="amount_card" data-col="F" oninput="recalcRow(this)">{{ $card > 0 ? number_format($card, 2, '.', '') : '0.00' }}</td>
                        <td class="excel-cell align-left" contenteditable="true" data-field="classification" data-col="G">{{ $row['classification'] ?? '' }}</td>
                        <td class="align-right fw-bold row-total-cell" style="background-color: #fafbfc;">{{ number_format($total, 2, '.', '') }}</td>
                        <td class="align-center images-cell" onclick="openRowImagesModal(this, event)">
                            @if($imgCount > 0)
                                <span class="badge-images has-images" title="{{ $imgCount }} şəkil əlavə edilib. Klikləyərək bax və ya yenisini əlavə et.">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="3" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                                    <span class="badge-images-text">{{ $imgCount }} şəkil</span>
                                </span>
                            @else
                                <span class="badge-images no-images" title="Xərc üçün ən azı 1 şəkil əlavə edilməlidir!">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    <span class="badge-images-text">Şəkil yoxdur ⚠️</span>
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="row-actions-group">
                                <button type="button" class="history-row-btn" onclick="openExpenseDetails(this, event)" title="Yaradılma vaxtına bax (created_at)">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </button>
                                <button type="button" class="del-row-btn" onclick="deleteRow(this)" title="Sətri sil">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="emptyRowPlaceholder">
                        <td colspan="11" class="text-center py-5 text-muted" style="background: #ffffff; font-size: 13px;">
                            <div class="py-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="d-block mx-auto mb-2 text-muted">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <p class="font-size-14 mb-1">Bu filial və ay üçün heç bir xərc qeydi tapılmadı.</p>
                                <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addNewRow()">
                                    ➕ İlk Sətri Əlavə Et
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end pe-3 fw-bold">YEKUN CƏMİ:</td>
                    <td class="align-right fw-bold" id="footerTotalCash">{{ number_format($summary['total_cash'] ?? 0, 2, '.', '') }}</td>
                    <td class="align-right fw-bold" id="footerTotalCard">{{ number_format($summary['total_card'] ?? 0, 2, '.', '') }}</td>
                    <td></td>
                    <td class="align-right fw-bold" id="footerGrandTotal">{{ number_format($summary['grand_total'] ?? 0, 2, '.', '') }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- 4. Bottom Excel Sheet Tabs Bar (AŞAĞIDA - Aylar və Sticky Yekun Statistikası) --}}
    <div class="excel-sheets-bar">
        {{-- Left Area: Horizontally Scrollable Months Tabs --}}
        <div class="excel-tabs-nav-wrapper">
            <div class="excel-tabs-scroll-container" id="excelTabsContainer">
                @foreach($availableMonths as $m)
                    @php
                        $isMonthActive = ($filterMonth === $m['key']);
                    @endphp
                    <a href="{{ route('expenses.index', ['branch_id' => $filterBranch, 'month' => $m['key']]) }}"
                       class="excel-tab {{ $isMonthActive ? 'active' : '' }}"
                       id="month-tab-{{ $m['key'] }}"
                       title="{{ $m['name'] }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        <span>{{ $m['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Right Area: Pinned Summary Statistics --}}
        <div class="excel-sheets-summary">
            <span class="excel-stat-item">
                <span class="text-muted">Sətir sayı:</span>
                <strong id="rowCountDisplay">{{ count($expenses) }}</strong>
            </span>
            <span class="excel-stat-divider">|</span>
            <span class="excel-stat-item">
                <span class="text-muted">Yekun Xərc:</span>
                <strong class="text-success font-size-13" id="bottomGrandTotalDisplay">{{ number_format($summary['grand_total'] ?? 0, 2) }} ₼</strong>
            </span>
        </div>
    </div>
</div>

{{-- Simple Created At Modal (Balaca Modal) --}}
<div id="expenseDetailsModal" class="expense-modal-backdrop" style="display: none;" onclick="closeModalOnBackdrop(event)">
    <div class="expense-modal-dialog" role="dialog" aria-modal="true">
        <div class="expense-modal-header">
            <h4 class="expense-modal-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Yaradılma Tarixi (Created At)</span>
            </h4>
            <button type="button" class="expense-modal-close" onclick="closeExpenseModal()" title="Bağla (Esc)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="expense-modal-body">
            <div class="created-at-display-box" id="modalCreatedAtBox">
                <span class="created-at-val" id="modalCreatedAtVal">—</span>
            </div>
            <div id="modalUnsavedNotice" style="display: none;" class="unsaved-notice-text">
                Bu xərc sətri hələ yadda saxlanılmayıb.
            </div>
        </div>

        <div class="expense-modal-footer">
            <button type="button" class="expense-modal-btn-close" onclick="closeExpenseModal()">Bağla</button>
        </div>
    </div>
</div>

<script>
    const BRANCH_ID = "{{ $filterBranch }}";
    const MONTH = "{{ $filterMonth }}";
    const BRANCH_GAMES = @json($branchGames);
    const CLASSIFICATIONS = @json($classifications);

    let isDirty = false;
    let deletedIds = [];
    let activeCell = null;

    document.addEventListener('DOMContentLoaded', function () {
        initCellEvents();
        initExcelTabs();

        // Sətir silmə (event delegation)
        const tableBody = document.getElementById('expensesTableBody');
        if (tableBody) {
            tableBody.addEventListener('click', function (e) {
                const btn = e.target.closest('.del-row-btn');
                if (btn) deleteRow(btn);
            });
        }

        // Add Row Button
        const addRowBtn = document.getElementById('addRowBtn');
        if (addRowBtn) {
            addRowBtn.addEventListener('click', addNewRow);
        }

        // Save Button
        const saveBtn = document.getElementById('saveBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', saveAllExpenses);
        }

        // Klaviatura qısayolları: Ctrl+S (Yadda Saxla) və Escape (Modalı bağla)
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveAllExpenses();
            } else if (e.key === 'Escape') {
                closeExpenseModal();
            }
        });

        // Səhifə keçidləri zamanı yadda saxlanılmamış dəyişiklik xəbərdarlığı
        document.querySelectorAll('.excel-tab, .branch-pill').forEach(link => {
            link.addEventListener('click', function (e) {
                if (isDirty) {
                    if (!confirm('Dəyişiklikləriniz hələ yadda saxlanılmayıb! Başqa aya və ya filiala keçmək istədiyinizdən əminsiniz?')) {
                        e.preventDefault();
                    }
                }
            });
        });

        // Beforeunload xəbərdarlığı
        window.addEventListener('beforeunload', function (e) {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    });

    function initCellEvents() {
        const cells = document.querySelectorAll('td.excel-cell');
        cells.forEach(cell => {
            cell.addEventListener('focus', function () {
                activeCell = this;
            });

            cell.addEventListener('input', function () {
                this.classList.add('is-dirty');
                markDirty();
            });

            // Enter moves down, Tab moves right
            cell.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const tr = this.closest('tr');
                    const nextTr = tr.nextElementSibling;
                    if (nextTr && !nextTr.id) {
                        const cellIdx = Array.from(tr.children).indexOf(this);
                        if (nextTr.children[cellIdx]) {
                            nextTr.children[cellIdx].focus();
                        }
                    }
                }
            });
        });
    }

    function markDirty() {
        isDirty = true;
        const btn = document.getElementById('saveBtn');
        if (btn) btn.classList.add('has-changes');
        const btnText = document.getElementById('saveBtnText');
        if (btnText) btnText.textContent = '💾 Yadda Saxla (Ctrl+S) *';

        const statusInd = document.getElementById('saveStatusIndicator');
        if (statusInd) {
            statusInd.className = 'badge bg-warning text-dark border ms-2';
            statusInd.innerText = 'Yadda saxlanılmamış dəyişikliklər var';
        }
    }

    function recalcRow(cell) {
        const tr = cell.closest('tr');
        const cashCell = tr.querySelector('[data-field="amount_cash"]');
        const cardCell = tr.querySelector('[data-field="amount_card"]');
        const totalCell = tr.querySelector('.row-total-cell');

        const cash = parseFloat(cashCell ? cashCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;
        const card = parseFloat(cardCell ? cardCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;
        const total = cash + card;

        if (totalCell) {
            totalCell.textContent = total.toFixed(2);
            totalCell.classList.add('is-dirty');
        }

        recalcTotals();
    }

    function recalcTotals() {
        let totalCash = 0;
        let totalCard = 0;
        let count = 0;

        const rows = document.querySelectorAll('#expensesTableBody tr:not(#emptyRowPlaceholder)');
        rows.forEach(tr => {
            const cashCell = tr.querySelector('[data-field="amount_cash"]');
            const cardCell = tr.querySelector('[data-field="amount_card"]');

            const cash = parseFloat(cashCell ? cashCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;
            const card = parseFloat(cardCell ? cardCell.innerText.replace(/[^0-9.-]/g, '') : 0) || 0;

            totalCash += cash;
            totalCard += card;
            count++;
        });

        const grandTotal = totalCash + totalCard;

        const footerCash = document.getElementById('footerTotalCash');
        if (footerCash) footerCash.textContent = totalCash.toFixed(2);

        const footerCard = document.getElementById('footerTotalCard');
        if (footerCard) footerCard.textContent = totalCard.toFixed(2);

        const footerGrand = document.getElementById('footerGrandTotal');
        if (footerGrand) footerGrand.textContent = grandTotal.toFixed(2);

        const bannerTotal = document.getElementById('bannerGrandTotal');
        if (bannerTotal) {
            bannerTotal.textContent = '₼ ' + grandTotal.toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        const rowCountDisplay = document.getElementById('rowCountDisplay');
        if (rowCountDisplay) rowCountDisplay.textContent = count;

        const bottomGrandTotal = document.getElementById('bottomGrandTotalDisplay');
        if (bottomGrandTotal) bottomGrandTotal.textContent = grandTotal.toFixed(2) + ' ₼';
    }

    function renumberRows() {
        const rows = document.querySelectorAll('#expensesTableBody tr:not(#emptyRowPlaceholder)');
        rows.forEach((tr, idx) => {
            const idxCell = tr.querySelector('.row-idx');
            if (idxCell) idxCell.textContent = idx + 1;
        });
    }

    function addNewRow() {
        const tableBody = document.getElementById('expensesTableBody');
        const placeholder = document.getElementById('emptyRowPlaceholder');
        if (placeholder) placeholder.remove();

        const count = tableBody.querySelectorAll('tr:not(#emptyRowPlaceholder)').length + 1;
        const today = new Date();
        const dd = String(today.getDate()).padStart(2, '0');
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const yyyy = today.getFullYear();
        const todayFormatted = `${dd}.${mm}.${yyyy}`;

        const tr = document.createElement('tr');
        tr.setAttribute('data-row-id', '');
        tr.setAttribute('data-created-at', '');
        tr.setAttribute('data-updated-at', '');
        tr.setAttribute('data-images', '[]');
        tr.innerHTML = `
            <td class="row-idx align-center" style="background: #f8f9fa; color: #6c757d; font-weight: 600;">${count}</td>
            <td class="excel-cell align-center date-cell is-dirty" contenteditable="true" data-field="date" data-col="A" title="Tarixi dəyişmək üçün klikləyin. Yaradılma vaxtına baxmaq üçün saat ikonuna klikləyin.">
                ${todayFormatted}
                <button type="button" class="cell-created-indicator" contenteditable="false" onclick="openExpenseDetails(this, event)" title="Yaradılma vaxtına bax (created_at)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </button>
            </td>
            <td class="excel-cell align-left font-weight-600 text-primary is-dirty" contenteditable="true" data-field="game_name" data-col="B">Ümumi</td>
            <td class="excel-cell align-left font-weight-500 is-dirty" contenteditable="true" data-field="title" data-col="C"></td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="note" data-col="D"></td>
            <td class="excel-cell align-right font-weight-600 is-dirty" contenteditable="true" data-field="amount_cash" data-col="E" oninput="recalcRow(this)">0.00</td>
            <td class="excel-cell align-right font-weight-600 is-dirty" contenteditable="true" data-field="amount_card" data-col="F" oninput="recalcRow(this)">0.00</td>
            <td class="excel-cell align-left is-dirty" contenteditable="true" data-field="classification" data-col="G"></td>
            <td class="align-right fw-bold row-total-cell is-dirty" style="background-color: #fafbfc;">0.00</td>
            <td class="align-center images-cell is-dirty" onclick="openRowImagesModal(this, event)">
                <span class="badge-images no-images" title="Xərc üçün ən azı 1 şəkil əlavə edilməlidir!">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span class="badge-images-text">Şəkil yoxdur ⚠️</span>
                </span>
            </td>
            <td class="text-center">
                <div class="row-actions-group">
                    <button type="button" class="history-row-btn" onclick="openExpenseDetails(this, event)" title="Yaradılma vaxtına bax (created_at)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </button>
                    <button type="button" class="del-row-btn" onclick="deleteRow(this)" title="Sətri sil">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>
            </td>
        `;

        tableBody.appendChild(tr);
        initCellEvents();
        markDirty();
        recalcTotals();

        const titleCell = tr.querySelector('[data-field="title"]');
        if (titleCell) titleCell.focus();
    }

    function deleteRow(btn) {
        const tr = btn.closest('tr');
        if (!tr) return;

        if (!confirm('Bu xərc sətrini silmək istədiyinizdən əminsiniz?')) {
            return;
        }

        const rowId = tr.getAttribute('data-row-id');
        if (rowId && !isNaN(rowId) && parseInt(rowId) > 0) {
            btn.disabled = true;
            btn.style.opacity = '0.4';

            fetch("{{ route('expenses.delete') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ id: parseInt(rowId) })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    tr.remove();
                    checkEmptyTableAfterDelete();
                    recalcTotals();
                    showToast('✓ ' + (data.message || 'Xərc bazadan silindi.'), '#107c41');
                } else {
                    alert('Xəta: ' + (data.message || 'Xərci silmək mümkün olmadı.'));
                    btn.disabled = false;
                    btn.style.opacity = '1';
                }
            })
            .catch(err => {
                alert('Silmə zamanı xəta baş verdi: ' + err.message);
                btn.disabled = false;
                btn.style.opacity = '1';
            });
        } else {
            // Hələ bazada saxlanılmamış yerli sətir
            tr.remove();
            checkEmptyTableAfterDelete();
            recalcTotals();
            markDirty();
        }
    }

    function checkEmptyTableAfterDelete() {
        const tableBody = document.getElementById('expensesTableBody');
        const remainingRows = tableBody.querySelectorAll('tr:not(#emptyRowPlaceholder)');
        if (remainingRows.length === 0) {
            tableBody.innerHTML = `
                <tr id="emptyRowPlaceholder">
                    <td colspan="11" class="text-center py-5 text-muted" style="background: #ffffff; font-size: 13px;">
                        <div class="py-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="d-block mx-auto mb-2 text-muted">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <p class="font-size-14 mb-1">Bu filial və ay üçün heç bir xərc qeydi tapılmadı.</p>
                            <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addNewRow()">
                                ➕ İlk Sətri Əlavə Et
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            renumberRows();
        }
    }

    function saveAllExpenses() {
        const rowsData = [];
        const rows = document.querySelectorAll('#expensesTableBody tr:not(#emptyRowPlaceholder)');

        let hasImageError = false;
        let firstMissingRow = null;
        let firstMissingTr = null;

        rows.forEach((tr, idx) => {
            const rowObj = {};
            rowObj['id'] = tr.getAttribute('data-row-id') || '';

            // Şəkilləri oxuyuruq
            let rowImgs = [];
            try {
                rowImgs = JSON.parse(tr.getAttribute('data-images') || '[]');
            } catch(e) {
                rowImgs = [];
            }
            if (!Array.isArray(rowImgs)) rowImgs = [];
            rowObj['images'] = rowImgs;

            tr.querySelectorAll('td.excel-cell').forEach(c => {
                const field = c.dataset.field;
                if (field) {
                    let val = c.innerText.trim();
                    if (field === 'date') {
                        const m = val.match(/\d{1,2}[.\/-]\d{1,2}[.\/-]\d{4}/);
                        if (m) val = m[0];
                    } else if (field === 'amount_cash' || field === 'amount_card') {
                        val = parseFloat(val.replace(/[^0-9.-]/g, '')) || 0;
                    }
                    rowObj[field] = val;
                }
            });
            rowObj['row_num'] = idx + 1;
            rowsData.push(rowObj);
        });

        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        const btnText = document.getElementById('saveBtnText');
        if (btnText) btnText.textContent = 'Bazada yenilənir...';

        fetch("{{ route('expenses.save') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                branch_id: BRANCH_ID,
                month: MONTH,
                expenses: rowsData,
                deleted_ids: deletedIds,
            }),
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            if (btnText) btnText.textContent = 'Yadda Saxla (Ctrl+S)';

            if (data.success) {
                isDirty = false;
                deletedIds = [];
                saveBtn.classList.remove('has-changes');

                const statusInd = document.getElementById('saveStatusIndicator');
                if (statusInd) {
                    statusInd.className = 'badge bg-success text-white ms-2';
                    statusInd.innerText = 'Bazada yeniləndi (' + data.saved_at + ')';
                }

                // Remove dirty classes
                document.querySelectorAll('.is-dirty').forEach(c => c.classList.remove('is-dirty'));

                // Əgər yeni sətirlər əlavə edilibsə və ya qeydlər yenilənibsə, data atributlarını yeniləyirik
                if (Array.isArray(data.expenses)) {
                    const activeRows = document.querySelectorAll('#expensesTableBody tr:not(#emptyRowPlaceholder)');
                    activeRows.forEach((tr, i) => {
                        const savedExp = data.expenses[i];
                        if (savedExp) {
                            if (savedExp.id) tr.setAttribute('data-row-id', savedExp.id);
                            if (savedExp.created_at) tr.setAttribute('data-created-at', savedExp.created_at);
                            if (savedExp.updated_at) tr.setAttribute('data-updated-at', savedExp.updated_at);
                        }
                    });
                }

                showToast('✓ ' + data.message, '#107c41');
            } else {
                showToast('Xəta: ' + (data.message || 'Məlumatları saxlamaq mümkün olmadı.'), '#dc3545');
            }
        })
        .catch(err => {
            saveBtn.disabled = false;
            if (btnText) btnText.textContent = 'Yadda Saxla (Ctrl+S)';
            console.error('Save error:', err);
            showToast('Serverlə əlaqə xətası baş verdi.', '#dc3545');
        });
    }

    function showToast(msg, bg = '#107c41') {
        const toast = document.getElementById('excelToast');
        if (!toast) return;
        toast.style.background = bg;
        const msgEl = document.getElementById('toastMessage');
        if (msgEl) msgEl.innerText = msg;
        const iconEl = document.getElementById('toastIcon');
        if (iconEl) iconEl.textContent = (bg === '#dc3545') ? '✕' : '✓';
        toast.style.display = 'flex';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }

    function initExcelTabs() {
        const tabsContainer = document.getElementById('excelTabsContainer');
        const activeTab = document.querySelector('.excel-tab.active');

        if (activeTab && tabsContainer) {
            setTimeout(() => {
                activeTab.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
            }, 150);
        }

        if (tabsContainer) {
            tabsContainer.addEventListener('wheel', function (e) {
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    tabsContainer.scrollLeft += e.deltaY;
                }
            }, { passive: false });

            let isDown = false;
            let startX = 0;
            let scrollLeft = 0;

            tabsContainer.addEventListener('mousedown', function (e) {
                if (e.button !== 0) return;
                isDown = true;
                tabsContainer.style.cursor = 'grabbing';
                startX = e.pageX - tabsContainer.offsetLeft;
                scrollLeft = tabsContainer.scrollLeft;
            });

            window.addEventListener('mouseup', function () {
                isDown = false;
                if (tabsContainer) tabsContainer.style.cursor = 'default';
            });

            tabsContainer.addEventListener('mousemove', function (e) {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - tabsContainer.offsetLeft;
                const walk = (x - startX) * 1.5;
                tabsContainer.scrollLeft = scrollLeft - walk;
            });
        }
    }

    /**
     * created_at modalını aç
     */
    function openExpenseDetails(el, event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }

        const tr = el.closest('tr');
        if (!tr) return;

        const rowId = tr.getAttribute('data-row-id') || '';
        let createdAt = tr.getAttribute('data-created-at') || '';
        const rawCreatedAt = tr.getAttribute('data-raw-created-at') || '';

        if (!createdAt && rawCreatedAt) {
            try {
                const d = new Date(rawCreatedAt);
                if (!isNaN(d.getTime())) {
                    const pad = n => String(n).padStart(2, '0');
                    createdAt = `${pad(d.getDate())}.${pad(d.getMonth() + 1)}.${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
                }
            } catch(e) {}
        }

        const createdAtBox = document.getElementById('modalCreatedAtBox');
        const createdAtValEl = document.getElementById('modalCreatedAtVal');
        const unsavedNotice = document.getElementById('modalUnsavedNotice');

        if (createdAt && createdAt !== 'null') {
            if (createdAtBox) createdAtBox.style.display = 'block';
            if (unsavedNotice) unsavedNotice.style.display = 'none';
            if (createdAtValEl) createdAtValEl.innerText = createdAt;
        } else if (!rowId) {
            if (createdAtBox) createdAtBox.style.display = 'none';
            if (unsavedNotice) unsavedNotice.style.display = 'block';
        } else {
            if (createdAtBox) createdAtBox.style.display = 'block';
            if (unsavedNotice) unsavedNotice.style.display = 'none';
            if (createdAtValEl) createdAtValEl.innerText = 'Qeyd olunmayıb';
        }

        const modal = document.getElementById('expenseDetailsModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    /**
     * Modalı bağla
     */
    function closeExpenseModal() {
        const modal = document.getElementById('expenseDetailsModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    /**
     * Arxa fona klik edildikdə bağla
     */
    function closeModalOnBackdrop(e) {
        if (e.target && e.target.id === 'expenseDetailsModal') {
            closeExpenseModal();
        }
    }
</script>
@endsection
