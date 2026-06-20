<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CustomerController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:customers.view', only: ['index', 'show']),
            new Middleware('permission:customers.create', only: ['create', 'store']),
            new Middleware('permission:customers.update', only: ['edit', 'update']),
            new Middleware('permission:customers.delete', only: ['destroy']),
        ];
    }

    public function index(): View
    {
        $filters = request()->only(['search']);

        $customers = $this->customerRepository->paginate(15, [], function ($query) use ($filters) {
            $query->where('is_guest', false);

            if (!empty($filters['search'])) {
                $query->search($filters['search']);
            }

            $query->orderBy('name');
        });

        return view('admin.customers.index', compact('customers', 'filters'));
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->customerRepository->create($request->validated() + ['is_guest' => false]);

        return redirect()->route('admin.customers.index')->with('success', 'Customer created.');
    }

    public function show(Customer $customer): View
    {
        $purchaseHistory = $this->customerRepository->purchaseHistory($customer, 15);

        return view('admin.customers.show', [
            'customer' => $customer,
            'sales' => $purchaseHistory,
            'totalSpent' => $customer->totalSpent(),
        ]);
    }

    public function update(StoreCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->customerRepository->update($customer, $request->validated());

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->sales()->exists()) {
            return redirect()
                ->route('admin.customers.index')
                ->with('error', 'Cannot delete a customer with purchase history. Their record is kept for sales reporting integrity.');
        }

        $this->customerRepository->delete($customer);

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted.');
    }
}
