<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="Description" content="Notes App Logout" />
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
        
        .logout-container {
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
        
        .logout-title {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.25rem;
        }
        
        .logout-message {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #555;
            margin-bottom: 2.5rem;
            padding: 0 1rem;
        }
        
        .btn-login {
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
            margin-bottom: 2rem;
        }
        
        .btn-login:hover {
            background-color: #0069d9;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 105, 217, 0.25);
            color: white;
            text-decoration: none;
        }
        
        /* Dark mode support */
        body.bg-dark {
            background-color: #1a1a2e;
        }
        
        .bg-dark .logout-container {
            background-color: #16213e;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .bg-dark .logout-message {
            color: #f8f9fa;
        }
        
        .bg-dark .logout-subtext {
            color: #d1d1d1;
        }
        
        .bg-dark .footer {
            color: #8a94a6;
        }
        
        .bg-dark .btn-login {
            background-color: #4f86f7;
        }
        
        .bg-dark .btn-login:hover {
            background-color: #3a75f0;
            box-shadow: 0 5px 15px rgba(58, 117, 240, 0.3);
        }
        
        .footer {
            margin-top: 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Responsive styles */
        @media (max-width: 576px) {
            body {
                padding: 0.75rem;
            }
            
            .logout-container {
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
            
            .logout-title {
                font-size: 1.75rem;
                margin-bottom: 1rem;
            }
            
            .logout-message {
                font-size: 1rem;
                margin-bottom: 2rem;
                padding: 0;
            }
            
            .btn-login {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
                margin-bottom: 1.5rem;
            }
            
            .footer {
                margin-top: 1rem;
                font-size: 0.85rem;
            }
        }
        
        /* Tablet styles */
        @media (min-width: 577px) and (max-width: 768px) {
            .logout-container {
                max-width: 360px;
                padding: 1.75rem;
            }
        }
        
        /* Landscape orientation on mobile */
        @media (max-height: 500px) and (max-width: 767px) {
            body {
                align-items: flex-start;
                padding-top: 1rem;
            }
            
            .logo-container img {
                max-height: 50px;
                margin-bottom: 0.5rem;
            }
            
            .logout-title {
                font-size: 1.25rem;
                margin-bottom: 0.5rem;
            }
            
            .logout-message {
                margin-bottom: 1rem;
            }
            
            .footer {
                margin-top: 1rem;
            }
        }
    </style>
    <title>Logged Out | Notes App</title>
</head>

<body class="mode-target">
    <div class="container">
        <div class="logout-container">
            <div class="logo-container">
                <img src="images/Skeleton.png" alt="Notes App Logo" />
            </div>
            
            <h1 class="logout-title">You've Been Logged Out</h1>
            <p class="logout-message">
                <i class="fas fa-check-circle text-success mr-2"></i>
                Your session has ended successfully. Thank you for using Notes App!
            </p>
            
            <a href="login.php" class="btn-login">
                <i class="fas fa-sign-in-alt mr-2"></i>Log Back In
            </a>
            
            <div class="footer">
                <p class="mb-0">&copy; 2025 Notes App. All rights reserved.</p>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    
    <script>
        // Check if dark mode is active in localStorage and apply it
        document.addEventListener('DOMContentLoaded', function() {
            const mode = localStorage.getItem('mode');
            if (mode === 'DARK') {
                document.body.classList.add('bg-dark');
            }
        });
    </script>
</body>

</html>