<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-secondary mb-1.5">
            Category Name <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                <x-icon name="tag" class="w-4 h-4" />
            </div>
            <input type="text" name="name" id="{{ $isEdit ?? false ? 'edit-name' : '' }}"
                value="{{ old('name', $category->name ?? '') }}" required placeholder="Enter category name..."
                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition @error('name') border-red-500 ring-2 ring-red-500 @enderror">
        </div>
        @error('name')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-secondary mb-1.5">Parent Category</label>
        <div class="relative">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                <x-icon name="folder" class="w-4 h-4" />
            </div>
            <select name="parent_id" id="{{ $isEdit ?? false ? 'edit-parent' : '' }}"
                class="w-full rounded-xl border-theme pl-9 pr-10 py-2.5 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition appearance-none cursor-pointer">
                <option value="">— No parent (top level) —</option>
                @foreach ($categories ?? \App\Models\Category::rootOnly()->get() as $option)
                    <option value="{{ $option->id }}" @selected(old('parent_id', $category->parent_id ?? null) == $option->id)>
                        {{ $option->name }}
                    </option>
                @endforeach
            </select>
            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary opacity-40 pointer-events-none">
                <x-icon name="chevron-down" class="w-4 h-4" />
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-secondary mb-1.5">Description</label>
        <div class="relative">
            <textarea name="description" id="{{ $isEdit ?? false ? 'edit-description' : '' }}" rows="2"
                placeholder="Optional description..."
                class="w-full rounded-xl border-theme px-4 py-2.5 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition resize-none">{{ old('description', $category->description ?? '') }}</textarea>
        </div>
    </div>

    <label class="flex items-center gap-3 text-sm text-secondary cursor-pointer group">
        <input type="checkbox" name="is_active" id="{{ $isEdit ?? false ? 'edit-active' : '' }}" value="1"
            @checked(old('is_active', $category->is_active ?? true))
            class="w-4 h-4 rounded border-theme text-primary-green focus:ring-primary-green focus:ring-2 transition">
        <span class="group-hover:text-primary transition">Active</span>
        <span class="text-xs text-secondary opacity-60">(Inactive categories won't appear in product forms)</span>
    </label>
</div>
