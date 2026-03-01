<div class="py-10">
    <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-bold leading-6 text-gray-900">Leave Requests</h1>
                <p class="mt-2 text-sm text-gray-600">Review and manage student leave of absence requests.</p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex items-center gap-4">
                <div class="relative">
                    <select wire:model.live="filterStatus"
                        class="block w-full rounded-lg border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="all">All Requests</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        class="block w-full rounded-lg border-0 py-2.5 pl-10 pr-4 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="Search student...">
                </div>
            </div>
        </div>

        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-xl bg-white">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th scope="col"
                                        class="py-4 pl-4 pr-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:pl-6">
                                        Student</th>
                                    <th scope="col"
                                        class="px-3 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Dates</th>
                                    <th scope="col"
                                        class="px-3 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Reason</th>
                                    <th scope="col"
                                        class="px-3 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col" class="relative py-4 pl-3 pr-4 sm:pr-6"><span
                                            class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($leaves as $leave)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-10 w-10 shrink-0 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                                {{ substr($leave->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $leave->user->name ??
                                                    'Unknown' }}</div>
                                                <div class="text-xs text-gray-500">{{ $leave->user->phone ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                        <div>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-400">to {{
                                            \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-600 max-w-xs truncate"
                                        title="{{ $leave->reason }}">
                                        {{ $leave->reason }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4">
                                        @if($leave->status === 'approved')
                                        <span
                                            class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Approved</span>
                                        @elseif($leave->status === 'rejected')
                                        <span
                                            class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20">Rejected</span>
                                        @else
                                        <span
                                            class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Pending</span>
                                        @endif
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right sm:pr-6">
                                        @if($leave->status === 'pending')
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="updateStatus({{ $leave->id }}, 'approved')"
                                                class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-emerald-50 hover:text-emerald-700 hover:ring-emerald-200 transition-all">Approve</button>
                                            <button wire:click="updateStatus({{ $leave->id }}, 'rejected')"
                                                class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-rose-50 hover:text-rose-700 hover:ring-rose-200 transition-all">Reject</button>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="mt-4 text-sm font-semibold text-gray-900">No leave requests found
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">There are no leave requests matching your
                                            current filter.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($leaves->hasPages())
        <div class="mt-6">
            {{ $leaves->links() }}
        </div>
        @endif
    </div>
</div>
