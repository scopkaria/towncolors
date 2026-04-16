<x-public-layout title="Our Digital Products">

    @push('head')
    <meta name="description" content="Town Colors digital products: business, hospital, educational, e-commerce, and custom mobile software solutions.">
    <link rel="canonical" href="{{ route('products.index') }}">
    @endpush

    <section class="relative overflow-hidden border-b border-warm-300/40 bg-warm-100 py-16 sm:py-24 dark:border-slate-700/40 dark:bg-navy-900">
        <div class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-brand-primary/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 right-0 h-72 w-72 rounded-full bg-accent/10 blur-3xl"></div>

        <div class="relative mx-auto max-w-5xl px-4 text-center sm:px-8">
            <span class="reveal inline-flex rounded-full border border-accent/30 bg-accent-light px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.25em] text-brand-primary">
                Product Suite
            </span>
            <h1 class="reveal reveal-delay-1 mt-5 font-display text-3xl font-bold leading-tight text-brand-ink sm:text-5xl">
                Our Digital Products
            </h1>
            <p class="reveal reveal-delay-2 mx-auto mt-5 max-w-3xl text-base leading-8 text-brand-muted sm:text-lg">
                We offer ready-made and custom-built software solutions designed to simplify operations,
                increase efficiency, and support business growth.
            </p>
        </div>
    </section>

    @php
        $products = [
            [
                'title' => 'Business Management Systems',
                'text' => 'Comprehensive systems for sales, inventory, finance, workflows, and reporting to improve operational control.',
            ],
            [
                'title' => 'Hospital Management Systems',
                'text' => 'Digital workflows for patient records, appointments, billing, and clinical operations with role-based access.',
            ],
            [
                'title' => 'Educational Systems',
                'text' => 'Tools for student management, attendance, communication, academic workflows, and institutional analytics.',
            ],
            [
                'title' => 'E-commerce Applications',
                'text' => 'WooCommerce-integrated solutions for product management, order processing, payments, and customer lifecycle growth.',
            ],
            [
                'title' => 'Custom Mobile Applications',
                'text' => 'APK-based mobile solutions tailored to your business process, service model, and market objectives.',
            ],
        ];
    @endphp

    <section class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($products as $index => $product)
                    <article class="reveal reveal-delay-{{ ($index % 3) + 1 }} rounded-3xl border border-warm-300/50 bg-warm-100 p-6 shadow-sm sm:p-7 dark:border-slate-700/50 dark:bg-navy-800">
                        <h2 class="font-display text-2xl text-brand-ink">{{ $product['title'] }}</h2>
                        <p class="mt-4 text-sm leading-8 text-brand-muted sm:text-base">{{ $product['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-t border-warm-300/40 bg-warm-200/50 py-16 dark:border-slate-700/40 dark:bg-navy-900/60">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-8">
            <h2 class="reveal font-display text-3xl font-bold text-brand-ink sm:text-4xl">Looking for a custom product build?</h2>
            <p class="reveal reveal-delay-1 mt-4 text-base leading-8 text-brand-muted">
                We can customize features, workflows, and integrations to match your exact business model and growth plan.
            </p>
            <div class="reveal reveal-delay-2 mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('contact.show') }}" class="btn-primary">Discuss Your Product</a>
                <a href="{{ route('cloud.index') }}" class="btn-secondary">Cloud & Hosting</a>
            </div>
        </div>
    </section>

</x-public-layout>
