<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h1 class="text-lg font-semibold text-slate-900">Create Support Ticket</h1>
        <p class="mt-1 text-sm text-slate-500">
            Describe your issue or request and our platform team will respond from the admin console.
        </p>

        <form wire:submit="submit" class="mt-6 space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-900">Subject</label>
                <p class="mt-1 text-xs text-slate-500">
                    A short summary like “Unable to mark attendance for evening batch”.
                </p>
                <input type="text" wire:model.defer="subject"
                    class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Tell us what you need help with">
                <x-input-error :messages="$errors->get('subject')" class="mt-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900">Priority</label>
                <p class="mt-1 text-xs text-slate-500">Use “Critical” only when operations are fully blocked.</p>
                <select wire:model.defer="priority"
                    class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Submit Ticket
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-slate-900">Recent Tickets</h2>
        <p class="mt-1 text-xs text-slate-500">
            Last 10 tickets you’ve opened with the platform team.
        </p>

        @if($tickets->isEmpty())
            <p class="mt-4 text-xs text-slate-400">No tickets yet. Use the form above to create your first ticket.</p>
        @else
            <div class="mt-4 space-y-3">
                @foreach($tickets as $ticket)
                    <div class="flex items-start justify-between rounded-lg border border-slate-200 px-3 py-2.5">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $ticket->subject }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">
                                {{ ucfirst($ticket->priority) }} priority &middot;
                                {{ $ticket->created_at->format('M d, Y H:i') }}
                            </p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium
                            @class([
                                'bg-yellow-100 text-yellow-800' => $ticket->status === 'open',
                                'bg-blue-100 text-blue-800' => $ticket->status === 'answered',
                                'bg-slate-100 text-slate-500' => $ticket->status === 'closed',
                            ])">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
