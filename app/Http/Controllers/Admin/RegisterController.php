<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/**
 * Admin-side management of POS registers (terminals/devices). Creating a
 * register here generates its registration_token -- the long-lived secret
 * that pairs a physical device to a warehouse for the offline-sync trust
 * model (see Register model + CheckRegisterSession middleware). The token
 * is shown to the admin exactly once, immediately after creation; the
 * cashier setting up that device copies it into the POS screen's one-time
 * pairing prompt (see register.js).
 */
class RegisterController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:warehouses.manage'),
        ];
    }

    public function index(): View
    {
        $registers = Register::with('warehouse')->orderBy('name')->get();

        return view('admin.registers.index', compact('registers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30', 'unique:registers,code'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
        ]);

        $register = Register::create($data + ['is_active' => true]);

        return redirect()
            ->route('admin.registers.index')
            ->with('success', "Register created. Pairing token: {$register->registration_token}")
            ->with('new_register_token', $register->registration_token);
    }

    public function deactivate(Register $register): RedirectResponse
    {
        $register->update(['is_active' => false]);

        return redirect()->route('admin.registers.index')->with('success', 'Register deactivated.');
    }

    public function regenerateToken(Register $register): RedirectResponse
    {
        $register->update(['registration_token' => \Illuminate\Support\Str::random(64)]);

        return redirect()
            ->route('admin.registers.index')
            ->with('success', "New pairing token generated for {$register->name}.")
            ->with('new_register_token', $register->registration_token);
    }
}
