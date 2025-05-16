<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: index.php');
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="Description" content="Notes App Login" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
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
        
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .logo-container img {
            max-height: 80px;
            margin-bottom: 1rem;
        }
        
        .form-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-group label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #555;
        }
        
        .form-control {
            border-radius: 5px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .btn-login {
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background-color: #0069d9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 105, 217, 0.2);
        }
        
        .btn-reset {
            background-color: transparent;
            color: #6c757d;
            border: 1px solid #6c757d;
            border-radius: 5px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            width: 100%;
            margin-top: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .btn-reset:hover {
            background-color: #f8f9fa;
        }
        
        .links-container {
            display: flex;
            justify-content: space-between;
            margin: 1.5rem 0;
            font-size: 0.9rem;
        }
        
        .links-container a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .links-container a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        
        #errorDiv {
            display: none;
            margin: 1rem 0;
            padding: 0.75rem;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        
        /* Dark mode support */
        body.bg-dark {
            background-color: #1a1a2e;
        }
        
        .bg-dark .login-container {
            background-color: #16213e;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .bg-dark .form-title {
            color: #f8f9fa;
        }
        
        .bg-dark .form-group label {
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
        
        .bg-dark .btn-reset {
            color: #d1d1d1;
            border-color: #293B5F;
        }
        
        .bg-dark .btn-reset:hover {
            background-color: rgba(79, 134, 247, 0.1);
        }
        
        .bg-dark .links-container a {
            color: #4f86f7;
        }
        
        .bg-dark .links-container a:hover {
            color: #3a75f0;
        }
        
        /* Responsive styles */
        @media (max-width: 576px) {
            body {
                padding: 0.5rem;
            }
            
            .login-container {
                padding: 1.5rem;
                border-radius: 8px;
            }
            
            .logo-container img {
                max-height: 60px;
            }
            
            .form-title {
                font-size: 1.5rem;
                margin-bottom: 1.25rem;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
            
            .form-control {
                padding: 0.6rem 0.8rem;
                font-size: 0.95rem;
            }
            
            .btn-login, .btn-reset {
                padding: 0.6rem 1.2rem;
                font-size: 0.95rem;
            }
            
            .links-container {
                flex-direction: column;
                align-items: center;
                gap: 0.75rem;
                margin: 1.25rem 0;
            }
        }
        
        /* Tablet styles */
        @media (min-width: 577px) and (max-width: 768px) {
            .login-container {
                max-width: 360px;
                padding: 1.75rem;
            }
        }
        
        /* Ensure form is usable when keyboard is open on mobile */
        @media (max-height: 600px) and (max-width: 576px) {
            body {
                align-items: flex-start;
                padding-top: 1rem;
            }
            
            .logo-container img {
                max-height: 50px;
                margin-bottom: 0.5rem;
            }
            
            .form-title {
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }
        }
    </style>
    <title>Login | Notes App</title>
</head>

<body class="mode-target">
    <div class="container">
        <div class="login-container">
            <div class="logo-container">
                <img src="images/Skeleton.png" alt="Notes App Logo" />
                <h1 class="form-title">Welcome Back</h1>
            </div>
            
            <form method="post" id="loginForm">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user mr-2"></i>Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock mr-2"></i>Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
                </div>

                <div class="links-container">
                    <div>
                        <a href="registration_form.php"><i class="fas fa-user-plus mr-1"></i>Create Account</a>
                    </div>
                    <div>
                        <a href="forget_password_form.php"><i class="fas fa-key mr-1"></i>Forgot Password?</a>
                    </div>
                </div>

                <div id="errorDiv" class="alert alert-danger text-center"></div>

                <button type="submit" class="btn btn-primary btn-login"><i class="fas fa-sign-in-alt mr-2"></i>Login</button>
                <button type="reset" class="btn btn-reset"><i class="fas fa-redo mr-2"></i>Reset</button>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted"> 2025 Notes App. All rights reserved.</small>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script src="js/login.js"></script>
</body>

</html>