<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Action Header -->
        <div class="mb-8 flex items-center justify-between no-print">
            <div>
                <a href="{{ route('library.students') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                    <svg class="mr-1 mt-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Back to Students
                </a>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 tracking-tight">Student ID Card</h1>
            </div>

            <button onclick="window.print()"
                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">
                <svg class="-ml-0.5 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0v3.396c0 .896.65 1.636 1.54 1.763 2.9.46 5.86.46 8.76 0 .89-.127 1.54-.867 1.54-1.763V8.125m-10.5 0l1.22-3.66m7.06 3.66l-1.22-3.66M8.74 3h6.52c.662 0 1.18.568 1.12 1.227L15.93 6.75h-7.86L7.62 4.227C7.68 3.568 8.078 3 8.74 3z" />
                </svg>
                Print Card
            </button>
        </div>

        <!-- Printable Card Container -->
        <div class="print-container flex justify-center">

            <div
                class="card-wrapper w-[340px] bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 relative">

                <!-- Background Decoration -->
                <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-indigo-600 to-indigo-800"></div>
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>

                <!-- Library Header -->
                <div class="relative pt-6 px-6 text-center">
                    <h2 class="text-lg font-bold text-white tracking-wide uppercase">{{ Auth::user()->tenant->name ??
                        'Library' }}</h2>
                    <p class="text-indigo-100 text-xs mt-0.5 tracking-wider font-medium">Official Access Pass</p>
                </div>

                <!-- Profile Photo -->
                <div class="relative mt-6 flex justify-center">
                    <div class="h-24 w-24 rounded-full bg-white p-1 shadow-md">
                        <div
                            class="h-full w-full rounded-full bg-gray-100 flex items-center justify-center border border-gray-200">
                            <span class="text-gray-400 font-bold text-3xl">{{ substr($student->name, 0, 1) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Student Details -->
                <div class="relative pt-4 px-6 text-center">
                    <h1 class="text-xl font-bold text-gray-900">{{ $student->name }}</h1>
                    <p class="text-sm font-medium text-indigo-600 mt-1">ID: #{{ str_pad($student->id, 5, '0',
                        STR_PAD_LEFT) }}</p>

                    <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-y-3 gap-x-2 text-left">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Phone</p>
                            <p class="text-xs font-medium text-gray-800 tracking-tight">{{ $student->phone }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Seat No.</p>
                            <p
                                class="text-xs font-semibold text-gray-800 bg-gray-50 inline-block px-1.5 py-0.5 rounded">
                                {{ $student->assignedSeat->name ?? 'Unassigned' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Current Plan</p>
                            <p class="text-xs font-medium text-gray-800">{{
                                optional($student->activeSubscription)->plan->name ?? 'No Active Subscription' }}</p>
                        </div>
                    </div>

                    <!-- QR Code Validation -->
                    <div class="mt-6 flex flex-col items-center">
                        <div class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 inline-block">
                            {!! $qrCode !!}
                        </div>
                        <p class="text-[9px] text-gray-400 mt-2 mb-4">Scan to verify digital profile</p>
                    </div>
                </div>

                <!-- Footer Strip -->
                <div class="bg-gray-50 px-6 py-3 text-center border-t border-gray-100">
                    <p class="text-[10px] text-gray-400 font-medium">Non-transferable. Must be presented upon request.
                    </p>
                </div>
            </div>

        </div>
    </div>
    <style>
        @media print {
            body {
                background-color: white !important;
                margin: 0;
                padding: 0;
            }

            .bg-gray-50\/50 {
                background-color: transparent !important;
            }

            .max-w-3xl {
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                display: flex;
                justify-content: flex-start;
                align-items: flex-start;
            }

            .card-wrapper {
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Hide navbar/sidebar components inherited from layout */
            header,
            nav,
            aside,
            .sidebar {
                display: none !important;
            }

            main {
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</div>