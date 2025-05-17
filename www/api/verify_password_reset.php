<?php
session_start();
require 'skeletondb.php';


$connect = get_conn();
$message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["reset_password"])) {
    $user_id = $_POST["uid"];
    $otp_code = trim($_POST["otp"]);
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Validate inputs
    if (empty($otp_code)) {
        $message = '<label class="text-danger">Please enter the OTP code.</label>';
    } else if (empty($new_password)) {
        $message = '<label class="text-danger">Please enter a new password.</label>';
    } else if ($new_password !== $confirm_password) {
        $message = '<label class="text-danger">Passwords do not match.</label>';
    } else {
        // Check whether OTP valid / expired
        $stmt = $connect->prepare("SELECT * FROM otp WHERE UserID = ? AND Code = ? AND Type = 'password_reset' AND ExpiresAt > NOW()");
        $stmt->bind_param("ii", $user_id, $otp_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // if otp valid, update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $connect->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);

            if ($stmt->execute()) {
                // Delete the used OTP
                $stmt = $connect->prepare("DELETE FROM otp WHERE UserID = ? AND Type = 'password_reset'");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                $message = '<label class="text-success">Password reset successful. You can now <a href="login.php">login</a> with your new password.</label>';
            } else {
                $message = '<label class="text-danger">Failed to update password. Please try again.</label>';
            }
        } else {
            $message = '<label class="text-danger">Invalid or expired OTP code. Please request a new password reset.</label>';
        }
    }
}
?>