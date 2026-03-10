<div class="py-8" x-data x-init="
    const makeFingerprint = () => {
        const raw = [navigator.userAgent, navigator.language, window.screen.width + 'x' + window.screen.height, Intl.DateTimeFormat().resolvedOptions().timeZone].join('|');
        return btoa(unescape(encodeURIComponent(raw))).replace(/=+$/,'').slice(0, 64);
    };
    $wire.set('deviceFingerprint', makeFingerprint());
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((pos) => {
            $wire.set('operatorLatitude', Number(pos.coords.latitude.toFixed(7)));
            $wire.set('operatorLongitude', Number(pos.coords.longitude.toFixed(7)));
        });
    }
">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between" data-tour="attendance.header">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Mark Attendance</h1>
                <p class="mt-1 text-sm text-slate-500">Search students, select multiple, then mark Present/Absent/Login/Clockout.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button"
                    x-on:click="window.dispatchEvent(new CustomEvent('start-tour', { detail: { title: 'Mark Attendance Tour', steps: [
                        'Select date and search students from the top filters.',
                        'Use bulk actions after selecting one or more students.',
                        'Conflict resolver appears when overlapping attendance is detected.',
                        'Use Attendance View for audit and historical records.'
                    ] } }))"
                    class="inline-flex items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                    Quick Tour
                </button>
                <a href="{{ route('library.attendance') }}" wire:navigate
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Attendance View
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Security Checks</p>
                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                    <span class="inline-flex rounded-full px-2.5 py-1 font-semibold {{ $securityEnabled['ip'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">IP {{ $securityEnabled['ip'] ? 'On' : 'Off' }}</span>
                    <span class="inline-flex rounded-full px-2.5 py-1 font-semibold {{ $securityEnabled['device'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">Device {{ $securityEnabled['device'] ? 'On' : 'Off' }}</span>
                    <span class="inline-flex rounded-full px-2.5 py-1 font-semibold {{ $securityEnabled['geofence'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">Geofence {{ $securityEnabled['geofence'] ? 'On' : 'Off' }}</span>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Operator Device</p>
                <p class="mt-2 truncate text-xs text-slate-600">{{ $deviceFingerprint ? substr($deviceFingerprint, 0, 22).'...' : 'Fingerprint pending...' }}</p>
            </div>
            <div class="rounded-xl border {{ $missedClockOutCount > 0 ? 'border-amber-200 bg-amber-50' : 'border-slate-200 bg-white' }} px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide {{ $missedClockOutCount > 0 ? 'text-amber-700' : 'text-slate-500' }}">Missed Clockouts</p>
                <p class="mt-2 text-sm font-semibold {{ $missedClockOutCount > 0 ? 'text-amber-700' : 'text-slate-700' }}">{{ $missedClockOutCount }} pending from previous dates</p>
            </div>
        </div>

        @if ($inlineMessage)
            <div class="rounded-xl border px-4 py-3 text-sm flex items-start justify-between gap-3
                {{ $inlineType === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : '' }}
                {{ $inlineType === 'error' ? 'border-rose-200 bg-rose-50 text-rose-700' : '' }}
                {{ $inlineType === 'warning' ? 'border-amber-200 bg-amber-50 text-amber-700' : '' }}">
                <span>{{ $inlineMessage }}</span>
                <button type="button" wire:click="clearInlineMessage" class="text-xs font-semibold uppercase tracking-wide opacity-80 hover:opacity-100">
                    Dismiss
                </button>
            </div>
        @endif

        @if(!empty($bulkConflicts))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4" data-tour="attendance.conflicts">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Bulk conflict resolver</p>
                        <p class="mt-1 text-xs text-amber-700">Some rows failed due to overlap/security/validation. Resolve quickly:</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" wire:click="markConflictRowsAbsent"
                            class="rounded-lg bg-amber-600 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-500">
                            Mark Failed as Absent
                        </button>
                        <button type="button" wire:click="removeConflictRowsFromSelection"
                            class="rounded-lg border border-amber-300 bg-white px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-100">
                            Remove Failed from Selection
                        </button>
                        <button type="button" wire:click="clearBulkConflicts"
                            class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            Dismiss
                        </button>
                    </div>
                </div>
                <div class="mt-3 max-h-48 overflow-auto rounded-xl border border-amber-200 bg-white">
                    <ul class="divide-y divide-amber-100 text-xs">
                        @foreach($bulkConflicts as $row)
                            @php
                                $student = $students->firstWhere('id', $row['student_id']);
                            @endphp
                            <li class="px-3 py-2 text-amber-800">
                                <span class="font-semibold">{{ $student?->name ?? ('Student #'.$row['student_id']) }}:</span>
                                {{ $row['message'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" data-tour="attendance.filters">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Attendance Date</label>
                    <input type="date" wire:model.live="attendanceDate"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Search Student</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Name or phone"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex items-end gap-2">
                    <button wire:click="selectAllVisible" type="button"
                        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Select All
                    </button>
                    <button wire:click="clearSelection" type="button"
                        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Clear
                    </button>
                </div>
            </div>

            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3" data-tour="attendance.bulk-actions">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Bulk Actions ({{ count($selectedStudentIds) }} selected)</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <button wire:click="markSelected('present')" type="button"
                        class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">
                        Mark Present
                    </button>
                    <button wire:click="markSelected('absent')" type="button"
                        class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-500">
                        Mark Absent
                    </button>
                    <button wire:click="loginSelected" type="button"
                        class="rounded-lg bg-sky-600 px-3 py-2 text-xs font-semibold text-white hover:bg-sky-500">
                        Login (Check In)
                    </button>
                    <button wire:click="clockOutSelected" type="button"
                        class="rounded-lg bg-amber-600 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-500">
                        Clockout (Check Out)
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" data-tour="attendance.table">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Select</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Check In</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Check Out</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($students as $student)
                            @php
                                $record = $existingAttendances->get($student->id);
                                $status = $record?->status;
                                $checkIn = $record?->check_in ? \Carbon\Carbon::parse($record->check_in)->format('H:i') : '';
                                $checkOut = $record?->check_out ? \Carbon\Carbon::parse($record->check_out)->format('H:i') : '';
                                $anomaly = (array) ($record?->anomaly_flags ?? []);
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <input type="checkbox" wire:model="selectedStudentIds" value="{{ $student->id }}"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-semibold text-slate-900">{{ $student->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $student->phone }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($status)
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $status === 'present' ? 'bg-emerald-50 text-emerald-700' : ($status === 'absent' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                            @if(!empty($anomaly['pattern_abuse']))
                                                <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">Anomaly</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Not Marked</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $checkIn ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $checkOut ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <button wire:click="markAttendance({{ $student->id }}, 'present')" type="button"
                                            class="rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                                            Present
                                        </button>
                                        <button wire:click="markAttendance({{ $student->id }}, 'absent')" type="button"
                                            class="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                            Absent
                                        </button>
                                        <button wire:click="login({{ $student->id }})" type="button"
                                            class="rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-100">
                                            Login
                                        </button>
                                        <button wire:click="clockOut({{ $student->id }})" type="button"
                                            class="rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100">
                                            Clockout
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="mt-3 text-sm font-semibold text-slate-700">No students found for your library/search.</p>
                                        <p class="mt-1 text-xs text-slate-500">Try clearing search filters or add students first.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
