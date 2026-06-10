<div>
    <div class="mb-6">
        <div class="mb-3">
            <h3 class="text-2xl font-semibold">{{ __('Payment Gateway') }}</h3>
            <p class="text-sm text-gray-500">{{ __('ToyyibPay (FPX) credentials used to collect subscription payments.') }}</p>
        </div>
    </div>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="mt-5 md:col-span-2 md:mt-0">
            <form wire:submit.prevent="submit" method="POST">
                <div class="shadow sm:overflow-hidden sm:rounded-md">
                    <div class="space-y-6 bg-white px-4 py-5 sm:p-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('User Secret Key') }}</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input
                                    wire:model.defer="secret_key"
                                    type="text"
                                    class="block w-full flex-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="{{ __('ToyyibPay user secret key') }}"
                                >
                            </div>
                            @error('secret_key')
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Category Code') }}</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input
                                    wire:model.defer="category_code"
                                    type="text"
                                    class="block w-full flex-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="{{ __('ToyyibPay category code') }}"
                                >
                            </div>
                            @error('category_code')
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <input wire:model.defer="is_sandbox" id="is_sandbox" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-slate-900 focus:ring-indigo-500">
                            <label for="is_sandbox" class="text-sm font-medium text-gray-700">{{ __('Sandbox mode (dev.toyyibpay.com)') }}</label>
                        </div>

                        <div class="flex items-center gap-2">
                            <input wire:model.defer="is_active" id="gw_is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-slate-900 focus:ring-indigo-500">
                            <label for="gw_is_active" class="text-sm font-medium text-gray-700">{{ __('Active') }}</label>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-4 flex justify-end sm:px-6 border-t">
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-slate-900 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="md:col-span-1"></div>
    </div>
</div>
