<x-app-layout>
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Subscribers</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $subscribers->count() }} {{ Str::plural('subscriber', $subscribers->count()) }} total
                </p>
            </div>
            @if($subscribers->isNotEmpty())
                <a href="{{ route('admin.subscribers.export') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-brand-primary px-4 py-2.5
                          text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export CSV
                </a>
            @endif
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            @if($subscribers->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-4 text-sm font-medium text-gray-500">No subscribers yet</p>
                    <p class="mt-1 text-xs text-gray-400">Subscribers will appear here once people sign up.</p>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Subscribed</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($subscribers as $sub)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-gray-400">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $sub->email }}</td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $sub->created_at->format('M j, Y') }}
                                    <span class="ml-1 text-xs text-gray-400">{{ $sub->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.subscribers.destroy', $sub) }}" method="POST"
                                          onsubmit="return confirm('Remove this subscriber?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs text-red-500 hover:text-red-700 transition">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</x-app-layout>
