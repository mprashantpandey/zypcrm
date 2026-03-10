<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Ops Monitor</h1>
            <p class="mt-1 text-sm text-slate-500">Queue reliability dashboard, failed job controls, and provider health checks.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Pending Jobs</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ $queueStats['pending'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Failed Jobs</p>
                <p class="mt-1 text-2xl font-bold text-rose-600">{{ $queueStats['failed'] }}</p>
            </div>
            @php
                $mailStatus = $providerHealth['mail']['status'];
                $pushStatus = $providerHealth['push']['status'];
                $waStatus = $providerHealth['whatsapp']['status'];
            @endphp
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Mail Health</p>
                <p class="mt-1 text-sm font-semibold {{ $mailStatus === 'healthy' ? 'text-emerald-600' : ($mailStatus === 'disabled' ? 'text-slate-500' : ($mailStatus === 'warning' ? 'text-amber-600' : 'text-rose-600')) }}">{{ ucfirst($mailStatus) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Push / WhatsApp</p>
                <p class="mt-1 text-sm font-semibold {{ ($pushStatus === 'healthy' && $waStatus === 'healthy') ? 'text-emerald-600' : 'text-amber-600' }}">
                    Push: {{ ucfirst($pushStatus) }} | WA: {{ ucfirst($waStatus) }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Provider Health</h2>
                <div class="mt-4 space-y-4">
                    @foreach(['mail' => 'Mail', 'push' => 'Push', 'whatsapp' => 'WhatsApp'] as $key => $label)
                        @php $state = $providerHealth[$key]['status']; @endphp
                        <div class="rounded-lg border border-slate-200 p-3">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-slate-900">{{ $label }}</p>
                                <span class="text-xs font-semibold {{ $state === 'healthy' ? 'text-emerald-600' : ($state === 'disabled' ? 'text-slate-500' : ($state === 'warning' ? 'text-amber-600' : 'text-rose-600')) }}">
                                    {{ ucfirst($state) }}
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $providerHealth[$key]['message'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-4 sm:p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Failed Jobs Monitor</h2>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" wire:click="retryAllFailedJobs"
                                class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                Retry All
                            </button>
                            <button type="button" wire:click="flushFailedJobs"
                                class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                Clear Failed
                            </button>
                        </div>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="failedSearch"
                        placeholder="Search failed jobs by queue/exception/id"
                        class="mt-3 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Queue</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Failed At</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Error</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($failedJobs as $job)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-semibold text-slate-900">#{{ $job->id }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $job->queue }}</td>
                                    <td class="px-4 py-3 text-xs text-slate-500">{{ \Carbon\Carbon::parse($job->failed_at)->format('d M Y, h:i A') }}</td>
                                    <td class="px-4 py-3 text-xs text-slate-600">
                                        {{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $job->exception), 120) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button type="button" wire:click="retryFailedJob({{ $job->id }})"
                                                class="rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                Retry
                                            </button>
                                            <button type="button" wire:click="forgetFailedJob({{ $job->id }})"
                                                class="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                                Remove
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">No failed jobs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($failedJobs instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="border-t border-slate-200 p-4">
                        {{ $failedJobs->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Queue Distribution</h2>
            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($queueStats['queues'] as $queue)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500">{{ $queue->queue }}</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">{{ $queue->total }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No pending jobs in queue.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
