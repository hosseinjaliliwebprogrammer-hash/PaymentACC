<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmed</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#f3f4f6;padding:24px 0;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                   style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">

                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg,#111827,#1f2937);padding:20px 24px;color:#f9fafb;">
                        <div style="font-size:18px;font-weight:700;">PaymentACC</div>
                        <div style="font-size:13px;opacity:.9;margin-top:4px;">
                            Payment Confirmation
                        </div>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:24px 24px;color:#111827;font-size:15px;line-height:1.7;">

                        <p style="margin:0 0 16px 0;">
                            Dear {{ $name }},
                        </p>

                        <p style="margin:0 0 20px 0;">
                            Your payment has been <strong>successfully confirmed</strong>.
                            <br><br>
                            We are now preparing your account. Your account details will be delivered to you
                            between <strong>10 minutes</strong> and <strong>1 hour</strong>.
                            <br>
                            Please be patient while we complete the setup.
                        </p>

                        <p style="margin:0 0 24px 0;">
                            You can always check your dashboard for updates using the button below.
                        </p>

                        <!-- Button (Centered) -->
                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="text-align:center;margin:20px 0;">
                            <tr>
                                <td align="center">
                                    <a href="https://centerpay.me/app/user-dashboard"
                                       style="display:inline-block;background-color:#2563eb;color:#ffffff;
                                              padding:12px 30px;border-radius:999px;font-size:15px;
                                              text-decoration:none;font-weight:600;text-align:center;">
                                        Go to Dashboard
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:24px 0 0 0;">
                            Thank you,<br>
                            <strong>The PaymentACC Team</strong>
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:12px 24px 18px 24px;text-align:center;font-size:11px;color:#9ca3af;background-color:#f9fafb;">
                        This email was sent automatically. Please do not reply directly to this message.
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
