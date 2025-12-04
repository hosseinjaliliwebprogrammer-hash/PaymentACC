<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed - PaymentACC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind (CDN) فقط برای این صفحه --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center py-10">

    <div class="w-full max-w-xl mx-auto px-4">
        <div class="bg-white shadow-xl rounded-3xl px-8 py-10 relative overflow-hidden">

            {{-- Subtle gradient background --}}
            <div class="absolute inset-0 opacity-5 pointer-events-none"
                 style="background: radial-gradient(circle at top left, #ef4444, #0f172a);">
            </div>

            <div class="relative z-10">
                {{-- Icon --}}
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-50 mb-6">
                    <svg class="h-9 w-9 text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m0 3.75h.007v.008H12v-.008zM10.34 3.94 3.94 10.34a8 8 0 1 0 11.32 11.32l6.4-6.4A8 8 0 0 0 10.34 3.94z" />
                    </svg>
                </div>

                {{-- Title --}}
                <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 text-center mb-2">
                    Payment failed
                </h1>

                {{-- Subtitle --}}
                <p class="text-center text-sm md:text-base text-gray-600 max-w-md mx-auto mb-6">
                    Unfortunately, your payment could not be completed. This can happen due to insufficient funds,
                    network issues, or a cancelled transaction.
                </p>

                {{-- Optional details --}}
                @php
                    $orderId = request('order_id');
                    $reason  = request('reason') ?? request('message');
                @endphp

                @if($orderId || $reason)
                    <div class="mb-6 space-y-3">
                        @if($orderId)
                            <div class="flex items-center justify-center">
                                <div class="inline-flex items-center rounded-full bg-gray-100 px-4 py-1.5 text-xs md:text-sm font-medium text-gray-800">
                                    <span class="mr-1 text-gray-500">Order ID:</span>
                                    <span class="font-semibold">#{{ $orderId }}</span>
                                </div>
                            </div>
                        @endif

                        @if($reason)
                            <div class="rounded-2xl border border-rose-100 bg-rose-50/70 px-4 py-3 text-xs md:text-sm text-rose-800 flex items-start gap-3">
                                <span class="mt-0.5">
                                    <svg class="h-4 w-4 md:h-5 md:w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 9v3.75m0 3.75h.007v.008H12v-.008zM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                                    </svg>
                                </span>
                                <p>
                                    <span class="font-semibold">Gateway message:</span>
                                    <span class="ml-1">{{ $reason }}</span>
                                </p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Help text --}}
                <div class="mb-8 rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-xs md:text-sm text-gray-700">
                    <p class="mb-1.5">
                        You can try the payment again using the same or a different payment method.
                    </p>
                    <p>
                        If the issue keeps happening, please contact us with your order ID so we can assist you.
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ url('/app/order') }}"
                       class="inline-flex w-full sm:w-auto items-center justify-center rounded-2xl bg-gray-900 px-6 py-2.5 text-sm md:text-base font-medium text-white hover:bg-gray-800 transition">
                        Try again
                    </a>

                    <a href="{{ url('/') }}"
                       class="inline-flex w-full sm:w-auto items-center justify-center rounded-2xl border border-gray-200 bg-white px-6 py-2.5 text-sm md:text-base font-medium text-gray-800 hover:bg-gray-50 transition">
                        Back to homepage
                    </a>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
