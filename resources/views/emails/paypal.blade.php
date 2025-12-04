<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your PayPal Payment Instructions</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#f3f4f6;padding:24px 0;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">

                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg,#111827,#1f2937);padding:20px 24px;color:#f9fafb;">
                        <div style="font-size:18px;font-weight:700;">PaymentACC</div>
                        <div style="font-size:13px;opacity:.9;margin-top:4px;">
                            PayPal Payment Instructions
                        </div>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:24px 24px 8px 24px;color:#111827;font-size:14px;line-height:1.6;">

                        <p style="margin:0 0 12px 0;">
                            Dear {{ $name }},
                        </p>

                        <p style="margin:0 0 16px 0;">
                            Please send the exact invoice amount ({{ $amount }} USD) manually to our PayPal account: {{ $gateway_email }}.
                            After you send the payment, click the "Submit PayPal Payment Details" button to let us know about your payment.
                            Once we receive and verify your payment, we will deliver your account immediately.
                        </p>

                        <!-- Payment details card -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:8px 0 16px 0;">
                            <tr>
                                <td style="background-color:#f9fafb;border-radius:10px;border:1px solid #e5e7eb;padding:16px 18px;">

                                    <p style="margin:0 0 6px 0;font-size:13px;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">
                                        Payment details
                                    </p>

                                    <p style="margin:0 0 6px 0;">
                                        <strong>PayPal email:</strong>
                                        {{ $gateway_email }}
                                    </p>

                                    <p style="margin:0 0 6px 0;">
                                        <strong>Amount:</strong>
                                        ${{ number_format($amount, 2) }}
                                    </p>

                                    <p style="margin:0;">
                                        <strong>Service:</strong>
                                        {{ $service }}
                                    </p>

                                </td>
                            </tr>
                        </table>
<p style="color:#dc2626;font-size:14px;margin:12px 0;">
    After making the PayPal payment, please click the button below to send us your payment details.
</p>

                        <!-- Submit PayPal Details Button -->
                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0; text-align:center;">
    <tr>
        <td align="center">
            <a href="{{ $submit_url }}"
               style="display:inline-block; background-color:#16a34a; color:#ffffff;
                      padding:10px 22px; border-radius:999px; font-size:14px;
                      text-decoration:none; font-weight:600;">
                Submit PayPal Payment Details
            </a>
        </td>
    </tr>
</table>


                        <p style="margin:0 0 14px 0;font-size:12px;color:#6b7280;line-height:1.5;">
                            Or paste this link into your browser:<br>
                            <span style="word-break:break-all;color:#16a34a;">
                                {{ $submit_url }}
                            </span>
                        </p>

                        <hr style="border:none;border-top:1px solid #e5e7eb;margin:28px 0;">

                        <!-- NEW: Go To Dashboard Button -->
                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td align="center">
                                    <a href="{{ url('/dashboard') }}"
                                       style="display:inline-block;background-color:#2563eb;color:#ffffff;
                                              padding:12px 30px;border-radius:999px;font-size:15px;
                                              text-decoration:none;font-weight:600;text-align:center;">
                                        Go to Dashboard
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:18px 0 0 0;">
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
