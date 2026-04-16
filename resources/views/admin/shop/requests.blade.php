<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Commerce</span>
            <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Software Requests</h1>
            <p class="max-w-2xl text-sm leading-7 text-brand-muted">Track and process incoming software purchase and manual request submissions.</p>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="space-y-4">
        @forelse ($requests as $request)
            <div class="rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                <div class="grid gap-5 lg:grid-cols-[1.15fr_0.85fr]">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-display text-xl text-brand-ink">{{ $request->product->title ?? 'Deleted Product' }}</h2>
                            <span class="rounded-full bg-warm-200 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-brand-muted">{{ $request->payment_method }}</span>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] {{ $request->status === 'pending' ? 'bg-accent-light text-accent-hover' : 'bg-emerald-100 text-emerald-700' }}">{{ $request->status }}</span>
                        </div>

                        <p class="mt-2 text-sm text-brand-muted">{{ $request->name }} · {{ $request->email }} @if($request->phone)· {{ $request->phone }}@endif</p>
                        @if ($request->company)
                            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-brand-muted">Company: {{ $request->company }}</p>
                        @endif
                        @if ($request->payment_reference)
                            <p class="mt-2 text-sm text-brand-muted"><span class="font-semibold text-brand-ink">Reference:</span> {{ $request->payment_reference }}</p>
                        @endif
                        @if ($request->message)
                            <p class="mt-2 text-sm leading-7 text-brand-muted">{{ $request->message }}</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.shop.requests.update', $request) }}" class="space-y-3 rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-brand-muted">Status</label>
                            <select name="status" class="mt-1 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                                @foreach (['pending', 'contacted', 'approved', 'rejected', 'completed'] as $status)
                                    <option value="{{ $status }}" {{ $request->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-brand-muted">Admin Note</label>
                            <textarea name="admin_note" rows="3" class="mt-1 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm text-brand-muted shadow-sm focus:border-brand-primary focus:ring-brand-primary">{{ $request->admin_note }}</textarea>
                        </div>
                        <button type="submit" class="btn-primary w-full justify-center py-2 text-xs">Save</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-dashed border-warm-300/50 bg-white/80 p-12 text-center">
                <h3 class="font-display text-xl text-brand-ink">No software requests yet</h3>
                <p class="mt-2 text-sm text-brand-muted">New checkout and manual requests will appear here.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $requests->links() }}</div>
</x-app-layout>
