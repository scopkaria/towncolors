<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                Company settings
            </span>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Settings</h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">Configure your company details, logo, and bank info for invoice branding.</p>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            {{-- Logo Section --}}
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8"
                 x-data="{
                     mediaOpen: false,
                     mediaItems: [],
                     selectedId: {{ $settings->logo_media_id ?? 'null' }},
                     selectedUrl: '{{ $settings->logo_media_id && $settings->logoMedia ? $settings->logoMedia->url() : '' }}',
                     filePreview: null,
                     async openPicker() {
                         this.mediaOpen = true;
                         if (this.mediaItems.length === 0) {
                             const res = await fetch('{{ route('admin.media.api') }}');
                             this.mediaItems = await res.json();
                         }
                     },
                     selectMedia(item) {
                         this.selectedId = item.id;
                         this.selectedUrl = item.url;
                         this.filePreview = null;
                         this.mediaOpen = false;
                     },
                     clearLogo() {
                         this.selectedId = null;
                         this.selectedUrl = '';
                         this.filePreview = null;
                     }
                 }">
                <h2 class="font-display text-xl text-brand-ink">Company Logo</h2>
                <p class="mt-1 text-sm text-brand-muted">Choose from the Media Library or upload a new file. Displays on the navbar, sidebar, login page, emails, and invoices.</p>

                {{-- Hidden input for logo_media_id --}}
                <input type="hidden" name="logo_media_id" :value="selectedId ?? ''">

                <div class="mt-6 flex flex-wrap items-center gap-6">
                    {{-- Current / Preview --}}
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-stone-200 bg-stone-50">
                        <template x-if="filePreview">
                            <img :src="filePreview" class="h-full w-full object-contain p-2" alt="Preview">
                        </template>
                        <template x-if="!filePreview && selectedUrl">
                            <img :src="selectedUrl" class="h-full w-full object-contain p-2" alt="Logo">
                        </template>
                        <template x-if="!filePreview && !selectedUrl">
                            @if ($settings->logoUrl() && !$settings->logo_media_id)
                                <img src="{{ $settings->logoUrl() }}" class="h-full w-full object-contain p-2" alt="Logo">
                            @else
                                <svg class="h-10 w-10 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v14.25c0 .828.672 1.5 1.5 1.5Z"/></svg>
                            @endif
                        </template>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        {{-- Pick from Media Library --}}
                        <button type="button" class="btn-primary inline-flex" @click="openPicker()">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v14.25c0 .828.672 1.5 1.5 1.5Z"/></svg>
                            From Media Library
                        </button>

                        {{-- Or upload a new file (legacy) --}}
                        <label class="btn-secondary inline-flex cursor-pointer">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                            Upload New
                            <input type="file" name="logo" accept="image/*" class="sr-only"
                                   @change="if ($event.target.files[0]) { filePreview = URL.createObjectURL($event.target.files[0]); selectedId = null; selectedUrl = ''; }">
                        </label>

                        {{-- Clear --}}
                        <button type="button" class="inline-flex items-center rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-brand-muted transition hover:text-red-500"
                                x-show="selectedId || selectedUrl || filePreview" @click="clearLogo()">
                            <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            Remove
                        </button>
                    </div>

                    @error('logo')
                        <p class="w-full text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    @error('logo_media_id')
                        <p class="w-full text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Media Picker Modal --}}
                <div x-show="mediaOpen" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
                     @keydown.escape.window="mediaOpen = false">
                    <div class="relative w-full max-w-3xl rounded-3xl border border-white/70 bg-white shadow-panel" @click.outside="mediaOpen = false">
                        <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                            <h3 class="font-display text-lg text-brand-ink">Select Logo from Media Library</h3>
                            <button type="button" @click="mediaOpen = false" class="rounded-xl p-2 text-brand-muted hover:bg-stone-100 hover:text-brand-ink">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="max-h-[60vh] overflow-y-auto p-6">
                            <template x-if="mediaItems.length === 0">
                                <p class="py-12 text-center text-sm text-brand-muted">No images in the Media Library yet. Upload some first.</p>
                            </template>
                            <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-5">
                                <template x-for="item in mediaItems" :key="item.id">
                                    <button type="button"
                                            class="group relative aspect-square overflow-hidden rounded-2xl border-2 transition"
                                            :class="selectedId === item.id ? 'border-brand-primary ring-2 ring-brand-primary/30' : 'border-stone-200 hover:border-brand-primary/50'"
                                            @click="selectMedia(item)">
                                        <img :src="item.url" :alt="item.name" class="h-full w-full object-cover">
                                        <div class="absolute inset-0 flex items-center justify-center bg-brand-primary/60 opacity-0 transition group-hover:opacity-100"
                                             x-show="selectedId !== item.id">
                                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        </div>
                                        <div x-show="selectedId === item.id" class="absolute inset-0 flex items-center justify-center bg-brand-primary/50">
                                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="flex items-center justify-between border-t border-stone-100 px-6 py-4">
                            <a href="{{ route('admin.media.index') }}" target="_blank" class="text-sm text-brand-primary hover:underline">Manage Media Library →</a>
                            <button type="button" @click="mediaOpen = false" class="btn-secondary">Done</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Company Info --}}
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                <h2 class="font-display text-xl text-brand-ink">Company Information</h2>
                <p class="mt-1 text-sm text-brand-muted">This information appears on all generated invoice PDFs.</p>

                <div class="mt-6 grid gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="company_name" class="block text-sm font-semibold text-brand-ink">Company Name</label>
                        <input type="text" name="company_name" id="company_name"
                               value="{{ old('company_name', $settings->company_name) }}"
                               placeholder="e.g. Towncore Ltd"
                               class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary">
                        @error('company_name')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-brand-ink">Phone</label>
                        <input type="text" name="phone" id="phone"
                               value="{{ old('phone', $settings->phone) }}"
                               placeholder="+255 xxx xxx xxx"
                               class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-brand-ink">Email</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $settings->email) }}"
                               placeholder="billing@company.com"
                               class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary">
                        @error('email')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-semibold text-brand-ink">Address</label>
                        <textarea name="address" id="address" rows="3"
                                  placeholder="Street, City, Country"
                                  class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary">{{ old('address', $settings->address) }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Bank Details --}}
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                <h2 class="font-display text-xl text-brand-ink">Bank Details</h2>
                <p class="mt-1 text-sm text-brand-muted">Payment information shown on invoices.</p>

                <div class="mt-6">
                    <label for="bank_details" class="block text-sm font-semibold text-brand-ink">Bank / Payment Info</label>
                    <textarea name="bank_details" id="bank_details" rows="4"
                              placeholder="Bank Name: ...&#10;Account Name: ...&#10;Account Number: ...&#10;Swift Code: ..."
                              class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary">{{ old('bank_details', $settings->bank_details) }}</textarea>
                    @error('bank_details')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Theme Colors --}}
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8"
                 x-data="{
                     primary:    '{{ old('primary_color', $settings->primary_color ?? '#F97316') }}',
                     secondary:  '{{ old('secondary_color', $settings->secondary_color ?? '#EA580C') }}',
                     background: '{{ old('background_color', $settings->background_color ?? '#F5F5F4') }}',
                 }">
                <h2 class="font-display text-xl text-brand-ink">Theme Colors</h2>
                <p class="mt-1 text-sm text-brand-muted">Customize the platform's brand colors. Changes apply site-wide after saving.</p>

                <div class="mt-6 grid gap-6 sm:grid-cols-3">
                    {{-- Primary Color --}}
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Primary Color</label>
                        <div class="mt-2 flex items-center gap-3">
                            <input type="color" name="primary_color" x-model="primary"
                                   class="h-10 w-14 cursor-pointer rounded-xl border border-stone-200 p-1 shadow-sm">
                            <span class="font-mono text-sm text-brand-muted" x-text="primary.toUpperCase()"></span>
                        </div>
                        <p class="mt-1 text-xs text-brand-muted">Buttons, links, highlights</p>
                        @error('primary_color')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Secondary Color --}}
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Secondary / Hover Color</label>
                        <div class="mt-2 flex items-center gap-3">
                            <input type="color" name="secondary_color" x-model="secondary"
                                   class="h-10 w-14 cursor-pointer rounded-xl border border-stone-200 p-1 shadow-sm">
                            <span class="font-mono text-sm text-brand-muted" x-text="secondary.toUpperCase()"></span>
                        </div>
                        <p class="mt-1 text-xs text-brand-muted">Hover states, active accents</p>
                        @error('secondary_color')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Background Color --}}
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Background Color</label>
                        <div class="mt-2 flex items-center gap-3">
                            <input type="color" name="background_color" x-model="background"
                                   class="h-10 w-14 cursor-pointer rounded-xl border border-stone-200 p-1 shadow-sm">
                            <span class="font-mono text-sm text-brand-muted" x-text="background.toUpperCase()"></span>
                        </div>
                        <p class="mt-1 text-xs text-brand-muted">Page surface / background</p>
                        @error('background_color')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Live preview strip --}}
                <div class="mt-6">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-brand-muted">Live Preview</p>
                    <div class="flex h-10 overflow-hidden rounded-2xl border border-white/70 shadow-sm">
                        <div class="flex-1 transition-colors" :style="'background-color:' + primary"></div>
                        <div class="flex-1 transition-colors" :style="'background-color:' + secondary"></div>
                        <div class="flex-1 transition-colors" :style="'background-color:' + background"></div>
                    </div>
                    <div class="mt-1 flex text-xs text-brand-muted">
                        <span class="flex-1 text-center">Primary</span>
                        <span class="flex-1 text-center">Secondary</span>
                        <span class="flex-1 text-center">Background</span>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
