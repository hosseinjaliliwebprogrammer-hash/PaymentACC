<x-filament-panels::page>

    <div class="max-w-lg mx-auto mt-16 text-center space-y-8">

        {{-- تیک سبز زیبا --}}
        <div class="flex justify-center">
            <div class="h-20 w-20 rounded-full bg-green-100 flex items-center justify-center animate-bounce">
                <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: green;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
        </div>

        {{-- عنوان --}}
        <h1 class="text-3xl font-bold text-gray-100" style="color: green;">
            Payment Submitted
        </h1>

        {{-- توضیحات --}}
        <p class="text-gray-300 text-lg leading-relaxed">
            Your PayPal payment details were successfully submitted.<br>
            Our team is currently reviewing your transaction.<br>
            You will receive your order details within <strong>1 hour</strong>.
        </p>

        {{-- کارت وضعیت --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-900 p-6 shadow-xl space-y-2 mt-6">
            <p class="text-black-400">
                Your submission has been received and is now being reviewed.
            </p>
            <p class="text-black-400">
                We will email you once the payment is approved.
            </p>
        </div>

        {{-- دکمه برگشت به داشبورد --}}
        <div class="pt-6">
            <a href="/app" 
               class="inline-block px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                Back to Dashboard
            </a>
        </div>

    </div>

</x-filament-panels::page>
