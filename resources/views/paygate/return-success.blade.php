@extends('layouts.app') {{-- یا اگر layout نداری می‌تونی خط بالا رو حذف کنی --}}

@section('content')
<div style="max-width:600px;margin:50px auto;text-align:center;padding:20px;border-radius:12px;border:1px solid #ddd;">
    @if ($status === 'success')
        <h1 style="color:#16a34a;">✅ Payment Processed</h1>
        <p>Your Paygate transaction has been processed successfully.</p>
        <p><strong>Amount:</strong> ${{ $amount }}</p>

        @if ($txid)
            <p style="font-size:12px;color:#555;">Transaction ID: <code>{{ $txid }}</code></p>
        @endif

        <p style="margin-top:20px;">
            <a href="/app" style="color:#2563eb;text-decoration:underline;">Return to your dashboard</a>
        </p>
    @else
        <h1 style="color:#dc2626;">❌ Payment Failed</h1>
        <p>Your payment could not be completed or was cancelled.</p>
    @endif
</div>
@endsection
