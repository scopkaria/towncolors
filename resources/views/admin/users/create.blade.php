<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Users</span>
            <h1 class="font-display text-3xl text-brand-ink">Create User</h1>
            <p class="text-sm text-brand-muted">Create client or freelancer accounts and email their credentials automatically.</p>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf
            <div class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-card space-y-5">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" placeholder="jane_client">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Phone (optional)</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" placeholder="+255...">
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Role</label>
                    <select name="role" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>{{ $role->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Temporary Password</label>
                    <input type="text" name="temporary_password" value="{{ old('temporary_password') }}" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                    <p class="mt-1 text-xs text-brand-muted">The user will be forced to change this password on first login.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>