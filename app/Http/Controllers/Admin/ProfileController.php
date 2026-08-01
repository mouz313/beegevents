<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'current_password' => 'required_with:password',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $data['avatar_path'] = ImageHelper::uploadAndCompress($request->file('avatar'), 'avatars', 'public', 524288, 300, 300);
        }

        if ($request->filled('password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Your current password is incorrect.'])->withInput();
            }
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'user' => $user->only('name', 'email', 'phone', 'avatar_path')]);
        }

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
