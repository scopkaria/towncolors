<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                Admin
            </span>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Portfolio Review</h1>
                    <p class="max-w-2xl text-sm leading-7 text-brand-muted">
                        Approve or reject portfolio items submitted by freelancers. Approved items appear on the public portfolio page.
                    </p>
                </div>
                <a href="{{ route('portfolio.public') }}" target="_blank"
                   class="btn-secondary inline-flex shrink-0 items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                    </svg>
                    View Public Page
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Flash --}}
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @php
        $pending  = $items->get('pending',  collect());
        $approved = $items->get('approved', collect());
        $rejected = $items->get('rejected', collect());
    @endphp

    {{-- Stats row --}}
    @php
        $stats = [
            ['label' => 'Pending',  'count' => $pending->count(),  'color' => 'amber'],
            ['label' => 'Approved', 'count' => $approved->count(), 'color' => 'emerald'],
            ['label' => 'Rejected', 'count' => $rejected->count(), 'color' => 'red'],
        ];
    @endphp
    <div class="grid grid-cols-3 gap-4">
        @foreach ($stats as $stat)
            <div class="rounded-2xl border border-{{ $stat['color'] }}-100 bg-{{ $stat['color'] }}-50 px-4 py-4 text-center">
                <p class="font-display text-3xl font-bold text-{{ $stat['color'] }}-600">{{ $stat['count'] }}</p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-{{ $stat['color'] }}-500">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ─── Pending (needs action) ─── --}}
    <div>
        <h2 class="flex items-center gap-2 font-display text-lg text-brand-ink">
            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
            Pending Review
            @if($pending->count()) <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-700">{{ $pending->count() }}</span> @endif
        </h2>

        @if ($pending->isEmpty())
            <div class="mt-4 rounded-3xl border border-dashed border-warm-300/50 bg-white/60 py-12 text-center">
                <p class="text-sm font-semibold text-brand-ink">All caught up!</p>
                <p class="mt-1 text-xs text-brand-muted">No items are waiting for review.</p>
            </div>
        @else
            <div class="mt-4 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($pending as $item)
                    <div class="flex flex-col overflow-hidden rounded-3xl border border-amber-100 bg-warm-100 shadow-card">
                        <div class="relative h-52 overflow-hidden bg-gradient-to-br from-amber-50 to-accent-light">
                            @if ($item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}"
                                     alt="{{ $item->title }}"
                                     class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center">
                                    <svg class="h-14 w-14 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                                    </svg>
                                </div>
                            @endif
                            <span class="absolute left-3 top-3 rounded-full bg-amber-400/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur-sm">Pending</span>
                        </div>

                        <div class="flex flex-1 flex-col p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-primary">
                                {{ $item->freelancer->name ?? 'Unknown freelancer' }}
                            </p>
                            <h3 class="mt-1 font-display text-lg text-brand-ink line-clamp-2">{{ $item->title }}</h3>
                            @if ($item->client_name || $item->industry)
                                <p class="mt-1 text-[11px] font-semibold uppercase tracking-[0.12em] text-brand-muted">
                                    {{ $item->client_name ?? 'No client name' }}
                                    @if ($item->industry)
                                        · {{ $item->industry }}
                                    @endif
                                </p>
                            @endif
                            @if ($item->description)
                                <p class="mt-2 flex-1 text-sm leading-relaxed text-brand-muted line-clamp-3">{{ $item->description }}</p>
                            @endif
                            @if ($item->project_url)
                                <a href="{{ $item->project_url }}" target="_blank" rel="noopener"
                                   class="mt-2 inline-flex w-fit items-center gap-1 text-xs font-semibold text-brand-primary transition hover:underline">
                                    Open website
                                </a>
                            @endif
                            @if ($item->results)
                                <p class="mt-2 text-xs leading-6 text-brand-muted line-clamp-2">
                                    <span class="font-semibold text-brand-ink">Results:</span>
                                    {{ $item->results }}
                                </p>
                            @endif
                            <p class="mt-3 text-[11px] text-brand-muted">Submitted {{ $item->created_at->format('M d, Y') }}</p>

                            <div class="mt-4 flex gap-3">
                                <a href="{{ route('admin.portfolio.edit', $item) }}" class="inline-flex items-center justify-center rounded-2xl border border-warm-300/50 bg-warm-100 px-3 py-2.5 text-sm font-semibold text-brand-ink transition hover:border-brand-primary hover:text-brand-primary">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.portfolio.approve', $item) }}" class="flex-1">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-full rounded-2xl bg-emerald-500 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-600">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.portfolio.reject', $item) }}" class="flex-1">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-full rounded-2xl border border-red-200 bg-red-50 px-3 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ─── Approved ─── --}}
    @if ($approved->count())
    <div>
        <h2 class="flex items-center gap-2 font-display text-lg text-brand-ink">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            Approved
            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">{{ $approved->count() }}</span>
        </h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($approved as $item)
                <div class="group overflow-hidden rounded-2xl border border-emerald-100 bg-warm-100 shadow-card">
                    <div class="relative h-36 overflow-hidden bg-emerald-50">
                        @if ($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}"
                                 alt="{{ $item->title }}"
                                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        @else
                            <div class="flex h-full items-center justify-center">
                                <svg class="h-10 w-10 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-3">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-emerald-600">{{ $item->freelancer->name ?? 'Unknown freelancer' }}</p>
                        <p class="mt-0.5 text-sm font-semibold text-brand-ink line-clamp-1">{{ $item->title }}</p>
                        <div class="mt-2 flex items-center gap-3">
                        <a href="{{ route('admin.portfolio.edit', $item) }}" class="text-[11px] text-brand-primary transition hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.portfolio.reject', $item) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-[11px] text-red-400 transition hover:text-red-600 hover:underline">Revoke approval</button>
                        </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── Rejected ─── --}}
    @if ($rejected->count())
    <div>
        <h2 class="flex items-center gap-2 font-display text-lg text-brand-ink">
            <span class="h-2 w-2 rounded-full bg-red-400"></span>
            Rejected
            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-600">{{ $rejected->count() }}</span>
        </h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($rejected as $item)
                <div class="overflow-hidden rounded-2xl border border-red-100 bg-warm-100 opacity-70 shadow-card">
                    <div class="relative h-36 overflow-hidden bg-red-50">
                        @if ($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}"
                                 alt="{{ $item->title }}"
                                 class="h-full w-full object-cover grayscale">
                        @else
                            <div class="flex h-full items-center justify-center">
                                <svg class="h-10 w-10 text-red-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-3">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-red-400">{{ $item->freelancer->name ?? 'Unknown freelancer' }}</p>
                        <p class="mt-0.5 text-sm font-semibold text-brand-ink line-clamp-1">{{ $item->title }}</p>
                        <div class="mt-2 flex items-center gap-3">
                        <a href="{{ route('admin.portfolio.edit', $item) }}" class="text-[11px] text-brand-primary transition hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.portfolio.approve', $item) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-[11px] text-emerald-500 transition hover:text-emerald-700 hover:underline">Re-approve</button>
                        </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</x-app-layout>
