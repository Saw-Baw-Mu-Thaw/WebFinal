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
$error_user_name = '';
$error_user_email = '';
$error_user_password = '';
$error_user_password_confirm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["register"])) {
    $user_name = trim($_POST["username"]);
    $user_email = trim($_POST["email"]);
    $user_password = trim($_POST["password"]);
    $user_password_confirm = trim($_POST["password_confirm"]);

    // Validate user input
    if (empty($user_name)) {
        $error_user_name = "Please enter your name.";
    }

    if (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $error_user_email = "Please enter a valid email.";
    }

    if (empty($user_password)) {
        $error_user_password = "Please enter your password.";
    }

    if ($user_password !== $user_password_confirm) {
        $error_user_password_confirm = "Passwords do not match.";
    }


    if ($error_user_name === '' && $error_user_email === '' && $error_user_password === '' && $error_user_password_confirm === '') {

        $stmt = $connect->prepare("SELECT Email FROM users WHERE Email = ?");
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = '<label class="text-danger">Email Already Registered</label>';
        } else {

            $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);


            $stmt = $connect->prepare("INSERT INTO users (Username, Email, Password, Verified) VALUES (?, ?, ?, 0)");
            $stmt->bind_param("sss", $user_name, $user_email, $hashed_password);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;

                //Insert Default Prefenences
                $stmt = $connect->prepare("INSERT INTO preferences (UserID, FontSize , Mode , Layout) VALUES (?, DEFAULT, DEFAULT, DEFAULT)");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                // Generate OTP and expiration
                $otp_code = random_int(100000, 999999);
                $expires_at = date("Y-m-d H:i:s", strtotime("+24 hours"));
                $type = 'activation';

                // Insert OTP into table
                $stmt = $connect->prepare("INSERT INTO otp (UserID, Code, Type, ExpiresAt) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iis", $user_id, $otp_code, $type, $Ex);
                $stmt->execute();

                // Auto login the user
                $_SESSION["userId"] = $user_id;
                $_SESSION['username'] = $user_name;

                // Generate activation link
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                $host = $_SERVER['HTTP_HOST'];
                $script_dir = dirname($_SERVER['SCRIPT_NAME']);
                $activation_link = $protocol . $host . $script_dir . "/email_verify_form.php?uid=" . $user_id;

                // Send verification email
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
                    $mail->Subject = 'Email Verification - Note Taking Website';
                    $mail->Body = "
                                 <p>Thank you for registering!</p>
                                 <p>Your OTP code is: <strong>$otp_code</strong></p>
                                 <p>Please click the link below to enter your OTP and verify your email:</p>
                                 <a href='$activation_link'>Verify Email Here</a>
                                 <p>This code will expire in 15 minutes.</p>
                                ";

                    $mail->send();

                    // Redirect to home or dashboard
                    header("Location: index.php?uid=$user_id");
                    exit();

                } catch (Exception $e) {
                    $message = '<label class="text-danger">Mailer Error: ' . $mail->ErrorInfo . '</label>';
                }
            } else {
                $message = '<label class="text-danger">Registration failed. Please try again.</label>';
            }
        }
    }
}
?>