<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:categories.manage'),
        ];
    }

    public function index(): View
    {
        $categories = $this->categoryRepository->tree();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryRepository->create($request->validated() + [
            'slug' => \Illuminate\Support\Str::slug($request->input('name')) . '-' . \Illuminate\Support\Str::random(6),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categoryRepository->update($category, $request->validated());

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot delete a category that still has products. Reassign them first.');
        }

        $this->categoryRepository->delete($category);

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
