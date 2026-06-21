@props(['id', 'title' => null, 'maxWidth' => 'md', 'description' => null, 'icon' => null])

@php
    $widths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
    ];
    $widthClass = $widths[$maxWidth] ?? $widths['md'];

    $iconColors = [
        'info' => 'bg-sage-100 text-sage-600',
        'success' => 'bg-sage-100 text-sage-600',
        'warning' => 'bg-amber-100 text-amber-600',
        'danger' => 'bg-red-100 text-red-600',
        'primary' => 'bg-sage-100 text-sage-600',
    ];
    $iconColor = $iconColors[$icon] ?? $iconColors['primary'];
@endphp

<div id="{{ $id }}" class="modal fixed inset-0 z-50 hidden text-left" aria-modal="true" role="dialog">

    {{-- Backdrop --}}
    <div class="modal-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
        data-modal-close="{{ $id }}">
    </div>

    {{-- Modal Panel --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div
            class="modal-panel w-full {{ $widthClass }} max-h-[90vh] flex flex-col
                    opacity-0 scale-95 translate-y-4 transition-all duration-300 ease-out">

            <div class="bg-card rounded-2xl shadow-2xl border border-sage-200 overflow-hidden">
                {{-- Header --}}
                @if ($title)
                    <div class="flex items-start justify-between px-6 py-5 border-b border-sage-100">
                        <div class="flex items-start gap-4 min-w-0">
                            @if ($icon)
                                <div
                                    class="flex-shrink-0 w-10 h-10 rounded-xl {{ $iconColor }} flex items-center justify-center">
                                    @if ($icon === 'success')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif ($icon === 'danger')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif ($icon === 'warning')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    @elseif ($icon === 'info')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <x-icon :name="$icon" class="w-5 h-5" />
                                    @endif
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h3 class="text-lg font-semibold text-sage-800 leading-tight">{{ $title }}</h3>
                                @if ($description)
                                    <p class="text-sm text-sage-500 mt-1">{{ $description }}</p>
                                @endif
                            </div>
                        </div>
                        <button type="button"
                            class="modal-close-btn flex-shrink-0 ml-4 p-1.5 rounded-lg text-sage-400 hover:text-sage-700 hover:bg-sage-50 transition"
                            data-modal-close="{{ $id }}">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                @endif

                {{-- Body --}}
                <div class="px-6 py-5 overflow-y-auto max-h-[calc(90vh-180px)]">
                    {{ $slot }}
                </div>

                {{-- Footer (if actions slot is provided) --}}
                @if (isset($actions))
                    <div
                        class="px-6 py-4 border-t border-sage-100 bg-sage-50/50 flex flex-col sm:flex-row sm:justify-end gap-2">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal management
            const modals = {};

            // Initialize modals
            document.querySelectorAll('.modal').forEach(modal => {
                const id = modal.id;
                const backdrop = modal.querySelector('.modal-backdrop');
                const closeBtns = modal.querySelectorAll('[data-modal-close="' + id + '"]');
                const panel = modal.querySelector('.modal-panel');

                modals[id] = {
                    modal: modal,
                    backdrop: backdrop,
                    panel: panel,
                    isOpen: false
                };

                // Close handlers
                closeBtns.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        closeModal(id);
                    });
                });

                // Click backdrop to close
                if (backdrop) {
                    backdrop.addEventListener('click', function() {
                        closeModal(id);
                    });
                }

                // Escape key handler
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && modals[id] && modals[id].isOpen) {
                        closeModal(id);
                    }
                });
            });

            // Open modal function
            window.openModal = function(id) {
                const modalData = modals[id];
                if (!modalData) return;

                const {
                    modal,
                    panel
                } = modalData;

                // Show modal
                modal.classList.remove('hidden');
                modalData.isOpen = true;

                // Prevent body scroll
                document.body.style.overflow = 'hidden';

                // Trigger animation after a tiny delay
                requestAnimationFrame(() => {
                    if (panel) {
                        panel.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
                        panel.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                    }
                });

                // Dispatch event
                document.dispatchEvent(new CustomEvent('modal-opened', {
                    detail: {
                        id
                    }
                }));
            };

            // Close modal function
            window.closeModal = function(id) {
                const modalData = modals[id];
                if (!modalData) return;

                const {
                    modal,
                    panel
                } = modalData;

                // Reverse animation
                if (panel) {
                    panel.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                    panel.classList.add('opacity-0', 'scale-95', 'translate-y-4');
                }

                // Hide modal after animation
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modalData.isOpen = false;
                    document.body.style.overflow = '';

                    // Dispatch event
                    document.dispatchEvent(new CustomEvent('modal-closed', {
                        detail: {
                            id
                        }
                    }));
                }, 300);
            };

            // Toggle modal
            window.toggleModal = function(id) {
                const modalData = modals[id];
                if (!modalData) return;

                if (modalData.isOpen) {
                    closeModal(id);
                } else {
                    openModal(id);
                }
            };

            // Custom event listeners for opening/closing from anywhere
            document.addEventListener('modal-open', function(e) {
                if (e.detail && e.detail.id) {
                    openModal(e.detail.id);
                }
            });

            document.addEventListener('modal-close', function(e) {
                if (e.detail && e.detail.id) {
                    closeModal(e.detail.id);
                }
            });

            // Handle data-modal-target attributes
            document.querySelectorAll('[data-modal-target]').forEach(trigger => {
                const targetId = trigger.getAttribute('data-modal-target');

                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    openModal(targetId);
                });
            });

            // Auto-initialize any modals that are pre-opened (should be rare)
            document.querySelectorAll('.modal:not(.hidden)').forEach(modal => {
                const id = modal.id;
                if (modals[id]) {
                    modals[id].isOpen = true;
                    const panel = modal.querySelector('.modal-panel');
                    if (panel) {
                        panel.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
                        panel.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                    }
                    document.body.style.overflow = 'hidden';
                }
            });
        });
    </script>
@endpush
