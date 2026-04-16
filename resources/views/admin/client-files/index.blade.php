<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Business</span>
            <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Client Files</h1>
            <p class="text-sm text-brand-muted">Select a client to browse their private file workspace.</p>
        </div>
    </x-slot>

    @if ($clients->isEmpty())
        <div class="rounded-3xl border border-dashed border-warm-400/50 bg-warm-200/50 p-16 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-warm-200">
                <svg class="h-7 w-7 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <p class="mt-4 font-semibold text-brand-ink">No clients found</p>
            <p class="mt-1 text-sm text-brand-muted">Clients will appear here once they have accounts.</p>
        </div>
    @else
        {{-- Summary bar --}}
        <div class="flex items-center justify-between">
            <p class="text-sm text-brand-muted">{{ $clients->count() }} client workspace{{ $clients->count() !== 1 ? 's' : '' }}</p>
            <p class="text-xs text-brand-muted">{{ $clients->sum('files_count') }} total files · {{ $clients->sum('folders_count') }} total folders</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $avatarColors = [
                    'from-accent to-amber-500',
                    'from-blue-400 to-indigo-500',
                    'from-emerald-400 to-teal-500',
                    'from-violet-400 to-purple-500',
                    'from-rose-400 to-pink-500',
                    'from-cyan-400 to-sky-500',
                ];
            @endphp
            @foreach ($clients as $i => $client)
                @php $color = $avatarColors[$i % count($avatarColors)]; @endphp
                <a href="{{ route('admin.clients.files', $client) }}"
                   class="group relative overflow-hidden rounded-3xl border border-white/70 bg-warm-100 shadow-card transition-all duration-200 hover:-translate-y-0.5 hover:shadow-panel">

                    {{-- Top colour band --}}
                    <div class="h-1.5 w-full bg-gradient-to-r {{ $color }}"></div>

                    <div class="p-6">
                        {{-- Header row --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3.5">
                                {{-- Avatar --}}
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $color }} text-white font-bold text-lg shadow-sm">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-brand-ink leading-tight">{{ $client->name }}</p>
                                    <p class="truncate text-xs text-brand-muted mt-0.5">{{ $client->email }}</p>
                                </div>
                            </div>
                            {{-- Arrow icon --}}
                            <div class="shrink-0 flex h-8 w-8 items-center justify-center rounded-xl border border-warm-300/40 bg-warm-200/50 transition group-hover:border-brand-primary/30 group-hover:bg-brand-primary/5">
                                <svg class="h-3.5 w-3.5 text-brand-muted transition group-hover:text-brand-primary group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                        </div>

                        {{-- Stats row --}}
                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-warm-200/50 px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 12h-15a4.483 4.483 0 0 0-3 1.146Z" />
                                    </svg>
                                    <p class="text-xl font-bold text-brand-ink">{{ $client->folders_count }}</p>
                                </div>
                                <p class="mt-1 text-[11px] font-medium uppercase tracking-wider text-brand-muted">Folder{{ $client->folders_count !== 1 ? 's' : '' }}</p>
                            </div>
                            <div class="rounded-2xl bg-warm-200/50 px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    <p class="text-xl font-bold text-brand-ink">{{ $client->files_count }}</p>
                                </div>
                                <p class="mt-1 text-[11px] font-medium uppercase tracking-wider text-brand-muted">File{{ $client->files_count !== 1 ? 's' : '' }}</p>
                            </div>
                        </div>

                        {{-- CTA --}}
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-xs text-brand-muted">Member since {{ $client->created_at->format('M Y') }}</p>
                            <span class="text-xs font-semibold text-brand-primary opacity-0 transition group-hover:opacity-100">Open workspace →</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-app-layout>
