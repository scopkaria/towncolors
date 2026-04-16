<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Media;
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
        $mediaItems = Media::query()
            ->whereIn('file_type', ['image', 'video'])
            ->latest()
            ->limit(300)
            ->get(['id', 'file_name', 'file_type']);

        return view('admin.settings.edit', compact('settings', 'mediaItems'));
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
            'light_logo_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'dark_logo_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'primary_color'    => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color'  => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'payment_card_enabled' => ['nullable', 'boolean'],
            'payment_paypal_enabled' => ['nullable', 'boolean'],
            'payment_selcom_enabled' => ['nullable', 'boolean'],
            'payment_mpesa_enabled' => ['nullable', 'boolean'],
            'payment_bank_enabled' => ['nullable', 'boolean'],
            'mpesa_paybill' => ['nullable', 'string', 'max:120'],
            'payment_notes' => ['nullable', 'string', 'max:2000'],
            'service_hero_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'blog_hero_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'shop_hero_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'cloud_hero_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'portfolio_hero_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'about_hero_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'contact_hero_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'service_hero_subtitle' => ['nullable', 'string', 'max:255'],
            'blog_hero_subtitle' => ['nullable', 'string', 'max:255'],
            'shop_hero_subtitle' => ['nullable', 'string', 'max:255'],
            'cloud_hero_subtitle' => ['nullable', 'string', 'max:255'],
            'portfolio_hero_subtitle' => ['nullable', 'string', 'max:255'],
            'about_hero_subtitle' => ['nullable', 'string', 'max:255'],
            'contact_hero_subtitle' => ['nullable', 'string', 'max:255'],
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

        foreach (['payment_card_enabled', 'payment_paypal_enabled', 'payment_selcom_enabled', 'payment_mpesa_enabled', 'payment_bank_enabled'] as $booleanField) {
            $validated[$booleanField] = $request->boolean($booleanField);
        }

        $settings->update($validated);

        return back()->with('success', 'Settings saved successfully.');
    }
}
