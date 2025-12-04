<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
<div class="container">
  <h3 class="mb-3">My Orders</h3>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Service</th>
            <th>Status</th>
            <th>Tracking</th>
            <th>Created</th>
          </tr>
        </thead>
        <tbody>
        @forelse($orders as $order)
          <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->service }}</td>
            <td><span class="badge text-bg-secondary">{{ ucfirst($order->status) }}</span></td>
            <td>{{ $order->tracking_code }}</td>
            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center">No orders yet.</td></tr>
        @endforelse
        </tbody>
      </table>

      <div class="mt-3">
        {{ $orders->links() }}
      </div>
    </div>
  </div>
</div>
</body>
</html>
