<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Seating Arrangement</h1>
                <p class="mt-1 text-sm text-gray-500">Manage all your seats, track occupancy, and generate new seating
                    plans.</p>
            </div>

            <div class="flex items-center gap-3">
                <button wire:click="openBulkModal"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5zm0 9.75h2.25A2.25 2.25 0 0010.5 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25zm9.75-9.75H18a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0 0018 3.75h-2.25A2.25 2.25 0 0013.5 6v2.25a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    Bulk Generate
                </button>
                <button wire:click="openModal"
                    class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Seat
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
            <div
                class="overflow-hidden rounded-xl bg-white px-4 py-5 shadow-sm border border-gray-100 sm:p-6 text-center">
                <dt class="truncate text-sm font-medium text-gray-500">Total Seats</dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $stats['total'] }}</dd>
            </div>
            <div
                class="overflow-hidden rounded-xl bg-white px-4 py-5 shadow-sm border border-blue-100 sm:p-6 text-center relative overflow-hidden">
                <div class="absolute right-0 top-0 w-2 h-full bg-blue-500"></div>
                <dt class="truncate text-sm font-medium text-blue-600">Occupied Seats</dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $stats['occupied'] }}</dd>
            </div>
            <div
                class="overflow-hidden rounded-xl bg-white px-4 py-5 shadow-sm border border-green-100 sm:p-6 text-center relative overflow-hidden">
                <div class="absolute right-0 top-0 w-2 h-full bg-green-500"></div>
                <dt class="truncate text-sm font-medium text-green-600">Vacant Seats</dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $stats['vacant'] }}</dd>
            </div>
        </dl>

        <!-- Search + Filter -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="relative rounded-md shadow-sm max-w-md w-full">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input wire:model.live="search" type="text"
                    class="block w-full rounded-lg border-0 py-2.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    placeholder="Search seats or student names...">
            </div>
            <select wire:model.live="occupancyFilter"
                class="rounded-lg border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                <option value="all">All Seats</option>
                <option value="occupied">Occupied</option>
                <option value="vacant">Vacant</option>
            </select>
        </div>

        <!-- Seats Grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @forelse ($seats as $seat)
            <div
                class="relative flex flex-col items-center space-y-3 rounded-xl border {{ $seat->user_id ? 'border-blue-200 bg-blue-50/30' : 'border-gray-200 bg-white' }} px-4 py-5 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 hover:border-indigo-400 transition-colors">

                <div class="flex-shrink-0 relative">
                    <!-- Setup Icon / Graphic based on status -->
                    @if($seat->user_id)
                    <div
                        class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center border-4 border-white shadow-sm">
                        <span class="text-blue-600 font-bold text-lg">{{ substr($seat->user->name, 0, 1) }}</span>
                    </div>
                    <!-- Status indicator dot -->
                    <span
                        class="absolute bottom-0 right-0 block h-4 w-4 rounded-full bg-blue-500 ring-2 ring-white"></span>
                    @else
                    <div
                        class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center border-4 border-white shadow-sm">
                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5" />
                        </svg>
                    </div>
                    <span
                        class="absolute bottom-0 right-0 block h-4 w-4 rounded-full bg-green-400 ring-2 ring-white"></span>
                    @endif
                </div>

                <div class="text-center w-full">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-bold text-gray-900">{{ $seat->name }}</p>
                    @if($seat->user_id)
                    <p class="truncate text-xs text-blue-600 font-medium mt-1">{{ $seat->user->name }}</p>
                    <p class="text-xs text-gray-500 mt-1.5 line-clamp-1 flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.069-3.769-6.662-6.662l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        {{ $seat->user->phone ?? 'N/A' }}
                    </p>
                    @else
                    <p class="truncate text-xs text-green-600 font-medium mt-1">Available</p>
                    <p class="text-xs text-gray-400 mt-1.5">Ready to assign</p>
                    @endif
                </div>

                <!-- Actions Dropdown / Menu -->
                <div
                    class="pt-3 mt-1 w-full border-t <?php echo $seat->user_id ? 'border-blue-100' : 'border-gray-100' ?> flex justify-center gap-2 relative z-10">
                    @if($seat->user_id)
                    <button wire:click="unassign({{ $seat->id }})"
                        wire:confirm="Are you sure you want to unassign this seat?"
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-white text-gray-500 hover:text-orange-600 hover:bg-orange-50 transition-colors shadow-sm"
                        title="Unassign">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                        </svg>
                    </button>
                    @endif
                    <button wire:click="edit({{ $seat->id }})"
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-white text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm"
                        title="Edit Seat">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button wire:click="delete({{ $seat->id }})"
                        wire:confirm="Are you sure you want to delete this seat?"
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-white text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors shadow-sm"
                        title="Remove Seat">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center bg-white rounded-xl border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6 13.5V10.5a2.25 2.25 0 012.25-2.25h1.5m6 0h1.5a2.25 2.25 0 012.25 2.25v3m-15 0h15m-15 0v3m15 0v3a2.25 2.25 0 01-2.25 2.25h-10.5a2.25 2.25 0 01-2.25-2.25v-3m0 0h15M6 13.5v-3m12 3v-3m-9-3h6" />
                </svg>
                <h3 class="mt-4 text-sm font-semibold text-gray-900">No seats found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new seat or generating in bulk.</p>
                <div class="mt-6 flex justify-center gap-3">
                    <button wire:click="openModal"
                        class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Single Seat
                    </button>
                    <button wire:click="openBulkModal"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        Bulk Generate
                    </button>
                </div>
            </div>
            @endforelse
        </div>

        @if($seats->hasPages())
        <div class="mt-8">
            {{ $seats->links() }}
        </div>
        @endif

        <!-- Add/Edit Single Seat Modal -->
        <div x-data="{ open: @entangle('isModalOpen') }" x-show="open" style="display: none;" class="relative z-50"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

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
                        class="relative transform overflow-hidden rounded-xl bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md sm:p-6 text-left">

                        <div class="absolute right-0 top-0 pr-4 pt-4">
                            <button type="button" wire:click="closeModal"
                                class="rounded-md bg-white text-gray-400 hover:text-gray-500 transition-colors">
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
                                        d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                                    {{ $seatId ? 'Edit Seat' : 'Add New Seat' }}
                                </h3>

                                <form wire:submit="save" class="mt-6 space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Seat
                                            Name / Number <span class="text-red-500">*</span></label>
                                        <div class="mt-2 text-left">
                                            <input type="text" wire:model="name" id="name"
                                                class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors"
                                                placeholder="e.g. A1, Seat-05, etc." required>
                                        </div>
                                        @error('name') <span class="text-sm text-red-500 mt-1 block text-left">{{
                                            $message }}</span> @enderror
                                    </div>

                                    <div class="mt-6 sm:mt-8 sm:flex sm:flex-row-reverse border-t border-gray-100 pt-4">
                                        <button type="submit"
                                            class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto transition-colors">
                                            {{ $seatId ? 'Save Options' : 'Add Seat' }}
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

        <!-- Bulk Generate Modal -->
        <div x-data="{ open: @entangle('isBulkModalOpen') }" x-show="open" style="display: none;" class="relative z-50"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="open" @click.away="open = false; $wire.closeBulkModal()"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-xl bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md sm:p-6 text-left">

                        <div class="absolute right-0 top-0 pr-4 pt-4">
                            <button type="button" wire:click="closeBulkModal"
                                class="rounded-md bg-white text-gray-400 hover:text-gray-500 transition-colors">
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
                                        d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5zm0 9.75h2.25A2.25 2.25 0 0010.5 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25zm9.75-9.75H18a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0 0018 3.75h-2.25A2.25 2.25 0 0013.5 6v2.25a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                                    Bulk Generate Seats
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">Automatically create multiple sequential seats
                                    instantly.</p>

                                <form wire:submit="generateBulk" class="mt-6 space-y-4 text-left">
                                    <div>
                                        <label for="prefix"
                                            class="block text-sm font-medium leading-6 text-gray-900">Seat Prefix <span
                                                class="text-red-500">*</span></label>
                                        <div class="mt-2 text-left">
                                            <input type="text" wire:model="prefix" id="prefix"
                                                class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors"
                                                placeholder="e.g. Seat-, A-, Desk " required>
                                            <p class="mt-1 text-xs text-gray-500">Example: 'Seat-' will create Seat-01,
                                                Seat-02, etc.</p>
                                        </div>
                                        @error('prefix') <span class="text-sm text-red-500 mt-1 block text-left">{{
                                            $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="count"
                                            class="block text-sm font-medium leading-6 text-gray-900">Number of Seats
                                            <span class="text-red-500">*</span></label>
                                        <div class="mt-2 text-left">
                                            <input type="number" wire:model="count" id="count" min="1" max="100"
                                                class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors"
                                                required>
                                        </div>
                                        @error('count') <span class="text-sm text-red-500 mt-1 block text-left">{{
                                            $message }}</span> @enderror
                                    </div>

                                    <div class="mt-6 sm:mt-8 sm:flex sm:flex-row-reverse border-t border-gray-100 pt-4">
                                        <button type="submit"
                                            class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto transition-colors">
                                            Generate
                                        </button>
                                        <button type="button" wire:click="closeBulkModal"
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

    </div>
</div>
