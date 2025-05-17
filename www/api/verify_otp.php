<?php
session_start();
require 'skeletondb.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['userId'] ?? null;
    $otp = $_POST['otp'] ?? null;

    if (!$userId || !$otp) {

        if (isset($_POST['otp'])) {
            echo "<script>console.log('OTP from form: " . htmlspecialchars($_POST['otp']) . "');</script>";
        } else {
            echo "<script>console.log('OTP from form: Not Set');</script>";
        }

        exit();
    }

    $conn = get_conn();


    $stmt = $conn->prepare('SELECT * FROM otp WHERE UserID = ? AND Code = ? AND Type = "activation" AND IsUsed = 0 AND ExpiresAt > NOW()');
    $stmt->bind_param('ii', $userId, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $stmt = $conn->prepare('UPDATE users SET Verified = 1 WHERE UserID = ?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        $stmt = $conn->prepare('UPDATE otp SET IsUsed = 1 WHERE UserID = ? AND Code = ?');
        $stmt->bind_param('ii', $userId, $otp);
        $stmt->execute();

        $_SESSION['verified'] = true;
        $_SESSION['success'] = "Your account has been verified!";

        header('Location: ../index.php');
        exit();
    } else {
        $_SESSION['error'] = "Invalid or expired OTP.";
        header("Location: ../email_verify_form.php");
    }
} else {
    echo "Invalid request method.";
}