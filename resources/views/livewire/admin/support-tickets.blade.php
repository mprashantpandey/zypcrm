<div>
    <div class="flex flex-col lg:flex-row gap-8 h-[calc(100vh-12rem)]">

        {{-- LEFT PANEL: Ticket List --}}
        <div class="w-full lg:w-96 flex-shrink-0 flex flex-col">
            {{-- Filters --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4 flex gap-3">
                <select wire:model.live="filterStatus"
                    class="flex-1 block rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="all">All Statuses</option>
                    <option value="open">Open</option>
                    <option value="answered">Answered</option>
                    <option value="closed">Closed</option>
                </select>
                <select wire:model.live="filterPriority"
                    class="flex-1 block rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="all">All Priorities</option>
                    <option value="critical">Critical</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            {{-- Ticket List --}}
            <div class="flex-1 overflow-y-auto space-y-2 pr-1">
                @forelse ($tickets as $ticket)
                @php
                $statusColors = [
                'open' => 'bg-yellow-100 text-yellow-800',
                'answered' => 'bg-blue-100 text-blue-800',
                'closed' => 'bg-gray-100 text-gray-500',
                ];
                $priorityColors = [
                'critical' => 'bg-red-500',
                'high' => 'bg-orange-400',
                'medium' => 'bg-blue-400',
                'low' => 'bg-gray-300',
                ];
                @endphp
                <button wire:click="selectTicket({{ $ticket->id }})"
                    class="w-full text-left bg-white rounded-xl border shadow-sm p-4 hover:shadow-md transition-all duration-200 {{ $selectedTicket && $selectedTicket->id === $ticket->id ? 'border-indigo-400 ring-2 ring-indigo-100' : 'border-gray-200' }}">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $ticket->subject }}</h4>
                        <span
                            class="inline-flex items-center shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $statusColors[$ticket->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span
                                class="inline-block w-2 h-2 rounded-full {{ $priorityColors[$ticket->priority] ?? 'bg-gray-300' }}"></span>
                            {{ ucfirst($ticket->priority) }} Priority
                        </span>
                        <span>{{ $ticket->replies_count }} {{ Str::plural('reply', $ticket->replies_count) }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1 truncate">
                        From: {{ $ticket->user->name ?? 'Unknown' }} &middot; {{ $ticket->created_at->diffForHumans() }}
                    </p>
                </button>
                @empty
                <div
                    class="flex flex-col items-center justify-center py-16 text-center bg-white rounded-xl border border-gray-200 border-dashed">
                    <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                        </path>
                    </svg>
                    <p class="text-sm font-medium text-gray-500">No tickets found</p>
                    <p class="text-xs text-gray-400 mt-1">All caught up! Change filters to see more.</p>
                </div>
                @endforelse
                <div class="pt-2">{{ $tickets->links() }}</div>
            </div>
        </div>

        {{-- RIGHT PANEL: Ticket Detail / Conversation --}}
        <div class="flex-1 min-w-0 flex flex-col bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if ($selectedTicket)
            {{-- Header --}}
            <div
                class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-start justify-between gap-4 flex-shrink-0">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">{{ $selectedTicket->subject }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Opened by <strong>{{ $selectedTicket->user->name }}</strong>
                        &middot; {{ $selectedTicket->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if ($selectedTicket->status !== 'closed')
                    <button wire:click="closeTicket"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-900 text-white hover:bg-gray-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Close Ticket
                    </button>
                    @else
                    <button wire:click="reopenTicket"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-500 transition-colors">
                        Reopen
                    </button>
                    @endif
                    <button wire:click="deleteTicket"
                        wire:confirm="Are you sure you want to permanently delete this ticket?"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

            {{-- Conversation Thread --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-5">
                @forelse ($selectedTicket->replies as $reply)
                @php $isAdmin = $reply->user->role === 'super_admin'; @endphp
                <div class="flex gap-3 {{ $isAdmin ? 'flex-row-reverse' : '' }}">
                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0 {{ $isAdmin ? 'bg-indigo-600' : 'bg-gray-400' }}">
                        {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="max-w-xl">
                        <div
                            class="rounded-2xl px-4 py-3 {{ $isAdmin ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-gray-100 text-gray-800 rounded-tl-none' }}">
                            <p class="text-sm">{{ $reply->message }}</p>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5 {{ $isAdmin ? 'text-right' : '' }}">
                            {{ $reply->user->name }} &middot; {{ $reply->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="flex items-center justify-center h-full">
                    <p class="text-sm text-gray-400">No replies yet. Start the conversation below.</p>
                </div>
                @endforelse
            </div>

            {{-- Reply Box --}}
            @if ($selectedTicket->status !== 'closed')
            <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex-shrink-0">
                <form wire:submit="sendReply" class="flex gap-3">
                    <textarea wire:model="replyMessage" rows="2" placeholder="Type your reply..."
                        class="flex-1 block rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm resize-none"></textarea>
                    <button type="submit"
                        class="self-end inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Send
                    </button>
                </form>
            </div>
            @else
            <div class="p-4 text-center text-xs text-gray-400 border-t border-gray-100 bg-gray-50/50 flex-shrink-0">
                This ticket is closed. Reopen it to reply.
            </div>
            @endif

            @else
            <div class="flex-1 flex flex-col items-center justify-center text-center p-8">
                <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-700">Select a ticket</h3>
                <p class="text-sm text-gray-400 mt-1">Choose a support ticket from the left to view the conversation and
                    reply.</p>
            </div>
            @endif
        </div>

    </div>
</div>