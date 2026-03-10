<div class="py-10 bg-gradient-to-br from-slate-50 to-gray-100/60 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="mb-8 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Library Settings</h1>
                <p class="mt-1 text-sm text-gray-500">Manage your library's public profile, contact information, and
                    operating hours.</p>
            </div>
            <div
                class="hidden sm:flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 shadow-sm">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-xs font-medium text-gray-600">
                    @if($activeTab === 'profile')
                        Public Profile
                    @elseif($activeTab === 'hours')
                        Operating Hours
                    @else
                        Attendance Security
                    @endif
                </span>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">

            <!-- ── Sidebar Navigation ─────────────────────── -->
            <div class="w-full lg:w-64 flex-shrink-0 lg:sticky lg:top-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                    <div class="px-2 pt-4 pb-2">
                        <p class="px-2 mb-2 text-[9px] font-bold tracking-[0.15em] uppercase text-gray-400">Library
                            Profile</p>

                        @php $a = ($activeTab === 'profile'); @endphp
                        <button wire:click="switchTab('profile')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">Public
                                    Profile</span>
                                <span class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">Name,
                                    contact, address</span>
                            </span>
                        </button>

                        @php $a = ($activeTab === 'hours'); @endphp
                        <button wire:click="switchTab('hours')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0">
                                <span
                                    class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">Operating
                                    Hours</span>
                                <span class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">Weekly
                                    schedule</span>
                            </span>
                        </button>

                        @php $a = ($activeTab === 'attendance_security'); @endphp
                        <button wire:click="switchTab('attendance_security')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15v2m6.364-7.364a9 9 0 11-12.728 0 9 9 0 0112.728 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 9a3 3 0 10-6 0c0 1.3.84 2.403 2.005 2.805V13.5h1.99v-1.695A3.001 3.001 0 0015 9z" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">Attendance Security</span>
                                <span class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">IP, device, geofence</span>
                            </span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- ── Main Content Area ──────────────────────── -->
            <div class="flex-1 min-w-0">

                @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    class="mb-6 rounded-xl bg-green-50/50 border border-green-200 p-4 shadow-sm flex items-start gap-3">
                    <svg class="h-5 w-5 text-green-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-green-800">Success</h3>
                        <div class="mt-1 text-sm text-green-700">
                            {{ session('message') }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- ── Public Profile Tab ─────────────────── -->
                <div
                    class="{{ $activeTab === 'profile' ? 'block' : 'hidden' }} bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
                    <div wire:loading wire:target="saveProfile"
                        class="absolute inset-0 bg-white/50 backdrop-blur-[2px] z-50 flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    </div>

                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-base font-semibold text-gray-900">Public Profile</h3>
                        <p class="mt-1 text-sm leading-6 text-gray-500">This information will be displayed to your
                            students and on public landing pages.</p>
                    </div>
                    <div class="p-6">
                        <form wire:submit.prevent="saveProfile" class="space-y-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-900">Library Name <span
                                            class="text-red-500">*</span></label>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <input type="text" wire:model="name" required
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="E.g. XYZ Study Hall">
                                    </div>
                                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Contact Email</label>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <input type="email" wire:model="email"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="hello@library.com">
                                    </div>
                                    @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Contact Phone</label>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <input type="text" wire:model="phone"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="+1 (555) 000-0000">
                                    </div>
                                    @error('phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="col-span-full">
                                    <label class="block text-sm font-medium text-gray-900">Physical Address</label>
                                    <div class="mt-2 text-sm text-gray-500 text-sm mb-2">The full address of your
                                        library location.</div>
                                    <textarea wire:model="address" rows="3"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="123 Education St, Knowledge City, Country 12345"></textarea>
                                    @error('address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="col-span-full">
                                    <label class="block text-sm font-medium text-gray-900">Public Page Slug</label>
                                    <div class="mt-2 text-sm text-gray-500 mb-2">Students can visit
                                        <span class="font-medium text-gray-700">{{ url('/libraries') }}/your-slug</span>.
                                    </div>
                                    <input type="text" wire:model="public_slug"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="your-library-name">
                                    @error('public_slug') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="col-span-full">
                                    <label class="block text-sm font-medium text-gray-900">Public Description</label>
                                    <textarea wire:model="public_description" rows="4"
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="Write a short intro about your library, study environment, and key benefits."></textarea>
                                    @error('public_description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="col-span-full">
                                    <label class="inline-flex items-center gap-3">
                                        <input type="checkbox" wire:model="public_page_enabled"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="text-sm font-medium text-gray-900">Enable public library page</span>
                                    </label>
                                </div>
                            </div>

                            <div x-data="{ showQr: false }" class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Share Public Page</p>
                                <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center">
                                    <input readonly value="{{ $this->publicPageUrl }}"
                                        class="w-full rounded-md border-indigo-200 bg-white text-sm text-gray-700 shadow-sm">
                                    <button type="button"
                                        onclick="navigator.clipboard.writeText('{{ $this->publicPageUrl }}')"
                                        class="inline-flex items-center justify-center rounded-md border border-indigo-600 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                        Copy Link
                                    </button>
                                    <a href="{{ $this->publicPageUrl }}" target="_blank"
                                        class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500">
                                        Open Page
                                    </a>
                                    <button type="button" @click="showQr = true"
                                        class="inline-flex items-center justify-center rounded-md border border-indigo-600 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                        Show QR
                                    </button>
                                    <a href="https://wa.me/?text={{ urlencode('Check out our library page: '.$this->publicPageUrl) }}" target="_blank"
                                        class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">
                                        Share WhatsApp
                                    </a>
                                </div>

                                <div x-show="showQr" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4" @click.self="showQr = false">
                                    <div class="w-full max-w-sm rounded-2xl bg-white p-5 shadow-2xl">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-semibold text-slate-900">Scan to open public page</h4>
                                            <button type="button" @click="showQr = false"
                                                class="rounded-md px-2 py-1 text-xs font-semibold text-slate-500 hover:bg-slate-100">Close</button>
                                        </div>
                                        <div class="mt-4 flex justify-center">
                                            <img
                                                src="https://quickchart.io/qr?size=240&text={{ urlencode($this->publicPageUrl) }}"
                                                alt="Public page QR code"
                                                class="h-60 w-60 rounded-xl border border-slate-200 p-2">
                                        </div>
                                        <p class="mt-3 break-all text-center text-xs text-slate-500">{{ $this->publicPageUrl }}</p>
                                        <a href="https://wa.me/?text={{ urlencode('Check out our library page: '.$this->publicPageUrl) }}" target="_blank"
                                            class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-3 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500">
                                            Share on WhatsApp
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-white p-4">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">Library Images</p>
                                        <p class="text-xs text-slate-500">Upload photos to show on your public page gallery.</p>
                                        <p class="text-[11px] text-slate-400">Policy: JPG/PNG/WebP only, max 4MB each, security-scanned before save.</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="file" wire:model="publicImages" multiple accept="image/*"
                                            class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 sm:w-auto">
                                        <button type="button" wire:click="uploadPublicImages" wire:loading.attr="disabled"
                                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500">
                                            Upload
                                        </button>
                                    </div>
                                </div>
                                @error('publicImages') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                @error('publicImages.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                                @if(!empty($publicImages))
                                    <div class="mt-4">
                                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Preview Before Upload</p>
                                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                            @foreach($publicImages as $tempImage)
                                                <div class="overflow-hidden rounded-xl border border-indigo-200 bg-indigo-50">
                                                    <div class="aspect-[4/3] w-full overflow-hidden">
                                                        <img src="{{ $tempImage->temporaryUrl() }}"
                                                            alt="Selected preview"
                                                            class="h-full w-full object-cover object-center">
                                                    </div>
                                                    <div class="px-2 py-1.5 text-[11px] font-medium text-indigo-700">Pending upload</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                    @forelse($galleryImages as $image)
                                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                            <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100">
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}"
                                                    alt="Library image"
                                                    class="h-full w-full object-cover object-center"
                                                    loading="lazy">
                                            </div>
                                            <button type="button" wire:click="deletePublicImage({{ $image->id }})"
                                                class="w-full border-t border-slate-200 bg-white px-2 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                                Remove
                                            </button>
                                        </div>
                                    @empty
                                        <div class="col-span-full rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4 text-xs text-slate-500">
                                            No images uploaded yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="flex justify-end border-t border-gray-100 pt-4">
                                <button type="submit" wire:loading.attr="disabled"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors flex items-center gap-2">
                                    <svg wire:loading wire:target="saveProfile"
                                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Save Profile Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- ── Operating Hours Tab ────────────────── -->
                <div
                    class="{{ $activeTab === 'hours' ? 'block' : 'hidden' }} bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
                    <div wire:loading wire:target="saveOperatingHours"
                        class="absolute inset-0 bg-white/50 backdrop-blur-[2px] z-50 flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    </div>

                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-base font-semibold text-gray-900">Operating Hours</h3>
                        <p class="mt-1 text-sm leading-6 text-gray-500">Define the weekly schedule. Mark a day as
                            closed if your library doesn't operate.</p>
                    </div>

                    <div class="p-6">
                        <form wire:submit.prevent="saveOperatingHours" class="space-y-6">
                            <div class="divide-y divide-gray-100">
                                @foreach($days as $day)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-4"
                                    wire:key="day-{{ $day }}">

                                    <!-- Day Toggle -->
                                    <div class="flex items-center gap-3 w-40">
                                        <button type="button"
                                            wire:click="$set('operating_hours.{{$day}}.closed', !{{ $operating_hours[$day]['closed'] ? 'true' : 'false' }})"
                                            class="{{ !$operating_hours[$day]['closed'] ? 'bg-indigo-600' : 'bg-gray-200' }} relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                                            role="switch"
                                            aria-checked="{{ !$operating_hours[$day]['closed'] ? 'true' : 'false' }}">
                                            <span class="sr-only">Toggle {{ ucfirst($day) }}</span>
                                            <span aria-hidden="true"
                                                class="{{ !$operating_hours[$day]['closed'] ? 'translate-x-5' : 'translate-x-0' }} pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                        </button>
                                        <span class="text-sm font-semibold text-gray-900 capitalize"
                                            style="min-width: 80px;">{{ $day }}</span>
                                    </div>

                                    <!-- Time Inputs -->
                                    <div
                                        class="flex-1 flex items-center justify-start sm:justify-end gap-3 sm:gap-4 pl-14 sm:pl-0">
                                        @if(!$operating_hours[$day]['closed'])
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <input type="time" wire:model="operating_hours.{{ $day }}.open"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-medium">
                                            </div>
                                            <span
                                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider">To</span>
                                            <div class="relative">
                                                <input type="time" wire:model="operating_hours.{{ $day }}.close"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-medium">
                                            </div>
                                        </div>
                                        @else
                                        <div class="flex items-center sm:justify-end w-full sm:w-[280px]">
                                            <span
                                                class="inline-flex items-center gap-x-1.5 rounded-md px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 ring-1 ring-inset ring-red-600/20">
                                                <svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6"
                                                    aria-hidden="true">
                                                    <circle cx="3" cy="3" r="3" />
                                                </svg>
                                                Library Closed
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    @error('operating_hours.'.$day.'.open') <span
                                        class="text-xs text-red-500 block pl-6 mt-1">{{ $message }}</span> @enderror
                                    @error('operating_hours.'.$day.'.close') <span
                                        class="text-xs text-red-500 block pl-6 mt-1">{{ $message }}</span> @enderror
                                </div>
                                @endforeach
                            </div>

                            <div class="flex justify-end border-t border-gray-100 pt-4">
                                <button type="submit" wire:loading.attr="disabled"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors flex items-center gap-2">
                                    <svg wire:loading wire:target="saveOperatingHours"
                                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Save Timings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ── Attendance Security Tab ─────────────── -->
                <div
                    class="{{ $activeTab === 'attendance_security' ? 'block' : 'hidden' }} bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
                    <div wire:loading wire:target="saveAttendanceSecurity"
                        class="absolute inset-0 bg-white/50 backdrop-blur-[2px] z-50 flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    </div>

                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-base font-semibold text-gray-900">Attendance Security</h3>
                        <p class="mt-1 text-sm leading-6 text-gray-500">Protect attendance actions using optional IP allowlist, device lock, and geofence checks.</p>
                    </div>

                    <div class="p-6">
                        <form wire:submit.prevent="saveAttendanceSecurity" class="space-y-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="sm:col-span-2 rounded-lg border border-slate-200 bg-slate-50 p-4">
                                    <label class="inline-flex items-center gap-3">
                                        <input type="checkbox" wire:model="attendance_enforce_ip"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="text-sm font-medium text-gray-900">Enforce allowed IP list for attendance actions</span>
                                    </label>
                                    <textarea wire:model="attendance_allowed_ips" rows="3"
                                        class="mt-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="One IP or CIDR per line, e.g.&#10;192.168.1.22&#10;103.44.11.0/24"></textarea>
                                    @error('attendance_allowed_ips') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="sm:col-span-2 rounded-lg border border-slate-200 bg-slate-50 p-4">
                                    <label class="inline-flex items-center gap-3">
                                        <input type="checkbox" wire:model="attendance_enforce_device"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="text-sm font-medium text-gray-900">Enforce single registered browser/device per library</span>
                                    </label>
                                    <p class="mt-2 text-xs text-slate-500">When enabled, first attendance action registers the current device fingerprint for this library.</p>
                                </div>

                                <div class="sm:col-span-2 rounded-lg border border-slate-200 bg-slate-50 p-4">
                                    <label class="inline-flex items-center gap-3">
                                        <input type="checkbox" wire:model="attendance_geofence_enabled"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="text-sm font-medium text-gray-900">Enable geofence validation</span>
                                    </label>

                                    <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Latitude</label>
                                            <input type="number" step="0.0000001" wire:model="attendance_geofence_latitude"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="28.6139000">
                                            @error('attendance_geofence_latitude') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Longitude</label>
                                            <input type="number" step="0.0000001" wire:model="attendance_geofence_longitude"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="77.2090000">
                                            @error('attendance_geofence_longitude') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Radius (meters)</label>
                                            <input type="number" min="30" max="5000" wire:model="attendance_geofence_radius_meters"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('attendance_geofence_radius_meters') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end border-t border-gray-100 pt-4">
                                <button type="submit" wire:loading.attr="disabled"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors">
                                    Save Security Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
