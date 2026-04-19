<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class MobileBrandingController extends Controller
{
    public function show(): JsonResponse
    {
        $settings = Setting::instance();

        $primary = $settings->primary_color ?: '#FFB162';
        $secondary = $settings->secondary_color ?: '#A35139';
        $background = $settings->background_color ?: '#EEE9DF';

        return response()->json([
            'app_name' => $settings->company_name ?: config('app.name', 'Towncore'),
            'colors' => [
                'primary' => $primary,
                'secondary' => $secondary,
                'background' => $background,
                'primary_dark' => $secondary,
                'text_light' => '#EEE9DF',
                'text_dark' => '#1B2632',
            ],
            'assets' => [
                'logo_url' => $this->absoluteUrl($settings->mobileLogoUrl()),
                'app_icon_url' => $this->absoluteUrl($settings->mobileIconUrl()),
            ],
        ]);
    }

    private function absoluteUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return url($url);
    }
}
