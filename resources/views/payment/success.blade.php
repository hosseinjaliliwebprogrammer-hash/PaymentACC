<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful - PaymentACC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind (CDN) فقط برای این صفحه --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center py-10">

    <div class="w-full max-w-xl mx-auto px-4">
        <div class="bg-white shadow-xl rounded-3xl px-8 py-10 relative overflow-hidden">

            {{-- Subtle gradient background --}}
            <div class="absolute inset-0 opacity-5 pointer-events-none"
                 style="background: radial-gradient(circle at top left, #22c55e, #0f172a);">
            </div>

            <div class="relative z-10">
                {{-- Icon --}}
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 mb-6">
                    <svg class="h-9 w-9 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                {{-- Title --}}
                <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 text-center mb-2">
                    Your payment was successful
                </h1>

                {{-- Subtitle --}}
                <p class="text-center text-sm md:text-base text-gray-600 max-w-md mx-auto mb-6">
                    Thank you for your purchase. Your account details will be delivered to you
                    within <span class="font-semibold text-gray-800">up to 1 hour</span> to your email.
                </p>

                {{-- Order info (optional) --}}
                @php
                    $orderId = request('order_id');
                @endphp

                @if($orderId)
                    <div class="flex items-center justify-center mb-6">
                        <div class="inline-flex items-center rounded-full bg-gray-100 px-4 py-1.5 text-xs md:text-sm font-medium text-gray-800">
                            <span class="mr-1 text-gray-500">Order ID:</span>
                            <span class="font-semibold">#{{ $orderId }}</span>
                        </div>
                    </div>
                @endif

                {{-- Info box --}}
                <div class="mb-8 rounded-2xl border border-emerald-100 bg-emerald-50/60 px-4 py-3 text-xs md:text-sm text-emerald-800 flex items-start gap-3">
                    <span class="mt-0.5">
                        <svg class="h-4 w-4 md:h-5 md:w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v3.75m0 3.75h.007v.008H12v-.008zM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                        </svg>
                    </span>
                    <p>
                        We will send all the required information to the email address you provided during checkout.
                        If you do not see the email, please check your <span class="font-medium">Spam / Junk</span> folder as well.
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ url('/') }}"
                       class="inline-flex w-full sm:w-auto items-center justify-center rounded-2xl bg-gray-900 px-6 py-2.5 text-sm md:text-base font-medium text-white hover:bg-gray-800 transition">
                        Back to homepage
                    </a>

                    <a href="{{ url('/app/order') }}"
                       class="inline-flex w-full sm:w-auto items-center justify-center rounded-2xl border border-gray-200 bg-white px-6 py-2.5 text-sm md:text-base font-medium text-gray-800 hover:bg-gray-50 transition">
                        Place another order
                    </a>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
