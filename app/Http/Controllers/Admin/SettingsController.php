<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('id')->get();
        $groups = $settings->groupBy('group');

        return view('admin.settings.index', compact('settings', 'groups'));
    }

    public function store(Request $request)
    {
        $rules = [
            'key' => 'required|string|max:100|regex:/^[a-z0-9_.]+$/|unique:settings,key',
            'label' => 'required|string|max:255',
            'group' => 'required|string|max:50',
            'type' => 'required|in:text,textarea,number,email,url,image,boolean',
            'description' => 'nullable|string|max:1000',
        ];

        $rules['value'] = $request->input('type') === 'image'
            ? 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048'
            : 'nullable|string';

        $validated = $request->validate($rules);

        $value = $validated['value'] ?? null;

        if ($validated['type'] === 'boolean') {
            $value = $request->boolean('value') ? '1' : '0';
        }

        if ($validated['type'] === 'image' && $request->hasFile('value')) {
            $value = ImageHelper::uploadAndCompress($request->file('value'), 'settings');
        }

        $setting = Setting::create([
            'key' => $validated['key'],
            'label' => $validated['label'],
            'group' => $validated['group'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'value' => $value,
            'is_public' => true,
        ]);

        Setting::forgetCache();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'setting' => $setting]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Setting "'.$setting->label.'" added.');
    }

    public function update(Request $request, Setting $setting)
    {
        $rules = [
            'label' => 'required|string|max:255',
            'group' => 'required|string|max:50',
            'type' => 'required|in:text,textarea,number,email,url,image,boolean',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'nullable|boolean',
        ];

        $rules['value'] = $request->input('type') === 'image'
            ? 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048'
            : 'nullable|string';

        $validated = $request->validate($rules);

        $data = [
            'label' => $validated['label'],
            'group' => $validated['group'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'is_public' => $request->boolean('is_public', $setting->is_public),
        ];

        if ($validated['type'] === 'image') {
            if ($request->hasFile('value')) {
                if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                    Storage::disk('public')->delete($setting->value);
                }
                $data['value'] = ImageHelper::uploadAndCompress($request->file('value'), 'settings');
            } else {
                $data['value'] = $setting->value;
            }
        } else {
            $data['value'] = $validated['type'] === 'boolean'
                ? ($request->boolean('value') ? '1' : '0')
                : ($validated['value'] ?? null);
        }

        $setting->update($data);
        Setting::forgetCache();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'setting' => $setting]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Setting "'.$setting->label.'" updated.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.value' => 'nullable|string',
        ]);

        $count = 0;

        foreach ($request->input('settings', []) as $id => $value) {
            $setting = Setting::find($id);
            if (! $setting || $setting->type === 'image') {
                continue;
            }

            $normalized = $setting->type === 'boolean' ? ($value ? '1' : '0') : $value;
            if ($setting->value !== $normalized) {
                $setting->update(['value' => $normalized]);
                $count++;
            }
        }

        Setting::forgetCache();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'updated' => $count]);
        }

        return redirect()->route('admin.settings.index')->with('success', $count.' settings updated.');
    }

    public function destroy(Request $request, Setting $setting)
    {
        if ($setting->type === 'image' && $setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting->delete();
        Setting::forgetCache();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Setting deleted.');
    }
}
