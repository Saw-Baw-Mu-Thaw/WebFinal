<?php
session_start();
require_once 'skeletondb.php';
require_once 'vendor/autoload.php';
date_default_timezone_set('UTC');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$connect = get_conn();
$connect->query("SET time_zone = '+00:00'");

$message = '';

// Request Password Reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["request_reset"])) {
    $user_email = trim($_POST["email"]);

    // Validate email
    if (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $message = '<label class="text-danger">Please enter a valid email.</label>';
    } else {
        // Check  email  in database
        $stmt = $connect->prepare("SELECT UserID, Username FROM users WHERE Email = ?");
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['UserID'];
            $user_name = $user['Username'];

            // Generate OTP with expired date
            $otp_code = random_int(100000, 999999);
            $expires_at = date("Y-m-d H:i:s", strtotime("+24 hours"));
            $type = 'password_reset';

            // Delete existing password resset in otp
            $stmt = $connect->prepare("DELETE FROM otp WHERE UserID = ? AND Type = ?");
            $stmt->bind_param("is", $user_id, $type);
            $stmt->execute();

            // Insert new OTP into table
            $stmt = $connect->prepare("INSERT INTO otp (UserID, Code, Type, ExpiresAt) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $user_id, $otp_code, $type, $expires_at);

            if ($stmt->execute()) {
                // Generate reset link
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                $host = $_SERVER['HTTP_HOST'];
                $script_dir = dirname($_SERVER['SCRIPT_NAME']);
                $reset_link = $protocol . $host . $script_dir . "/forget_password_verify_form.php?uid=" . $user_id;

                // Send reset email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'webfinal2005@gmail.com';
                    $mail->Password = 'dctg hyjz dvas kkmn';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('webfinal2005@gmail.com', 'Note Taking Website');
                    $mail->addAddress($user_email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset - Note Taking Website';
                    $mail->Body = "
                        <p>Hello $user_name,</p>
                        <p>We received a request to reset your password.</p>
                        <p>Your OTP code is: <strong>$otp_code</strong></p>
                        <p>Please click the link below to enter your OTP and reset your password:</p>
                        <a href='$reset_link'>Reset Password Here</a>
                        <p>If you did not request this password reset, please ignore this email.</p>
                    ";

                    $mail->send();
                    $message = '<label class="text-success">Password reset instructions have been sent to your email.</label>';
                } catch (Exception $e) {
                    $message = '<label class="text-danger">Mailer Error: ' . $mail->ErrorInfo . '</label>';
                }
            } else {
                $message = '<label class="text-danger">Failed to generate reset code. Please try again.</label>';
            }
        } else {
            $message = '<label class="text-success">If your email is registered , you will receive password reset message in email.</label>';
        }
    }
}
?>