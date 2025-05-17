<?php

require 'skeletondb.php';

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $conn = get_conn();

    // Check if the user is verified
    $stmt = $conn->prepare('SELECT Verified FROM users WHERE UserID = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $isVerified = isset($user) && $user['Verified'] == 1;
}
?>

<!-- Display unverified notification on all pages -->
<?php if (isset($isVerified) && !$isVerified): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; text-align: center;">
        <strong>Your account is unverified!</strong> Please check your email and activate your account. If you didn’t
        receive the email,
        <!-- <a href="resend_verification.php?uid=<?= $userId ?>"> -->
        Resend Verification Email</a> to resend it.
    </div>
<?php endif; ?>