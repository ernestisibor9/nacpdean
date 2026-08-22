<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>NACPDEAN - Verify Email</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>


<body class="bg-light">


    <div class="container">

        <div class="row justify-content-center">

            <div class="col-md-6 col-lg-5 mt-5">


                <div class="card shadow-sm">

                    <div class="card-body p-4">


                        <div class="text-center mb-4">

                            <h3>
                                Verify Your Email
                            </h3>

                            <p class="text-muted">

                                We sent a 6-digit verification
                                code to:

                            </p>

                            <strong>
                                {{ $user->email }}
                            </strong>

                        </div>


                        {{-- SUCCESS --}}

                        @if (session('success'))
                            <div class="alert alert-success">

                                {{ session('success') }}

                            </div>
                        @endif


                        {{-- ERROR --}}

                        @if (session('error'))
                            <div class="alert alert-danger">

                                {{ session('error') }}

                            </div>
                        @endif


                        {{-- VALIDATION ERRORS --}}

                        @if ($errors->any())

                            <div class="alert alert-danger">

                                @foreach ($errors->all() as $error)
                                    <div>
                                        {{ $error }}
                                    </div>
                                @endforeach

                            </div>

                        @endif


                        {{-- VERIFY FORM --}}

                        <form method="POST" action="{{ route('verification.otp.verify') }}">

                            @csrf


                            <div class="mb-4">

                                <label class="form-label">
                                    Verification Code
                                </label>

                                <input type="text" name="otp" class="form-control text-center" maxlength="6"
                                    inputmode="numeric" autocomplete="one-time-code" placeholder="000000" required>

                            </div>


                            <button type="submit" class="btn btn-primary w-100">

                                Verify Email

                            </button>

                        </form>


                        {{-- RESEND --}}

                        <div class="text-center mt-4">

                            <p class="mb-2">
                                Didn't receive the code?
                            </p>


                            <form method="POST" action="{{ route('verification.otp.resend') }}">

                                @csrf

                                <button type="submit" class="btn btn-link">

                                    Resend OTP

                                </button>

                            </form>

                        </div>


                        <div class="text-center mt-2">

                            <a href="{{ route('login') }}">
                                Back to Login
                            </a>

                        </div>


                    </div>

                </div>


            </div>

        </div>

    </div>


</body>

</html>
