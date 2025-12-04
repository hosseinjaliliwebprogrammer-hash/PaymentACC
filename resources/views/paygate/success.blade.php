<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Success</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; color: #333; }
        .box {
            max-width: 600px;
            margin: 80px auto;
            background: white;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        a {
            display: inline-block;
            margin-top: 25px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 6px;
        }
        a:hover { background: #1e40af; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="color:#16a34a;">✅ Payment Processed</h2>
        <p>Your Paygate transaction has been processed successfully.</p>
        <p><strong>Amount:</strong> ${{ $amount }}</p>
        <a href="/app">Return to your dashboard</a>
    </div>
</body>
</html>
