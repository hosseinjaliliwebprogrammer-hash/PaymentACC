<x-filament-panels::page>

    {{-- 🔵 لایه لودینگ روی کل صفحه --}}
    <div 
        wire:loading 
        wire:target="submit"
        class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex flex-col items-center justify-center"
    >
        <svg class="animate-spin h-14 w-14 text-white mb-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>

        <p class="text-white text-xl font-semibold tracking-wide text-center">
            Please wait…<br>
            Connecting to payment gateway
        </p>
    </div>
    {{-- 🔵 پایان لایه لودینگ --}}

    {{-- فرم اصلی سفارش --}}
    <x-filament-panels::form wire:submit="submit" class="max-w-xl mx-auto w-full space-y-8">

        {{-- ====================== مرحله ۱ ====================== --}}
        @if($step === 1)
            <div class="space-y-6">

                {{-- فیلدهای اصلی فرم --}}
                <div class="space-y-4">

                    {{ $this->form }}

                    {{-- 🔥 باکس زیر فیلد Email، قبل از Password --}}
                    @if($emailExists)
                        <div class="p-4 rounded-xl text-white text-sm shadow-md"
                             style="background: #01a401 !important; font-size:14px; padding: 20px; line-height: 1.6;">
                            <strong>This email already exists.</strong><br>
                            You can 
                            <a href="/app/login" class="underline font-semibold">log in here</a> or 
                            <a href="/app/password-reset/request" class="underline font-semibold">reset your password</a>.
                        </div>
                    @endif

                </div>

            </div>

            {{-- 🔥 باکس بعد از زدن Next --}}
            @if($errors->has('email'))
                <div class="mt-4 p-4 rounded-xl bg-green-600/80 border border-green-500 text-white text-sm shadow-md">
                    <strong>This email already exists.</strong><br>
                    You can 
                    <a href="/app/login" class="underline font-semibold">log in here</a> or 
                    <a href="/app/password-reset/request" class="underline font-semibold">reset your password</a>.
                </div>
            @endif

            <div class="pt-4">
                <x-filament::button
                    type="button"
                    wire:click="nextStep"
                    class="w-full py-3 text-base font-semibold"
                    :disabled="$emailExists"
                >
                    Next
                </x-filament::button>
            </div>
        @endif

        {{-- ====================== مرحله ۲ ====================== --}}
        @if($step === 2)
            <div 
                x-data 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="space-y-8"
            >
                <h2 class="text-xl font-semibold text-center text-gray-100">
                    Select Your Payment Method
                </h2><br>

                <div class="space-y-4">

                    {{-- PayPal --}}
                    <label
                        for="paypalOption"
                        class="flex items-center justify-between border rounded-2xl px-6 py-4 cursor-pointer transition-all duration-200
                               {{ ($data['payment_method'] ?? null) === 'paypal'
                                    ? 'border-primary-500 bg-gray-800/60 shadow-lg scale-[1.02]'
                                    : 'border-gray-700 hover:border-primary-400 hover:bg-gray-800/40' }}">
                        <div class="flex items-center gap-4">

                            <input type="radio"
                                   id="paypalOption"
                                   wire:model="data.payment_method"
                                   value="paypal"
                                   class="text-primary-500 focus:ring-primary-500">

                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg"
                                 alt="PayPal" class="h-8">
                        </div>

                        <p class="text-xs text-gray-400">Recommended for global users</p>
                    </label>

                    {{-- Crypto --}}
                    <label
                        for="nowpayOption"
                        class="flex items-center justify-between border rounded-2xl px-6 py-4 cursor-pointer transition-all duration-200
                               {{ ($data['payment_method'] ?? null) === 'nowpay'
                                    ? 'border-primary-500 bg-gray-800/60 shadow-lg scale-[1.02]'
                                    : 'border-gray-700 hover:border-primary-400 hover:bg-gray-800/40' }}">

                        <div class="flex items-center gap-4">

                            <input type="radio"
                                   id="nowpayOption"
                                   wire:model="data.payment_method"
                                   value="nowpay"
                                   class="text-primary-500 focus:ring-primary-500">

                            <img src="https://cryptologos.cc/logos/bitcoin-btc-logo.svg?v=029"
                                 alt="Crypto"
                                 class="h-8 opacity-90">
                        </div>

                        <p class="text-xs text-gray-400">Crypto Payments (BTC, ETH, USDT)</p>
                    </label>

                    {{-- Paygate --}}
                    <label
                        for="paygateOption"
                        class="flex items-center justify-between border rounded-2xl px-6 py-4 cursor-pointer transition-all duration-200
                               {{ ($data['payment_method'] ?? null) === 'paygate'
                                    ? 'border-primary-500 bg-gray-800/60 shadow-lg scale-[1.02]'
                                    : 'border-gray-700 hover:border-primary-400 hover:bg-gray-800/40' }}">

                        <div class="flex items-center gap-4">

                            <input type="radio"
                                   id="paygateOption"
                                   wire:model="data.payment_method"
                                   value="paygate"
                                   class="text-primary-500 focus:ring-primary-500">

                            <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg"
                                 alt="Paygate" class="h-8">
                        </div>

                        <p class="text-xs text-gray-400">Supports Visa, MasterCard & Crypto</p>
                    </label>

                </div>

                <div class="flex justify-between items-center pt-6">
                    <x-filament::button type="button" wire:click="previousStep" color="gray" class="px-6">
                        Back
                    </x-filament::button>

                    <x-filament::button type="submit" class="px-8 py-3 text-base font-semibold">
                        Continue to payment
                    </x-filament::button>
                </div>

            </div>
        @endif

    </x-filament-panels::form>
</x-filament-panels::page>
