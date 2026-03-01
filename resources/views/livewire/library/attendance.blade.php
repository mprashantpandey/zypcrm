<div class="py-10">
    <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-bold leading-6 text-gray-900">Attendance</h1>
                <p class="mt-2 text-sm text-gray-600">Manage daily attendance and time-tracking for your students.</p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex items-center gap-4">
                <div class="relative">
                    <label for="attendanceDate" class="sr-only">Date</label>
                    <input type="date" id="attendanceDate" wire:model.live="attendanceDate"
                        class="block w-full rounded-lg border-0 py-2.5 pl-3 pr-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 font-medium cursor-pointer">
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
                        placeholder="Search students...">
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
                                        Student
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Check In
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Check Out
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($students as $student)
                                @php
                                $record = $existingAttendances->get($student->id);
                                $status = $record ? $record->status : null;
                                $checkIn = $record ? $record->check_in : null;
                                $checkOut = $record ? $record->check_out : null;
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-10 w-10 shrink-0 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                                {{ substr($student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $student->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $student->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4">
                                        <div class="flex items-center gap-2">
                                            <button wire:click="markAttendance({{ $student->id }}, 'present')"
                                                class="inline-flex items-center justify-center rounded-md px-3 py-1.5 text-xs font-semibold transition-all duration-200 shadow-sm ring-1 ring-inset {{ $status === 'present' ? 'bg-emerald-500 text-white ring-emerald-500 shadow-emerald-500/20' : 'bg-white text-gray-700 ring-gray-300 hover:bg-emerald-50 hover:text-emerald-700 hover:ring-emerald-200' }}">
                                                Present
                                            </button>
                                            <button wire:click="markAttendance({{ $student->id }}, 'absent')"
                                                class="inline-flex items-center justify-center rounded-md px-3 py-1.5 text-xs font-semibold transition-all duration-200 shadow-sm ring-1 ring-inset {{ $status === 'absent' ? 'bg-rose-500 text-white ring-rose-500 shadow-rose-500/20' : 'bg-white text-gray-700 ring-gray-300 hover:bg-rose-50 hover:text-rose-700 hover:ring-rose-200' }}">
                                                Absent
                                            </button>
                                            <button wire:click="markAttendance({{ $student->id }}, 'leave')"
                                                class="inline-flex items-center justify-center rounded-md px-3 py-1.5 text-xs font-semibold transition-all duration-200 shadow-sm ring-1 ring-inset {{ $status === 'leave' ? 'bg-amber-500 text-white ring-amber-500 shadow-amber-500/20' : 'bg-white text-gray-700 ring-gray-300 hover:bg-amber-50 hover:text-amber-700 hover:ring-amber-200' }}">
                                                Leave
                                            </button>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4">
                                        @if($status === 'present')
                                        <input type="time" title="Check In Time"
                                            value="{{ $checkIn ? \Carbon\Carbon::parse($checkIn)->format('H:i') : '' }}"
                                            wire:change="updateTime({{ $student->id }}, 'check_in', $event.target.value)"
                                            class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-white cursor-pointer hover:bg-gray-50 transition-colors w-32">
                                        @else
                                        <span
                                            class="inline-flex items-center justify-center h-9 w-32 rounded-md bg-gray-50 border border-gray-100 text-gray-400 text-sm font-medium">N/A</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4">
                                        @if($status === 'present')
                                        <input type="time" title="Check Out Time"
                                            value="{{ $checkOut ? \Carbon\Carbon::parse($checkOut)->format('H:i') : '' }}"
                                            wire:change="updateTime({{ $student->id }}, 'check_out', $event.target.value)"
                                            class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-white cursor-pointer hover:bg-gray-50 transition-colors w-32">
                                        @else
                                        <span
                                            class="inline-flex items-center justify-center h-9 w-32 rounded-md bg-gray-50 border border-gray-100 text-gray-400 text-sm font-medium">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <h3 class="mt-4 text-sm font-semibold text-gray-900">No students found</h3>
                                        <p class="mt-1 text-sm text-gray-500">Add some students to start tracking
                                            attendance.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>