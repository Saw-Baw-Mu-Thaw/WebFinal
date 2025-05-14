<?php
require 'api/registration_service.php';

?>
<!DOCTYPE html>
<html>

<head>
    <title>Register - Note Taking Website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-box {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        .register-box h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .register-box input[type="text"],
        .register-box input[type="email"],
        .register-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .register-box input[type="submit"] {
            width: 100%;
            padding: 10px;
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .register-box input[type="submit"]:hover {
            background: #3272d3;
        }

        .message {
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
        }

        .message strong {
            color: #333;
        }
    </style>
</head>

<body>
    <form class="register-box" method="POST" action="">
        <h2>Register</h2>
        <input type="text" name="username" placeholder="User Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirm" placeholder="Confirm Password" required>
        <input type="submit" name="register" value="Register">
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </form>
</body>

</html>