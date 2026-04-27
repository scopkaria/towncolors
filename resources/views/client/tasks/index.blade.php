<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Client · Tasks</span>
            <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Submit a Professional Task</h1>
            <p class="text-sm text-brand-muted">Send task details with optional image and voice note. Admin will assign to freelancer or internal team.</p>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <h2 class="font-display text-xl text-brand-ink">New Task Request</h2>
            <form method="POST" action="{{ route('client.tasks.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Task Title</label>
                    <input type="text" name="title" required class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" placeholder="Monthly social media campaign">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Priority</label>
                    <select name="priority" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Task Description</label>
                    <textarea name="description" rows="5" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none resize-y" placeholder="Explain what needs to be done, deliverables, and deadlines."></textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Image (optional)</label>
                    <input type="file" name="task_image" accept="image/*" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                    <p class="mt-1 text-xs text-brand-muted">Accepted image formats up to 10MB.</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Voice Note (optional)</label>
                    <input id="voice_note_input" type="file" name="voice_note" accept="audio/*" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                    <div class="mt-2 flex flex-wrap gap-2">
                        <button id="start_voice_recording" type="button" class="rounded-lg border border-warm-300/50 bg-white px-3 py-1.5 text-xs font-semibold text-brand-ink">Start Recording</button>
                        <button id="stop_voice_recording" type="button" class="rounded-lg border border-warm-300/50 bg-white px-3 py-1.5 text-xs font-semibold text-brand-ink" disabled>Stop Recording</button>
                        <span id="voice_recording_status" class="inline-flex items-center text-xs text-brand-muted">Recorder ready.</span>
                    </div>
                    <audio id="voice_recording_preview" class="mt-2 hidden w-full" controls></audio>
                    <p class="mt-1 text-xs text-brand-muted">Accepted audio formats up to 10MB.</p>
                </div>

                <button type="submit" class="btn-primary w-full">Submit Task</button>
            </form>
        </section>

        <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <h2 class="font-display text-xl text-brand-ink">My Submitted Tasks</h2>
            <div class="mt-5 space-y-4">
                @forelse ($tasks as $task)
                    <article class="rounded-2xl border border-warm-300/50 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <h3 class="font-semibold text-brand-ink">{{ $task->title }}</h3>
                            <div class="flex gap-2">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $task->priorityBadge() }}">{{ ucfirst($task->priority) }}</span>
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $task->statusBadge() }}">{{ str_replace('_', ' ', ucfirst($task->status)) }}</span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-brand-muted">{{ $task->description ?: 'No description provided.' }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-brand-muted">
                            <span>Submitted {{ $task->created_at->diffForHumans() }}</span>
                            <span>Assigned to: {{ $task->assignee?->name ?? 'Awaiting assignment' }}</span>
                            @if ($task->due_date)
                                <span>Due: {{ $task->due_date->format('M d, Y') }}</span>
                            @endif
                            @if ($task->image_path)
                                <a href="{{ asset('storage/' . $task->image_path) }}" target="_blank" class="font-semibold text-brand-primary hover:underline">View Image</a>
                            @endif
                            @if ($task->voice_note_path)
                                <a href="{{ asset('storage/' . $task->voice_note_path) }}" target="_blank" class="font-semibold text-brand-primary hover:underline">Play Voice Note</a>
                            @endif
                        </div>

                        @if ($task->image_path)
                            <div class="mt-3 w-full max-w-xs overflow-hidden rounded-xl border border-warm-300/50 bg-warm-100">
                                <img src="{{ asset('storage/' . $task->image_path) }}" alt="Task image for {{ $task->title }}" class="h-auto w-full object-cover">
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-warm-400/50 bg-warm-200/50 p-10 text-center text-sm text-brand-muted">No tasks submitted yet.</div>
                @endforelse
            </div>

            <div class="mt-5">{{ $tasks->links() }}</div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const startButton = document.getElementById('start_voice_recording');
            const stopButton = document.getElementById('stop_voice_recording');
            const statusLabel = document.getElementById('voice_recording_status');
            const preview = document.getElementById('voice_recording_preview');
            const voiceInput = document.getElementById('voice_note_input');

            if (!startButton || !stopButton || !statusLabel || !preview || !voiceInput) {
                return;
            }

            if (!navigator.mediaDevices || typeof MediaRecorder === 'undefined') {
                statusLabel.textContent = 'Recording is not supported in this browser. You can still upload audio files.';
                startButton.disabled = true;
                stopButton.disabled = true;
                return;
            }

            let mediaRecorder = null;
            let stream = null;
            let chunks = [];

            const resetUi = () => {
                startButton.disabled = false;
                stopButton.disabled = true;
            };

            startButton.addEventListener('click', async () => {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    chunks = [];

                    mediaRecorder = new MediaRecorder(stream);

                    mediaRecorder.addEventListener('dataavailable', (event) => {
                        if (event.data && event.data.size > 0) {
                            chunks.push(event.data);
                        }
                    });

                    mediaRecorder.addEventListener('stop', () => {
                        const mimeType = chunks[0]?.type || 'audio/webm';
                        const blob = new Blob(chunks, { type: mimeType });
                        const extension = mimeType.includes('ogg') ? 'ogg' : mimeType.includes('mp4') ? 'm4a' : 'webm';
                        const file = new File([blob], `voice-note-${Date.now()}.${extension}`, { type: mimeType });

                        const transfer = new DataTransfer();
                        transfer.items.add(file);
                        voiceInput.files = transfer.files;

                        preview.src = URL.createObjectURL(blob);
                        preview.classList.remove('hidden');
                        statusLabel.textContent = 'Recording attached. You can submit now or record again.';

                        stream?.getTracks().forEach((track) => track.stop());
                        stream = null;
                        resetUi();
                    });

                    mediaRecorder.start();
                    statusLabel.textContent = 'Recording in progress...';
                    startButton.disabled = true;
                    stopButton.disabled = false;
                } catch (error) {
                    statusLabel.textContent = 'Could not start recording. Check microphone permission.';
                    resetUi();
                }
            });

            stopButton.addEventListener('click', () => {
                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    mediaRecorder.stop();
                }
            });
        });
    </script>
</x-app-layout>
