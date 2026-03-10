<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Backup & Disaster Readiness</h1>
            <p class="mt-1 text-sm text-slate-500">One-click snapshots, restore points, environment health checks, and incident timeline.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap gap-2">
                <button wire:click="createSnapshot" type="button"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Create Snapshot
                </button>
                <button wire:click="runHealthChecks" type="button"
                    class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                    Run Health Checks
                </button>
            </div>
            @if(!empty($healthChecks))
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($healthChecks as $check)
                        <div class="rounded-lg border p-3 {{ $check['status'] === 'healthy' ? 'border-emerald-200 bg-emerald-50' : ($check['status'] === 'warning' ? 'border-amber-200 bg-amber-50' : 'border-rose-200 bg-rose-50') }}">
                            <p class="text-xs font-semibold uppercase tracking-wide {{ $check['status'] === 'healthy' ? 'text-emerald-700' : ($check['status'] === 'warning' ? 'text-amber-700' : 'text-rose-700') }}">{{ str_replace('_', ' ', $check['key']) }}</p>
                            <p class="mt-1 text-xs {{ $check['status'] === 'healthy' ? 'text-emerald-800' : ($check['status'] === 'warning' ? 'text-amber-800' : 'text-rose-800') }}">{{ $check['message'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Backup Snapshots</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Size</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Created</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($snapshots as $snapshot)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-semibold text-slate-900">{{ $snapshot->name }}</td>
                                    <td class="px-4 py-3 text-xs font-semibold {{ $snapshot->status === 'success' ? 'text-emerald-700' : ($snapshot->status === 'restoring' ? 'text-amber-700' : 'text-rose-700') }}">{{ ucfirst($snapshot->status) }}</td>
                                    <td class="px-4 py-3 text-xs text-slate-600">{{ number_format($snapshot->size_bytes / 1024, 1) }} KB</td>
                                    <td class="px-4 py-3 text-xs text-slate-600">{{ $snapshot->created_at?->format('d M Y, h:i A') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.disaster.snapshot.download', $snapshot->id) }}"
                                                class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Download
                                            </a>
                                            <button wire:click="restoreSnapshot({{ $snapshot->id }})" type="button"
                                                wire:confirm="Restore this snapshot? This will overwrite current database state."
                                                class="rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100">
                                                Restore
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No snapshots found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($snapshots instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="border-t border-slate-200 p-4">{{ $snapshots->links() }}</div>
                @endif
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Incident Timeline</h2>
                </div>
                <div class="max-h-[480px] overflow-auto">
                    @forelse($incidents as $incident)
                        <div class="border-b border-slate-100 px-4 py-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-slate-900">{{ $incident->title }}</p>
                                <span class="text-[11px] font-semibold uppercase tracking-wide {{ $incident->level === 'error' ? 'text-rose-700' : ($incident->level === 'warning' ? 'text-amber-700' : 'text-slate-600') }}">{{ $incident->level }}</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-600">{{ $incident->message }}</p>
                            <p class="mt-1 text-[11px] text-slate-400">{{ $incident->occurred_at?->format('d M Y, h:i A') }}</p>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-slate-500">No incidents recorded.</div>
                    @endforelse
                </div>
                @if($incidents instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="border-t border-slate-200 p-4">{{ $incidents->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
