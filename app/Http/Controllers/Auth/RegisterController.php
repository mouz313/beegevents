<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register', ['role' => in_array(request('role'), ['customer', 'vendor']) ? request('role') : 'customer']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'in:customer,vendor'],
        ]);

        $user = User::create([
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(match($user->role) {
            'vendor' => route('vendor.onboarding'),
            default => route('customer.dashboard'),
        });
    }
}
