<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">

    <title>NACPDEAN Email Verification</title>

</head>

<body style="font-family: Arial, sans-serif; background:#f4f6f8; padding:30px;">

    <div style="
        max-width:600px;
        margin:auto;
        background:#ffffff;
        padding:30px;
        border-radius:8px;
    ">

        <h2 style="margin-bottom:20px;">
            NACPDEAN Email Verification
        </h2>


        <p>
            Hello {{ $user->name }},
        </p>


        <p>
            Thank you for registering with NACPDEAN.
        </p>


        <p>
            Please use the verification code below to verify your email address:
        </p>


        <div style="
            text-align:center;
            margin:30px 0;
        ">

            <span style="
                display:inline-block;
                background:#f0f0f0;
                padding:15px 30px;
                font-size:32px;
                font-weight:bold;
                letter-spacing:8px;
                border-radius:6px;
            ">
                {{ $otp }}
            </span>

        </div>


        <p>
            This verification code will expire in
            <strong>10 minutes</strong>.
        </p>


        <p>
            If you did not create a NACPDEAN account,
            you can safely ignore this email.
        </p>


        <p style="margin-top:30px;">
            Regards,<br>
            <strong>NACPDEAN</strong>
        </p>

    </div>

</body>
</html>
