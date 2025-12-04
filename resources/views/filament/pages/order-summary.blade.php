<x-filament-panels::page>
    <div id="top" class="space-y-6 print:space-y-3">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">Order Summary</h2>
                <p class="text-xs sm:text-sm text-gray-500">
                    Tracking: <span class="font-mono">{{ $this->order->tracking_code }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2 print:hidden">
                <x-filament::button
                    color="gray"
                    icon="heroicon-o-printer"
                    x-on:click="window.print()"
                >
                    Print
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    href="{{ \App\Filament\Site\Pages\OrderForm::getUrl() }}"
                    icon="heroicon-o-arrow-uturn-left"
                >
                    Back to Order
                </x-filament::button>
            </div>
        </div>

        {{-- ACTIONS: under header, centered + larger --}}
        <div class="flex flex-col sm:flex-row gap-4 pt-4 justify-center items-center print:hidden">
            <x-filament::button
                tag="a"
                href="/app/user-dashboard"
                color="success"
                class="px-6 py-3 text-lg"
            >
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

        {{-- ✅ Success message for payment email --}}
        <x-filament::section>
            <div class="p-6 rounded-xl border border-green-300 bg-green-50 dark:bg-green-900/30 dark:border-green-700 flex items-start gap-4">
                <div class="text-green-600 dark:text-green-400 text-3xl">✅</div>
                <div class="text-gray-800 dark:text-gray-100 text-base leading-relaxed font-medium" style="font-size: 17px; line-height: 1.6;">
                    <strong>Your PayPal payment link or account address has been sent to your email.</strong><br>
                    Please check your inbox to complete the payment.<br>
                    If you don’t see the message within a few minutes, make sure to check your
                    <strong>Spam</strong> or <strong>Junk</strong> folder.<br><br>
                    Thank you for your order — we’ll start processing it as soon as the payment is confirmed.
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
