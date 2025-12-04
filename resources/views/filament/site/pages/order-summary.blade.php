<x-filament-panels::page>
    <div class="space-y-6 print:space-y-3">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">Order Summary</h2>
                <p class="text-xs sm:text-sm text-gray-500">
                    Tracking: <span class="font-mono">{{ $this->order->tracking_code }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2 print:hidden">
                <x-filament::button color="gray" icon="heroicon-o-printer" x-on:click="window.print()">Print</x-filament::button>
                <x-filament::button tag="a" href="/app/orders" icon="heroicon-o-arrow-uturn-left">
                    Back to Order
                </x-filament::button>
            </div>
        </div>

        {{-- ACTIONS: under header, centered + larger --}}
        @php
            $dashboardUrl = class_exists(\App\Filament\Site\Pages\MyDashboard::class)
                ? \App\Filament\Site\Pages\MyDashboard::getUrl()
                : url('/admin');
        @endphp

        <div class="flex flex-col sm:flex-row gap-4 pt-4 justify-center items-center print:hidden">
            <x-filament::button tag="a" href="/app/user-dashboard" color="success" class="px-6 py-3 text-lg">
                Go To Dashboard
            </x-filament::button>

            <x-filament::button
                tag="a"
                href="#top"
                color="warning"
                class="px-6 py-3 text-lg"
            >
                How To Payment
            </x-filament::button>
        </div>

        {{-- ⭐ NEW SUCCESS BOX WITH ANIMATED ARROW --}}
  <div class="w-full flex justify-center print:hidden">
    <div 
        class="w-full max-w-xl rounded-xl text-center shadow-sm"
        style="background:#00b700; color:#fff; padding-top:10px; padding-bottom:10px;"
    >

                <div class="text-3xl mb-2">☑️</div>

                <h3 class="text-xl font-bold text-green-700 dark:text-green-300 mb-1">
                    Success!!
                </h3>

                <p 
    class="text-sm mb-3" 
    style="color:white; font-weight:bolder;"
>
    Check your email for the payment
</p>



            </div>
        </div>

        {{-- EXISTING Success message for payment email --}}
        <x-filament::section>
    <div class="p-6 rounded-xl border border-green-300 bg-green-50 dark:bg-green-900/30 dark:border-green-700 flex items-start gap-4">
        <div class="text-green-600 dark:text-green-400 text-3xl">✔️</div>
        <div class="text-gray-800 dark:text-gray-100 text-base leading-relaxed font-medium" style="font-size: 17px; line-height: 1.6;">

            <strong>Your payment instructions have been sent to your email.</strong><br><br>

            Please open your email inbox and look for our message.<br>
            If you cannot find it, check the <strong>Spam</strong> or <strong>Junk</strong> folder.  
            Sometimes emails go there by mistake.<br><br>

            After you see the email, follow the simple steps inside to finish your payment.  
            We will work on your order as soon as we receive your payment.  
        </div>
    </div>
</x-filament::section>


        {{-- Order card --}}
        <x-filament::section>
            <x-slot name="heading">Order</x-slot>
            <x-slot name="description">Basic details</x-slot>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-gray-500">Service</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-100">
                        {{ $this->order->product->name ?? $this->order->service ?? '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Amount</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-100">
                        {{ number_format((float)($this->publicInstructions['amount'] ?? $this->order->amount), 2) }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Status</dt>
                    <dd>
                        @php
                            $status = $this->order->status ?? 'pending';
                            $map = [
                                'pending'  => 'warning',
                                'paid'     => 'success',
                                'success'  => 'success',
                                'failed'   => 'danger',
                                'canceled' => 'danger',
                            ];
                        @endphp
                        <x-filament::badge :color="$map[$status] ?? 'gray'">
                            {{ \Illuminate\Support\Str::of($status)->headline() }}
                        </x-filament::badge>
                    </dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Created at</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-100">
                        {{ $this->order->created_at?->format('Y-m-d H:i') }}
                    </dd>
                </div>
            </dl>
        </x-filament::section>

        {{-- Note (optional) --}}
        @if(!empty($this->publicInstructions['note']))
            <div class="p-3 rounded border border-yellow-200/40 bg-yellow-500/10 print:border print:bg-white">
                {{ $this->publicInstructions['note'] }}
            </div>
        @endif

        <p class="text-xs text-gray-400 print:hidden">
            Tip: To save, click Print and choose “Save as PDF”.
        </p>

    </div>
</x-filament-panels::page>
