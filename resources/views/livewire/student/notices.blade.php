<div class="space-y-6">
    @if($activeTenant)
        <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
            Viewing notices for: <span class="font-semibold">{{ $activeTenant->name }}</span>
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Notifications</h2>
                <p class="mt-1 text-sm text-gray-500">System notifications sent directly to your account.</p>
            </div>
            <button wire:click="markAllRead"
                class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                Mark all read ({{ $unreadCount }})
            </button>
        </div>
        <div class="mt-4 space-y-3">
            @forelse ($notifications as $notification)
                <div class="rounded-lg border border-gray-100 p-4 {{ $notification->read_at ? 'bg-white' : 'bg-amber-50/40' }}">
                    <p class="text-sm font-semibold text-gray-900">{{ data_get($notification->data, 'title', 'Notification') }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ data_get($notification->data, 'body', '') }}</p>
                    <p class="mt-2 text-xs text-gray-500">{{ $notification->created_at->format('M d, Y h:i A') }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No notifications yet.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Library Notices</h3>
        <p class="mt-1 text-sm text-gray-500">Important announcements from your library and platform admin.</p>
        <div class="mt-4 space-y-3">
            @forelse ($notices as $notice)
                <div class="rounded-lg border border-gray-100 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-gray-900">{{ $notice->title }}</p>
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">{{ strtoupper($notice->level) }}</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">{{ $notice->body }}</p>
                    <p class="mt-2 text-xs text-gray-500">
                        {{ $notice->tenant?->name ?? 'Platform' }} • {{ $notice->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No active notices found.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $notices->links() }}</div>
    </div>
</div>

