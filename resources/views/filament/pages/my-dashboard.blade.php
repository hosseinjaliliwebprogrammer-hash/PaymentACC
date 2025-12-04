<x-filament::page>
    <h2 class="text-xl font-bold mb-4">👋 Welcome, {{ auth()->user()->name }}</h2>

    <div class="space-y-4 text-gray-700">
        <p>Here you can view your orders and manage your account.</p>

        <x-filament::button tag="a" href="{{ url('/admin/orders') }}">
            View My Orders
        </x-filament::button>
    </div>
</x-filament::page>
