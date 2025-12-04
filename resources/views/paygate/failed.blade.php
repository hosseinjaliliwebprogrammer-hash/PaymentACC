<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed</title>
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
            background: #dc2626;
            color: white;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 6px;
        }
        a:hover { background: #b91c1c; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="color:#dc2626;">❌ Payment Failed</h2>
        <p>Something went wrong while processing your payment.</p>
        <a href="/app/order">Try Again</a>
    </div>
</body>
</html>
