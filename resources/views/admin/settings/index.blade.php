@extends('layouts.admin')

@section('page-title', 'Settings')
@section('breadcrumb', 'System Configuration')

@section('content')
    <div class="space-y-6">
        {{-- Tabs --}}
        <div class="border-b border-theme">
            <nav class="flex gap-6 -mb-px relative" id="settings-tabs">
                @can('settings.store')
                    <button type="button" data-tab="store"
                        class="settings-tab-btn relative px-1 py-3 text-sm font-medium transition-colors duration-200
                        border-b-2 border-transparent text-secondary hover:text-primary
                        active-tab:border-sage-600 active-tab:text-sage-600
                        dark:active-tab:border-sage-400 dark:active-tab:text-sage-400">
                        Store Info
                        <span class="absolute inset-x-0 -bottom-px h-0.5 bg-sage-600 dark:bg-sage-400 scale-x-0 transition-transform duration-300 origin-left group-hover:scale-x-100"></span>
                    </button>
                @endcan
                @can('settings.tax')
                    <button type="button" data-tab="tax"
                        class="settings-tab-btn relative px-1 py-3 text-sm font-medium transition-colors duration-200
                        border-b-2 border-transparent text-secondary hover:text-primary
                        active-tab:border-sage-600 active-tab:text-sage-600
                        dark:active-tab:border-sage-400 dark:active-tab:text-sage-400">
                        Tax
                        <span class="absolute inset-x-0 -bottom-px h-0.5 bg-sage-600 dark:bg-sage-400 scale-x-0 transition-transform duration-300 origin-left group-hover:scale-x-100"></span>
                    </button>
                @endcan
                @can('settings.currency')
                    <button type="button" data-tab="currency"
                        class="settings-tab-btn relative px-1 py-3 text-sm font-medium transition-colors duration-200
                        border-b-2 border-transparent text-secondary hover:text-primary
                        active-tab:border-sage-600 active-tab:text-sage-600
                        dark:active-tab:border-sage-400 dark:active-tab:text-sage-400">
                        Currency
                        <span class="absolute inset-x-0 -bottom-px h-0.5 bg-sage-600 dark:bg-sage-400 scale-x-0 transition-transform duration-300 origin-left group-hover:scale-x-100"></span>
                    </button>
                @endcan
                @can('settings.receipt')
                    <button type="button" data-tab="receipt"
                        class="settings-tab-btn relative px-1 py-3 text-sm font-medium transition-colors duration-200
                        border-b-2 border-transparent text-secondary hover:text-primary
                        active-tab:border-sage-600 active-tab:text-sage-600
                        dark:active-tab:border-sage-400 dark:active-tab:text-sage-400">
                        Receipt
                        <span class="absolute inset-x-0 -bottom-px h-0.5 bg-sage-600 dark:bg-sage-400 scale-x-0 transition-transform duration-300 origin-left group-hover:scale-x-100"></span>
                    </button>
                @endcan
                @can('settings.backup')
                    <button type="button" data-tab="backup"
                        class="settings-tab-btn relative px-1 py-3 text-sm font-medium transition-colors duration-200
                        border-b-2 border-transparent text-secondary hover:text-primary
                        active-tab:border-sage-600 active-tab:text-sage-600
                        dark:active-tab:border-sage-400 dark:active-tab:text-sage-400">
                        Backup & Restore
                        <span class="absolute inset-x-0 -bottom-px h-0.5 bg-sage-600 dark:bg-sage-400 scale-x-0 transition-transform duration-300 origin-left group-hover:scale-x-100"></span>
                    </button>
                @endcan
            </nav>
        </div>

        {{-- Store Info --}}
        @can('settings.store')
            <div data-tab-panel="store" class="settings-tab-panel bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
                <form method="POST" action="{{ route('admin.settings.store.update') }}" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf @method('PUT')
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                            <x-icon name="building" class="w-4 h-4" />
                        </div>
                        <h3 class="font-semibold text-primary">Store Information</h3>
                        <span class="ml-auto text-xs text-secondary bg-sage-100/50 dark:bg-sage-800/30 px-2.5 py-1 rounded-full border border-sage-200 dark:border-sage-700">Required fields *</span>
                    </div>
                    <p class="text-sm text-secondary -mt-2 mb-4">Shown on receipts, reports, and the dashboard.</p>

                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">Store Name <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                <x-icon name="building" class="w-4 h-4" />
                            </div>
                            <input type="text" name="name" value="{{ old('name', $store['name']) }}" required
                                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-secondary mb-1.5">Phone</label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                    <x-icon name="phone" class="w-4 h-4" />
                                </div>
                                <input type="text" name="phone" value="{{ old('phone', $store['phone']) }}"
                                    class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-secondary mb-1.5">Email</label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                    <x-icon name="mail" class="w-4 h-4" />
                                </div>
                                <input type="email" name="email" value="{{ old('email', $store['email']) }}"
                                    class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">Address</label>
                        <div class="relative">
                            <div class="absolute left-3 top-3 text-secondary opacity-40">
                                <x-icon name="home" class="w-4 h-4" />
                            </div>
                            <textarea name="address" rows="2"
                                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition resize-none">{{ old('address', $store['address']) }}</textarea>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">Tax ID</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                <x-icon name="file-text" class="w-4 h-4" />
                            </div>
                            <input type="text" name="tax_id" value="{{ old('tax_id', $store['tax_id']) }}"
                                class="w-full sm:w-64 rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">Logo</label>
                        @if ($store['logo_path'])
                            <img src="{{ asset('storage/' . $store['logo_path']) }}"
                                class="w-16 h-16 object-contain rounded-xl border border-theme mb-2">
                        @endif
                        <input type="file" name="logo" accept="image/*"
                            class="text-sm text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-sage-100/50 dark:file:bg-sage-800/30 file:text-sage-700 dark:file:text-sage-300 hover:file:bg-sage-100 dark:hover:file:bg-sage-800/50 transition">
                    </div>
                    <div class="pt-2">
                        <button type="submit"
                            class="rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-sm font-medium px-5 py-2.5 text-white shadow-sm hover:shadow-md transition flex items-center gap-2">
                            <x-icon name="save" class="w-4 h-4" />
                            Save Store Info
                        </button>
                    </div>
                </form>
            </div>
        @endcan

        {{-- Tax --}}
        @can('settings.tax')
            <div data-tab-panel="tax" class="settings-tab-panel hidden bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
                <form method="POST" action="{{ route('admin.settings.tax.update') }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                            <x-icon name="calculator" class="w-4 h-4" />
                        </div>
                        <h3 class="font-semibold text-primary">Tax Configuration</h3>
                    </div>
                    <p class="text-sm text-secondary -mt-2 mb-4">Default tax applied to products without their own tax rate override.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-secondary mb-1.5">Default Tax Rate (%)</label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                    <x-icon name="percentage" class="w-4 h-4" />
                                </div>
                                <input type="number" step="0.01" min="0" max="100" name="default_rate_percent"
                                    value="{{ old('default_rate_percent', $store['default_tax_rate_percent']) }}" required
                                    class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-secondary mb-1.5">Tax Label</label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                    <x-icon name="tag" class="w-4 h-4" />
                                </div>
                                <input type="text" name="label" value="{{ old('label', $store['tax_label']) }}"
                                    placeholder="e.g. Tax, VAT, GST" required
                                    class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition">
                            </div>
                        </div>
                    </div>
                    <label class="flex items-center gap-3 text-sm text-secondary cursor-pointer group">
                        <input type="checkbox" name="prices_include_tax" value="1" @checked(old('prices_include_tax', $store['prices_include_tax']))
                            class="w-4 h-4 rounded border-theme text-sage-600 dark:text-sage-400 focus:ring-sage-400 dark:focus:ring-sage-500 focus:ring-2 transition">
                        <span class="group-hover:text-primary transition">Product prices already include tax (tax-inclusive pricing)</span>
                    </label>
                    <div class="pt-2">
                        <button type="submit"
                            class="rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-sm font-medium px-5 py-2.5 text-white shadow-sm hover:shadow-md transition flex items-center gap-2">
                            <x-icon name="save" class="w-4 h-4" />
                            Save Tax Settings
                        </button>
                    </div>
                </form>
            </div>
        @endcan

        {{-- Currency --}}
        @can('settings.currency')
            <div data-tab-panel="currency" class="settings-tab-panel hidden bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
                <form method="POST" action="{{ route('admin.settings.currency.update') }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                            <x-icon name="cash" class="w-4 h-4" />
                        </div>
                        <h3 class="font-semibold text-primary">Currency Settings</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-secondary mb-1.5">Symbol</label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                    <x-icon name="currency" class="w-4 h-4" />
                                </div>
                                <input type="text" name="symbol" value="{{ old('symbol', $store['currency_symbol']) }}"
                                    required
                                    class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-secondary mb-1.5">ISO Code</label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                    <x-icon name="barcode" class="w-4 h-4" />
                                </div>
                                <input type="text" name="code" value="{{ old('code', $store['currency_code']) }}"
                                    maxlength="3" required
                                    class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm uppercase focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-secondary mb-1.5">Symbol Position</label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                    <x-icon name="align-left" class="w-4 h-4" />
                                </div>
                                <select name="position"
                                    class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition appearance-none cursor-pointer">
                                    <option value="before" @selected(old('position', $store['currency_position']) === 'before')>Before amount ($10.00)</option>
                                    <option value="after" @selected(old('position', $store['currency_position']) === 'after')>After amount (10.00$)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="pt-2">
                        <button type="submit"
                            class="rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-sm font-medium px-5 py-2.5 text-white shadow-sm hover:shadow-md transition flex items-center gap-2">
                            <x-icon name="save" class="w-4 h-4" />
                            Save Currency Settings
                        </button>
                    </div>
                </form>
            </div>
        @endcan

        {{-- Receipt --}}
        @can('settings.receipt')
            <div data-tab-panel="receipt" class="settings-tab-panel hidden bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
                <form method="POST" action="{{ route('admin.settings.receipt.update') }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                            <x-icon name="printer" class="w-4 h-4" />
                        </div>
                        <h3 class="font-semibold text-primary">Receipt Settings</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">Paper Size</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                <x-icon name="file" class="w-4 h-4" />
                            </div>
                            <select name="paper_size"
                                class="w-full sm:w-64 rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition appearance-none cursor-pointer">
                                <option value="58mm" @selected(old('paper_size', $store['receipt_paper_size']) === '58mm')>58mm Thermal</option>
                                <option value="80mm" @selected(old('paper_size', $store['receipt_paper_size']) === '80mm')>80mm Thermal</option>
                                <option value="a4" @selected(old('paper_size', $store['receipt_paper_size']) === 'a4')>A4</option>
                            </select>
                        </div>
                    </div>
                    <label class="flex items-center gap-3 text-sm text-secondary cursor-pointer group">
                        <input type="checkbox" name="show_logo" value="1" @checked(old('show_logo', $store['receipt_show_logo']))
                            class="w-4 h-4 rounded border-theme text-sage-600 dark:text-sage-400 focus:ring-sage-400 dark:focus:ring-sage-500 focus:ring-2 transition">
                        <span class="group-hover:text-primary transition">Show store logo on receipts</span>
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">Footer Text</label>
                        <div class="relative">
                            <div class="absolute left-3 top-3 text-secondary opacity-40">
                                <x-icon name="text" class="w-4 h-4" />
                            </div>
                            <textarea name="footer_text" rows="2"
                                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition resize-none">{{ old('footer_text', $store['receipt_footer']) }}</textarea>
                        </div>
                    </div>
                    <div class="pt-2">
                        <button type="submit"
                            class="rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-sm font-medium px-5 py-2.5 text-white shadow-sm hover:shadow-md transition flex items-center gap-2">
                            <x-icon name="save" class="w-4 h-4" />
                            Save Receipt Settings
                        </button>
                    </div>
                </form>
            </div>
        @endcan

        {{-- Backup & Restore --}}
        @can('settings.backup')
            <div data-tab-panel="backup" class="settings-tab-panel hidden bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                            <x-icon name="archive" class="w-4 h-4" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-primary">Backup & Restore</h3>
                            <p class="text-sm text-secondary mt-0.5">Database backups also run automatically every night at 2 AM.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.backups.create') }}">
                        @csrf
                        <button type="submit"
                            class="rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-sm font-medium px-5 py-2.5 text-white shadow-sm hover:shadow-md transition flex items-center gap-2">
                            <x-icon name="archive" class="w-4 h-4" />
                            Backup Now
                        </button>
                    </form>
                </div>

                <div class="divide-y divide-theme border border-theme rounded-2xl overflow-hidden">
                    @forelse ($backups as $backup)
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 py-4 hover:bg-sage-50/50 dark:hover:bg-sage-900/20 transition">
                            <div>
                                <p class="font-mono-num text-sm text-primary">{{ $backup['filename'] }}</p>
                                <p class="text-xs text-secondary mt-0.5">{{ $backup['created_at']->format('M d, Y g:i A') }} &middot;
                                    {{ number_format($backup['size_bytes'] / 1024 / 1024, 2) }} MB</p>
                            </div>
                            <div class="flex gap-2 mt-2 sm:mt-0">
                                <a href="{{ route('admin.settings.backups.download', $backup['filename']) }}"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-sage-600 dark:text-sage-400 hover:text-sage-800 dark:hover:text-sage-300 transition">Download</a>
                                @can('settings.restore')
                                    <button type="button" data-modal-target="restore-{{ $loop->index }}"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 hover:text-amber-800 transition">Restore</button>
                                @endcan
                                <form method="POST"
                                    action="{{ route('admin.settings.backups.delete', $backup['filename']) }}"
                                    onsubmit="return confirm('Delete this backup file permanently?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-800 transition">Delete</button>
                                </form>
                            </div>
                        </div>

                        @can('settings.restore')
                            <x-modal id="restore-{{ $loop->index }}" title="Restore Database" icon="danger">
                                <div class="text-sm text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-4 flex items-start gap-3">
                                    <x-icon name="alert-triangle" class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <strong>Warning:</strong> this will permanently overwrite ALL current data with the contents of
                                        this backup. This cannot be undone.
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('admin.settings.backups.restore', $backup['filename']) }}">
                                    @csrf
                                    <label class="block text-sm font-medium text-secondary mb-1.5">Type <span
                                            class="font-mono-num font-bold text-primary">RESTORE</span> to confirm</label>
                                    <input type="text" name="confirmation" required
                                        class="w-full rounded-xl border-theme px-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm font-mono-num focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                                    <div class="mt-4 flex justify-end gap-2">
                                        <button type="button" data-modal-close="restore-{{ $loop->index }}"
                                            class="rounded-xl border border-theme text-sm font-medium px-5 py-2 text-secondary hover:bg-sage-50 dark:hover:bg-sage-900/20 hover:text-primary transition">Cancel</button>
                                        <button type="submit"
                                            class="rounded-xl bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-sm font-medium px-5 py-2 text-white shadow-sm hover:shadow-md transition flex items-center gap-2">
                                            <x-icon name="archive" class="w-4 h-4" />
                                            Restore Database
                                        </button>
                                    </div>
                                </form>
                            </x-modal>
                        @endcan
                    @empty
                        <div class="px-5 py-12 text-center text-sm text-secondary">No backups yet. Click "Backup Now" to create
                            one.</div>
                    @endforelse
                </div>
            </div>
        @endcan
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // Set initial active tab
            const activeTab = $('.settings-tab-btn').first();
            if (activeTab.length) {
                activeTab.addClass('border-sage-600 text-sage-600 dark:border-sage-400 dark:text-sage-400');
                activeTab.removeClass('border-transparent text-secondary');
                // Add underline indicator
                activeTab.find('span').css('transform', 'scaleX(1)');
            }

            $('.settings-tab-btn').on('click', function() {
                const tab = $(this).data('tab');

                // Remove active styles from all tabs
                $('.settings-tab-btn').each(function() {
                    $(this).removeClass('border-sage-600 text-sage-600 dark:border-sage-400 dark:text-sage-400');
                    $(this).addClass('border-transparent text-secondary');
                    $(this).find('span').css('transform', 'scaleX(0)');
                });

                // Add active styles to clicked tab
                $(this).removeClass('border-transparent text-secondary');
                $(this).addClass('border-sage-600 text-sage-600 dark:border-sage-400 dark:text-sage-400');
                $(this).find('span').css('transform', 'scaleX(1)');

                // Show corresponding panel
                $('.settings-tab-panel').addClass('hidden');
                $(`[data-tab-panel="${tab}"]`).removeClass('hidden');
            });
        });
    </script>
@endpush
