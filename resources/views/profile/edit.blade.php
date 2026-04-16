<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            <h2 class="font-display text-3xl text-brand-ink leading-tight">Profile</h2>
            <p class="text-sm text-brand-muted">Manage your account details, profile image, and password.</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('warning'))
                <div class="p-4 sm:p-6 rounded-2xl border border-amber-200 bg-amber-50 text-sm text-amber-800">{{ session('warning') }}</div>
            @endif
            <div class="p-4 sm:p-8 bg-warm-100 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-warm-100 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-warm-100 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
