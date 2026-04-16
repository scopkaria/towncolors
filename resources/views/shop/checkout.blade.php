<x-public-layout :title="'Checkout · ' . $product->title">

<section class="relative overflow-hidden border-b border-warm-300/40 bg-warm-100/80 py-14 backdrop-blur-sm sm:py-20">
    <div class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-emerald-100/70 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-20 right-0 h-72 w-72 rounded-full bg-accent/15 blur-3xl"></div>

    <div class="relative mx-auto max-w-6xl px-4 sm:px-8">
        <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.3em] text-emerald-700">Software Checkout</span>
        <h1 class="mt-4 font-display text-3xl text-brand-ink sm:text-5xl">{{ $product->title }}</h1>
        <p class="mt-3 max-w-3xl text-sm leading-7 text-brand-muted sm:text-base">Choose a payment method and submit your request. Our team validates payment and starts your onboarding quickly.</p>
    </div>
</section>

<section class="py-12 sm:py-16">
    <div class="mx-auto grid max-w-6xl gap-6 px-4 sm:px-8 lg:grid-cols-[1.05fr_0.95fr]">
        <div class="rounded-3xl border border-warm-300/50 bg-warm-100/75 p-6 shadow-sm backdrop-blur-sm sm:p-8">
            @if (session('success'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @auth
                <form method="POST" action="{{ route('shop.checkout.store', $product) }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="payment_method" class="block text-sm font-semibold text-brand-ink">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                            @if (!empty($enabledMethods))
                                @foreach ($enabledMethods as $key => $label)
                                    <option value="{{ $key }}" {{ old('payment_method') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            @else
                                <option value="manual_request" selected>Manual request</option>
                            @endif
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-brand-ink">Phone</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div>
                            <label for="company" class="block text-sm font-semibold text-brand-ink">Company</label>
                            <input type="text" id="company" name="company" value="{{ old('company') }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                    </div>

                    <div>
                        <label for="payment_reference" class="block text-sm font-semibold text-brand-ink">Payment Reference</label>
                        <input type="text" id="payment_reference" name="payment_reference" value="{{ old('payment_reference') }}" placeholder="Transaction ID, reference code, receipt number" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-semibold text-brand-ink">Project Message</label>
                        <textarea id="message" name="message" rows="4" placeholder="Share deployment needs, customization scope, and timeline." class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center">Submit Purchase Request</button>
                </form>
            @else
                <div class="rounded-2xl border border-accent/30 bg-accent-light p-5 text-sm text-brand-ink">
                    <p class="font-semibold">Create an account or login to continue.</p>
                    <p class="mt-2 text-brand-muted">For secure handling and follow-up, software requests are attached to your account.</p>
                </div>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('login.client') }}" class="btn-secondary flex-1 justify-center">Login</a>
                    <a href="{{ route('register.client') }}" class="btn-primary flex-1 justify-center">Create account</a>
                </div>
            @endauth
        </div>

        <aside class="rounded-3xl border border-warm-300/50 bg-warm-100/75 p-6 shadow-sm backdrop-blur-sm sm:p-8">
            <h2 class="font-display text-2xl text-brand-ink">Order Summary</h2>
            <p class="mt-3 text-sm leading-7 text-brand-muted">{{ $product->description }}</p>

            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex items-center justify-between border-b border-warm-300/40 pb-3">
                    <dt class="text-brand-muted">Price</dt>
                    <dd class="font-semibold text-brand-ink">{{ $product->currency ?? 'USD' }} {{ number_format((float) ($product->price ?? 0), 2) }}</dd>
                </div>
                @if ($product->industry)
                    <div class="flex items-center justify-between border-b border-warm-300/40 pb-3">
                        <dt class="text-brand-muted">Industry</dt>
                        <dd class="font-semibold text-brand-ink">{{ $product->industry }}</dd>
                    </div>
                @endif
                @if ($product->completion_year)
                    <div class="flex items-center justify-between border-b border-warm-300/40 pb-3">
                        <dt class="text-brand-muted">Release Year</dt>
                        <dd class="font-semibold text-brand-ink">{{ $product->completion_year }}</dd>
                    </div>
                @endif
            </dl>

            @if (!empty($enabledMethods))
                <h3 class="mt-6 text-xs font-semibold uppercase tracking-[0.22em] text-brand-muted">Enabled Methods</h3>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($enabledMethods as $label)
                        <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-emerald-700">{{ $label }}</span>
                    @endforeach
                </div>
            @else
                <p class="mt-6 rounded-2xl border border-accent/30 bg-accent-light px-4 py-3 text-xs font-semibold uppercase tracking-[0.12em] text-brand-primary">No online methods enabled. Manual admin follow-up mode is active.</p>
            @endif

            @if ($settings->payment_notes)
                <div class="mt-6 rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-brand-muted">Payment Notes</p>
                    <p class="mt-2 text-sm leading-7 text-brand-muted">{{ $settings->payment_notes }}</p>
                </div>
            @endif
        </aside>
    </div>
</section>

</x-public-layout>
