<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Models\Supplier;
use App\Repositories\Eloquent\EloquentSupplierRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SupplierController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly EloquentSupplierRepository $supplierRepository,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:suppliers.view', only: ['index', 'show']),
            new Middleware('permission:suppliers.create', only: ['create', 'store']),
            new Middleware('permission:suppliers.update', only: ['edit', 'update']),
            new Middleware('permission:suppliers.delete', only: ['destroy']),
        ];
    }

    public function index(): View
    {
        $filters = request()->only(['search', 'is_active']);
        $suppliers = $this->supplierRepository->paginateWithFilters($filters, 15);

        return view('admin.suppliers.index', compact('suppliers', 'filters'));
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $this->supplierRepository->create($request->validated());

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created.');
    }

    public function update(StoreSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->supplierRepository->update($supplier, $request->validated());

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->exists()) {
            return redirect()
                ->route('admin.suppliers.index')
                ->with('error', 'Cannot delete a supplier with existing purchase history. Deactivate instead.');
        }

        $this->supplierRepository->delete($supplier);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted.');
    }
}
