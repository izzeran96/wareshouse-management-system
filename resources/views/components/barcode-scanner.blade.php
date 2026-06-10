@props([
    'event' => 'barcode-scanned',
    'label' => __('Scan Barcode'),
    'title' => __('Scan a barcode or QR code'),
])

@php
    $readerId = 'qr-reader-' . uniqid();
@endphp

<div x-data="barcodeScanner('{{ $event }}', '{{ $readerId }}')" class="inline-block">
    <button
        type="button"
        @click="openModal()"
        class="inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white py-2 px-4 text-sm font-medium text-slate-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 013.75 7.125v-2.25zM3.75 16.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-2.25zM13.5 4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0113.5 7.125v-2.25z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 18.75h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
        </svg>
        <span>{{ $label }}</span>
    </button>

    <!-- Modal -->
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background: rgba(0,0,0,0.5);"
        @keydown.escape.window="closeModal()"
    >
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md" @click.outside="closeModal()">
            <div class="flex items-center justify-between border-b px-4 py-3">
                <h4 class="text-base font-semibold text-slate-900">{{ $title }}</h4>
                <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4 space-y-4">
                <!-- Camera preview -->
                <div id="{{ $readerId }}" class="w-full overflow-hidden rounded-md bg-black/5 min-h-[200px]"></div>
                <p x-show="cameraError" x-text="cameraError" class="text-sm text-red-500"></p>

                <!-- Hardware scanner / manual entry -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        {{ __('Or use a hardware scanner / type the code') }}
                    </label>
                    <form @submit.prevent="submitManual()" class="mt-1 flex gap-2">
                        <input
                            x-ref="manual"
                            x-model="manualCode"
                            type="text"
                            autocomplete="off"
                            class="block w-full flex-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="{{ __('Scan here…') }}"
                        >
                        <button type="submit" class="rounded-md bg-slate-900 py-2 px-4 text-sm font-medium text-white hover:bg-slate-800">
                            {{ __('OK') }}
                        </button>
                    </form>
                    <p class="mt-1 text-xs text-gray-400">
                        {{ __('A USB/Bluetooth scanner types here and submits automatically.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script>
            function barcodeScanner(eventName, readerId) {
                return {
                    open: false,
                    manualCode: '',
                    cameraError: '',
                    html5: null,
                    readerId: readerId,
                    openModal() {
                        this.open = true;
                        this.cameraError = '';
                        this.$nextTick(() => {
                            this.startCamera();
                            if (this.$refs.manual) this.$refs.manual.focus();
                        });
                    },
                    closeModal() {
                        this.stopCamera();
                        this.open = false;
                        this.manualCode = '';
                    },
                    emit(code) {
                        code = (code || '').trim();
                        if (!code) return;
                        window.dispatchEvent(new CustomEvent(eventName, { detail: { code: code } }));
                        this.closeModal();
                    },
                    submitManual() {
                        this.emit(this.manualCode);
                    },
                    startCamera() {
                        if (typeof Html5Qrcode === 'undefined') {
                            this.cameraError = '{{ __('Camera scanner library could not be loaded (check your connection). You can still type the code.') }}';
                            return;
                        }
                        try {
                            this.html5 = new Html5Qrcode(this.readerId);
                            this.html5.start(
                                { facingMode: 'environment' },
                                { fps: 10, qrbox: { width: 250, height: 250 } },
                                (decodedText) => { this.emit(decodedText); },
                                () => {}
                            ).catch((err) => {
                                this.cameraError = '{{ __('Camera unavailable. Use a hardware scanner or type the code.') }}';
                            });
                        } catch (e) {
                            this.cameraError = '{{ __('Camera unavailable. Use a hardware scanner or type the code.') }}';
                        }
                    },
                    stopCamera() {
                        if (this.html5) {
                            const ref = this.html5;
                            this.html5 = null;
                            ref.stop().then(() => ref.clear()).catch(() => {});
                        }
                    }
                }
            }
        </script>
    @endpush
@endonce
