@extends('layouts.admin')

@section('page-title', 'Settings')

@section('content')
    <div class="">
        <div class="border-b border-slate-200 mb-6">
            <nav class="flex gap-6 -mb-px" id="settings-tabs">
                @can('settings.store')
                    <button type="button" data-tab="store"
                        class="settings-tab-btn border-b-2 border-indigo-600 text-indigo-600 px-1 py-3 text-sm font-medium">Store
                        Info</button>
                @endcan
                @can('settings.tax')
                    <button type="button" data-tab="tax"
                        class="settings-tab-btn border-b-2 border-transparent text-slate-500 hover:text-slate-700 px-1 py-3 text-sm font-medium">Tax</button>
                @endcan
                @can('settings.currency')
                    <button type="button" data-tab="currency"
                        class="settings-tab-btn border-b-2 border-transparent text-slate-500 hover:text-slate-700 px-1 py-3 text-sm font-medium">Currency</button>
                @endcan
                @can('settings.receipt')
                    <button type="button" data-tab="receipt"
                        class="settings-tab-btn border-b-2 border-transparent text-slate-500 hover:text-slate-700 px-1 py-3 text-sm font-medium">Receipt</button>
                @endcan
                @can('settings.backup')
                    <button type="button" data-tab="backup"
                        class="settings-tab-btn border-b-2 border-transparent text-slate-500 hover:text-slate-700 px-1 py-3 text-sm font-medium">Backup
                        & Restore</button>
                @endcan
            </nav>
        </div>

        {{-- Store Info --}}
        @can('settings.store')
            <div data-tab-panel="store" class="settings-tab-panel bg-white rounded-xl border border-slate-200 p-5">
                <form method="POST" action="{{ route('admin.settings.store.update') }}" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf @method('PUT')
                    <h3 class="font-semibold text-slate-900">Store Information</h3>
                    <p class="text-sm text-slate-500 -mt-2">Shown on receipts, reports, and the dashboard.</p>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Store Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $store['name']) }}" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $store['phone']) }}"
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $store['email']) }}"
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                        <textarea name="address" rows="2"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $store['address']) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tax ID</label>
                        <input type="text" name="tax_id" value="{{ old('tax_id', $store['tax_id']) }}"
                            class="w-full sm:w-64 rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Logo</label>
                        @if ($store['logo_path'])
                            <img src="{{ asset('storage/' . $store['logo_path']) }}"
                                class="w-16 h-16 object-contain rounded-lg border border-slate-200 mb-2">
                        @endif
                        <input type="file" name="logo" accept="image/*" class="text-sm">
                    </div>
                    <div class="pt-2">
                        <button type="submit"
                            class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-5 py-2.5 text-white">Save
                            Store Info</button>
                    </div>
                </form>
            </div>
        @endcan

        {{-- Tax --}}
        @can('settings.tax')
            <div data-tab-panel="tax" class="settings-tab-panel hidden bg-white rounded-xl border border-slate-200 p-5">
                <form method="POST" action="{{ route('admin.settings.tax.update') }}" class="space-y-4">
                    @csrf @method('PUT')
                    <h3 class="font-semibold text-slate-900">Tax Configuration</h3>
                    <p class="text-sm text-slate-500 -mt-2">Default tax applied to products without their own tax rate override.
                    </p>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Default Tax Rate (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="default_rate_percent"
                                value="{{ old('default_rate_percent', $store['default_tax_rate_percent']) }}" required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tax Label</label>
                            <input type="text" name="label" value="{{ old('label', $store['tax_label']) }}"
                                placeholder="e.g. Tax, VAT, GST" required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="prices_include_tax" value="1" @checked(old('prices_include_tax', $store['prices_include_tax']))
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        Product prices already include tax (tax-inclusive pricing)
                    </label>
                    <div class="pt-2">
                        <button type="submit"
                            class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-5 py-2.5 text-white">Save
                            Tax Settings</button>
                    </div>
                </form>
            </div>
        @endcan

        {{-- Currency --}}
        @can('settings.currency')
            <div data-tab-panel="currency" class="settings-tab-panel hidden bg-white rounded-xl border border-slate-200 p-5">
                <form method="POST" action="{{ route('admin.settings.currency.update') }}" class="space-y-4">
                    @csrf @method('PUT')
                    <h3 class="font-semibold text-slate-900">Currency Settings</h3>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Symbol</label>
                            <input type="text" name="symbol" value="{{ old('symbol', $store['currency_symbol']) }}"
                                required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">ISO Code</label>
                            <input type="text" name="code" value="{{ old('code', $store['currency_code']) }}"
                                maxlength="3" required
                                class="w-full rounded-lg border-slate-300 text-sm uppercase focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Symbol Position</label>
                            <select name="position"
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="before" @selected(old('position', $store['currency_position']) === 'before')>Before amount ($10.00)</option>
                                <option value="after" @selected(old('position', $store['currency_position']) === 'after')>After amount (10.00$)</option>
                            </select>
                        </div>
                    </div>
                    <div class="pt-2">
                        <button type="submit"
                            class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-5 py-2.5 text-white">Save
                            Currency Settings</button>
                    </div>
                </form>
            </div>
        @endcan

        {{-- Receipt --}}
        @can('settings.receipt')
            <div data-tab-panel="receipt" class="settings-tab-panel hidden bg-white rounded-xl border border-slate-200 p-5">
                <form method="POST" action="{{ route('admin.settings.receipt.update') }}" class="space-y-4">
                    @csrf @method('PUT')
                    <h3 class="font-semibold text-slate-900">Receipt Settings</h3>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Paper Size</label>
                        <select name="paper_size"
                            class="w-full sm:w-64 rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="58mm" @selected(old('paper_size', $store['receipt_paper_size']) === '58mm')>58mm Thermal</option>
                            <option value="80mm" @selected(old('paper_size', $store['receipt_paper_size']) === '80mm')>80mm Thermal</option>
                            <option value="a4" @selected(old('paper_size', $store['receipt_paper_size']) === 'a4')>A4</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="show_logo" value="1" @checked(old('show_logo', $store['receipt_show_logo']))
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        Show store logo on receipts
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Footer Text</label>
                        <textarea name="footer_text" rows="2"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('footer_text', $store['receipt_footer']) }}</textarea>
                    </div>
                    <div class="pt-2">
                        <button type="submit"
                            class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-5 py-2.5 text-white">Save
                            Receipt Settings</button>
                    </div>
                </form>
            </div>
        @endcan

        {{-- Backup & Restore --}}
        @can('settings.backup')
            <div data-tab-panel="backup" class="settings-tab-panel hidden bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-slate-900">Backup & Restore</h3>
                        <p class="text-sm text-slate-500 mt-0.5">Database backups also run automatically every night at 2 AM.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.backups.create') }}">
                        @csrf
                        <button type="submit"
                            class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-4 py-2 text-white">Backup
                            Now</button>
                    </form>
                </div>

                <div class="divide-y divide-slate-100 border border-slate-200 rounded-lg">
                    @forelse ($backups as $backup)
                        <div class="flex items-center justify-between px-4 py-3">
                            <div>
                                <p class="font-mono-num text-sm text-slate-900">{{ $backup['filename'] }}</p>
                                <p class="text-xs text-slate-400">{{ $backup['created_at']->format('M d, Y g:i A') }} &middot;
                                    {{ number_format($backup['size_bytes'] / 1024 / 1024, 2) }} MB</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.settings.backups.download', $backup['filename']) }}"
                                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Download</a>
                                @can('settings.restore')
                                    <button type="button" data-modal-target="restore-{{ $loop->index }}"
                                        class="text-xs font-medium text-amber-600 hover:text-amber-800">Restore</button>
                                @endcan
                                <form method="POST"
                                    action="{{ route('admin.settings.backups.delete', $backup['filename']) }}"
                                    onsubmit="return confirm('Delete this backup file permanently?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs font-medium text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </div>
                        </div>

                        @can('settings.restore')
                            <x-modal id="restore-{{ $loop->index }}" title="Restore Database">
                                <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                                    <strong>Warning:</strong> this will permanently overwrite ALL current data with the contents of
                                    this backup. This cannot be undone.
                                </div>
                                <form method="POST" action="{{ route('admin.settings.backups.restore', $backup['filename']) }}">
                                    @csrf
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Type <span
                                            class="font-mono-num font-bold">RESTORE</span> to confirm</label>
                                    <input type="text" name="confirmation" required
                                        class="w-full rounded-lg border-slate-300 text-sm font-mono-num focus:border-red-500 focus:ring-red-500">
                                    <div class="mt-4 flex justify-end gap-2">
                                        <button type="button" data-modal-close="restore-{{ $loop->index }}"
                                            class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                                        <button type="submit"
                                            class="rounded-lg bg-red-600 hover:bg-red-500 text-sm font-medium px-4 py-2 text-white">Restore
                                            Database</button>
                                    </div>
                                </form>
                            </x-modal>
                        @endcan
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-slate-400">No backups yet. Click "Backup Now" to create
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
            $('.settings-tab-btn').on('click', function() {
                const tab = $(this).data('tab');
                $('.settings-tab-btn').removeClass('border-indigo-600 text-indigo-600').addClass(
                    'border-transparent text-slate-500');
                $(this).removeClass('border-transparent text-slate-500').addClass(
                    'border-indigo-600 text-indigo-600');
                $('.settings-tab-panel').addClass('hidden');
                $(`[data-tab-panel="${tab}"]`).removeClass('hidden');
            });
        });
    </script>
@endpush
