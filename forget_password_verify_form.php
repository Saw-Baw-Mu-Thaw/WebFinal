<?php
require_once 'api/verify_password_reset.php';

$uid = $_GET['uid'] ?? null;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Password Reset</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/global.css">
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Verify Password Reset</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if (!$uid) {
                            echo '<div class="alert alert-danger">Invalid request. Please try again.</div>';
                            echo '<div class="text-center mt-3"><a href="forgot_password.php">Back to Password Reset</a></div>';
                        } else {
                            if (!empty($message)) {
                                echo '<div class="alert alert-info">' . $message . '</div>';
                            }
                            ?>
                            <form method="post" action="">
                                <input type="hidden" name="uid" value="<?php echo htmlspecialchars($uid); ?>">
                                <div class="form-group">
                                    <label for="otp">OTP Code</label>
                                    <input type="text" name="otp" id="otp" class="form-control" required>
                                    <small class="form-text text-muted">Enter the 6-digit code sent to your email.</small>
                                </div>
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="reset_password" class="btn btn-primary btn-block">Reset
                                        Password</button>
                                </div>
                            </form>
                            <div class="text-center mt-3">
                                <a href="forgot_password.php">Request a new code</a> | <a href="login.php">Back to Login</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>