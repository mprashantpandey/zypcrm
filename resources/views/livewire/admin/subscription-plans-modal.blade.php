<div class="fixed z-50 inset-0 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
    <div class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div
            class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-headline">
                            {{ $plan_id ? 'Edit Plan' : 'Create New Plan' }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Configure pricing and limits for this package.</p>

                        <div class="mt-6 space-y-4">
                            <!-- Name -->
                            <div>
                                <label for="plan_name" class="block text-sm font-medium text-gray-700">Plan Name</label>
                                <input type="text" id="plan_name" wire:model="name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm"
                                    placeholder="e.g. Pro Tier">
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="plan_desc"
                                    class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="plan_desc" wire:model="description" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm"
                                    placeholder="Brief plan description"></textarea>
                                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Price -->
                                <div>
                                    <label for="plan_price"
                                        class="block text-sm font-medium text-gray-700">Price</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">{{ $global_currency }}</span>
                                        </div>
                                        <input type="number" step="0.01" id="plan_price" wire:model="price"
                                            class="block w-full pl-7 rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 sm:text-sm"
                                            placeholder="0.00">
                                    </div>
                                    @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Billing Cycle -->
                                <div>
                                    <label for="plan_cycle" class="block text-sm font-medium text-gray-700">Billing
                                        Cycle</label>
                                    <select id="plan_cycle" wire:model="billing_cycle"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                                        <option value="monthly">Monthly</option>
                                        <option value="quarterly">Quarterly</option>
                                        <option value="half_yearly">Half-Yearly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                    @error('billing_cycle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Capacity -->
                                <div>
                                    <label for="plan_students" class="block text-sm font-medium text-gray-700">Max
                                        Students</label>
                                    <input type="number" id="plan_students" wire:model="max_students"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm"
                                        placeholder="0 for unlimited">
                                    @error('max_students') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <div class="flex items-center">
                                        <button type="button"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 {{ $is_active ? 'bg-gray-900' : 'bg-gray-200' }}"
                                            role="switch" aria-checked="{{ $is_active ? 'true' : 'false' }}"
                                            wire:click="$toggle('is_active')">
                                            <span class="sr-only">Toggle active status</span>
                                            <span aria-hidden="true"
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                        </button>
                                        <span class="ml-3 text-sm text-gray-600" id="annual-billing-label">
                                            {{ $is_active ? 'Active' : 'Draft' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Stripe Price ID -->
                                <div>
                                    <label for="stripe_price_id" class="block text-sm font-medium text-gray-700">Stripe
                                        Price ID</label>
                                    <input type="text" id="stripe_price_id" wire:model="stripe_price_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm"
                                        placeholder="price_1Nxyz...">
                                    <p class="mt-1 text-xs text-gray-500">Optional. ID from Stripe Dashboard</p>
                                </div>

                                <!-- Razorpay Plan ID -->
                                <div>
                                    <label for="razorpay_plan_id"
                                        class="block text-sm font-medium text-gray-700">Razorpay Plan ID</label>
                                    <input type="text" id="razorpay_plan_id" wire:model="razorpay_plan_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm"
                                        placeholder="plan_xyz...">
                                    <p class="mt-1 text-xs text-gray-500">Optional. ID from Razorpay Dashboard</p>
                                </div>
                            </div>

                            <!-- Features -->
                            <div>
                                <label for="plan_features"
                                    class="block text-sm font-medium text-gray-700">Features</label>
                                <textarea id="plan_features" wire:model="features" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm font-mono text-xs"
                                    placeholder="Unlimited readers&#10;Premium support&#10;Custom domain..."></textarea>
                                <p class="mt-1 text-xs text-gray-500">Enter one feature per line.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50/80 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                <button wire:click.prevent="store()" type="button"
                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-gray-900 text-base font-medium text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    {{ $plan_id ? 'Save changes' : 'Create plan' }}
                </button>
                <button wire:click="closeModal()" type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
