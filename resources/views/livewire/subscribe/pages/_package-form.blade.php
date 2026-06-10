<div class="space-y-6 bg-white px-4 py-5 sm:p-6">
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Title') }}</label>
        <div class="mt-1 flex rounded-md shadow-sm">
            <input
                wire:model.defer="title"
                type="text"
                class="block w-full flex-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="{{ __('e.g. Monthly Plan') }}"
            >
        </div>
        @error('title')
            <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
        <div class="mt-1">
            <textarea
                wire:model.defer="description"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="{{ __('What this plan includes') }}"
            ></textarea>
        </div>
        @error('description')
            <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Price (RM)') }}</label>
            <div class="mt-1 flex rounded-md shadow-sm">
                <input
                    wire:model.defer="price"
                    type="number"
                    step="0.01"
                    min="0"
                    class="block w-full flex-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="0.00"
                >
            </div>
            @error('price')
                <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Duration (days)') }}</label>
            <div class="mt-1 flex rounded-md shadow-sm">
                <input
                    wire:model.defer="duration_days"
                    type="number"
                    min="1"
                    class="block w-full flex-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="30"
                >
            </div>
            @error('duration_days')
                <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input
            wire:model.defer="is_active"
            id="is_active"
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300 text-slate-900 focus:ring-indigo-500"
        >
        <label for="is_active" class="text-sm font-medium text-gray-700">
            {{ __('Active (available for subscription)') }}
        </label>
    </div>
</div>
