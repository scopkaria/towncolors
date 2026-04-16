<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.leads.index') }}" class="rounded-2xl border border-warm-300/50 bg-warm-100 p-2 text-brand-muted transition hover:border-accent/30 hover:text-brand-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </a>
                <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Admin · Lead detail
                </span>
            </div>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-1">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">{{ $lead->name }}</h1>
                    <p class="text-sm text-brand-muted">Submitted {{ $lead->created_at->format('M j, Y \a\t g:i A') }} · {{ $lead->created_at->diffForHumans() }}</p>
                </div>
                @php $badge = $lead->statusBadge(); @endphp
                <span class="inline-flex self-start rounded-full border px-3 py-1 text-xs font-semibold {{ $badge['class'] }}">
                    {{ $badge['label'] }}
                </span>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">

        {{-- Lead info cards --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                <p class="text-sm font-medium text-brand-muted">Email</p>
                <p class="mt-3 font-display text-lg text-brand-ink">
                    <a href="mailto:{{ $lead->email }}" class="hover:text-brand-primary">{{ $lead->email }}</a>
                </p>
            </div>
            <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                <p class="text-sm font-medium text-brand-muted">Project Type</p>
                <p class="mt-3 font-display text-lg text-brand-ink">
                    {{ $lead->project_type ? (\App\Models\Lead::projectTypes()[$lead->project_type] ?? $lead->project_type) : 'Not specified' }}
                </p>
            </div>
            <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                <p class="text-sm font-medium text-brand-muted">Converted Client</p>
                <p class="mt-3 font-display text-lg text-brand-ink">
                    {{ $lead->convertedUser?->name ?? '—' }}
                </p>
                @if ($lead->convertedUser)
                    <p class="mt-1 text-xs text-brand-muted">{{ $lead->convertedUser->email }}</p>
                @endif
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1fr_380px]">

            {{-- Left: message + notes --}}
            <div class="space-y-6">

                {{-- Message --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Inquiry Message</p>
                    <div class="mt-4 rounded-2xl border border-warm-300/40 bg-warm-200/50 px-5 py-4 text-sm leading-7 text-brand-muted whitespace-pre-line">{{ $lead->message }}</div>
                </div>

                {{-- Admin Notes (if any) --}}
                @if ($lead->admin_notes)
                    <div class="rounded-3xl border border-sky-100 bg-sky-50 p-6 shadow-panel">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-600">Admin Notes</p>
                        <div class="mt-3 text-sm leading-7 text-sky-800 whitespace-pre-line">{{ $lead->admin_notes }}</div>
                    </div>
                @endif
            </div>

            {{-- Right: actions panel --}}
            <div class="space-y-6">

                {{-- Update Status & Notes --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Update Status</p>
                    <form method="POST" action="{{ route('admin.leads.status', $lead) }}" class="mt-5 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="status" class="block text-sm font-semibold text-brand-ink">Status</label>
                            <select name="status" id="status"
                                    class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                                <option value="new"       {{ $lead->status === 'new'       ? 'selected' : '' }}>New</option>
                                <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                <option value="converted" {{ $lead->status === 'converted' ? 'selected' : '' }}>Converted</option>
                            </select>
                        </div>

                        <div>
                            <label for="admin_notes" class="block text-sm font-semibold text-brand-ink">Admin notes (internal)</label>
                            <textarea name="admin_notes" id="admin_notes" rows="4"
                                      placeholder="Add internal notes visible only to admins…"
                                      class="mt-2 w-full resize-none rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm leading-6 text-brand-ink shadow-sm transition placeholder:text-stone-400 focus:border-brand-primary focus:ring-brand-primary">{{ old('admin_notes', $lead->admin_notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn-primary w-full justify-center">Save Changes</button>
                    </form>
                </div>

                {{-- Convert to Client --}}
                @if ($lead->status !== 'converted')
                    <div class="rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-teal-50 p-6 shadow-panel" x-data="{ open: false }">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Convert to Client</p>
                        <p class="mt-2 text-xs leading-5 text-emerald-800/80">
                            Create a client account for this lead. They can log in and submit projects immediately.
                        </p>

                        <button @click="open = !open" type="button"
                                class="mt-4 w-full justify-center rounded-2xl border border-emerald-200 bg-warm-100 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50">
                            <span x-show="!open">Set Password & Convert</span>
                            <span x-show="open" x-cloak>Cancel</span>
                        </button>

                        <form x-show="open" x-cloak method="POST" action="{{ route('admin.leads.convert', $lead) }}"
                              x-transition:enter="transition duration-200 ease-out"
                              x-transition:enter-start="opacity-0 translate-y-2"
                              x-transition:enter-end="opacity-100 translate-y-0"
                              class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label for="password" class="block text-sm font-semibold text-emerald-800">
                                    Client password <span class="text-red-400">*</span>
                                </label>
                                <input id="password" name="password" type="password" required minlength="8"
                                       placeholder="min. 8 characters"
                                       class="mt-2 w-full rounded-2xl border-emerald-200 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-emerald-400 focus:ring-emerald-300 placeholder:text-stone-400">
                                @error('password')
                                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="text-xs text-emerald-800/70">
                                Account will be created with email <strong>{{ $lead->email }}</strong>. If that email already has an account, it will be linked.
                            </p>
                            <button type="submit"
                                    class="w-full justify-center rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2">
                                Convert Lead → Client
                            </button>
                        </form>
                    </div>
                @else
                    <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-6 text-center shadow-panel">
                        <svg class="mx-auto h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <p class="mt-3 text-sm font-semibold text-emerald-800">Converted to client</p>
                        @if ($lead->convertedUser)
                            <p class="mt-1 text-xs text-emerald-700">{{ $lead->convertedUser->name }} · {{ $lead->convertedUser->email }}</p>
                        @endif
                    </div>
                @endif

                {{-- Quick reply mailto --}}
                <a href="mailto:{{ $lead->email }}?subject=Re: Your inquiry about {{ $lead->project_type ? (\App\Models\Lead::projectTypes()[$lead->project_type] ?? $lead->project_type) : 'your project' }}"
                   class="flex w-full items-center justify-center gap-2 rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-3 text-sm font-semibold text-brand-ink shadow-sm transition hover:border-accent/30 hover:text-brand-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    Reply via Email
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
