<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Platform Students</h1>
            <p class="mt-1 text-sm text-gray-500">View students across all libraries and their memberships.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <div class="relative max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search students..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg bg-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Library Memberships</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($students as $student)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $student->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div>{{ $student->phone ?: '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $student->email ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="flex flex-wrap gap-2">
                                    @forelse($student->memberships as $membership)
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 text-indigo-700 px-2.5 py-1 text-xs font-semibold">
                                        {{ $membership->tenant?->name ?? 'Unknown' }}
                                    </span>
                                    @empty
                                    <span class="text-xs text-gray-400">No memberships</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $student->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.students.show', $student) }}" wire:navigate
                                        class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                                        View
                                    </a>
                                    <button wire:click="openMembershipManager({{ $student->id }})"
                                        class="inline-flex items-center rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                        Manage Libraries
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No students found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-white">
                {{ $students->links() }}
            </div>
        </div>
    </div>

    @if($isMembershipModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 px-4">
        <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Manage Library Memberships</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $membershipStudentName }}</p>
                </div>
                <button wire:click="closeMembershipManager" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>

            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                <p class="text-sm text-gray-600">Select the libraries this student should be enrolled in.</p>
                @error('membershipTenantIds')
                <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($tenants as $tenant)
                    <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                        <input type="checkbox" value="{{ $tenant->id }}" wire:model="membershipTenantIds"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-800">{{ $tenant->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-2">
                <button wire:click="closeMembershipManager"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button wire:click="syncMemberships"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Save Memberships
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
