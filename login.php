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
    <meta name="Description" content="Enter your description here" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Login</title>
</head>

<body>
    <div class="container">
        <div class="m-auto col-lg-4 col-md-3 col-lg-3 col-12 p-2">
            <img class="img-fluid" src="images/Skeleton.png" alt="SkeleLogo" />
            <h1 class="text-center">Login</h1>
            <div class="border border-rounded p-3">
                <form method="post" id="loginForm">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Username"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>

                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="registration_form.php">Register here</a></p>
                    </div>

                    <div class=" mt-3 text-right">
                        <a href="forget_password_form.php"> Forget Password?</a>
                    </div>

                    <div id="errorDiv" class="alert alert-danger text-center"></div>

                    <button type="submit" class="btn btn-success">Login</button>
                    <button type="reset" class="btn btn-danger">Reset</button>
                </form>

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