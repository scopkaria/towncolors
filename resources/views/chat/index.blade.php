<x-app-layout>
    {{-- No page header — maximise chat height --}}

    {{-- ─────────────────────────────────────────────────────────────────────
         Two-panel global chat hub
         Left  : conversation list + search + new-DM button
         Right : active conversation or empty state
    ──────────────────────────────────────────────────────────────────────── --}}
    <div x-data="globalChat()"
         x-init="init()"
         @keydown.escape.window="showNewChat = false; showEmoji = false; showAttach = false;"
         class="flex overflow-hidden rounded-3xl border border-white/70 bg-warm-100 shadow-panel dark:border-white/[0.08] dark:bg-navy-800"
         style="height: calc(100vh - 160px); min-height: 480px;">

        {{-- ══════════════════════════════════════════════
             LEFT PANEL — Conversation list
        ══════════════════════════════════════════════ --}}
        <div class="flex flex-col border-r border-warm-300/40 bg-warm-100 dark:border-white/[0.08] dark:bg-navy-800 w-full sm:w-[300px] sm:min-w-[300px]"
             :class="activeConversation && mobileView ? 'hidden' : 'flex'">

            {{-- Header — Chats / Contacts tab switcher --}}
            <div class="flex items-center justify-between border-b border-warm-300/40 px-3 py-3 dark:border-white/[0.06]">
                <div class="flex gap-1 rounded-2xl bg-warm-200 p-1 dark:bg-white/[0.06]">
                    <button @click="activeTab = 'chats'"
                            class="rounded-xl px-3 py-1.5 text-xs font-semibold transition"
                            :class="activeTab === 'chats' ? 'bg-warm-100 text-brand-primary shadow-sm dark:bg-white/[0.10] dark:text-accent' : 'text-brand-muted hover:text-brand-ink dark:hover:text-white'">Chats</button>
                    <button @click="activeTab = 'contacts'"
                            class="rounded-xl px-3 py-1.5 text-xs font-semibold transition"
                            :class="activeTab === 'contacts' ? 'bg-warm-100 text-brand-primary shadow-sm dark:bg-white/[0.10] dark:text-accent' : 'text-brand-muted hover:text-brand-ink dark:hover:text-white'">Contacts</button>
                </div>
                <button @click="showNewChat = true" title="New direct message"
                        class="flex h-8 w-8 items-center justify-center rounded-xl bg-accent-light text-brand-primary transition hover:bg-accent-light">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </button>
            </div>

            {{-- Search (shared, label changes per tab) --}}
            <div class="px-3 py-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-brand-muted/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    <input type="text"
                           :placeholder="activeTab === 'chats' ? 'Search chats…' : 'Search contacts…'"
                           :value="activeTab === 'chats' ? search : contactSearch"
                           @input="activeTab === 'chats' ? search = $event.target.value : contactSearch = $event.target.value"
                           class="w-full rounded-2xl border-0 bg-warm-200 py-2 pl-9 pr-4 text-sm text-brand-ink placeholder-brand-muted/50 focus:bg-warm-100 focus:ring-2 focus:ring-accent/30 dark:bg-white/[0.06] dark:text-white dark:placeholder-[#71717A] dark:focus:bg-white/[0.08] dark:focus:ring-accent/30">
                </div>
            </div>

            {{-- Conversation list --}}
            <div class="flex-1 overflow-y-auto">

                {{-- ── CHATS TAB ── --}}
                <div x-show="activeTab === 'chats'">

                <template x-if="filteredConversations.length === 0 && !loadingConvs">
                    <div class="flex flex-col items-center justify-center px-6 py-12 text-center">
                        <div class="rounded-2xl bg-accent-light p-4">
                            <svg class="h-7 w-7 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-brand-ink">No conversations yet</p>
                        <p class="mt-1 text-xs text-brand-muted">Start a new direct message.</p>
                    </div>
                </template>

                <template x-if="loadingConvs && conversations.length === 0">
                    <div class="flex items-center justify-center py-12">
                        <svg class="h-5 w-5 animate-spin text-brand-primary" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                </template>

                <template x-for="conv in filteredConversations" :key="conv.id">
                    <button @click="selectConversation(conv)"
                            class="flex w-full items-center gap-3 px-3 py-3 text-left transition hover:bg-warm-200/50 dark:hover:bg-white/[0.04]"
                            :class="activeConversation?.id === conv.id ? 'bg-accent-light border-r-2 border-brand-primary' : ''">
                        <template x-if="conv.avatar_url">
                            <img :src="conv.avatar_url" alt="" class="h-11 w-11 shrink-0 rounded-2xl object-cover">
                        </template>
                        <template x-if="!conv.avatar_url">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl font-display text-sm font-bold"
                                 :class="conv.avatar_color === 'orange' ? 'bg-accent-light text-brand-primary' : 'bg-blue-100 text-blue-600'">
                                <span x-text="conv.avatar_text"></span>
                            </div>
                        </template>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-baseline justify-between gap-1">
                                <span class="truncate text-sm font-semibold"
                                      :class="activeConversation?.id === conv.id ? 'text-brand-primary' : 'text-brand-ink'"
                                      x-text="conv.title"></span>
                                <span class="shrink-0 text-[10px] text-brand-muted/70" x-text="conv.last_message?.time ?? ''"></span>
                            </div>
                            <p class="mt-0.5 truncate text-xs text-brand-muted"
                               x-text="conv.last_message
                                   ? (conv.last_message.mine ? 'You: ' : conv.last_message.sender + ': ') + conv.last_message.text
                                   : 'No messages yet'"></p>
                            <p x-show="conv.subtitle" x-text="conv.subtitle"
                               class="mt-0.5 truncate text-[10px] text-brand-muted/70"></p>
                        </div>
                    </button>
                </template>

                </div>{{-- /chats tab --}}

                {{-- ── CONTACTS TAB ── --}}
                <div x-show="activeTab === 'contacts'" x-cloak>

                    <template x-if="contactGroups.length === 0">
                        <div class="flex flex-col items-center justify-center px-6 py-12 text-center">
                            <p class="text-sm text-brand-muted">No contacts available.</p>
                        </div>
                    </template>

                    <template x-for="group in contactGroups" :key="group.name">
                        <div>
                            {{-- Category label --}}
                            <div class="sticky top-0 z-10 flex items-center gap-2 bg-warm-200/95 px-3 py-1.5 backdrop-blur-sm dark:bg-navy-800/95">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-muted" x-text="group.name"></span>
                                <span class="rounded-full bg-warm-300 px-1.5 py-0.5 text-[9px] font-bold text-brand-muted tabular-nums" x-text="group.users.length"></span>
                            </div>
                            {{-- Users --}}
                            <template x-for="u in group.users" :key="u.id">
                                <div class="flex items-center gap-3 px-3 py-2.5 transition hover:bg-warm-200/50">
                                    <template x-if="u.avatar_url">
                                        <img :src="u.avatar_url" alt="" class="h-10 w-10 shrink-0 rounded-2xl object-cover">
                                    </template>
                                    <template x-if="!u.avatar_url">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl font-display text-sm font-bold"
                                             :class="u.colorClass">
                                            <span x-text="u.initials"></span>
                                        </div>
                                    </template>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-brand-ink" x-text="u.name"></p>
                                        <p class="text-xs text-brand-muted" x-text="u.role"></p>
                                    </div>
                                    <button @click="startDirectChat(u.id)" title="Send message"
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border border-warm-300/50 text-brand-muted transition hover:border-accent hover:bg-accent-light hover:text-brand-primary">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                </div>{{-- /contacts tab --}}

            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             RIGHT PANEL — Active conversation
        ══════════════════════════════════════════════ --}}
        <div class="flex min-w-0 flex-1 flex-col"
             :class="!activeConversation && mobileView ? 'hidden' : 'flex'">

            {{-- Empty / welcome state --}}
            <div x-show="!activeConversation" x-cloak
                 class="flex flex-1 flex-col items-center justify-center p-10 text-center">
                <div class="rounded-3xl bg-gradient-to-br from-accent-light to-amber-50 p-8">
                    <svg class="mx-auto h-12 w-12 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>
                    </svg>
                </div>
                <h3 class="mt-5 font-display text-xl text-brand-ink">Your conversations</h3>
                <p class="mt-2 max-w-xs text-sm text-brand-muted">Select a conversation from the list or start a new direct message.</p>
                <button @click="showNewChat = true"
                        class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-brand-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-hover">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    New direct message
                </button>
            </div>

            {{-- Active conversation --}}
            <div x-show="activeConversation" x-cloak class="flex min-h-0 flex-1 flex-col">

                {{-- Chat header --}}
                <div class="flex shrink-0 items-center gap-3 border-b border-warm-300/40 px-5 py-4 dark:border-white/[0.06]">
                    <button @click="activeConversation = null; mobileView = true"
                            class="lg:hidden flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border border-warm-300/50 text-brand-muted transition hover:border-accent/30 hover:text-brand-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
                        </svg>
                    </button>
                    <template x-if="activeConversation?.avatar_url">
                        <img :src="activeConversation?.avatar_url" alt="" class="h-10 w-10 shrink-0 rounded-2xl object-cover">
                    </template>
                    <template x-if="!activeConversation?.avatar_url">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl font-display text-sm font-bold"
                             :class="activeConversation?.avatar_color === 'orange' ? 'bg-accent-light text-brand-primary' : 'bg-blue-100 text-blue-600'">
                            <span x-text="activeConversation?.avatar_text ?? ''"></span>
                        </div>
                    </template>
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate font-display text-base font-semibold text-brand-ink" x-text="activeConversation?.title ?? ''"></h3>
                        <p x-show="activeConversation?.subtitle" x-text="activeConversation?.subtitle ?? ''" class="truncate text-xs text-brand-muted"></p>
                    </div>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider"
                          :class="activeConversation?.type === 'project' ? 'bg-accent-light text-brand-primary' : 'bg-blue-50 text-blue-600'"
                          x-text="activeConversation?.type === 'project' ? 'Project' : 'Direct'"></span>

                    {{-- Voice call --}}
                    <button @click="openCall(activeConversation.room_name, 'audio')"
                            title="Voice call"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-warm-300/50 text-brand-muted transition hover:border-green-300 hover:bg-green-50 hover:text-green-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0 6.075 4.924 11 11 11l.002-.002 3.246-1.248a11.025 11.025 0 0 0 3.257-2.05A11 11 0 0 0 21.75 6.338C21.75 3.75 19.875 1.5 17.249 1.5c-.814 0-1.63.182-2.373.534a11.22 11.22 0 0 0-2.576 1.908l-.6.6a11.222 11.222 0 0 0-1.908 2.576A6.45 6.45 0 0 0 9.25 7.5a6.5 6.5 0 0 0-.534 2.624 11.222 11.222 0 0 0 1.908 2.576l.6.6a11.22 11.22 0 0 0 2.576 1.908"/>
                        </svg>
                    </button>

                    {{-- Video call --}}
                    <button @click="openCall(activeConversation.room_name, 'video')"
                            title="Video call"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-warm-300/50 text-brand-muted transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/>
                        </svg>
                    </button>
                </div>

                {{-- Messages area --}}
                <div x-ref="chatBox"
                     class="flex-1 overflow-y-auto px-4 py-4 sm:px-6"
                     style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='30' cy='30' r='1' fill='%23e7e5e4' opacity='0.35'/%3E%3C/svg%3E&quot;);background-color:var(--chat-bg, #fafaf9);">

                    <div x-show="loadingMessages" x-cloak class="flex h-full items-center justify-center">
                        <div class="flex items-center gap-2 text-brand-muted">
                            <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span class="text-sm">Loading messages…</span>
                        </div>
                    </div>

                    <div x-show="!loadingMessages && messages.length === 0" x-cloak
                         class="flex h-full flex-col items-center justify-center text-center">
                        <div class="rounded-full bg-accent-light p-5">
                            <svg class="h-8 w-8 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                            </svg>
                        </div>
                        <p class="mt-3 font-display text-base text-brand-ink">No messages yet</p>
                        <p class="mt-1 text-xs text-brand-muted">Say hello to start the conversation!</p>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(msg, idx) in messages" :key="msg.id">
                            <div class="chat-message-enter"
                                 :class="msg.sender_id == currentUserId ? 'flex justify-end' : 'flex justify-start'">
                                <div class="max-w-[80%] sm:max-w-[70%]">

                                    <template x-if="idx === 0 || messages[idx-1].sender_id !== msg.sender_id">
                                        <p class="mb-0.5 px-2 text-[10px] font-bold uppercase tracking-wider"
                                           :class="msg.sender_id == currentUserId ? 'text-right text-accent' : 'text-brand-muted'"
                                           x-text="msg.sender_name"></p>
                                    </template>

                                    <div class="relative overflow-hidden shadow-sm"
                                         :class="msg.sender_id == currentUserId
                                             ? 'bg-brand-primary text-white rounded-2xl rounded-tr-md'
                                             : 'bg-warm-100 text-brand-ink rounded-2xl rounded-tl-md border border-warm-300/40'">

                                        {{-- IMAGE --}}
                                        <template x-if="msg.message_type === 'image' && msg.file_path">
                                            <div>
                                                <a :href="msg.file_path" target="_blank" class="block">
                                                    <img :src="msg.file_path" :alt="msg.file_name || 'Image'" class="max-h-64 w-full rounded-t-xl object-cover" loading="lazy">
                                                </a>
                                                <template x-if="msg.message"><p class="px-3 py-2 text-sm leading-relaxed whitespace-pre-line" x-text="msg.message"></p></template>
                                                <div class="flex justify-end px-3 pb-1.5"><span class="text-[9px]" :class="msg.sender_id == currentUserId ? 'text-white/50' : 'text-brand-muted/50'" x-text="msg.created_at"></span></div>
                                            </div>
                                        </template>

                                        {{-- AUDIO --}}
                                        <template x-if="msg.message_type === 'audio' && msg.file_path">
                                            <div>
                                                <div class="flex items-center gap-3 px-3 py-2.5">
                                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full" :class="msg.sender_id == currentUserId ? 'bg-white/20' : 'bg-accent-light'">
                                                        <svg class="h-4 w-4" :class="msg.sender_id == currentUserId ? 'text-white' : 'text-brand-primary'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z"/></svg>
                                                    </div>
                                                    <audio :src="msg.file_path" controls preload="metadata" class="h-8 flex-1" style="max-width:220px;"></audio>
                                                </div>
                                                <div class="flex justify-end px-3 pb-1.5"><span class="text-[9px]" :class="msg.sender_id == currentUserId ? 'text-white/50' : 'text-brand-muted/50'" x-text="msg.created_at"></span></div>
                                            </div>
                                        </template>

                                        {{-- LOCATION --}}
                                        <template x-if="msg.message_type === 'location' && msg.latitude">
                                            <div>
                                                <a :href="'https://www.google.com/maps?q=' + msg.latitude + ',' + msg.longitude" target="_blank" rel="noopener" class="block">
                                                    <div class="flex h-24 items-center justify-center bg-warm-200">
                                                        <div class="text-center">
                                                            <svg class="mx-auto h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                                            <p class="mt-1 text-[10px] text-brand-muted">View on Google Maps</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2 px-3 py-2.5">
                                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full" :class="msg.sender_id == currentUserId ? 'bg-white/20' : 'bg-red-50'">
                                                            <svg class="h-4 w-4" :class="msg.sender_id == currentUserId ? 'text-white' : 'text-red-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs font-semibold" :class="msg.sender_id == currentUserId ? 'text-white' : 'text-brand-ink'">Shared Location</p>
                                                            <p class="truncate text-[10px]" :class="msg.sender_id == currentUserId ? 'text-white/70' : 'text-brand-muted'" x-text="parseFloat(msg.latitude).toFixed(5) + ', ' + parseFloat(msg.longitude).toFixed(5)"></p>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="flex justify-end px-3 pb-1.5"><span class="text-[9px]" :class="msg.sender_id == currentUserId ? 'text-white/50' : 'text-brand-muted/50'" x-text="msg.created_at"></span></div>
                                            </div>
                                        </template>

                                        {{-- DOCUMENT --}}
                                        <template x-if="msg.message_type === 'document' && msg.file_path">
                                            <div class="px-3 py-2.5">
                                                <a :href="msg.file_path" target="_blank" rel="noopener"
                                                   class="flex items-center gap-3 rounded-xl border px-3 py-2.5 transition"
                                                   :class="msg.sender_id == currentUserId ? 'border-white/20 hover:bg-white/10' : 'border-warm-300/50 hover:bg-warm-200/50'">
                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg" :class="msg.sender_id == currentUserId ? 'bg-white/20' : 'bg-blue-50'">
                                                        <svg class="h-5 w-5" :class="msg.sender_id == currentUserId ? 'text-white' : 'text-blue-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="truncate text-sm font-medium" x-text="msg.file_name"></p>
                                                        <p class="text-[10px] uppercase tracking-wider" :class="msg.sender_id == currentUserId ? 'text-white/60' : 'text-brand-muted'" x-text="(msg.file_name || '').split('.').pop()"></p>
                                                    </div>
                                                    <svg class="h-5 w-5 shrink-0" :class="msg.sender_id == currentUserId ? 'text-white/60' : 'text-brand-muted'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                                </a>
                                                <template x-if="msg.message"><p class="mt-2 text-sm leading-relaxed whitespace-pre-line" x-text="msg.message"></p></template>
                                                <div class="flex justify-end pb-1"><span class="text-[9px]" :class="msg.sender_id == currentUserId ? 'text-white/50' : 'text-brand-muted/50'" x-text="msg.created_at"></span></div>
                                            </div>
                                        </template>

                                        {{-- TEXT (default) --}}
                                        <template x-if="!['image','audio','document','location'].includes(msg.message_type)">
                                            <div class="px-3 py-2">
                                                <p x-show="msg.message" x-text="msg.message" class="text-sm leading-relaxed whitespace-pre-line"></p>
                                                <template x-if="msg.file_path">
                                                    <a :href="msg.file_path" target="_blank" rel="noopener"
                                                       class="mt-1.5 flex items-center gap-2 rounded-lg border px-2.5 py-1.5 text-xs font-medium transition"
                                                       :class="msg.sender_id == currentUserId ? 'border-white/20 hover:bg-white/10' : 'border-warm-300/50 hover:bg-warm-200/50'">
                                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                                                        <span x-text="msg.file_name" class="truncate"></span>
                                                    </a>
                                                </template>
                                                <div class="mt-1 flex justify-end"><span class="text-[9px]" :class="msg.sender_id == currentUserId ? 'text-white/50' : 'text-brand-muted/50'" x-text="msg.created_at"></span></div>
                                            </div>
                                        </template>

                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── Input Area ── --}}
                <div class="relative shrink-0 border-t border-warm-300/40 bg-warm-100 px-3 py-3 sm:px-4 dark:border-white/[0.06] dark:bg-navy-800">

                    {{-- File Preview --}}
                    <div x-show="previewData" x-cloak class="mb-2.5 overflow-hidden rounded-2xl border border-warm-300/50 bg-warm-200/50">
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
                        <span class="flex-1 text-sm font-medium text-red-700">Recording audio…</span>
                        <span class="text-xs tabular-nums text-red-500" x-text="recordingTime"></span>
                        <button @click="stopRecording()" class="rounded-full bg-red-500 p-1.5 text-white transition hover:bg-red-600">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                        </button>
                    </div>

                    <div class="flex items-end gap-2">

                        {{-- Emoji --}}
                        <div class="relative">
                            <button type="button" @click="showEmoji = !showEmoji; showAttach = false"
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-brand-muted transition hover:bg-accent-light hover:text-brand-primary"
                                    :class="showEmoji ? 'bg-accent-light text-brand-primary' : ''">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z"/></svg>
                            </button>
                            <div x-show="showEmoji" x-cloak @click.outside="showEmoji = false"
                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute bottom-12 left-0 z-50 max-h-72 w-72 overflow-y-auto rounded-2xl border border-warm-300/50 bg-warm-100 p-3 shadow-panel">
                                <div class="mb-1.5 text-[10px] font-bold uppercase tracking-wider text-brand-muted">Smileys</div>
                                <div class="grid grid-cols-8 gap-0.5">
                                    <template x-for="emoji in emojis" :key="emoji">
                                        <button type="button" @click="insertEmoji(emoji)" class="flex h-8 w-8 items-center justify-center rounded-lg text-lg transition hover:scale-110 hover:bg-accent-light" x-text="emoji"></button>
                                    </template>
                                </div>
                                <div class="mb-1.5 mt-2 text-[10px] font-bold uppercase tracking-wider text-brand-muted">Gestures</div>
                                <div class="grid grid-cols-8 gap-0.5">
                                    <template x-for="emoji in gestureEmojis" :key="emoji">
                                        <button type="button" @click="insertEmoji(emoji)" class="flex h-8 w-8 items-center justify-center rounded-lg text-lg transition hover:scale-110 hover:bg-accent-light" x-text="emoji"></button>
                                    </template>
                                </div>
                                <div class="mb-1.5 mt-2 text-[10px] font-bold uppercase tracking-wider text-brand-muted">Objects</div>
                                <div class="grid grid-cols-8 gap-0.5">
                                    <template x-for="emoji in objectEmojis" :key="emoji">
                                        <button type="button" @click="insertEmoji(emoji)" class="flex h-8 w-8 items-center justify-center rounded-lg text-lg transition hover:scale-110 hover:bg-accent-light" x-text="emoji"></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Attach --}}
                        <div class="relative">
                            <button type="button" @click="showAttach = !showAttach; showEmoji = false"
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-brand-muted transition hover:bg-accent-light hover:text-brand-primary"
                                    :class="showAttach ? 'bg-accent-light text-brand-primary' : ''">
                                <svg class="h-5 w-5 transition-transform duration-200" :class="showAttach ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            </button>
                            <div x-show="showAttach" x-cloak @click.outside="showAttach = false"
                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute bottom-12 left-0 z-50 w-52 rounded-2xl border border-warm-300/50 bg-warm-100 p-2 shadow-panel">
                                <input type="file" x-ref="photoInput"  accept="image/*"             class="hidden" @change="handleFileSelect($event,'image')">
                                <input type="file" x-ref="docInput"                                  class="hidden" @change="handleFileSelect($event,'document')">
                                <input type="file" x-ref="cameraInput" accept="image/*" capture="environment" class="hidden" @change="handleFileSelect($event,'image')">
                                <button type="button" @click="$refs.cameraInput.click(); showAttach=false" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm hover:bg-warm-200/50">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-violet-100"><svg class="h-4 w-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"/></svg></div>
                                    <span class="font-medium text-brand-ink">Camera</span>
                                </button>
                                <button type="button" @click="$refs.photoInput.click(); showAttach=false" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm hover:bg-warm-200/50">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100"><svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/></svg></div>
                                    <span class="font-medium text-brand-ink">Photo</span>
                                </button>
                                <button type="button" @click="$refs.docInput.click(); showAttach=false" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm hover:bg-warm-200/50">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100"><svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg></div>
                                    <span class="font-medium text-brand-ink">Document</span>
                                </button>
                                <button type="button" @click="sendLocation(); showAttach=false" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm hover:bg-warm-200/50">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-red-100"><svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg></div>
                                    <span class="font-medium text-brand-ink">Location</span>
                                </button>
                                <button type="button" @click="startRecording(); showAttach=false" :disabled="isRecording" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm hover:bg-warm-200/50 disabled:opacity-50">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-accent-light"><svg class="h-4 w-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z"/></svg></div>
                                    <span class="font-medium text-brand-ink">Audio</span>
                                </button>
                            </div>
                        </div>

                        {{-- Text input --}}
                        <textarea x-model="newMessage" x-ref="messageInput"
                                  @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                                  placeholder="Type a message…" rows="1"
                                  class="flex-1 resize-none rounded-2xl border-0 bg-warm-200 px-4 py-2.5 text-sm text-brand-ink placeholder-brand-muted/50 transition focus:bg-warm-100 focus:ring-2 focus:ring-accent/30 dark:bg-white/[0.06] dark:text-white dark:placeholder-[#71717A] dark:focus:bg-white/[0.08] dark:focus:ring-accent/30"
                                  style="max-height:100px;"
                                  @input="autoResize($event)"></textarea>

                        {{-- Mic / Send toggle --}}
                        <template x-if="!newMessage.trim() && !previewData && !isRecording">
                            <button type="button" @click="startRecording()"
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-brand-muted transition hover:bg-accent-light hover:text-brand-primary">
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

            </div>{{-- /active conversation --}}
        </div>{{-- /right panel --}}
    </div>{{-- /two-panel container --}}

    {{-- ══════════════════════════════════════════════
         New Direct Message Modal
    ══════════════════════════════════════════════ --}}
    <div x-show="showNewChat" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[60] flex items-center justify-center bg-navy-800/50 backdrop-blur-sm p-4"
         @click.self="showNewChat = false">
        <div x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="w-full max-w-sm rounded-3xl border border-white/70 bg-warm-100 shadow-panel dark:border-white/[0.08] dark:bg-navy-800">
            <div class="flex items-center justify-between border-b border-warm-300/40 px-6 py-5 dark:border-white/[0.06]">
                <h3 class="font-display text-lg text-brand-ink">New Direct Message</h3>
                <button @click="showNewChat = false; userSearch = ''"
                        class="flex h-8 w-8 items-center justify-center rounded-xl border border-warm-300/50 text-brand-muted transition hover:border-red-200 hover:text-red-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4">
                <div class="relative mb-3">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-brand-muted/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input type="text" x-model="userSearch" placeholder="Search people…"
                           class="w-full rounded-2xl border-0 bg-warm-200 py-2 pl-9 pr-4 text-sm focus:bg-warm-100 focus:ring-2 focus:ring-accent/30">
                </div>
                <div class="max-h-64 space-y-1 overflow-y-auto">
                    <template x-if="availableUsers.length === 0">
                        <p class="py-6 text-center text-sm text-brand-muted">No users available to message.</p>
                    </template>
                    <template x-if="filteredUsers.length === 0 && userSearch.length > 0">
                        <p class="py-4 text-center text-sm text-brand-muted">No results for "<span x-text="userSearch"></span>"</p>
                    </template>
                    <template x-for="u in filteredUsers" :key="u.id">
                        <button @click="startDirectChat(u.id)"
                                class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-left transition hover:bg-accent-light">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 font-display text-sm font-bold text-slate-600" x-text="u.initials"></div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-brand-ink" x-text="u.name"></p>
                                <p class="text-xs text-brand-muted" x-text="u.role"></p>
                            </div>
                            <svg class="h-4 w-4 shrink-0 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        </button>
                    </template>
                </div>
                <div x-show="startingChat" x-cloak class="mt-3 flex items-center justify-center gap-2 text-sm text-brand-muted">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Starting conversation…
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         Jitsi Meet — Voice / Video Call Modal
    ══════════════════════════════════════════════ --}}
    <div x-show="showCallModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] flex flex-col bg-navy-800"
         @keydown.escape.window="closeCall()">

        {{-- Call toolbar --}}
        <div class="flex shrink-0 items-center justify-between bg-slate-900/95 px-5 py-3 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                {{-- Live pulse indicator --}}
                <span class="relative flex h-3 w-3">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-60"></span>
                    <span class="relative inline-flex h-3 w-3 rounded-full bg-green-500"></span>
                </span>
                <div>
                    <p class="text-xs font-bold text-white" x-text="callType === 'audio' ? 'Voice Call' : 'Video Call'"></p>
                    <p class="text-[10px] text-slate-400" x-text="activeConversation?.title ?? ''"></p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Room name badge --}}
                <span class="hidden rounded-full bg-slate-700/80 px-3 py-1 font-mono text-[10px] text-slate-300 sm:block"
                      x-text="callRoomName"></span>

                {{-- Copy room link --}}
                <button @click="copyCallLink()"
                        title="Copy call link"
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-700 text-slate-300 transition hover:bg-slate-600 hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/>
                    </svg>
                </button>

                {{-- End call (red) --}}
                <button @click="closeCall()"
                        title="End call"
                        class="flex h-9 items-center gap-2 rounded-xl bg-red-600 px-3 text-sm font-semibold text-white transition hover:bg-red-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                    <span class="hidden sm:block">End Call</span>
                </button>
            </div>
        </div>

        {{-- Jitsi Meet container — populated by the External API (avoids X-Frame-Options block) --}}
        <div x-ref="jitsiContainer"
             id="jitsi-container"
             class="min-h-0 flex-1 w-full bg-navy-800">
        </div>
    </div>

    @push('scripts')
    <script>
    function globalChat() {
        return {
            conversations:      @json($conversations),
            activeConversation: null,
            activeTab:          'chats',
            search:             '',
            contactSearch:      '',
            loadingConvs:       false,
            mobileView:         window.innerWidth < 1024,

            messages:           [],
            loadingMessages:    false,
            polling:            null,

            showNewChat:        false,
            availableUsers:     @json($availableUsers),
            userSearch:         '',
            startingChat:       false,

            showCallModal:      false,
            callRoomName:       '',
            callType:           'video',

            newMessage:         '',
            file:               null,
            fileName:           '',
            fileType:           'text',
            previewData:        false,
            previewUrl:         '',
            previewType:        '',
            sending:            false,
            showEmoji:          false,
            showAttach:         false,

            isRecording:        false,
            mediaRecorder:      null,
            audioChunks:        [],
            recordingTimer:     null,
            recordingSeconds:   0,
            recordingTime:      '0:00',

            currentUserId:      {{ auth()->id() }},
            listUrl:            '{{ route('conversations.list') }}',
            storeUrl:           '{{ route('conversations.store') }}',

            emojis:        ['😀','😂','😍','🥰','😎','😢','😡','🤔','👍','❤️','🔥','🎉','✅','🙏','💯','😊','🤣','😭','😱','🥳','😴','🤗','🤝','💪'],
            gestureEmojis: ['👋','👌','✌️','🤞','🫡','👏','🙌','🤲','💅','✍️','🫶','🤙','👆','👇','👈','👉'],
            objectEmojis:  ['📎','📄','📁','💻','📱','⏰','📌','💡','🔑','📧','💰','📊','🎯','🚀','⭐','🏆'],

            // Contact colour + category mapping
            get contactGroups() {
                const catMap   = { 'Client': 'Customers', 'Freelancer': 'Freelancers', 'Admin': 'Admins' };
                const colorMap = { 'Client': 'bg-green-100 text-green-700', 'Freelancer': 'bg-blue-100 text-blue-700', 'Admin': 'bg-purple-100 text-purple-700' };
                const q        = this.contactSearch.toLowerCase();
                const users    = q
                    ? this.availableUsers.filter(u => u.name.toLowerCase().includes(q) || u.role.toLowerCase().includes(q))
                    : this.availableUsers;
                const order    = ['Customers', 'Freelancers', 'Admins'];
                const groups   = {};
                users.forEach(u => {
                    const cat = catMap[u.role] || (u.role + 's');
                    if (!groups[cat]) groups[cat] = [];
                    groups[cat].push({ ...u, colorClass: colorMap[u.role] || 'bg-slate-100 text-slate-600' });
                });
                return order.filter(n => groups[n]).map(n => ({ name: n, users: groups[n] }));
            },

            get filteredConversations() {
                if (!this.search) return this.conversations;
                const q = this.search.toLowerCase();
                return this.conversations.filter(c =>
                    c.title.toLowerCase().includes(q) ||
                    (c.subtitle || '').toLowerCase().includes(q)
                );
            },

            get filteredUsers() {
                if (!this.userSearch) return this.availableUsers;
                const q = this.userSearch.toLowerCase();
                return this.availableUsers.filter(u =>
                    u.name.toLowerCase().includes(q) || u.role.toLowerCase().includes(q)
                );
            },

            init() {
                const params = new URLSearchParams(window.location.search);
                const convId = parseInt(params.get('conversation'));
                if (convId) {
                    const conv = this.conversations.find(c => c.id === convId);
                    if (conv) this.$nextTick(() => this.selectConversation(conv));
                }
                setInterval(() => this.refreshConversations(), 10000);
                window.addEventListener('resize', () => { this.mobileView = window.innerWidth < 1024; });
            },

            async selectConversation(conv) {
                if (this.activeConversation?.id === conv.id) return;
                if (this.polling) clearInterval(this.polling);
                this.activeConversation = conv;
                this.messages           = [];
                this.loadingMessages    = true;
                this.mobileView         = false;
                history.replaceState(null, '', '?conversation=' + conv.id);
                await this.fetchMessages();
                this.polling = setInterval(() => this.pollMessages(), 4000);
            },

            async fetchMessages() {
                if (!this.activeConversation) return;
                try {
                    const res = await fetch(this.activeConversation.fetch_url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.ok) {
                        this.messages = await res.json();
                        this.$nextTick(() => this.scrollToBottom());
                    }
                } catch (e) { console.error('fetchMessages:', e); }
                finally { this.loadingMessages = false; }
            },

            async pollMessages() {
                if (!this.activeConversation) return;
                const lastId = this.messages.length ? this.messages[this.messages.length - 1].id : 0;
                try {
                    const res = await fetch(this.activeConversation.fetch_url + '?after=' + lastId, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.ok) {
                        const newMsgs = await res.json();
                        if (newMsgs.length) {
                            this.messages.push(...newMsgs);
                            this.updateConvLastMsg(newMsgs[newMsgs.length - 1], false);
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    }
                } catch (e) { /* silent */ }
            },

            async refreshConversations() {
                try {
                    const res = await fetch(this.listUrl, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.ok) this.conversations = await res.json();
                } catch (e) { /* silent */ }
            },

            async sendMessage() {
                if (this.sending || !this.activeConversation) return;
                if (!this.newMessage.trim() && !this.file) return;
                this.sending    = true;
                this.showEmoji  = false;
                this.showAttach = false;
                const formData = new FormData();
                if (this.newMessage.trim()) formData.append('message', this.newMessage.trim());
                if (this.file) { formData.append('file', this.file); formData.append('message_type', this.fileType); }
                try {
                    const res = await fetch(this.activeConversation.send_url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'Accept':           'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });
                    if (res.ok) {
                        const msg = await res.json();
                        this.messages.push(msg);
                        this.updateConvLastMsg(msg, true);
                        this.newMessage = '';
                        this.removeFile();
                        this.$nextTick(() => {
                            this.scrollToBottom();
                            if (this.$refs.messageInput) this.$refs.messageInput.style.height = 'auto';
                        });
                    } else if (res.status === 419) {
                        alert('Session expired. Please refresh the page.');
                    } else {
                        try { const e = await res.json(); alert(e.error || e.message || 'Failed to send.'); }
                        catch { alert('Server error (' + res.status + '). Try again.'); }
                    }
                } catch (e) { alert('Network error. Check your connection.'); }
                finally { this.sending = false; }
            },

            async sendLocation() {
                if (!navigator.geolocation || !this.activeConversation) { alert('Geolocation not supported.'); return; }
                this.sending = true;
                navigator.geolocation.getCurrentPosition(
                    async (pos) => {
                        const fd = new FormData();
                        fd.append('message_type', 'location');
                        fd.append('latitude',     pos.coords.latitude);
                        fd.append('longitude',    pos.coords.longitude);
                        fd.append('message',      'Shared location');
                        try {
                            const res = await fetch(this.activeConversation.send_url, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                                body: fd,
                            });
                            if (res.ok) { this.messages.push(await res.json()); this.$nextTick(() => this.scrollToBottom()); }
                            else alert('Failed to send location.');
                        } catch (e) { alert('Network error.'); }
                        finally { this.sending = false; }
                    },
                    () => { alert('Location access denied.'); this.sending = false; },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            },

            async startDirectChat(userId) {
                this.startingChat = true;
                try {
                    const res = await fetch(this.storeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type':     'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'Accept':           'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ user_id: userId }),
                    });
                    if (res.ok) {
                        const data = await res.json();
                        this.showNewChat = false;
                        this.userSearch  = '';
                        await this.refreshConversations();
                        const conv = this.conversations.find(c => c.id === data.id);
                        if (conv) await this.selectConversation(conv);
                    } else {
                        try { const e = await res.json(); alert(e.message || e.error || 'Could not start conversation.'); }
                        catch { alert('Could not start conversation.'); }
                    }
                } catch (e) { alert('Network error.'); }
                finally { this.startingChat = false; }
            },

            updateConvLastMsg(msg, mine) {
                const conv = this.conversations.find(c => c.id === this.activeConversation?.id);
                if (conv) conv.last_message = { text: msg.message || '📎 ' + (msg.message_type || 'Attachment'), sender: msg.sender_name, mine, time: 'just now' };
            },

            scrollToBottom() { const b = this.$refs.chatBox; if (b) b.scrollTop = b.scrollHeight; },

            autoResize(e) { e.target.style.height = 'auto'; e.target.style.height = Math.min(e.target.scrollHeight, 100) + 'px'; },

            insertEmoji(emoji) {
                const input = this.$refs.messageInput;
                if (!input) return;
                const s = input.selectionStart, e = input.selectionEnd;
                this.newMessage = this.newMessage.substring(0, s) + emoji + this.newMessage.substring(e);
                this.$nextTick(() => { input.focus(); input.setSelectionRange(s + emoji.length, s + emoji.length); });
            },

            handleFileSelect(event, type) {
                const file = event.target.files[0];
                if (!file) return;
                if (file.size > 20 * 1024 * 1024) { alert('File must be under 20 MB.'); return; }
                this.file = file; this.fileType = type; this.fileName = file.name;
                this.previewData = true; this.previewType = type;
                if (type === 'image' || type === 'audio') this.previewUrl = URL.createObjectURL(file);
            },

            removeFile() {
                this.file = null; this.fileName = ''; this.fileType = 'text';
                this.previewData = false; this.previewUrl = ''; this.previewType = '';
                ['photoInput','docInput','cameraInput'].forEach(r => { if (this.$refs[r]) this.$refs[r].value = ''; });
            },

            async startRecording() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    this.audioChunks = []; this.recordingSeconds = 0; this.recordingTime = '0:00';
                    this.mediaRecorder = new MediaRecorder(stream);
                    this.mediaRecorder.ondataavailable = e => { if (e.data.size > 0) this.audioChunks.push(e.data); };
                    this.mediaRecorder.onstop = () => {
                        stream.getTracks().forEach(t => t.stop());
                        const blob = new Blob(this.audioChunks, { type: 'audio/webm' });
                        this.file = new File([blob], 'voice-message.webm', { type: 'audio/webm' });
                        this.fileName = 'Voice message (' + this.recordingTime + ')';
                        this.fileType = 'audio'; this.previewData = true; this.previewType = 'audio';
                        this.previewUrl = URL.createObjectURL(blob);
                    };
                    this.mediaRecorder.start();
                    this.isRecording = true;
                    this.recordingTimer = setInterval(() => {
                        this.recordingSeconds++;
                        const m = Math.floor(this.recordingSeconds / 60), s = this.recordingSeconds % 60;
                        this.recordingTime = m + ':' + (s < 10 ? '0' : '') + s;
                    }, 1000);
                } catch (e) { alert('Microphone access denied or unavailable.'); }
            },

            stopRecording() {
                if (this.mediaRecorder && this.isRecording) this.mediaRecorder.stop();
                this.isRecording = false;
                if (this.recordingTimer) { clearInterval(this.recordingTimer); this.recordingTimer = null; }
            },

            // ── Jitsi Meet calls ──────────────────────────
            _jitsiApi: null,

            openCall(roomName, type) {
                this.callRoomName  = roomName;
                this.callType      = type;
                this.showCallModal = true;

                // Load the Jitsi External API script if not already present,
                // then initialise the meeting inside #jitsi-container.
                const init = () => {
                    // Destroy any previous meeting
                    if (this._jitsiApi) { try { this._jitsiApi.dispose(); } catch(e){} }

                    this._jitsiApi = new JitsiMeetExternalAPI('meet.jit.si', {
                        roomName:  roomName,
                        parentNode: document.getElementById('jitsi-container'),
                        width:  '100%',
                        height: '100%',
                        configOverwrite: {
                            prejoinPageEnabled:    false,
                            startWithVideoMuted:   (type === 'audio'),
                            startWithAudioMuted:   false,
                            disableDeepLinking:    true,
                            enableNoisyMicDetection: false,
                        },
                        interfaceConfigOverwrite: {
                            SHOW_JITSI_WATERMARK:  false,
                            SHOW_BRAND_WATERMARK:  false,
                            TOOLBAR_ALWAYS_VISIBLE: true,
                        },
                    });

                    // Auto-close modal when the user hangs up from inside Jitsi
                    this._jitsiApi.addListener('readyToClose', () => this.closeCall());
                };

                if (typeof JitsiMeetExternalAPI !== 'undefined') {
                    this.$nextTick(init);
                } else {
                    const script = document.createElement('script');
                    script.src   = 'https://meet.jit.si/external_api.js';
                    script.onload = () => this.$nextTick(init);
                    script.onerror = () => alert('Could not load Jitsi. Check your internet connection.');
                    document.head.appendChild(script);
                }
            },

            closeCall() {
                if (this._jitsiApi) {
                    try { this._jitsiApi.dispose(); } catch(e) {}
                    this._jitsiApi = null;
                }
                this.showCallModal = false;
                this.callRoomName  = '';
                // Clear the container so no stale content shows on re-open
                const el = document.getElementById('jitsi-container');
                if (el) el.innerHTML = '';
            },

            copyCallLink() {
                const url = 'https://meet.jit.si/' + encodeURIComponent(this.callRoomName);
                navigator.clipboard.writeText(url).then(
                    () => alert('Call link copied!\n' + url),
                    () => prompt('Copy this link:', url)
                );
            },
        };
    }
    </script>
    @endpush

</x-app-layout>
