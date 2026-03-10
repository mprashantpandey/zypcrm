<div class="space-y-6">
    @if (session('message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('message') }}
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">Publish Student Notice</h2>
        <p class="mt-1 text-sm text-gray-500">Send notices to students of your library.</p>

        <form wire:submit="createNotice" class="mt-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" wire:model="title"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Message</label>
                <textarea wire:model="body" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Level</label>
                    <select wire:model="level"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="info">Info</option>
                        <option value="success">Success</option>
                        <option value="warning">Warning</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Starts At (optional)</label>
                    <input type="datetime-local" wire:model="startsAt"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ends At (optional)</label>
                    <input type="datetime-local" wire:model="endsAt"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" wire:model="deliveryInApp" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    In-App
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" wire:model="deliveryEmail" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Email
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" wire:model="deliveryPush" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Push
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" wire:model="deliveryWhatsapp" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    WhatsApp
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" wire:model="isActive" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Active
                </label>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Publish Notice
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Admin Notices</h3>
            <div class="mt-4 space-y-3">
                @forelse ($visibleNotices as $notice)
                    <div class="rounded-lg border border-gray-100 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-semibold text-gray-900">{{ $notice->title }}</p>
                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-600">{{ strtoupper($notice->audience) }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ $notice->body }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No admin notices available.</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $visibleNotices->links() }}</div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Sent by Library</h3>
            <div class="mt-4 space-y-3">
                @forelse ($sentNotices as $notice)
                    <div class="rounded-lg border border-gray-100 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-semibold text-gray-900">{{ $notice->title }}</p>
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">{{ strtoupper($notice->level) }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ $notice->body }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No library notices sent yet.</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $sentNotices->links() }}</div>
        </div>
    </div>
</div>
