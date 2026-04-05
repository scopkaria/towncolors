<x-public-layout title="Contact Us">

    {{-- ===================== HERO ===================== --}}
    <section class="relative overflow-hidden bg-white py-16 sm:py-24 border-b border-stone-100">
        {{-- Decorative blobs --}}
        <div class="pointer-events-none absolute -top-24 -right-24 h-80 w-80 rounded-full bg-brand-primary/8 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-orange-50 blur-3xl"></div>

        <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <span class="mb-4 inline-block rounded-full border border-orange-200 bg-orange-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-primary reveal">
                Get In Touch
            </span>
            <h1 class="font-display text-[1.75rem] font-bold leading-tight text-brand-ink sm:text-4xl lg:text-5xl reveal reveal-delay-1">
                Let's Start a <span class="text-brand-primary">Conversation</span>
            </h1>
            <p class="mt-4 text-base text-brand-muted sm:text-lg reveal reveal-delay-2">
                Have a project in mind, a question, or just want to say hello? Fill out the form and our team will get back to you promptly.
            </p>
        </div>
    </section>

    {{-- ===================== MAIN CONTENT ===================== --}}
    <section class="bg-brand-surface py-16 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            {{-- Success banner --}}
            @if(session('success'))
            <div class="mb-10 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-5 reveal">
                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            @endif

            <div class="grid gap-12 lg:grid-cols-5 lg:gap-16">

                {{-- ===== CONTACT FORM (left / wider) ===== --}}
                <div class="lg:col-span-3 reveal">
                    <div class="card-premium rounded-2xl bg-white p-8 shadow-lg dark:bg-slate-800/90 dark:shadow-slate-900/50 sm:p-10">
                        <h2 class="font-display text-xl font-bold text-brand-ink sm:text-2xl">Send Us a Message</h2>
                        <p class="mt-1 text-sm text-brand-muted">All fields are required.</p>

                        <form action="{{ route('contact.store') }}" method="POST" class="mt-8 space-y-6" novalidate>
                            @csrf

                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-semibold text-brand-ink">
                                    Your Name
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    autocomplete="name"
                                    placeholder="Jane Smith"
                                    class="mt-1.5 block w-full rounded-xl border px-4 py-3 text-sm text-brand-ink placeholder-brand-muted/60 shadow-sm transition
                                           focus:outline-none focus:ring-2 focus:ring-brand-primary/40
                                           @error('name') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror"
                                >
                                @error('name')
                                <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-semibold text-brand-ink">
                                    Email Address
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    placeholder="jane@example.com"
                                    class="mt-1.5 block w-full rounded-xl border px-4 py-3 text-sm text-brand-ink placeholder-brand-muted/60 shadow-sm transition
                                           focus:outline-none focus:ring-2 focus:ring-brand-primary/40
                                           @error('email') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror"
                                >
                                @error('email')
                                <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Message --}}
                            <div>
                                <label for="message" class="block text-sm font-semibold text-brand-ink">
                                    Message
                                </label>
                                <textarea
                                    id="message"
                                    name="message"
                                    rows="6"
                                    placeholder="Tell us about your project or question…"
                                    class="mt-1.5 block w-full resize-none rounded-xl border px-4 py-3 text-sm text-brand-ink placeholder-brand-muted/60 shadow-sm transition
                                           focus:outline-none focus:ring-2 focus:ring-brand-primary/40
                                           @error('message') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                                @enderror
                                <p class="mt-1 text-right text-xs text-brand-muted">Min 10 characters</p>
                            </div>

                            <button type="submit" class="btn-primary w-full justify-center">
                                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ===== CONTACT INFO (right / narrower) ===== --}}
                <div class="lg:col-span-2 reveal reveal-delay-1">
                    <div class="space-y-6">

                        <div>
                            <h2 class="font-display text-xl font-bold text-brand-ink sm:text-2xl">Contact Information</h2>
                            <p class="mt-1 text-sm text-brand-muted">Prefer direct contact? Reach us through any of the channels below.</p>
                        </div>

                        {{-- Info cards --}}
                        <div class="space-y-4">

                            @if($settings->phone)
                            <a href="tel:{{ $settings->phone }}"
                               class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-brand-primary/40 hover:shadow-md dark:border-slate-600/50 dark:bg-slate-800/80">
                                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-brand-primary/10 text-brand-primary transition group-hover:bg-brand-primary group-hover:text-white">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-widest text-brand-muted">Phone</p>
                                    <p class="mt-0.5 text-sm font-medium text-brand-ink">{{ $settings->phone }}</p>
                                </div>
                            </a>
                            @endif

                            @if($settings->email)
                            <a href="mailto:{{ $settings->email }}"
                               class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-brand-primary/40 hover:shadow-md dark:border-slate-600/50 dark:bg-slate-800/80">
                                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-brand-primary/10 text-brand-primary transition group-hover:bg-brand-primary group-hover:text-white">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-widest text-brand-muted">Email</p>
                                    <p class="mt-0.5 text-sm font-medium text-brand-ink break-all">{{ $settings->email }}</p>
                                </div>
                            </a>
                            @endif

                            @if($settings->address)
                            <div class="flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-slate-600/50 dark:bg-slate-800/80">
                                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-brand-primary/10 text-brand-primary">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-widest text-brand-muted">Address</p>
                                    <p class="mt-0.5 text-sm font-medium text-brand-ink leading-relaxed">{{ $settings->address }}</p>
                                </div>
                            </div>
                            @endif

                        </div>

                        {{-- Response time note --}}
                        <div class="rounded-2xl border border-brand-primary/20 bg-brand-primary/5 p-5">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs font-bold uppercase tracking-widest text-brand-primary">Response Time</p>
                            </div>
                            <p class="mt-2 text-sm text-brand-muted leading-relaxed">
                                We typically respond to all messages within <strong class="text-brand-ink">24 business hours</strong>. Urgent inquiries can be sent directly to our email.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===================== CTA STRIP ===================== --}}
    <section class="bg-stone-50 border-t border-stone-100 py-14 sm:py-20">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="font-display text-2xl font-bold text-brand-ink sm:text-3xl reveal">
                Ready to build something amazing?
            </h2>
            <p class="mt-3 text-sm text-brand-muted sm:text-base reveal reveal-delay-1">
                Explore our work and see how we can bring your next project to life.
            </p>
            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row reveal reveal-delay-2">
                <a href="{{ route('portfolio.public') }}" class="btn-primary">View Portfolio</a>
                <a href="{{ route('services.index') }}" class="btn-secondary">Explore Services</a>
            </div>
        </div>
    </section>

</x-public-layout>
