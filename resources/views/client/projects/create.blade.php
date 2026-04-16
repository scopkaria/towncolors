<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                New project
            </span>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Create a Project</h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">Describe what you need and attach any relevant files.</p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('client.projects.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel space-y-6">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-semibold text-brand-ink">Project Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary"
                        placeholder="e.g. Website Redesign">
                    @error('title')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Categories --}}
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">
                        Service Type
                        <span class="ml-1 text-xs font-normal text-brand-muted">(optional, select all that apply)</span>
                    </label>
                    @if ($categories->isEmpty())
                        <p class="mt-2 text-xs text-brand-muted italic">No categories configured yet.</p>
                    @else
                        @php $oldIds = array_map('intval', old('category_ids', [])); @endphp
                        <div class="mt-3 space-y-3">
                            @foreach ($categories as $root)
                                <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4">
                                    {{-- Root category --}}
                                    <label class="flex cursor-pointer items-center gap-3">
                                        <input type="checkbox" name="category_ids[]" value="{{ $root->id }}"
                                               {{ in_array($root->id, $oldIds) ? 'checked' : '' }}
                                               class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary">
                                        <span class="flex items-center gap-2 text-sm font-semibold text-brand-ink">
                                            @if ($root->color)
                                                <span class="inline-block h-2.5 w-2.5 rounded-full" style="background-color: {{ $root->color }}"></span>
                                            @endif
                                            {{ $root->name }}
                                        </span>
                                    </label>
                                    {{-- Subcategories --}}
                                    @if ($root->children->isNotEmpty())
                                        <div class="mt-3 ml-7 grid gap-2 sm:grid-cols-2">
                                            @foreach ($root->children as $child)
                                                <label class="flex cursor-pointer items-center gap-2">
                                                    <input type="checkbox" name="category_ids[]" value="{{ $child->id }}"
                                                           {{ in_array($child->id, $oldIds) ? 'checked' : '' }}
                                                           class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary">
                                                    <span class="flex items-center gap-1.5 text-xs font-medium text-brand-muted">
                                                        @if ($child->color)
                                                            <span class="inline-block h-2 w-2 rounded-full" style="background-color: {{ $child->color }}"></span>
                                                        @endif
                                                        {{ $child->name }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @error('category_ids')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    @error('category_ids.*')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-semibold text-brand-ink">Description</label>
                    <textarea name="description" id="description" rows="5" required
                        class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary"
                        placeholder="Describe the project scope, deliverables, and timeline...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- File Upload --}}
                <div>
                    <label for="files" class="block text-sm font-semibold text-brand-ink">Attachments</label>
                    <p class="mt-1 text-xs text-brand-muted">Upload briefs, mockups, or reference files. Max 10 MB each.</p>
                    <div class="mt-3 rounded-2xl border-2 border-dashed border-warm-300/50 bg-warm-200/50 px-6 py-8 text-center transition duration-200 hover:border-brand-primary hover:bg-accent/10">
                        <svg class="mx-auto h-10 w-10 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                        <p class="mt-3 text-sm text-brand-muted">Drag files here or <label for="files" class="cursor-pointer font-semibold text-brand-primary hover:text-brand-hover">browse</label></p>
                        <input type="file" name="files[]" id="files" multiple class="hidden">
                    </div>
                    @error('files.*')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('client.projects.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                    Create Project
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
