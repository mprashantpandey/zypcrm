<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" data-tour="students.header">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Student Management</h1>
                <p class="mt-1 text-sm text-gray-500">Manage your library's students, assign seats, and track their
                    details.</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live="search" type="text"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors"
                        placeholder="Search students...">
                </div>
                <button wire:click="exportCSV"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export
                </button>
                <button type="button"
                    x-on:click="window.dispatchEvent(new CustomEvent('start-tour', { detail: { title: 'Students Page Tour', steps: [
                        'Search students by name, phone, or email.',
                        'Use Add Student to create or link existing students from other libraries.',
                        'Choose re-admission due handling before saving a returning student.',
                        'Use Profile and ID card actions for quick operations.'
                    ] } }))"
                    class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 shadow-sm hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Quick Tour
                </button>
                <button wire:click="openModal"
                    class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Student
                </button>
            </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden text-left">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Student
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Plan & Seat
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Joined Date
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($students as $student)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div
                                            class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                        <div class="text-sm text-gray-500">ID: #{{ str_pad($student->id, 5, '0',
                                            STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $student->phone }}</div>
                                <div class="text-sm text-gray-500">{{ $student->email ?? 'No email' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1.5">
                                    @if($student->activeSubscription && $student->activeSubscription->plan)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 w-fit">
                                        Plan: {{ $student->activeSubscription->plan->name }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Expires: {{ $student->activeSubscription->end_date->format('M d, Y') }}
                                    </span>
                                    @endif

                                    @if($student->assignedSeat)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 w-fit mt-1">
                                        Seat: {{ $student->assignedSeat->name }}
                                    </span>
                                    @endif

                                    @if(!$student->activeSubscription && !$student->assignedSeat)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 w-fit">
                                        Unassigned
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $student->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('library.student.card', $student->id) }}" target="_blank"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3 inline-flex items-center"
                                    title="Print ID Card">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                    </svg>
                                    ID Card
                                </a>
                                <button wire:click="viewProfile({{ $student->id }})"
                                    class="text-emerald-600 hover:text-emerald-900 mr-3">Profile</button>
                                <button wire:click="edit({{ $student->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button wire:click="delete({{ $student->id }})"
                                    wire:confirm="Are you sure you want to remove this student? This action cannot be undone."
                                    class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900">No students found</h3>
                                    <p class="mt-1 text-sm text-gray-500">Add a new student to get started or try
                                        adjusting your search.</p>
                                    <button wire:click="openModal"
                                        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                        Add Student
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $students->links() }}
            </div>
            @endif
        </div>

        <!-- Add/Edit Modal (Tailwind UI pattern) -->
        <div x-data="{ open: @entangle('isModalOpen') }" x-show="open" style="display: none;"
            class="relative z-50 text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="open" @click.away="open = false; $wire.closeModal()"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-xl bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 text-left">

                        <div class="absolute right-0 top-0 pr-4 pt-4 block">
                            <button type="button" wire:click="closeModal"
                                class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="sm:flex sm:items-start text-left w-full">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                                    {{ $studentId ? 'Edit Student Details' : 'Add New Student' }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">Please enter the required information to register
                                    this student with your library.</p>

                                <form wire:submit="save" class="mt-6 space-y-4 text-left">
                                    <div>
                                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Full
                                            Name <span class="text-red-500">*</span></label>
                                        <div class="mt-2 text-left">
                                            <input type="text" wire:model="name" id="name"
                                                class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors"
                                                required>
                                        </div>
                                        @error('name') <span class="text-sm text-red-500 mt-1 block text-left">{{
                                            $message }}</span> @enderror
                                    </div>

                                    <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 text-left">
                                        <div>
                                            <label for="phone"
                                                class="block text-sm font-medium leading-6 text-gray-900">Phone Number
                                                <span class="text-red-500">*</span></label>
                                            <div class="mt-2">
                                                <input type="text" wire:model="phone" id="phone"
                                                    class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors"
                                                    required>
                                            </div>
                                            @error('phone') <span class="text-sm text-red-500 mt-1 block text-left">{{
                                                $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="email"
                                                class="block text-sm font-medium leading-6 text-gray-900">Email <span
                                                    class="text-gray-400 font-normal">(Optional)</span></label>
                                            <div class="mt-2">
                                                <input type="email" wire:model="email" id="email"
                                                    class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors">
                                            </div>
                                            @error('email') <span class="text-sm text-red-500 mt-1 block text-left">{{
                                                $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="text-left">
                                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                                            Account Password {!! !$studentId ? '<span class="text-red-500">*</span>' :
                                            '<span class="text-gray-400 font-normal">(Optional)</span>' !!}
                                        </label>
                                        <div class="mt-2">
                                            <input type="password" wire:model="password" id="password"
                                                class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors"
                                                placeholder="{{ $studentId ? 'Leave blank to keep current password' : 'Enter a secure password for the student' }}"
                                                {{ !$studentId ? 'required' : '' }}>
                                        </div>
                                        @error('password') <span class="text-sm text-red-500 mt-1 block text-left">{{
                                            $message }}</span> @enderror
                                    </div>

                                    @if(!$studentId)
                                    <div class="text-left rounded-lg border border-amber-200 bg-amber-50 p-3">
                                        <label for="readmissionDueMode" class="block text-sm font-medium leading-6 text-amber-900">
                                            Re-admission Dues Handling
                                        </label>
                                        <p class="mt-1 text-xs text-amber-800">
                                            If this phone/email belongs to an existing student, choose how previous dues should be handled.
                                        </p>
                                        <div class="mt-2">
                                            <select wire:model="readmissionDueMode" id="readmissionDueMode"
                                                class="block w-full rounded-lg border-amber-200 py-2 text-amber-900 shadow-sm focus:border-amber-400 focus:ring-amber-400 sm:text-sm">
                                                <option value="keep">Keep previous pending dues as-is</option>
                                                <option value="carry_forward">Carry forward dues into a new pending invoice</option>
                                                <option value="waive">Waive previous pending dues on re-admission</option>
                                            </select>
                                        </div>
                                        @error('readmissionDueMode') <span class="text-sm text-red-500 mt-1 block text-left">{{ $message }}</span> @enderror
                                    </div>
                                    @endif

                                    @if(!$studentId)
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-left rounded-lg border border-indigo-200 bg-indigo-50/50 p-3">
                                        <div>
                                            <label for="promoCodeInput" class="block text-sm font-medium leading-6 text-indigo-900">Promo Code</label>
                                            <input type="text" wire:model="promoCodeInput" id="promoCodeInput"
                                                class="mt-2 block w-full rounded-lg border-indigo-200 py-2 text-indigo-900 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 sm:text-sm"
                                                placeholder="Optional discount code">
                                            @error('promoCodeInput') <span class="text-sm text-red-500 mt-1 block text-left">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label for="referralCodeInput" class="block text-sm font-medium leading-6 text-indigo-900">Referral Code</label>
                                            <input type="text" wire:model="referralCodeInput" id="referralCodeInput"
                                                class="mt-2 block w-full rounded-lg border-indigo-200 py-2 text-indigo-900 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 sm:text-sm"
                                                placeholder="Optional student referral code">
                                            @error('referralCodeInput') <span class="text-sm text-red-500 mt-1 block text-left">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="inline-flex items-center gap-2">
                                                <input type="checkbox" wire:model="useReferralCredit"
                                                    class="rounded border-indigo-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="text-xs font-medium text-indigo-900">Apply available referral credit (if any) to first invoice</span>
                                            </label>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-4">
                                        <h4 class="text-sm font-medium text-gray-900">Subscription & Allocation</h4>

                                        <div class="text-left">
                                            <label for="libraryPlanId"
                                                class="block text-sm font-medium leading-6 text-gray-900">Assign Plan
                                                <span class="text-gray-400 font-normal">(Optional)</span></label>
                                            <div class="mt-2">
                                                <select wire:model.live="libraryPlanId" id="libraryPlanId"
                                                    class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors cursor-pointer">
                                                    <option value="">-- No Plan --</option>
                                                    @foreach($libraryPlans as $plan)
                                                    <option value="{{ $plan->id }}">{{ $plan->name }} ({{
                                                        $global_currency }}{{ $plan->price
                                                        }} / {{ $plan->duration_days }} days)</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('libraryPlanId') <span
                                                class="text-sm text-red-500 mt-1 block text-left">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        @if($libraryPlanId)
                                        <div class="text-left">
                                            <label for="planStartDate"
                                                class="block text-sm font-medium leading-6 text-gray-900">Plan Start
                                                Date</label>
                                            <div class="mt-2 text-left">
                                                <input type="date" wire:model="planStartDate" id="planStartDate"
                                                    class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors"
                                                    required>
                                            </div>
                                            @error('planStartDate') <span
                                                class="text-sm text-red-500 mt-1 block text-left">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        @endif

                                        <div class="text-left">
                                            <label for="assignedSeatId"
                                                class="block text-sm font-medium leading-6 text-gray-900">Assign Seat
                                                <span class="text-gray-400 font-normal">(Optional)</span></label>
                                            <div class="mt-2">
                                                <select wire:model="assignedSeatId" id="assignedSeatId"
                                                    class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors cursor-pointer">
                                                    <option value="">-- Unassigned --</option>
                                                    @foreach($availableSeats as $seat)
                                                    <option value="{{ $seat->id }}">Seat: {{ $seat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('assignedSeatId') <span
                                                class="text-sm text-red-500 mt-1 block text-left">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mt-6 sm:mt-8 sm:flex sm:flex-row-reverse border-t border-gray-100 pt-4">
                                        <button type="submit"
                                            class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto transition-colors">
                                            <svg wire:loading wire:target="save"
                                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            {{ $studentId ? 'Save Changes' : 'Register Student' }}
                                        </button>
                                        <button type="button" wire:click="closeModal"
                                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($isProfileModalOpen && $profileStudent)
        <!-- Profile Modal -->
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-xl font-bold leading-6 text-gray-900 mb-6 border-b pb-4 flex items-center justify-between"
                                        id="modal-title">
                                        Student Profile
                                        <button wire:click="closeProfileModal"
                                            class="text-gray-400 hover:text-gray-500 transition-colors">
                                            <span class="sr-only">Close</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <!-- Personal Info -->
                                        <div class="space-y-4">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-2xl font-bold">
                                                    {{ substr($profileStudent->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h4 class="text-lg font-bold text-gray-900">{{ $profileStudent->name
                                                        }}</h4>
                                                    <p class="text-sm text-gray-500">{{ $profileStudent->email ?? 'No
                                                        email' }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">{{ $profileStudent->phone }}</p>
                                                </div>
                                            </div>

                                            <div class="mt-6">
                                                <h5
                                                    class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">
                                                    Subscription Status</h5>
                                                @if($profileStudent && $profileStudent->activeSubscription)
                                                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                                                    <div class="flex justify-between items-start mb-2">
                                                        <span class="font-medium text-gray-900">{{
                                                            $profileStudent->activeSubscription->plan->name }}</span>
                                                        <span
                                                            class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Active</span>
                                                    </div>
                                                    <div class="text-sm text-gray-600 mt-2 space-y-1">
                                                        <div class="flex justify-between">
                                                            <span>Expires:</span>
                                                            <span
                                                                class="font-medium {{ \Carbon\Carbon::parse($profileStudent->activeSubscription->end_date)->isPast() ? 'text-rose-600' : 'text-gray-900' }}">
                                                                {{
                                                                \Carbon\Carbon::parse($profileStudent->activeSubscription->end_date)->format('M
                                                                d, Y') }}
                                                            </span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span>Assigned Seat:</span>
                                                            <span class="font-medium text-gray-900">{{
                                                                $profileStudent->assignedSeat->name ?? 'None' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                <div
                                                    class="rounded-xl bg-rose-50 border border-rose-100 p-4 text-center">
                                                    <span class="text-sm font-medium text-rose-700">No Active
                                                        Subscription</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Attendance & Leaves -->
                                        <div>
                                            <h5
                                                class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">
                                                Attendance (This Month)</h5>
                                            <div class="grid grid-cols-3 gap-3">
                                                <div
                                                    class="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center">
                                                    <div class="text-2xl font-bold text-emerald-700">{{
                                                        $profileAttendanceStats['present'] ?? 0 }}</div>
                                                    <div class="text-xs font-medium text-emerald-600 mt-1">Present</div>
                                                </div>
                                                <div
                                                    class="rounded-xl bg-rose-50 border border-rose-100 p-3 text-center">
                                                    <div class="text-2xl font-bold text-rose-700">{{
                                                        $profileAttendanceStats['absent'] ?? 0 }}</div>
                                                    <div class="text-xs font-medium text-rose-600 mt-1">Absent</div>
                                                </div>
                                                <div
                                                    class="rounded-xl bg-amber-50 border border-amber-100 p-3 text-center">
                                                    <div class="text-2xl font-bold text-amber-700">{{
                                                        $profileAttendanceStats['leave'] ?? 0 }}</div>
                                                    <div class="text-xs font-medium text-amber-600 mt-1">Leave</div>
                                                </div>
                                            </div>

                                            <div class="mt-6">
                                                <h5
                                                    class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">
                                                    Recent Leave Requests</h5>
                                                @if(count($profileRecentLeaves) > 0)
                                                <ul class="divide-y divide-gray-100 rounded-xl border border-gray-100">
                                                    @foreach($profileRecentLeaves as $leave)
                                                    <li
                                                        class="p-3 text-sm flex justify-between items-center hover:bg-gray-50 transition-colors">
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{
                                                                \Carbon\Carbon::parse($leave->start_date)->format('M d')
                                                                }} - {{
                                                                \Carbon\Carbon::parse($leave->end_date)->format('M d')
                                                                }}</div>
                                                            <div class="text-xs text-gray-500 truncate max-w-[150px]">{{
                                                                $leave->reason }}</div>
                                                        </div>
                                                        <div>
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
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                @else
                                                <p class="text-sm text-gray-500 italic">No recent leaves.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                            <button type="button" wire:click="closeProfileModal"
                                class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-medium text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-all duration-200">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
