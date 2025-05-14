<?php
session_start();

require_once 'php/config.php';

if (isset($_GET['uid'])) {
    $_SESSION['userId'] = (int) $_GET['uid'];
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Verify Email</title>
</head>

<body>
    <h2>Email Verification</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error'];
        unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <form method="POST" action="api/verify_otp.php">
        <label for="otp">Enter OTP:</label>
        <input type="text" name="otp" id="otp" required>
        <br><br>
        <button type="submit">Verify</button>
    </form>
</body>

</html>