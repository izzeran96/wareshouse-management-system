<div x-data="{}" @barcode-scanned.window="$wire.handleScan($event.detail.code)">
    <div class="mb-6">
        <h3 class="text-2xl font-semibold">{{ __('Scan Station') }}</h3>
        <p class="text-sm text-gray-500">{{ __('Scan an item to receive or dispatch it.') }}</p>
    </div>

    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow p-6">
            <!-- Scan input -->
            <div class="flex justify-center mb-4">
                <x-barcode-scanner :label="__('Open Scanner')" />
            </div>

            <form wire:submit.prevent="lookup" class="flex gap-2 mb-4">
                <input
                    wire:model.defer="code"
                    type="text"
                    autocomplete="off"
                    class="block w-full flex-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="{{ __('Scan or type a code…') }}"
                >
                <button type="submit" class="rounded-md bg-slate-900 py-2 px-4 text-sm font-medium text-white hover:bg-slate-800">
                    {{ __('Find') }}
                </button>
            </form>

            @if($goods)
                <div class="border rounded-md p-4 mb-4 bg-gray-50">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-lg font-semibold text-slate-900">{{ $goods['name'] }}</div>
                            <div class="text-sm text-gray-500">{{ __('Code') }}: {{ $goods['code'] }}</div>
                            @if($goods['barcode'])
                                <div class="text-sm text-gray-500">{{ __('Barcode') }}: {{ $goods['barcode'] }}</div>
                            @endif
                        </div>
                        <button wire:click.prevent="clearGoods" type="button" class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 text-sm">
                        {{ __('Current stock') }}:
                        <span class="font-semibold">{{ $goods['stock'] }} {{ $goods['unit'] }}</span>
                    </div>
                </div>

                <!-- Action -->
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <button
                        type="button"
                        wire:click="$set('action', 'receiving')"
                        class="rounded-md py-2 px-4 text-sm font-medium border {{ $action === 'receiving' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300' }}"
                    >
                        {{ __('Receive (+)') }}
                    </button>
                    <button
                        type="button"
                        wire:click="$set('action', 'dispatching')"
                        class="rounded-md py-2 px-4 text-sm font-medium border {{ $action === 'dispatching' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 border-gray-300' }}"
                    >
                        {{ __('Dispatch (-)') }}
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Quantity') }}</label>
                    <div class="mt-1 flex items-center gap-2">
                        <button type="button" wire:click="decrementQty" class="h-10 w-10 rounded-md border border-gray-300 text-lg font-bold">−</button>
                        <input
                            wire:model="quantity"
                            type="number"
                            min="1"
                            class="block w-full flex-1 text-center rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-lg"
                        >
                        <button type="button" wire:click="incrementQty" class="h-10 w-10 rounded-md border border-gray-300 text-lg font-bold">+</button>
                    </div>
                    @error('quantity')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <button
                    wire:click.prevent="submit"
                    wire:loading.attr="disabled"
                    class="w-full rounded-md bg-slate-900 py-3 px-4 text-base font-medium text-white shadow-sm hover:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60"
                >
                    {{ __('Confirm') }}
                </button>
            @endif
        </div>

        @if(count($recentScans))
            <div class="mt-6">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">{{ __('Recent') }}</h4>
                <ul class="bg-white rounded-lg shadow divide-y">
                    @foreach($recentScans as $scan)
                        <li class="flex items-center justify-between px-4 py-2 text-sm" wire:key="recent-{{ $loop->index }}">
                            <span>{{ $scan['name'] }} <span class="text-gray-400">({{ $scan['code'] }})</span></span>
                            <span class="font-medium {{ $scan['action'] === 'dispatching' ? 'text-red-600' : 'text-green-600' }}">
                                {{ $scan['action'] === 'dispatching' ? '-' : '+' }}{{ $scan['quantity'] }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
