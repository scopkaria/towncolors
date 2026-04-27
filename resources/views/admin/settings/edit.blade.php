<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                System configuration
            </span>
            <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Settings</h1>
            <p class="max-w-3xl text-sm text-brand-muted">Professional control panel for branding, company details, payments, and mobile assets.</p>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data"
          x-data="settingsEditor()"
          class="grid gap-6 lg:grid-cols-[220px_minmax(0,1fr)_300px]">
        @csrf

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-3xl border border-white/70 bg-white/90 p-3 shadow-panel">
                <template x-for="tab in tabs" :key="tab.key">
                    <button type="button"
                            @click="activeTab = tab.key"
                            class="mb-1 flex w-full items-center justify-between rounded-2xl px-3 py-2.5 text-left text-sm font-semibold transition"
                            :class="activeTab === tab.key ? 'bg-brand-primary text-white' : 'text-brand-ink hover:bg-warm-200/60'">
                        <span x-text="tab.label"></span>
                    </button>
                </template>
            </div>
        </aside>

        <section class="space-y-6">
            <div x-show="activeTab === 'general'" x-cloak class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                <h2 class="font-display text-xl text-brand-ink">General</h2>
                <p class="mt-1 text-sm text-brand-muted">Core organization identity and contact defaults.</p>

                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="company_name" class="block text-sm font-semibold text-brand-ink">Company Name</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $settings->company_name) }}"
                               class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                        @error('company_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-brand-ink">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $settings->email) }}"
                               class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                        @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-brand-ink">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $settings->phone) }}"
                               class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                        @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'logos'" x-cloak class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                <h2 class="font-display text-xl text-brand-ink">Logos</h2>
                <p class="mt-1 text-sm text-brand-muted">Each logo supports Media Library selection, new upload, and removal.</p>

                <div class="mt-6 space-y-4">
                    <template x-for="field in logoFields" :key="field.key">
                        <div class="rounded-2xl border border-warm-300/50 bg-warm-100/70 p-4">
                            <div class="flex flex-wrap items-center gap-4">
                                <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-2xl border border-warm-300/50 bg-white">
                                    <template x-if="logos[field.key].url">
                                        <img :src="logos[field.key].url" :alt="field.label" class="h-full w-full object-contain p-2">
                                    </template>
                                    <template x-if="!logos[field.key].url">
                                        <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-brand-muted">No logo</span>
                                    </template>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-brand-ink" x-text="field.label"></p>
                                    <p class="truncate text-xs text-brand-muted" x-text="logos[field.key].url ? logos[field.key].url : 'No media selected'"></p>
                                    <input type="hidden" :name="field.key" :value="logos[field.key].id || ''">
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button type="button" class="btn-secondary" @click="openPicker(field.key)">Select from Media Library</button>
                                    <label class="btn-secondary cursor-pointer">
                                        Upload New
                                        <input type="file" class="sr-only" accept="image/*" multiple @change="quickUploadForField($event, field.key)">
                                    </label>
                                    <button type="button" class="btn-secondary" x-show="logos[field.key].url" @click="clearLogo(field.key)">Remove</button>
                                </div>
                            </div>
                        </div>
                    </template>
                    @error('logo_media_id')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                    @error('light_logo_media_id')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                    @error('dark_logo_media_id')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                    @error('mobile_icon_media_id')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div x-show="activeTab === 'company'" x-cloak class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                <h2 class="font-display text-xl text-brand-ink">Company Info</h2>

                <div class="mt-6 space-y-5">
                    <div>
                        <label for="address" class="block text-sm font-semibold text-brand-ink">Address</label>
                        <textarea name="address" id="address" rows="4" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">{{ old('address', $settings->address) }}</textarea>
                        @error('address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'payments'" x-cloak class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                <h2 class="font-display text-xl text-brand-ink">Payment Methods</h2>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <label class="flex items-center justify-between rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-3 text-sm font-semibold text-brand-ink">
                        Card payments
                        <input type="checkbox" name="payment_card_enabled" value="1" {{ old('payment_card_enabled', $settings->payment_card_enabled) ? 'checked' : '' }} class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary">
                    </label>
                    <label class="flex items-center justify-between rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-3 text-sm font-semibold text-brand-ink">
                        PayPal
                        <input type="checkbox" name="payment_paypal_enabled" value="1" {{ old('payment_paypal_enabled', $settings->payment_paypal_enabled) ? 'checked' : '' }} class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary">
                    </label>
                    <label class="flex items-center justify-between rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-3 text-sm font-semibold text-brand-ink">
                        Selcom
                        <input type="checkbox" name="payment_selcom_enabled" value="1" {{ old('payment_selcom_enabled', $settings->payment_selcom_enabled) ? 'checked' : '' }} class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary">
                    </label>
                    <label class="flex items-center justify-between rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-3 text-sm font-semibold text-brand-ink">
                        Bank transfer
                        <input type="checkbox" name="payment_bank_enabled" value="1" {{ old('payment_bank_enabled', $settings->payment_bank_enabled) ? 'checked' : '' }} class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary">
                    </label>
                    <label class="sm:col-span-2 flex items-center justify-between rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-3 text-sm font-semibold text-brand-ink">
                        M-Pesa / Paybill
                        <input type="checkbox" name="payment_mpesa_enabled" value="1" {{ old('payment_mpesa_enabled', $settings->payment_mpesa_enabled) ? 'checked' : '' }} class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary">
                    </label>
                </div>
            </div>

            <div x-show="activeTab === 'bank'" x-cloak class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                <h2 class="font-display text-xl text-brand-ink">Bank Details</h2>

                <div class="mt-6 grid gap-5">
                    <div>
                        <label for="bank_details" class="block text-sm font-semibold text-brand-ink">Bank / Payment Info</label>
                        <textarea id="bank_details" name="bank_details" rows="4" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">{{ old('bank_details', $settings->bank_details) }}</textarea>
                        @error('bank_details')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="mpesa_paybill" class="block text-sm font-semibold text-brand-ink">M-Pesa / Paybill Details</label>
                        <input type="text" id="mpesa_paybill" name="mpesa_paybill" value="{{ old('mpesa_paybill', $settings->mpesa_paybill) }}"
                               class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                        @error('mpesa_paybill')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="payment_notes" class="block text-sm font-semibold text-brand-ink">Payment Instructions</label>
                        <textarea id="payment_notes" name="payment_notes" rows="3" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">{{ old('payment_notes', $settings->payment_notes) }}</textarea>
                        @error('payment_notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'chat'" x-cloak class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                <h2 class="font-display text-xl text-brand-ink">Live Chat</h2>
                <p class="mt-1 text-sm text-brand-muted">Manage your live chat widget status and availability.</p>

                <div class="mt-6 space-y-4">
                    <label class="flex items-center justify-between rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-4 text-sm font-semibold text-brand-ink">
                        <span>Enable Live Chat</span>
                        <input type="checkbox" name="live_chat_enabled" value="1" {{ old('live_chat_enabled', $settings->live_chat_enabled ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-warm-400/50 text-emerald-500 focus:ring-emerald-500">
                    </label>
                    <p class="text-xs text-brand-muted">When enabled, the chat widget appears as online on your website. When disabled, it appears as offline.</p>
                </div>
            </div>

            <div x-show="activeTab === 'theme'" x-cloak class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8"
                 x-data="{ primary: '{{ old('primary_color', $settings->primary_color ?? '#FFB162') }}', secondary: '{{ old('secondary_color', $settings->secondary_color ?? '#A35139') }}', background: '{{ old('background_color', $settings->background_color ?? '#EEE9DF') }}' }">
                <h2 class="font-display text-xl text-brand-ink">Theme & Colors</h2>

                <div class="mt-6 grid gap-6 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Primary Color</label>
                        <div class="mt-2 flex items-center gap-3">
                            <input type="color" name="primary_color" x-model="primary" class="h-10 w-14 rounded-xl border border-warm-300/50 p-1">
                            <span class="font-mono text-sm text-brand-muted" x-text="primary.toUpperCase()"></span>
                        </div>
                        @error('primary_color')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Secondary Color</label>
                        <div class="mt-2 flex items-center gap-3">
                            <input type="color" name="secondary_color" x-model="secondary" class="h-10 w-14 rounded-xl border border-warm-300/50 p-1">
                            <span class="font-mono text-sm text-brand-muted" x-text="secondary.toUpperCase()"></span>
                        </div>
                        @error('secondary_color')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Background Color</label>
                        <div class="mt-2 flex items-center gap-3">
                            <input type="color" name="background_color" x-model="background" class="h-10 w-14 rounded-xl border border-warm-300/50 p-1">
                            <span class="font-mono text-sm text-brand-muted" x-text="background.toUpperCase()"></span>
                        </div>
                        @error('background_color')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-2xl border border-warm-300/50">
                    <div class="flex h-10">
                        <div class="flex-1" :style="'background:' + primary"></div>
                        <div class="flex-1" :style="'background:' + secondary"></div>
                        <div class="flex-1" :style="'background:' + background"></div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'mobile'" x-cloak class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                <h2 class="font-display text-xl text-brand-ink">Mobile App</h2>
                <p class="mt-1 text-sm text-brand-muted">Manage mobile in-app logo and launcher icon source.</p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <template x-for="field in mobileFields" :key="field.key">
                        <div class="rounded-2xl border border-warm-300/50 bg-warm-100/70 p-4">
                            <p class="text-sm font-semibold text-brand-ink" x-text="field.label"></p>
                            <input type="hidden" :name="field.key" :value="logos[field.key].id || ''">
                            <div class="mt-3 flex items-center gap-3">
                                <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl border border-warm-300/50 bg-white">
                                    <template x-if="logos[field.key].url"><img :src="logos[field.key].url" class="h-full w-full object-contain p-1"></template>
                                    <template x-if="!logos[field.key].url"><span class="text-[10px] text-brand-muted">No media</span></template>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" class="btn-secondary" @click="openPicker(field.key)">Select from Media Library</button>
                                    <label class="btn-secondary cursor-pointer">
                                        Upload New
                                        <input type="file" class="sr-only" accept="image/*" multiple @change="quickUploadForField($event, field.key)">
                                    </label>
                                    <button type="button" class="btn-secondary" x-show="logos[field.key].url" @click="clearLogo(field.key)">Remove</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-3xl border border-white/70 bg-white/95 p-5 shadow-panel">
                <h3 class="font-display text-lg text-brand-ink">Save Settings</h3>
                <p class="mt-1 text-xs text-brand-muted">This panel is always visible so saving does not require long scrolling.</p>

                <div class="mt-4 space-y-3">
                    <button type="submit" class="btn-primary w-full justify-center">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        Save Settings
                    </button>
                    <p class="text-[11px] leading-5 text-brand-muted">Tip: Use tabs on the left to jump quickly between sections.</p>
                </div>
            </div>
        </aside>

        <div x-show="picker.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-900/70 p-4 backdrop-blur-sm" @keydown.escape.window="closePicker()">
            <div class="relative flex h-[80vh] w-[78vw] max-w-6xl flex-col rounded-3xl border border-white/70 bg-warm-100 shadow-panel" @click.outside="closePicker()">
                <button type="button" class="absolute right-3 top-3 rounded-xl p-2 text-brand-muted hover:bg-warm-200 hover:text-brand-ink" @click="closePicker()">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>

                <div class="border-b border-warm-300/40 px-6 py-4">
                    <h3 class="font-display text-lg text-brand-ink" x-text="'Media for ' + (pickerTitles[picker.field] || 'Field')"></h3>
                    <p class="text-xs text-brand-muted">WordPress-style workflow: select from library or upload new files.</p>
                </div>

                <div class="border-b border-warm-300/40 px-6 py-3">
                    <div class="inline-flex rounded-xl border border-warm-300/50 bg-warm-100 p-1">
                        <button type="button" class="rounded-lg px-3 py-1.5 text-xs font-semibold" :class="picker.view === 'library' ? 'bg-brand-primary text-white' : 'text-brand-ink'" @click="picker.view = 'library'">Select from Library</button>
                        <button type="button" class="rounded-lg px-3 py-1.5 text-xs font-semibold" :class="picker.view === 'upload' ? 'bg-brand-primary text-white' : 'text-brand-ink'" @click="picker.view = 'upload'">Upload New</button>
                    </div>
                </div>

                <div class="min-h-0 flex-1 p-6">
                    <div x-show="picker.view === 'library'" class="flex h-full min-h-0 flex-col">
                        <input type="text" x-model="picker.search" placeholder="Search media..." class="mb-4 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                        <div class="min-h-0 flex-1 overflow-y-auto">
                            <template x-if="filteredMedia.length === 0"><p class="py-12 text-center text-sm text-brand-muted">No media found.</p></template>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5" x-show="filteredMedia.length">
                                <template x-for="item in filteredMedia" :key="'m-' + item.id">
                                    <button type="button"
                                            class="group relative overflow-hidden rounded-2xl border-2 bg-warm-100 text-left transition"
                                            :class="String(picker.selectedId) === String(item.id) ? 'border-brand-primary ring-2 ring-brand-primary/30' : 'border-warm-300/50 hover:border-brand-primary/40'"
                                            @click="picker.selectedId = String(item.id)">
                                        <div class="aspect-square overflow-hidden bg-warm-200">
                                            <img :src="item.url" :alt="item.name" class="h-full w-full object-cover transition duration-200 group-hover:scale-105">
                                        </div>
                                        <div class="px-2.5 py-2">
                                            <p class="truncate text-xs font-semibold text-brand-ink" x-text="item.name"></p>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-warm-300/40 pt-4">
                            <a href="{{ route('admin.media.index') }}" target="_blank" class="text-sm text-brand-primary hover:underline">Open full Media Library</a>
                            <button type="button" class="btn-primary" :disabled="!picker.selectedId" @click="useSelected()">Use Selected</button>
                        </div>
                    </div>

                    <div x-show="picker.view === 'upload'" class="h-full">
                        <div class="rounded-2xl border-2 border-dashed border-warm-300/60 bg-warm-100 p-8 text-center">
                            <p class="text-sm font-semibold text-brand-ink">Upload file(s) to Media Library</p>
                            <p class="mt-1 text-xs text-brand-muted">Supports multi-upload. First uploaded image is auto-selected.</p>
                            <label class="btn-primary mt-4 inline-flex cursor-pointer">
                                Choose Files
                                <input type="file" class="sr-only" multiple @change="uploadFromPicker($event)">
                            </label>
                        </div>

                        <template x-if="picker.uploaded.length">
                            <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700">
                                Uploaded <span x-text="picker.uploaded.length"></span> file(s). First file selected automatically.
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function settingsEditor() {
            return {
                activeTab: 'general',
                tabs: [
                    { key: 'general', label: 'General' },
                    { key: 'logos', label: 'Logos' },
                    { key: 'company', label: 'Company Info' },
                    { key: 'payments', label: 'Payment Methods' },
                    { key: 'bank', label: 'Bank Details' },
                    { key: 'chat', label: 'Live Chat' },
                    { key: 'theme', label: 'Theme & Colors' },
                    { key: 'mobile', label: 'Mobile App' },
                ],
                logoFields: [
                    { key: 'logo_media_id', label: 'Company Logo' },
                    { key: 'light_logo_media_id', label: 'Light Logo' },
                    { key: 'dark_logo_media_id', label: 'Dark Logo' },
                    { key: 'mobile_icon_media_id', label: 'Mobile Icon' },
                ],
                mobileFields: [
                    { key: 'mobile_logo_media_id', label: 'Mobile App Logo' },
                    { key: 'mobile_icon_media_id', label: 'Mobile App Icon' },
                ],
                pickerTitles: {
                    logo_media_id: 'Company Logo',
                    light_logo_media_id: 'Light Logo',
                    dark_logo_media_id: 'Dark Logo',
                    mobile_logo_media_id: 'Mobile App Logo',
                    mobile_icon_media_id: 'Mobile App Icon',
                },
                logos: {
                    logo_media_id: {
                        id: '{{ old('logo_media_id', $settings->logo_media_id) }}' || null,
                        url: '{{ old('logo_media_id', $settings->logo_media_id) && $settings->logoMedia ? $settings->logoMedia->url() : ($settings->logoUrl() ?? '') }}',
                    },
                    light_logo_media_id: {
                        id: '{{ old('light_logo_media_id', $settings->light_logo_media_id) }}' || null,
                        url: '{{ old('light_logo_media_id', $settings->light_logo_media_id) && $settings->lightLogoMedia ? $settings->lightLogoMedia->url() : '' }}',
                    },
                    dark_logo_media_id: {
                        id: '{{ old('dark_logo_media_id', $settings->dark_logo_media_id) }}' || null,
                        url: '{{ old('dark_logo_media_id', $settings->dark_logo_media_id) && $settings->darkLogoMedia ? $settings->darkLogoMedia->url() : '' }}',
                    },
                    mobile_logo_media_id: {
                        id: '{{ old('mobile_logo_media_id', $settings->mobile_logo_media_id) }}' || null,
                        url: '{{ old('mobile_logo_media_id', $settings->mobile_logo_media_id) && $settings->mobileLogoMedia ? $settings->mobileLogoMedia->url() : '' }}',
                    },
                    mobile_icon_media_id: {
                        id: '{{ old('mobile_icon_media_id', $settings->mobile_icon_media_id) }}' || null,
                        url: '{{ old('mobile_icon_media_id', $settings->mobile_icon_media_id) && $settings->mobileIconMedia ? $settings->mobileIconMedia->url() : '' }}',
                    },
                },
                picker: {
                    open: false,
                    field: null,
                    view: 'library',
                    search: '',
                    selectedId: null,
                    uploaded: [],
                },
                mediaItems: [],

                get filteredMedia() {
                    const q = this.picker.search.trim().toLowerCase();
                    if (!q) return this.mediaItems;
                    return this.mediaItems.filter(item => {
                        return item.name.toLowerCase().includes(q) || item.type.toLowerCase().includes(q);
                    });
                },

                async ensureMediaLoaded() {
                    if (this.mediaItems.length > 0) return;
                    const res = await fetch('{{ route('admin.media.api') }}');
                    this.mediaItems = await res.json();
                },

                async openPicker(field) {
                    this.picker.open = true;
                    this.picker.field = field;
                    this.picker.view = 'library';
                    this.picker.search = '';
                    this.picker.selectedId = this.logos[field]?.id ? String(this.logos[field].id) : null;
                    this.picker.uploaded = [];
                    await this.ensureMediaLoaded();
                },

                closePicker() {
                    this.picker.open = false;
                    this.picker.field = null;
                },

                useSelected() {
                    const id = this.picker.selectedId;
                    if (!id || !this.picker.field) return;
                    const item = this.mediaItems.find(m => String(m.id) === String(id));
                    if (!item) return;
                    this.logos[this.picker.field].id = item.id;
                    this.logos[this.picker.field].url = item.url;
                    this.closePicker();
                },

                clearLogo(field) {
                    this.logos[field].id = null;
                    this.logos[field].url = '';
                },

                async uploadToLibrary(files) {
                    if (!files || files.length === 0) return [];
                    const form = new FormData();
                    Array.from(files).forEach(file => form.append('files[]', file));

                    const res = await fetch('{{ route('admin.media.api.upload') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: form,
                    });

                    if (!res.ok) {
                        throw new Error('Upload failed');
                    }

                    const payload = await res.json();
                    const items = payload.items || [];
                    if (items.length) {
                        this.mediaItems = [...items, ...this.mediaItems.filter(existing => !items.some(i => String(i.id) === String(existing.id)))];
                    }
                    return items;
                },

                async quickUploadForField(event, field) {
                    try {
                        const files = event.target.files;
                        const uploaded = await this.uploadToLibrary(files);
                        if (uploaded.length > 0) {
                            this.logos[field].id = uploaded[0].id;
                            this.logos[field].url = uploaded[0].url;
                        }
                    } catch (e) {
                        alert('Upload failed. Please try again.');
                    } finally {
                        event.target.value = '';
                    }
                },

                async uploadFromPicker(event) {
                    try {
                        const uploaded = await this.uploadToLibrary(event.target.files);
                        this.picker.uploaded = uploaded;
                        if (uploaded.length > 0) {
                            this.picker.selectedId = String(uploaded[0].id);
                            this.useSelected();
                        }
                    } catch (e) {
                        alert('Upload failed. Please try again.');
                    } finally {
                        event.target.value = '';
                    }
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
