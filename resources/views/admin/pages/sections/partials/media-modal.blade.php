{{-- Multi-image picker modal — used inside an Alpine x-data="mediaPicker(...)" component --}}
<template x-teleport="body">
    <div x-show="show" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="close()">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close()"></div>

        <div class="relative z-10 flex max-h-[85vh] w-full max-w-3xl flex-col rounded-3xl border border-white/20 bg-warm-100 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between border-b border-warm-300/40 px-6 py-4">
                <h3 class="font-display text-lg text-brand-ink">Select Logo from Media Library</h3>
                <button type="button" @click="close()"
                        class="flex h-8 w-8 items-center justify-center rounded-xl border border-warm-300/50 text-brand-muted hover:border-brand-primary hover:text-brand-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-5">
                <div x-show="loading" class="flex h-40 items-center justify-center">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-brand-primary border-t-transparent"></div>
                </div>
                <div x-show="!loading" class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                    <template x-for="img in images" :key="img.id">
                        <button type="button" @click="pick(img)"
                                :class="selected.find(s=>s.id===img.id) ? 'ring-2 ring-brand-primary ring-offset-2 opacity-70' : ''"
                                class="group relative overflow-hidden rounded-2xl border border-warm-300/50 bg-warm-200/50 aspect-square transition hover:border-brand-primary">
                            <img :src="img.url" :alt="img.name" class="h-full w-full object-contain p-2 transition group-hover:scale-105">
                        </button>
                    </template>
                    <p x-show="images.length === 0 && !loading" class="col-span-full text-center text-sm text-brand-muted py-12">
                        No images in media library.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
