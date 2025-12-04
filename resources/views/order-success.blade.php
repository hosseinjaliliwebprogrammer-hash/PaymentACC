@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50">
  <div class="bg-white shadow rounded-xl p-10 text-center max-w-md">
    <h2 class="text-2xl font-bold text-green-600 mb-4">Order Received!</h2>
    <p class="text-gray-700 mb-6">
      Thank you for your order.<br>
      Your tracking code is:
    </p>
    <div class="bg-gray-100 text-lg font-mono p-3 rounded-lg inline-block mb-6">
      {{ $tracking }}
    </div>
    <a href="/admin" class="inline-block px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
      Go to Dashboard
    </a>
  </div>
</div>
@endsection
