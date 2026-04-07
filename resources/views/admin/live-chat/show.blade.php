<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary dark:border-orange-500/30 dark:bg-orange-500/10">
                Admin · Live Chat
            </span>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink dark:text-white sm:text-4xl">
                        Chat with {{ $session->visitor_name ?? 'Visitor' }}
                    </h1>
                    <p class="text-sm text-brand-muted dark:text-[#A1A1AA]">
                        {{ $session->visitor_email ?? 'No email provided' }}
                        &middot;
                        <span class="capitalize">{{ $session->status }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.liveChat.index') }}"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-brand-ink transition hover:border-orange-300 hover:text-orange-600
                              dark:border-white/[0.10] dark:bg-white/[0.04] dark:text-white dark:hover:border-orange-500 dark:hover:text-orange-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div x-data="adminLiveChat({{ $session->id }}, '{{ $session->status }}')" class="space-y-4">

        {{-- Action bar --}}
        <div class="flex items-center gap-3">
            <template x-if="status !== 'closed' && !joined">
                <button @click="joinSession()"
                        class="btn-primary">
                    Join Conversation
                </button>
            </template>
            <template x-if="status !== 'closed' && joined">
                <button @click="closeSession()"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100
                               dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20">
                    Close Chat
                </button>
            </template>
            <span x-show="status === 'closed'" class="inline-flex rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-500 dark:bg-white/[0.06] dark:text-[#A1A1AA]">Closed</span>
        </div>

        {{-- Messages --}}
        <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel dark:border-white/[0.08] dark:bg-[#141416]">
            <div x-ref="chatBox" class="space-y-3 overflow-y-auto px-6 py-6" style="max-height: 500px; min-height: 300px;">
                <template x-for="msg in messages" :key="msg.id">
                    <div :class="msg.sender_type === 'agent' ? 'flex justify-end' : 'flex justify-start'">
                        <div class="max-w-[75%]">
                            <div :class="msg.sender_type === 'agent'
                                    ? 'rounded-2xl rounded-br-md bg-orange-500 px-4 py-2.5 text-sm text-white'
                                    : 'rounded-2xl rounded-bl-md bg-stone-100 px-4 py-2.5 text-sm text-brand-ink dark:bg-white/[0.06] dark:text-white'"
                                 x-text="msg.body"></div>
                            <p class="mt-1 text-[10px] text-brand-muted dark:text-[#71717A]"
                               x-text="msg.sender_type === 'agent' ? (msg.agent?.name || 'Agent') : '{{ $session->visitor_name ?? "Visitor" }}'">
                            </p>
                        </div>
                    </div>
                </template>

                <template x-if="messages.length === 0">
                    <p class="py-12 text-center text-sm text-brand-muted dark:text-[#A1A1AA]">No messages yet.</p>
                </template>
            </div>

            {{-- Input --}}
            <div class="border-t border-stone-100 px-6 py-4 dark:border-white/[0.06]" x-show="status !== 'closed'">
                <form @submit.prevent="sendMessage()" class="flex items-center gap-3">
                    <input x-model="newMessage" type="text" placeholder="Type your reply…"
                           class="flex-1 rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-brand-ink outline-none transition
                                  focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20
                                  dark:border-white/[0.10] dark:bg-[#1a1a1e] dark:text-white dark:focus:border-orange-500 dark:focus:ring-orange-500/20" />
                    <button type="submit" :disabled="!newMessage.trim()"
                            class="btn-primary disabled:opacity-40">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function adminLiveChat(sessionId, initialStatus) {
        return {
            sessionId,
            status: initialStatus,
            joined: initialStatus === 'active',
            messages: @json($messages),
            newMessage: '',
            lastMsgId: 0,
            pollTimer: null,

            init() {
                if (this.messages.length) {
                    this.lastMsgId = this.messages[this.messages.length - 1].id;
                }
                this.$nextTick(() => this.scrollToBottom());
                this.startPolling();
            },

            async joinSession() {
                try {
                    const res = await fetch(`/admin/live-chat/${this.sessionId}/join`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();
                    this.status = data.status;
                    this.joined = true;
                } catch (e) { console.error(e); }
            },

            async sendMessage() {
                const body = this.newMessage.trim();
                if (!body) return;
                this.newMessage = '';

                try {
                    const res = await fetch(`/admin/live-chat/${this.sessionId}/send`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ body }),
                    });
                    if (res.ok) this.fetchMessages();
                } catch (e) { console.error(e); }
            },

            async fetchMessages() {
                try {
                    const url = `/admin/live-chat/${this.sessionId}/messages?after=${this.lastMsgId}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    this.status = data.status;
                    if (data.messages.length) {
                        const ids = new Set(this.messages.map(m => m.id));
                        data.messages.forEach(m => { if (!ids.has(m.id)) this.messages.push(m); });
                        this.lastMsgId = data.messages[data.messages.length - 1].id;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                    if (data.status === 'closed') { this.stopPolling(); }
                } catch (e) { console.error(e); }
            },

            async closeSession() {
                if (!confirm('Close this chat session?')) return;
                try {
                    const res = await fetch(`/admin/live-chat/${this.sessionId}/close`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();
                    this.status = data.status;
                    this.stopPolling();
                } catch (e) { console.error(e); }
            },

            startPolling() {
                this.stopPolling();
                this.pollTimer = setInterval(() => this.fetchMessages(), 3000);
            },

            stopPolling() {
                if (this.pollTimer) { clearInterval(this.pollTimer); this.pollTimer = null; }
            },

            scrollToBottom() {
                const el = this.$refs.chatBox;
                if (el) el.scrollTop = el.scrollHeight;
            },

            destroy() { this.stopPolling(); }
        };
    }
    </script>
    @endpush
</x-app-layout>
