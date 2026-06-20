<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Supplier Name <span
                class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" required
            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Contact Person</label>
        <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}"
            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}"
            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}"
            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tax ID</label>
        <input type="text" name="tax_id" value="{{ old('tax_id', $supplier->tax_id ?? '') }}"
            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
        <textarea name="address" rows="2"
            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $supplier->address ?? '') }}</textarea>
    </div>
    <label class="flex items-center gap-2 text-sm text-slate-700 sm:col-span-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $supplier->is_active ?? true))
            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        Active
    </label>
</div>
