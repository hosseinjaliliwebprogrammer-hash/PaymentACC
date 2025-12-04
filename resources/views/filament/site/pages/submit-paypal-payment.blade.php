<x-filament-panels::page>

    {{-- لایه لودینگ هنگام Submit --}}
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
            Submitting your payment…<br>
            Please wait
        </p>
    </div>

    {{-- فرم اصلی --}}
    <x-filament-panels::form wire:submit="submit" class="max-w-xl mx-auto w-full space-y-8">

        {{-- عنوان --}}
        <h2 class="text-2xl font-bold text-gray-100 text-center">
            Submit PayPal Payment Details
        </h2>

        {{-- کارت Order Details --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-900 px-6 py-4 shadow-lg space-y-1">
            <p class="text-sm text-gray-300">
                <strong class="text-gray-100" style="color:red; padding-right: 10px;">Order ID:</strong> {{ $order->id }}
            </p>
            <p class="text-sm text-gray-300">
                <strong class="text-gray-100" style="color:red; padding-right: 10px;">Amount:</strong> ${{ number_format($order->amount, 2) }}
            </p>
        </div>

        {{-- فیلدها --}}
        <div class="space-y-6">

            {{-- Transaction ID --}}
            <div class="space-y-2">
    <label class="text-sm font-medium text-gray-300">PayPal Transaction ID</label>
    <input type="text"
           wire:model.defer="transaction_id"
           placeholder="e.g. 1AB23456CD7890123"
           class="w-full rounded-xl border-gray-700 bg-gray-800 text-black placeholder-black
                  focus:border-blue-500 focus:ring-blue-500">
    @error('transaction_id') 
        <span class="text-red-400 text-sm">{{ $message }}</span> 
    @enderror
</div>


            {{-- PayPal Email --}}
            <div class="space-y-2">
    <label class="text-sm font-medium text-gray-300">Your PayPal Email</label>
    <input type="email"
           wire:model.defer="paypal_email"
           placeholder="you@example.com"
           class="w-full rounded-xl border-gray-700 bg-gray-800 text-black placeholder-black
                  focus:border-blue-500 focus:ring-blue-500">
    @error('paypal_email') 
        <span class="text-red-400 text-sm">{{ $message }}</span> 
    @enderror
</div>


            {{-- Screenshot --}}
            <div class="space-y-2">
                <label class="text-sm font-medium text-gray-300">Screenshot (optional)</label>
                <input type="file"
                       wire:model="screenshot"
                       class="w-full rounded-xl border-gray-700 bg-gray-800 text-gray-100">
                @error('screenshot') 
                    <span class="text-red-400 text-sm">{{ $message }}</span> 
                @enderror
            </div>

        </div>

        {{-- دکمه Submit --}}
        <div class="pt-4">
            <x-filament::button 
                type="submit" 
                class="w-full py-3 text-base font-semibold"
                color="primary"
            >
                Submit Payment Details
            </x-filament::button>
        </div>

    </x-filament-panels::form>

</x-filament-panels::page>
