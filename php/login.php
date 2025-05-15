<?php
session_start();

require 'skeletondb.php';

if (isset($_SESSION['username'])) {
    header('Location: index.php');
    die();
}

$user = "";
$pass = "";
$error = "";

if (isset($_POST['submit']) && !empty($_POST['submit'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $user = $_POST['username'];
        $pass = $_POST['password'];

        // grab password from db here
        if (authenticate($user, $pass)) {
            $_SESSION['username'] = $_POST['username'];

            //redirect to index.php
            header('Location: index.php');
        } else {
            $error = "Incorrect Username or Password";
        }
    }
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
            <img class="img-fluid" src="/images/Skeleton.png" alt="SkeleLogo" />
            <h1 class="text-center">Login</h1>
            <div class="border border-rounded p-3">
                <form method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Username"
                            required value='<?= $user ?>'>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password" required value='<?= $pass ?>'>
                    </div>

                    <?php
                    if (!empty($error)) {
                        echo "<div class='alert alert-danger text-center'>$error</div>";
                    }
                    ?>

                    <button type="submit" name='submit' value='submit' class="btn btn-success">Login</button>
                    <button type="reset" class="btn btn-danger">Reset</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
</body>


</html>