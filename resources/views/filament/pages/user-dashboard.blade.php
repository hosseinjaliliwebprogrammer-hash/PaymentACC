<x-filament::page>
    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // اگر کاربر لاگین نباشه، از مقادیر صفر استفاده می‌کنیم
        if ($user) {
            $totalOrders   = \App\Models\Order::where('user_id', $user->id)->count();

            $activeOrders  = \App\Models\Order::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'processing', 'unpaid'])
                ->count();

            $totalSpent    = \App\Models\Order::where('user_id', $user->id)
                ->where('status', 'paid')
                ->sum('amount'); // اگر اسم فیلد مبلغ چیز دیگه‌ایه اینجا عوض کن

            $openTickets   = \App\Models\Ticket::where('user_id', $user->id)
                ->where('status', 'open')
                ->count();

            $totalTickets  = \App\Models\Ticket::where('user_id', $user->id)->count();

            $recentOrders  = \App\Models\Order::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            $recentTickets = \App\Models\Ticket::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
        } else {
            $totalOrders = $activeOrders = $totalSpent = $openTickets = $totalTickets = 0;
            $recentOrders = collect();
            $recentTickets = collect();
        }
    @endphp

    <div class="space-y-8">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 shadow-sm rounded-2xl px-6 py-5 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-50">
                Welcome back, {{ $user?->name }} 👋
            </h2>
            <p class="text-gray-600 dark:text-gray-300 mt-2 text-sm">
                Manage your <strong>orders</strong>, <strong>support tickets</strong>, and account settings easily.
            </p>
        </div>

        {{-- Stats Cards – رنگ‌های ملایم --}}
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

            {{-- Total Orders --}}
            <div class="rounded-2xl p-4 shadow-sm border border-blue-100 bg-blue-50">
                <p class="text-xs font-medium text-blue-700 uppercase tracking-wide">
                    Total Orders
                </p>
                <p class="mt-2 text-3xl font-bold text-blue-900">
                    {{ $totalOrders }}
                </p>
                <p class="mt-1 text-sm text-blue-700">
                    All your orders
                </p>
            </div>

            {{-- Active Orders --}}
            <div class="rounded-2xl p-4 shadow-sm border border-green-100 bg-green-50" style="background-color: #E0B3A6;">
                <p class="text-xs font-medium text-green-700 uppercase tracking-wide">
                    Active Orders
                </p>
                <p class="mt-2 text-3xl font-bold text-green-900">
                    {{ $activeOrders }}
                </p>
                <p class="mt-1 text-sm text-green-700">
                    Orders still in progress
                </p>
            </div>

            {{-- Total Spent --}}
            <div class="rounded-2xl p-4 shadow-sm border border-purple-100 bg-purple-50">
                <p class="text-xs font-medium text-purple-700 uppercase tracking-wide">
                    Total Spent
                </p>
                <p class="mt-2 text-3xl font-bold text-purple-900">
                    ${{ number_format($totalSpent, 2) }}
                </p>
                <p class="mt-1 text-sm text-purple-700">
                    Paid order total
                </p>
            </div>

            {{-- Open Tickets --}}
            <div class="rounded-2xl p-4 shadow_sm border border-orange-100 bg-orange-50" style="background-color: #E0B3A6;">
                <p class="text-xs font-medium text-orange-700 uppercase tracking-wide">
                    Open Tickets
                </p>
                <p class="mt-2 text-3xl font-bold text-orange-900">
                    {{ $openTickets }}
                </p>
                <p class="mt-1 text-sm text-orange-700">
                    Awaiting replies
                </p>
            </div>

        </div>

        {{-- Quick actions --}}
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ url('/user/orders') }}"
               class="inline-flex items-center rounded-full bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                My Orders
            </a>

            <a href="{{ url('/user/tickets') }}"
               class="inline-flex items-center rounded-full bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 transition">
                My Tickets
            </a>

            <a href="{{ url('/user/tickets/create') }}"
               class="inline-flex items-center rounded-full bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">
                New Ticket
            </a>

            <a href="{{ url('/user/profile') }}"
               class="inline-flex items-center rounded-full bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 transition">
                Account Settings
            </a>
        </div>

        {{-- Recent Orders + Recent Tickets --}}
        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Recent Orders --}}
            <div class="rounded-2xl p-5 shadow-sm border border-blue-100 bg-white">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold" style="font-size:18px; color:#FF0000;font-weight:bolder;padding:10px;">
                        Recent Orders
                    </h3>
                    <a href="/app/orders" class="text-xs font-medium text-blue-700 hover:underline" style="font-size: 16px;">
                        View all
                    </a>
                </div>

                @if ($recentOrders->isEmpty())
                    <p class="text-sm font-bold text-center" style="font-size:20px; color:red; padding-bottom: 18px;">
                        No orders yet.
                    </p>
                @else
                    <div class="space-y-3">

                        @foreach ($recentOrders as $order)
                            @php
                                $status = strtolower($order->status);
                                // استایل پیش‌فرض
                                $statusStyle = '';
                                // شبیه عکس: Pending → زرد، Completed/Paid → سبز
                                if ($status === 'pending') {
                                    $statusStyle = 'background-color:#fff7e6;color:#d97706;border:1px solid #fde68a;';
                                } elseif ($status === 'completed' || $status === 'paid') {
                                    $statusStyle = 'background-color:#dcfce7;color:#15803d;border:1px solid #bbf7d0;';
                                }
                            @endphp

                            <div
                                class="flex items-center justify-between rounded-xl border border-blue-100 px-3 py-2.5"
                                style="background-color: {{ $loop->even ? '#F0F0F0' : '#FFFFFF' }};"
                            >
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        <a
    href="{{ url('/app/orders/' . $order->tracking_code . '/summary') }}"
    class="hover:underline"
>
    Order #{{ $order->id }}
</a>

                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $order->created_at->format('Y-m-d H:i') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">
                                    {{-- وضعیت با استایل زرد / سبز --}}
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                        style="{{ $statusStyle }}"
                                    >
                                        {{ ucfirst($order->status) }}
                                    </span>

                                    <p class="text-sm font-semibold text-gray-900">
                                        ${{ number_format($order->amount, 2) }}
                                    </p>

                                </div>
                            </div>
                        @endforeach

                    </div>
                @endif
            </div>

            {{-- Recent Tickets --}}
            <div class="rounded-2xl p-5 shadow-sm border border-purple-100 bg-white">
                <div class="mb-4 flex items-center justify_between">
                    <h3 class="text-sm font-semibold" style="font-size:18px; color:#FF0000;font-weight:bolder;padding:10px;">
                        Recent Tickets
                    </h3>

                    <a href="/app/my-tickets" class="text-xs font-medium text-purple-700 hover:underline">
                        View all
                    </a>
                </div>

                @if ($recentTickets->isEmpty())
                    <p class="text-sm font-bold text-center" style="font-size:20px; color:red; padding-bottom: 18px;">
                        No tickets yet.
                    </p>
                @else
                    <div class="space-y-3">
                        @foreach ($recentTickets as $ticket)

                            @php
                                // ========== وضعیت (Status) ==========
                                $status = strtolower($ticket->status);
                                $statusStyle = '';

                                if ($status === 'open') {
                                    // زرد ملایم
                                    $statusStyle = 'background-color:#fff7e6;color:#d97706;border:1px solid #fde68a;';
                                } elseif ($status === 'in_progress') {
                                    // آبی ملایم
                                    $statusStyle = 'background-color:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;';
                                } elseif ($status === 'closed') {
                                    // سبز ملایم
                                    $statusStyle = 'background-color:#dcfce7;color:#15803d;border:1px solid #bbf7d0;';
                                }

                                // ========== پرایوریتی ==========
                                $priority = strtolower($ticket->priority);
                                $priorityStyle = '';

                                if ($priority === 'high') {
                                    $priorityStyle = 'background-color:#fee2e2;color:#b91c1c;border:1px solid #fecaca;';
                                } elseif ($priority === 'normal') {
                                    $priorityStyle = 'background-color:#e5e7eb;color:#374151;border:1px solid #d1d5db;';
                                } elseif ($priority === 'low') {
                                    $priorityStyle = 'background-color:#d1fae5;color:#065f46;border:1px solid #a7f3d0;';
                                }
                            @endphp

                            <div
                                class="flex items-center justify-between rounded-xl border border-purple-100 px-3 py-2.5"
                                style="background-color: {{ $loop->even ? '#F0F0F0' : '#FFFFFF' }};"
                            >
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        <a href="{{ url('/app/view-ticket?record=' . $ticket->id) }}" class="hover:underline">
                                            {{ $ticket->subject }}
                                        </a>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $ticket->created_at->format('Y-m-d H:i') }}
                                    </p>
                                </div>

                                <div class="flex flex-col items-end gap-1">

                                    {{-- Status --}}
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                        style="{{ $statusStyle }}"
                                    >
                                        {{ ucfirst($ticket->status) }}
                                    </span>

                                    {{-- ✅ Priority دیگر نمایش داده نمی‌شود --}}

                                </div>
                            </div>

                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-filament::page>
