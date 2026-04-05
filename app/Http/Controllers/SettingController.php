<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(Request $request): View
    {
        abort_unless($request->user()->role === UserRole::ADMIN, 403);

        $settings = Setting::instance();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role === UserRole::ADMIN, 403);

        $validated = $request->validate([
            'company_name'     => ['nullable', 'string', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'email'            => ['nullable', 'email', 'max:255'],
            'address'          => ['nullable', 'string', 'max:1000'],
            'bank_details'     => ['nullable', 'string', 'max:2000'],
            'logo'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'logo_media_id'    => ['nullable', 'integer', 'exists:media,id'],
            'primary_color'    => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color'  => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $settings = Setting::instance();

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['logo']);

        $settings->update($validated);

        return back()->with('success', 'Settings saved successfully.');
    }
}
