/**
 * Shared admin-panel jQuery behaviors: modal open/close, user menu dropdown,
 * and a tiny CSRF-aware AJAX setup helper. Loaded on every admin.blade.php
 * page. Page-specific behavior (DataTables init, image preview, etc.) lives
 * in its own per-page <script> block so this file stays generic.
 */
$(function () {
    // --- CSRF token on every AJAX request ----------------------------------
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // --- Modal open/close ----------------------------------------------------
    $(document).on('click', '[data-modal-target]', function () {
        $('#' + $(this).data('modal-target')).removeClass('hidden');
    });

    $(document).on('click', '[data-modal-close]', function () {
        $('#' + $(this).data('modal-close')).addClass('hidden');
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            $('.fixed.inset-0.z-50:not(.hidden)').addClass('hidden');
        }
    });

    // --- User menu dropdown ---------------------------------------------------
    $('#user-menu-btn').on('click', function (e) {
        e.stopPropagation();
        $('#user-menu').toggleClass('hidden');
    });
    $(document).on('click', function () {
        $('#user-menu').addClass('hidden');
    });
    $('#user-menu').on('click', function (e) { e.stopPropagation(); });

    // --- Mobile sidebar toggle (simple show/hide, no animation library) -----
    $('#mobile-menu-btn').on('click', function () {
        $('aside').toggleClass('hidden').toggleClass('flex');
    });
});
