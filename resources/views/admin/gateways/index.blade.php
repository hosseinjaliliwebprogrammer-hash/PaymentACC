<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-4">Gateways Management</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Link</th>
                    <th>Limit</th>
                    <th>Used</th>
                    <th>Remaining</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gateways as $gateway)
                <tr @if(!$gateway->is_active) class="table-secondary" @endif>
                    <td>{{ $gateway->id }}</td>
                    <td>{{ $gateway->email }}</td>
                    <td>{{ $gateway->link ?? '-' }}</td>
                    <td>{{ $gateway->limit_amount }}</td>
                    <td>{{ $gateway->used_amount }}</td>
                    <td>
                        @if($gateway->limit_amount == 0)
                            ∞
                        @else
                            {{ $gateway->limit_amount - $gateway->used_amount }}
                        @endif
                    </td>
                    <td>
                        @if($gateway->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.gateways.toggle', $gateway) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-primary">
                                {{ $gateway->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
