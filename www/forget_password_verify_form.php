<?php
require_once 'api/verify_password_reset.php';

$uid = $_GET['uid'] ?? null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Notes App</title>
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
        
        .reset-container {
            max-width: 480px;
            width: 100%;
            padding: 2.5rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .logo-container {
            margin-bottom: 2rem;
        }
        
        .logo-container img {
            max-height: 80px;
            margin-bottom: 1.25rem;
        }
        
        .reset-title {
            font-size: 1.85rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .reset-message {
            font-size: 1.05rem;
            line-height: 1.5;
            color: #555;
            margin-bottom: 1.5rem;
            padding: 0 0.5rem;
        }
        
        .form-control {
            height: auto;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .form-text {
            text-align: left;
            margin-top: -1rem;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
        }
        
        .otp-input {
            letter-spacing: 0.5em;
            text-align: center;
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .btn-reset {
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            padding: 0.85rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            color: white;
            display: inline-block;
            width: 100%;
            margin-bottom: 1.5rem;
        }
        
        .btn-reset:hover {
            background-color: #0069d9;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 105, 217, 0.25);
        }
        
        .link-container {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .action-link {
            color: #007bff;
            text-decoration: none;
            font-size: 0.95rem;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .action-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        
        .footer {
            margin-top: 1.5rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .password-group {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
        }
        
        /* Dark mode support */
        body.bg-dark {
            background-color: #1a1a2e;
        }
        
        .bg-dark .reset-container {
            background-color: #16213e;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .bg-dark .reset-title {
            color: #f8f9fa;
        }
        
        .bg-dark .reset-message {
            color: #d1d1d1;
        }
        
        .bg-dark .form-control {
            background-color: #1f2b46;
            border-color: #293B5F;
            color: #f8f9fa;
        }
        
        .bg-dark .form-control:focus {
            border-color: #4f86f7;
            box-shadow: 0 0 0 0.2rem rgba(79, 134, 247, 0.25);
        }
        
        .bg-dark .form-text {
            color: #a0aec0;
        }
        
        .bg-dark .btn-reset {
            background-color: #4f86f7;
        }
        
        .bg-dark .btn-reset:hover {
            background-color: #3a75f0;
            box-shadow: 0 5px 15px rgba(58, 117, 240, 0.3);
        }
        
        .bg-dark .action-link {
            color: #4f86f7;
        }
        
        .bg-dark .action-link:hover {
            color: #3a75f0;
        }
        
        .bg-dark .footer {
            color: #8a94a6;
        }
        
        .bg-dark .password-toggle {
            color: #a0aec0;
        }
        
        /* Responsive styles */
        @media (max-width: 576px) {
            body {
                padding: 0.75rem;
            }
            
            .reset-container {
                padding: 2rem 1.5rem;
                border-radius: 8px;
            }
            
            .logo-container {
                margin-bottom: 1.5rem;
            }
            
            .logo-container img {
                max-height: 70px;
                margin-bottom: 1rem;
            }
            
            .reset-title {
                font-size: 1.6rem;
                margin-bottom: 0.75rem;
            }
            
            .reset-message {
                font-size: 0.95rem;
                margin-bottom: 1.25rem;
                padding: 0;
            }
            
            .form-control {
                padding: 0.6rem 0.8rem;
            }
            
            .btn-reset {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
                margin-bottom: 1.25rem;
            }
            
            .link-container {
                flex-direction: column;
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body class="mode-target">
    <div class="container">
        <div class="reset-container mode-target">
            <div class="logo-container">
                <img src="images/Skeleton.png" alt="Notes App Logo" />
            </div>
            
            <h1 class="reset-title">Reset Your Password</h1>
            
            <?php if (!$uid): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> Invalid request. Please try again.
                </div>
                <div class="mt-4">
                    <a href="forget_password_form.php" class="action-link">
                        <i class="fas fa-arrow-left me-1"></i> Back to Password Reset
                    </a>
                </div>
            <?php else: ?>
                <p class="reset-message">
                    <i class="fas fa-shield-alt text-primary me-1"></i>
                    Enter the verification code sent to your email and create a new password.
                </p>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="" id="resetForm">
                    <input type="hidden" name="uid" value="<?php echo htmlspecialchars($uid); ?>">
                    
                    <div class="form-group">
                        <input type="text" name="otp" id="otp" class="form-control otp-input" 
                               placeholder="000000" maxlength="6" required>
                        <small class="form-text text-muted">Enter the 6-digit code sent to your email</small>
                    </div>
                    
                    <div class="form-group password-group">
                        <input type="password" name="new_password" id="new_password" class="form-control" 
                               placeholder="New Password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <div class="form-group password-group">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
                               placeholder="Confirm New Password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <small class="form-text text-muted">Both passwords must match</small>
                    </div>
                    
                    <button type="submit" name="reset_password" class="btn-reset">
                        <i class="fas fa-lock me-2"></i> Reset Password
                    </button>
                </form>
                
                <div class="link-container">
                    <a href="forget_password_form.php" class="action-link">
                        <i class="fas fa-sync-alt me-1"></i> Request a new code
                    </a>
                    <a href="login.php" class="action-link">
                        <i class="fas fa-arrow-left me-1"></i> Back to Login
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="footer">
                <p class="mb-0">&copy; 2025 Notes App. All rights reserved.</p>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Check if dark mode is active in localStorage and apply it
        document.addEventListener('DOMContentLoaded', function() {
            const mode = localStorage.getItem('mode');
            if (mode === 'DARK') {
                document.body.classList.add('bg-dark');
            }
            
            // Auto-focus the OTP input
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.focus();
                
                // Only allow numbers in OTP field
                otpInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
                
                // Auto-focus to password field when OTP is complete
                otpInput.addEventListener('keyup', function() {
                    if (this.value.length === 6) {
                        document.getElementById('new_password').focus();
                    }
                });
            }
        });
        
        // Toggle password visibility
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Form validation
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match. Please try again.');
            }
        });
    </script>
</body>
</html>