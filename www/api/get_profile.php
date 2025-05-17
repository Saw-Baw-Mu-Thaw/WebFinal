<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username']) || !isset($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(array('code' => 3, 'message' => 'Authentication required')));
}

require_once 'skeletondb.php';

// Get the user's profile information
$userId = $_SESSION['userId'];
$conn = get_conn();

$query = "SELECT UserID, Username, Email, Verified, ProfilePic FROM users WHERE UserID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_close($conn);

if (!$user) {
    http_response_code(404);
    die(json_encode(array('code' => 1, 'message' => 'User not found')));
}

// Prepare profile picture URL
if ($user['ProfilePic']) {
    $profilePicUrl = $user['ProfilePic'];
} else {
    $profilePicUrl = './images/default-avatar.jpg';
}

// Return the user profile data
die(json_encode(array(
    'code' => 0,
    'userId' => $user['UserID'],
    'username' => $user['Username'],
    'email' => $user['Email'],
    'verified' => $user['Verified'],
    'profilePic' => $profilePicUrl
)));
