@extends('layouts.app') {{-- یا هر لایه‌ای که داری --}}

@section('content')
<div class="max-w-3xl mx-auto p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold">Order Receipt</h1>
            <p class="text-sm text-gray-500">Tracking: {{ $order->tracking_code }}</p>
        </div>
        <button onclick="window.print()" class="px-3 py-2 border rounded">Print</button>
    </div>

    <div class="border rounded p-4 space-y-2">
        <div class="text-xs text-gray-500">Service</div>
        <div class="font-medium">{{ $order->product->name ?? $order->service ?? '—' }}</div>

        <div class="text-xs text-gray-500 mt-3">Amount</div>
        <div class="font-medium">{{ number_format((float)$order->amount, 2) }}</div>

        <div class="text-xs text-gray-500 mt-3">Status</div>
        <div class="font-medium capitalize">{{ $order->status }}</div>

        <div class="text-xs text-gray-500 mt-3">Created at</div>
        <div class="font-medium">{{ $order->created_at?->format('Y-m-d H:i') }}</div>
    </div>

    <div class="border rounded p-4 space-y-3">
        <h2 class="font-semibold">Payment Instructions</h2>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <div class="text-xs text-gray-500">Provider</div>
                <div class="font-medium">{{ $instructions['provider'] ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Method</div>
                <div class="font-medium">{{ \Illuminate\Support\Str::title($instructions['delivery_mode'] ?? '—') }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Display</div>
                <div class="font-medium">{{ $instructions['display'] ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Amount</div>
                <div class="font-medium">{{ number_format((float)($instructions['amount'] ?? $order->amount), 2) }}</div>
            </div>
        </div>

        @if(!empty($instructions['email']))
            <div class="p-3 bg-blue-50 border rounded flex items-center justify-between gap-3">
                <div>
                    <div class="text-xs text-gray-500">Pay to this email</div>
                    <div class="font-mono text-sm">{{ $instructions['email'] }}</div>
                </div>
                <button class="px-2 py-1 border rounded text-sm"
                        onclick="navigator.clipboard.writeText('{{ $instructions['email'] }}')">Copy</button>
            </div>
        @endif

        @if(!empty($instructions['url']))
            <div class="p-3 bg-blue-50 border rounded flex items-center justify-between gap-3">
                <div>
                    <div class="text-xs text-gray-500">Payment URL</div>
                    <a class="font-mono text-sm underline" href="{{ $instructions['url'] }}" target="_blank" rel="noopener">
                        {{ $instructions['url'] }}
                    </a>
                </div>
                <button class="px-2 py-1 border rounded text-sm"
                        onclick="navigator.clipboard.writeText('{{ $instructions['url'] }}')">Copy</button>
            </div>
        @endif

        @if(!empty($instructions['email_subject']) || !empty($instructions['email_body']))
            <div class="space-y-2">
                @if(!empty($instructions['email_subject']))
                    <div>
                        <div class="text-xs text-gray-500">Email subject</div>
                        <div class="font-medium">{{ $instructions['email_subject'] }}</div>
                    </div>
                @endif

                @if(!empty($instructions['email_body']))
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Email body</div>
                        <pre class="whitespace-pre-wrap rounded bg-gray-50 p-3 text-sm border">{{ $instructions['email_body'] }}</pre>
                    </div>
                @endif
            </div>
        @endif

        @if(!empty($instructions['note']))
            <div class="p-3 bg-yellow-50 border rounded">
                {{ $instructions['note'] }}
            </div>
        @endif
    </div>

    @auth
        <a href="{{ \Filament\Facades\Filament::getPanel('site')->generateUrl('pages.OrderSummary', ['tracking_code' => $order->tracking_code]) }}"
           class="underline text-sm">Open in dashboard</a>
    @endauth
</div>
@endsection
