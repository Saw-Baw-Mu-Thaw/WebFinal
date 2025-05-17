<?php
require_once 'api/password_reset_service.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Notes App</title>
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
        
        .forgot-container {
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
        
        .forgot-title {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.25rem;
        }
        
        .forgot-message {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #555;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }
        
        .form-control {
            height: auto;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .form-text {
            text-align: left;
            margin-top: -1.25rem;
            margin-bottom: 1.5rem;
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
        
        .login-link {
            color: #007bff;
            text-decoration: none;
            font-size: 0.95rem;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .login-link:hover {
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
        
        .bg-dark .forgot-container {
            background-color: #16213e;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .bg-dark .forgot-title {
            color: #f8f9fa;
        }
        
        .bg-dark .forgot-message {
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
        
        .bg-dark .login-link {
            color: #4f86f7;
        }
        
        .bg-dark .login-link:hover {
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
            
            .forgot-container {
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
            
            .forgot-title {
                font-size: 1.75rem;
                margin-bottom: 1rem;
            }
            
            .forgot-message {
                font-size: 1rem;
                margin-bottom: 1.5rem;
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
        }
    </style>
</head>
<body class="mode-target">
    <div class="container">
        <div class="forgot-container mode-target">
            <div class="logo-container">
                <img src="images/Skeleton.png" alt="Notes App Logo" />
            </div>
            
            <h1 class="forgot-title">Forgot Password</h1>
            
            <p class="forgot-message">
                <i class="fas fa-key text-primary mr-2"></i>
                Enter your email address below and we'll send you a code to reset your password.
            </p>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-info" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email address" required>
                    <small class="form-text text-muted">We'll send a verification code to this email</small>
                </div>
                
                <button type="submit" name="request_reset" class="btn-reset">
                    <i class="fas fa-paper-plane mr-2"></i> Send Reset Code
                </button>
            </form>
            
            <a href="login.php" class="login-link">
                <i class="fas fa-arrow-left mr-1"></i> Back to Login
            </a>
            
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
            
            // Auto-focus the email input
            document.getElementById('email').focus();
        });
    </script>
</body>
</html>