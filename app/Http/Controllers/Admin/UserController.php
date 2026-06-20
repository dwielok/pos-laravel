<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(User::class, 'user');
    }

    public function index(): View
    {
        $users = User::with('roles')->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? true,
            'email_verified_at' => now(), // admin-created accounts skip email verification
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(StoreUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        if ($user->sales()->exists() || $user->purchases()->exists()) {
            // Deactivate rather than delete -- preserves the audit trail
            // (who rang up which sale, who recorded which purchase) while
            // still revoking access immediately.
            $user->update(['is_active' => false]);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User has transaction history and was deactivated instead of deleted, to preserve records.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
