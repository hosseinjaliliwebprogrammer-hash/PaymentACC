@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-10 px-4">
  <div class="w-full max-w-xl bg-white rounded-2xl shadow p-8 space-y-6">
    <h2 class="text-2xl font-bold text-center text-gray-800">Choose your service</h2>

    @if ($errors->any())
      <div class="p-3 bg-red-100 text-red-700 rounded">
        <ul class="list-disc list-inside text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('order.submit') }}" class="space-y-5">
      @csrf

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
        <input name="name" type="text" class="w-full border rounded-lg p-2.5" required value="{{ old('name') }}">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Your Email Address</label>
        <input name="email" type="email" class="w-full border rounded-lg p-2.5" required value="{{ old('email') }}">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input name="password" type="password" class="w-full border rounded-lg p-2.5" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Choose your service</label>
        <input name="service" type="text" class="w-full border rounded-lg p-2.5" placeholder="e.g. Premium Package" required value="{{ old('service') }}">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (USD)</label>
        <input name="amount" type="number" step="0.01" class="w-full border rounded-lg p-2.5" required value="{{ old('amount') }}">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
        <select name="gateway_id" class="w-full border rounded-lg p-2.5">
          <option value="">Auto select by system</option>
          @foreach($gateways as $g)
            <option value="{{ $g->id }}">{{ $g->name ?? $g->email }}</option>
          @endforeach
        </select>
      </div>

      <div class="flex items-center space-x-2">
        <input type="checkbox" name="terms" id="terms" required>
        <label for="terms" class="text-sm text-gray-600">I agree to the Terms</label>
      </div>

      <p class="text-xs text-gray-500">Note: Your account will be created immediately after payment, and you can manage your orders anytime.</p>

      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg py-3 transition">
        Continue to Payment
      </button>
    </form>
  </div>
</div>
@endsection
