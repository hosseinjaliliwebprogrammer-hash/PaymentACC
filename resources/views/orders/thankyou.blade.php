<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">

<div class="container text-center">
    <h2 class="mb-3 text-success">✅ Thank You!</h2>
    <p>Your order has been submitted successfully.</p>
    <p><strong>Tracking Code:</strong> {{ $code }}</p>

    <a href="/order" class="btn btn-outline-primary mt-3">Submit Another Order</a>
</div>

</body>
</html>
