<?php
session_start();

if (isset($_GET['uid'])) {
    $_SESSION['userId'] = (int) $_GET['uid'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Notes App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .container {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .verify-container {
            max-width: 450px;
            width: 100%;
            padding: 2.5rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo-container {
            margin-bottom: 2.5rem;
        }

        .logo-container img {
            max-height: 90px;
            margin-bottom: 1.5rem;
        }

        .verify-title {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.25rem;
        }

        .verify-message {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #555;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .otp-input-container {
            margin-bottom: 2rem;
        }

        .otp-input {
            letter-spacing: 0.5rem;
            font-size: 1.5rem;
            text-align: center;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #ddd;
            width: 100%;
            max-width: 250px;
            margin: 0 auto;
            transition: all 0.3s ease;
        }

        .otp-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            outline: none;
        }

        .btn-verify {
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            padding: 0.85rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            color: white;
            display: inline-block;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }

        .btn-verify:hover {
            background-color: #0069d9;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 105, 217, 0.25);
        }

        .resend-link {
            color: #007bff;
            text-decoration: none;
            font-size: 0.95rem;
            display: inline-block;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .resend-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .footer {
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Dark mode support */
        body.bg-dark {
            background-color: #1a1a2e;
        }

        .bg-dark .verify-container {
            background-color: #16213e;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .bg-dark .verify-title {
            color: #f8f9fa;
        }

        .bg-dark .verify-message {
            color: #d1d1d1;
        }

        .bg-dark .otp-input {
            background-color: #1f2b46;
            border-color: #293B5F;
            color: #f8f9fa;
        }

        .bg-dark .otp-input:focus {
            border-color: #4f86f7;
            box-shadow: 0 0 0 0.2rem rgba(79, 134, 247, 0.25);
        }

        .bg-dark .btn-verify {
            background-color: #4f86f7;
        }

        .bg-dark .btn-verify:hover {
            background-color: #3a75f0;
            box-shadow: 0 5px 15px rgba(58, 117, 240, 0.3);
        }

        .bg-dark .resend-link {
            color: #4f86f7;
        }

        .bg-dark .resend-link:hover {
            color: #3a75f0;
        }

        .bg-dark .footer {
            color: #8a94a6;
        }

        /* Responsive styles */
        @media (max-width: 576px) {
            body {
                padding: 0.75rem;
            }

            .verify-container {
                padding: 2rem 1.5rem;
                border-radius: 8px;
            }

            .logo-container {
                margin-bottom: 2rem;
            }

            .logo-container img {
                max-height: 70px;
                margin-bottom: 1rem;
            }

            .verify-title {
                font-size: 1.75rem;
                margin-bottom: 1rem;
            }

            .verify-message {
                font-size: 1rem;
                margin-bottom: 1.5rem;
                padding: 0;
            }

            .otp-input {
                font-size: 1.25rem;
                padding: 0.6rem 0.8rem;
                max-width: 200px;
            }

            .btn-verify {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
                margin-bottom: 1.25rem;
            }
        }
    </style>
</head>

<body class="mode-target">
    <div class="container">
        <div class="verify-container mode-target">
            <div class="logo-container">
                <img src="images/Skeleton.png" alt="Notes App Logo" />
            </div>

            <h1 class="verify-title">Email Verification</h1>

            <p class="verify-message">
                <i class="fas fa-envelope-open-text text-primary mr-2"></i>
                We've sent a verification code to your email address. Please enter the code below to verify your
                account.
            </p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="api/verify_otp.php">
                <div class="otp-input-container">
                    <input type="text" name="otp" id="otp" class="otp-input" placeholder="Enter code" maxlength="6"
                        required>
                </div>

                <button type="submit" class="btn-verify">
                    <i class="fas fa-check-circle mr-2"></i> Verify Email
                </button>
            </form>

            <a href="#" class="resend-link" id="resendOtp">
                <i class="fas fa-redo-alt mr-1"></i> Didn't receive the code? Resend
            </a>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Check if dark mode is active in localStorage and apply it
        document.addEventListener('DOMContentLoaded', function () {
            const mode = localStorage.getItem('mode');
            if (mode === 'DARK') {
                document.body.classList.add('bg-dark');
            }

            // Add OTP input enhancements
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                
                // Auto-focus the OTP input
                otpInput.focus();

                // Only allow numbers
                otpInput.addEventListener('input', function (e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            // Resend OTP functionality (placeholder)
            document.getElementById('resendOtp').addEventListener('click', function (e) {
                e.preventDefault();
                alert('A new verification code has been sent to your email.');
            });
        });
    </script>
</body>

</html>