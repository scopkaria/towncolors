<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route(auth()->user()->role->value . '.messages') }}"
               class="rounded-2xl border border-stone-200 bg-white p-2 text-brand-muted transition hover:border-orange-200 hover:text-brand-primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-primary font-display text-sm font-bold text-white">
                    {{ strtoupper(substr($project->title, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <h1 class="truncate font-display text-lg text-brand-ink">{{ $project->title }}</h1>
                    <p class="text-xs text-brand-muted">
                        {{ $project->client->name }}
                        @if($project->freelancer)
                            &middot; {{ $project->freelancer->name }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div x-data="chatApp()" x-init="init()" @keydown.escape="closeAllMenus()" class="flex flex-col" style="height: calc(100vh - 220px);">

        {{-- Messages Container --}}
        <div x-ref="chatBox"
             class="chat-messages flex-1 overflow-y-auto rounded-t-3xl border border-b-0 border-white/70 bg-gradient-to-b from-stone-50/80 to-white/90 px-4 py-4 sm:px-6 dark:border-white/[0.08] dark:from-[#0e0e10] dark:to-[#141416]"
             style="background-image: url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='30' cy='30' r='1' fill='%23e7e5e4' opacity='0.4'/%3E%3C/svg%3E&quot;);">

            {{-- Loading --}}
            <div x-show="loading" class="flex h-full items-center justify-center">
                <div class="flex items-center gap-2 text-brand-muted">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span class="text-sm">Loading messages...</span>
                </div>
            </div>

            {{-- Empty --}}
            <div x-show="!loading && messages.length === 0" x-cloak class="flex h-full flex-col items-center justify-center text-center">
                <div class="rounded-full bg-orange-50 p-5">
                    <svg class="h-10 w-10 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                    </svg>
                </div>
                <p class="mt-3 font-display text-base text-brand-ink">No messages yet</p>
                <p class="mt-1 text-xs text-brand-muted">Say hello to start the conversation</p>
            </div>

            {{-- Messages --}}
            <div class="space-y-2">
                <template x-for="(msg, idx) in messages" :key="msg.id">
                    <div class="chat-message-enter" :class="msg.sender_id == currentUserId ? 'flex justify-end' : 'flex justify-start'">
                        <div class="max-w-[80%] sm:max-w-[70%]">
                            {{-- Sender name --}}
                            <template x-if="idx === 0 || messages[idx-1].sender_id !== msg.sender_id">
                                <p class="mb-0.5 px-2 text-[10px] font-bold uppercase tracking-wider"
                                   :class="msg.sender_id == currentUserId ? 'text-right text-orange-400' : 'text-brand-muted'"
                                   x-text="msg.sender_name"></p>
                            </template>

                            {{-- Bubble --}}
                            <div class="relative overflow-hidden shadow-sm"
                                 :class="msg.sender_id == currentUserId
                                     ? 'bg-brand-primary text-white rounded-2xl rounded-tr-md'
                                     : 'bg-white text-brand-ink rounded-2xl rounded-tl-md border border-stone-100'">

                                {{-- IMAGE --}}
                                <template x-if="msg.message_type === 'image' && msg.file_path">
                                    <div>
                                        <a :href="msg.file_path" target="_blank" class="block">
                                            <img :src="msg.file_path" :alt="msg.file_name || 'Image'" class="max-h-64 w-full rounded-t-xl object-cover" loading="lazy">
                                        </a>
                                        <template x-if="msg.message && msg.message !== ''">
                                            <p class="px-3 py-2 text-sm leading-relaxed whitespace-pre-line" x-text="msg.message"></p>
                                        </template>
                                        <div class="flex items-center justify-end px-3 pb-1.5">
                                            <span class="text-[9px]" :class="msg.sender_id == currentUserId ? 'text-white/50' : 'text-brand-muted/50'" x-text="msg.created_at"></span>
                                        </div>
                                    </div>
                                </template>

                                {{-- AUDIO --}}
                                <template x-if="msg.message_type === 'audio' && msg.file_path">
                                    <div>
                                        <div class="flex items-center gap-3 px-3 py-2.5">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                                                 :class="msg.sender_id == currentUserId ? 'bg-white/20' : 'bg-orange-50'">
                                                <svg class="h-4 w-4" :class="msg.sender_id == currentUserId ? 'text-white' : 'text-brand-primary'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z"/></svg>
                                            </div>
                                            <audio :src="msg.file_path" controls preload="metadata" class="h-8 flex-1" style="max-width: 220px;"></audio>
                                        </div>
                                        <div class="flex items-center justify-end px-3 pb-1.5">
                                            <span class="text-[9px]" :class="msg.sender_id == currentUserId ? 'text-white/50' : 'text-brand-muted/50'" x-text="msg.created_at"></span>
                                        </div>
                                    </div>
                                </template>

                                {{-- LOCATION --}}
                                <template x-if="msg.message_type === 'location' && msg.latitude">
                                    <div>
                                        <a :href="'https://www.google.com/maps?q=' + msg.latitude + ',' + msg.longitude" target="_blank" rel="noopener" class="block">
                                            <div class="flex h-28 items-center justify-center bg-stone-100">
                                                <div class="text-center">
                                                    <svg class="mx-auto h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                                    <p class="mt-1 text-[10px] text-brand-muted">View on Google Maps</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 px-3 py-2.5">
                                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
                                                     :class="msg.sender_id == currentUserId ? 'bg-white/20' : 'bg-red-50'">
                                                    <svg class="h-4 w-4" :class="msg.sender_id == currentUserId ? 'text-white' : 'text-red-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-semibold" :class="msg.sender_id == currentUserId ? 'text-white' : 'text-brand-ink'">Shared Location</p>
                                                    <p class="truncate text-[10px]" :class="msg.sender_id == currentUserId ? 'text-white/70' : 'text-brand-muted'" x-text="parseFloat(msg.latitude).toFixed(5) + ', ' + parseFloat(msg.longitude).toFixed(5)"></p>
                                                </div>
                                                <svg class="h-4 w-4 shrink-0" :class="msg.sender_id == currentUserId ? 'text-white/60' : 'text-brand-muted'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                            </div>
                                        </a>
                                        <div class="flex items-center justify-end px-3 pb-1.5">
                                            <span class="text-[9px]" :class="msg.sender_id == currentUserId ? 'text-white/50' : 'text-brand-muted/50'" x-text="msg.created_at"></span>
                                        </div>
                                    </div>
                                </template>

                                {{-- DOCUMENT --}}
                                <template x-if="msg.message_type === 'document' && msg.file_path">
                                    <div class="px-3 py-2.5">
                                        <a :href="msg.file_path" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-xl border px-3 py-2.5 transition"
                                           :class="msg.sender_id == currentUserId ? 'border-white/20 hover:bg-white/10' : 'border-stone-200 hover:bg-stone-50'">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg"
                                                 :class="msg.sender_id == currentUserId ? 'bg-white/20' : 'bg-blue-50'">
                                                <svg class="h-5 w-5" :class="msg.sender_id == currentUserId ? 'text-white' : 'text-blue-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium" x-text="msg.file_name"></p>
                                                <p class="text-[10px] uppercase tracking-wider" :class="msg.sender_id == currentUserId ? 'text-white/60' : 'text-brand-muted'" x-text="(msg.file_name || '').split('.').pop()"></p>
                                            </div>
                                            <svg class="h-5 w-5 shrink-0" :class="msg.sender_id == currentUserId ? 'text-white/60' : 'text-brand-muted'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                        </a>
                                        <template x-if="msg.message && msg.message !== ''">
                                            <p class="mt-2 text-sm leading-relaxed whitespace-pre-line" x-text="msg.message"></p>
                                        </template>
                                        <div class="flex items-center justify-end px-3 pb-1">
                                            <span class="text-[9px]" :class="msg.sender_id == currentUserId ? 'text-white/50' : 'text-brand-muted/50'" x-text="msg.created_at"></span>
                                        </div>
                                    </div>
                                </template>

                                {{-- TEXT (default) --}}
                                <template x-if="msg.message_type === 'text' || (!msg.message_type && !['image','audio','document','location'].includes(msg.message_type))">
                                    <div class="px-3 py-2">
                                        <p x-show="msg.message" x-text="msg.message" class="text-sm leading-relaxed whitespace-pre-line"></p>
                                        <template x-if="msg.file_path">
                                            <a :href="msg.file_path" target="_blank" rel="noopener"
                                               class="mt-1.5 flex items-center gap-2 rounded-lg border px-2.5 py-1.5 text-xs font-medium transition"
                                               :class="msg.sender_id == currentUserId ? 'border-white/20 hover:bg-white/10' : 'border-stone-200 hover:bg-stone-50'">
                                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                                                <span x-text="msg.file_name" class="truncate"></span>
                                            </a>
                                        </template>
                                        <div class="flex items-center justify-end mt-1">
                                            <span class="text-[9px]" :class="msg.sender_id == currentUserId ? 'text-white/50' : 'text-brand-muted/50'" x-text="msg.created_at"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="relative rounded-b-3xl border border-t-0 border-white/70 bg-white px-3 py-3 shadow-panel sm:px-4 dark:border-white/[0.08] dark:bg-[#141416]">

            {{-- File / Image / Audio Preview --}}
            <div x-show="previewData" x-cloak class="mb-2.5 overflow-hidden rounded-2xl border border-stone-200 bg-stone-50">
                <template x-if="previewType === 'image' && previewUrl">
                    <img :src="previewUrl" class="max-h-36 w-full object-cover">
                </template>
                <template x-if="previewType === 'audio' && previewUrl">
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-primary/10">
                            <svg class="h-4 w-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z"/></svg>
                        </div>
                        <audio :src="previewUrl" controls class="h-8 flex-1"></audio>
                    </div>
                </template>
                <div class="flex items-center gap-3 px-4 py-2">
                    <svg class="h-4 w-4 shrink-0 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                    <span x-text="fileName" class="flex-1 truncate text-sm text-brand-ink"></span>
                    <button type="button" @click="removeFile()" class="rounded-full p-1 text-brand-muted transition hover:bg-red-50 hover:text-red-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Recording indicator --}}
            <div x-show="isRecording" x-cloak class="mb-2.5 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
                <span class="relative flex h-3 w-3">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex h-3 w-3 rounded-full bg-red-500"></span>
                </span>
                <span class="flex-1 text-sm font-medium text-red-700">Recording audio&hellip;</span>
                <span class="text-xs tabular-nums text-red-500" x-text="recordingTime"></span>
                <button @click="stopRecording()" class="rounded-full bg-red-500 p-1.5 text-white transition hover:bg-red-600" title="Stop recording">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                </button>
            </div>

            <div class="flex items-end gap-2">
                {{-- Emoji Button --}}
                <div class="relative">
                    <button type="button" @click="showEmoji = !showEmoji; showAttach = false"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-brand-muted transition hover:bg-orange-50 hover:text-brand-primary"
                            :class="showEmoji ? 'bg-orange-50 text-brand-primary' : ''">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z"/></svg>
                    </button>
                    {{-- Emoji Picker --}}
                    <div x-show="showEmoji" x-cloak @click.outside="showEmoji = false"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute bottom-12 left-0 z-50 w-72 max-h-72 overflow-y-auto rounded-2xl border border-stone-200 bg-white p-3 shadow-panel">
                        <div class="mb-2 text-[10px] font-bold uppercase tracking-wider text-brand-muted">Smileys</div>
                        <div class="grid grid-cols-8 gap-0.5">
                            <template x-for="emoji in emojis" :key="emoji">
                                <button type="button" @click="insertEmoji(emoji)"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg text-lg transition hover:bg-orange-50 hover:scale-110"
                                        x-text="emoji"></button>
                            </template>
                        </div>
                        <div class="mt-2 mb-2 text-[10px] font-bold uppercase tracking-wider text-brand-muted">Gestures</div>
                        <div class="grid grid-cols-8 gap-0.5">
                            <template x-for="emoji in gestureEmojis" :key="emoji">
                                <button type="button" @click="insertEmoji(emoji)"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg text-lg transition hover:bg-orange-50 hover:scale-110"
                                        x-text="emoji"></button>
                            </template>
                        </div>
                        <div class="mt-2 mb-2 text-[10px] font-bold uppercase tracking-wider text-brand-muted">Objects</div>
                        <div class="grid grid-cols-8 gap-0.5">
                            <template x-for="emoji in objectEmojis" :key="emoji">
                                <button type="button" @click="insertEmoji(emoji)"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg text-lg transition hover:bg-orange-50 hover:scale-110"
                                        x-text="emoji"></button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Attach Button --}}
                <div class="relative">
                    <button type="button" @click="showAttach = !showAttach; showEmoji = false"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-brand-muted transition hover:bg-orange-50 hover:text-brand-primary"
                            :class="showAttach ? 'bg-orange-50 text-brand-primary' : ''">
                        <svg class="h-5 w-5 transition-transform duration-200" :class="showAttach ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </button>
                    {{-- Attachment Menu --}}
                    <div x-show="showAttach" x-cloak @click.outside="showAttach = false"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute bottom-12 left-0 z-50 w-52 rounded-2xl border border-stone-200 bg-white p-2 shadow-panel">
                        {{-- Hidden file inputs --}}
                        <input type="file" x-ref="photoInput" accept="image/*" class="hidden" @change="handleFileSelect($event, 'image')">
                        <input type="file" x-ref="docInput" class="hidden" @change="handleFileSelect($event, 'document')">
                        <input type="file" x-ref="cameraInput" accept="image/*" capture="environment" class="hidden" @change="handleFileSelect($event, 'image')">

                        <button type="button" @click="$refs.cameraInput.click(); showAttach = false"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition hover:bg-stone-50">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-violet-100">
                                <svg class="h-4 w-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"/></svg>
                            </div>
                            <span class="font-medium text-brand-ink">Camera</span>
                        </button>
                        <button type="button" @click="$refs.photoInput.click(); showAttach = false"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition hover:bg-stone-50">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/></svg>
                            </div>
                            <span class="font-medium text-brand-ink">Photo</span>
                        </button>
                        <button type="button" @click="$refs.docInput.click(); showAttach = false"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition hover:bg-stone-50">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100">
                                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            </div>
                            <span class="font-medium text-brand-ink">Document</span>
                        </button>
                        <button type="button" @click="sendLocation(); showAttach = false"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition hover:bg-stone-50">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-red-100">
                                <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            </div>
                            <span class="font-medium text-brand-ink">Location</span>
                        </button>
                        <button type="button" @click="startRecording(); showAttach = false"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition hover:bg-stone-50"
                                :disabled="isRecording">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-100">
                                <svg class="h-4 w-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z"/></svg>
                            </div>
                            <span class="font-medium text-brand-ink">Audio</span>
                        </button>
                    </div>
                </div>

                {{-- Text Input --}}
                <div class="flex-1">
                    <textarea x-model="newMessage" x-ref="messageInput"
                              @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                              placeholder="Type a message"
                              rows="1"
                              class="w-full resize-none rounded-2xl border-0 bg-stone-100 px-4 py-2.5 text-sm text-brand-ink placeholder-brand-muted/50 transition focus:bg-white focus:ring-2 focus:ring-orange-200 dark:bg-white/[0.06] dark:text-white dark:placeholder-[#71717A] dark:focus:bg-white/[0.08] dark:focus:ring-orange-500/30"
                              style="max-height: 100px;"
                              @input="autoResize($event)"></textarea>
                </div>

                {{-- Mic / Send Toggle --}}
                <template x-if="!newMessage.trim() && !previewData && !isRecording">
                    <button type="button" @click="startRecording()"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-brand-muted transition hover:bg-orange-50 hover:text-brand-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z"/></svg>
                    </button>
                </template>
                <template x-if="newMessage.trim() || previewData">
                    <button type="button" @click="sendMessage()" :disabled="sending"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-primary text-white transition hover:bg-brand-hover disabled:opacity-50"
                            :class="sending ? 'animate-pulse' : 'hover:scale-105'">
                        <svg x-show="!sending" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                        <svg x-show="sending" x-cloak class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <script>
        function chatApp() {
            return {
                messages: [],
                newMessage: '',
                file: null,
                fileName: '',
                fileType: 'text',
                previewData: false,
                previewUrl: '',
                previewType: '',
                sending: false,
                loading: true,
                polling: null,
                showEmoji: false,
                showAttach: false,
                isRecording: false,
                mediaRecorder: null,
                audioChunks: [],
                recordingTimer: null,
                recordingSeconds: 0,
                recordingTime: '0:00',
                currentUserId: {{ auth()->id() }},
                fetchUrl: "{{ route('chat.messages', $project) }}",
                storeUrl: "{{ route('chat.store', $project) }}",

                emojis: ['😀','😂','😍','🥰','😎','😢','😡','🤔','👍','❤️','🔥','🎉','✅','🙏','💯','😊','🤣','😭','😱','🥳','😴','🤗','🤝','💪'],
                gestureEmojis: ['👋','👌','✌️','🤞','🫡','👏','🙌','🤲','💅','✍️','🫶','🤙','👆','👇','👈','👉'],
                objectEmojis: ['📎','📄','📁','💻','📱','⏰','📌','💡','🔑','📧','💰','📊','🎯','🚀','⭐','🏆'],

                init() {
                    this.fetchMessages();
                    this.polling = setInterval(() => this.pollMessages(), 4000);
                },

                closeAllMenus() { this.showEmoji = false; this.showAttach = false; },

                insertEmoji(emoji) {
                    const input = this.$refs.messageInput;
                    const start = input.selectionStart;
                    const end = input.selectionEnd;
                    this.newMessage = this.newMessage.substring(0, start) + emoji + this.newMessage.substring(end);
                    this.$nextTick(() => {
                        input.focus();
                        input.setSelectionRange(start + emoji.length, start + emoji.length);
                    });
                },

                async fetchMessages() {
                    try {
                        const res = await fetch(this.fetchUrl, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (res.ok) {
                            this.messages = await res.json();
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    } catch (e) { console.error('Fetch error:', e); }
                    finally { this.loading = false; }
                },

                async pollMessages() {
                    const lastId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : 0;
                    try {
                        const res = await fetch(this.fetchUrl + '?after=' + lastId, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (res.ok) {
                            const newMsgs = await res.json();
                            if (newMsgs.length > 0) {
                                this.messages.push(...newMsgs);
                                this.$nextTick(() => this.scrollToBottom());
                            }
                        }
                    } catch (e) { console.error('Poll error:', e); }
                },

                async sendMessage() {
                    if (this.sending) return;
                    if (!this.newMessage.trim() && !this.file) return;

                    this.sending = true;
                    this.closeAllMenus();
                    const formData = new FormData();
                    if (this.newMessage.trim()) formData.append('message', this.newMessage.trim());
                    if (this.file) {
                        formData.append('file', this.file);
                        formData.append('message_type', this.fileType);
                    }

                    try {
                        const res = await fetch(this.storeUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: formData,
                        });
                        if (res.ok) {
                            const msg = await res.json();
                            this.messages.push(msg);
                            this.newMessage = '';
                            this.removeFile();
                            this.$nextTick(() => {
                                this.scrollToBottom();
                                this.$refs.messageInput.style.height = 'auto';
                            });
                        } else if (res.status === 419) {
                            alert('Session expired. Please refresh the page.');
                        } else {
                            try { const err = await res.json(); alert(err.message || err.error || 'Failed to send.'); }
                            catch { alert('Server error (' + res.status + '). Try again.'); }
                        }
                    } catch (e) { console.error('Send error:', e); alert('Network error.'); }
                    finally { this.sending = false; }
                },

                async sendLocation() {
                    if (!navigator.geolocation) { alert('Geolocation is not supported by your browser.'); return; }
                    this.sending = true;
                    navigator.geolocation.getCurrentPosition(
                        async (pos) => {
                            const formData = new FormData();
                            formData.append('message_type', 'location');
                            formData.append('latitude', pos.coords.latitude);
                            formData.append('longitude', pos.coords.longitude);
                            formData.append('message', 'Shared location');
                            try {
                                const res = await fetch(this.storeUrl, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                    body: formData,
                                });
                                if (res.ok) {
                                    const msg = await res.json();
                                    this.messages.push(msg);
                                    this.$nextTick(() => this.scrollToBottom());
                                } else { alert('Failed to send location.'); }
                            } catch (e) { alert('Network error.'); }
                            finally { this.sending = false; }
                        },
                        (err) => { alert('Location access denied. Please enable location permissions.'); this.sending = false; },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                },

                async startRecording() {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        this.audioChunks = [];
                        this.mediaRecorder = new MediaRecorder(stream);
                        this.mediaRecorder.ondataavailable = (e) => { if (e.data.size > 0) this.audioChunks.push(e.data); };
                        this.mediaRecorder.onstop = () => {
                            stream.getTracks().forEach(t => t.stop());
                            const blob = new Blob(this.audioChunks, { type: 'audio/webm' });
                            this.file = new File([blob], 'voice-message.webm', { type: 'audio/webm' });
                            this.fileName = 'Voice message (' + this.recordingTime + ')';
                            this.fileType = 'audio';
                            this.previewData = true;
                            this.previewType = 'audio';
                            this.previewUrl = URL.createObjectURL(blob);
                            this.isRecording = false;
                            clearInterval(this.recordingTimer);
                        };
                        this.mediaRecorder.start();
                        this.isRecording = true;
                        this.recordingSeconds = 0;
                        this.recordingTime = '0:00';
                        this.recordingTimer = setInterval(() => {
                            this.recordingSeconds++;
                            const m = Math.floor(this.recordingSeconds / 60);
                            const s = this.recordingSeconds % 60;
                            this.recordingTime = m + ':' + String(s).padStart(2, '0');
                        }, 1000);
                    } catch (e) {
                        alert('Microphone access denied. Please allow microphone permissions.');
                    }
                },

                stopRecording() {
                    if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                        this.mediaRecorder.stop();
                    }
                },

                handleFileSelect(e, type) {
                    const f = e.target.files[0];
                    if (!f) return;
                    if (f.size > 20 * 1024 * 1024) { alert('File must be under 20MB.'); e.target.value = ''; return; }
                    this.file = f;
                    this.fileName = f.name;
                    this.fileType = type || 'document';
                    this.previewData = true;
                    this.previewType = type;
                    if (type === 'image') {
                        this.previewUrl = URL.createObjectURL(f);
                    } else {
                        this.previewUrl = '';
                    }
                },

                removeFile() {
                    if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                    this.file = null;
                    this.fileName = '';
                    this.fileType = 'text';
                    this.previewData = false;
                    this.previewUrl = '';
                    this.previewType = '';
                    if (this.$refs.photoInput) this.$refs.photoInput.value = '';
                    if (this.$refs.docInput) this.$refs.docInput.value = '';
                    if (this.$refs.cameraInput) this.$refs.cameraInput.value = '';
                },

                scrollToBottom() {
                    const box = this.$refs.chatBox;
                    if (box) box.scrollTo({ top: box.scrollHeight, behavior: 'smooth' });
                },

                autoResize(e) {
                    e.target.style.height = 'auto';
                    e.target.style.height = Math.min(e.target.scrollHeight, 100) + 'px';
                },

                destroy() {
                    if (this.polling) clearInterval(this.polling);
                    if (this.recordingTimer) clearInterval(this.recordingTimer);
                    if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                }
            };
        }
    </script>
</x-app-layout>
