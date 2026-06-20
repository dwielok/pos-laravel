<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        // $this->middleware('can:roles.manage');
    }

    public function index(): View
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get()->groupBy(fn($p) => explode('.', $p->name)[0]);

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles,name', 'alpha_dash'],
        ]);

        Role::create(['name' => $data['name'], 'guard_name' => 'web']);

        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function updatePermissions(Request $request, Role $role): RedirectResponse
    {
        // The 'admin' role is intentionally protected from having its
        // permissions edited through this UI -- it's also given a Gate
        // bypass in AuthServiceProvider, so editing its permission list
        // here would be misleading (it would appear to restrict admin
        // access without actually doing so).
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')->with('error', 'The admin role cannot be modified.');
        }

        $data = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', "Permissions updated for '{$role->name}'.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['admin', 'manager', 'cashier', 'stock_clerk'], true)) {
            return redirect()->route('admin.roles.index')->with('error', 'Built-in roles cannot be deleted.');
        }

        if ($role->users()->exists()) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete a role assigned to users.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }
}
