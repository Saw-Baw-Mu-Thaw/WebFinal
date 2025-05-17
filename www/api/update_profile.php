<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username']) || !isset($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(array('code' => 3, 'message' => 'Authentication required')));
}

require_once 'skeletondb.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(array('code' => 2, 'message' => 'Method not allowed')));
}

// Get the user's ID from the session
$userId = $_SESSION['userId'];

// Get the current user information to verify password
$conn = get_conn();
$query = "SELECT Password FROM users WHERE UserID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Check if the current password is correct
$currentPassword = isset($_POST['currentPassword']) ? $_POST['currentPassword'] : '';

if (!password_verify($currentPassword, $user['Password'])) {
    http_response_code(401);
    die(json_encode(array('code' => 4, 'message' => 'Current password is incorrect')));
}

// Initialize the updates array
$updates = array();
$updateFields = '';
$updateParams = array();
$types = '';

// Email update
if (isset($_POST['email']) && !empty($_POST['email'])) {
    $email = $_POST['email'];
    $updateFields .= "Email = ?, ";
    $updateParams[] = $email;
    $types .= 's';
}

// Password update
if (isset($_POST['newPassword']) && !empty($_POST['newPassword'])) {
    $newPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
    $updateFields .= "Password = ?, ";
    $updateParams[] = $newPassword;
    $types .= 's';
}

// Handle profile picture upload
$profilePicUrl = null;
if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
    // Create uploads directory if it doesn't exist
    $uploadsDir = '../images/avatars/';
    if (!file_exists($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }
    
    // Generate a unique filename
    $filename = uniqid('avatar_') . '_' . $_FILES['profilePicture']['name'];
    $targetFile = $uploadsDir . $filename;
    
    // Check file type
    $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
    $fileType = $_FILES['profilePicture']['type'];
    
    if (!in_array($fileType, $allowedTypes)) {
        http_response_code(400);
        die(json_encode(array('code' => 5, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.')));
    }
    
    // Attempt to move the uploaded file
    if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetFile)) {
        $profilePicUrl = 'images/avatars/' . $filename;
        $updateFields .= "ProfilePic = ?, ";
        $updateParams[] = $profilePicUrl;
        $types .= 's';
    } else {
        http_response_code(500);
        die(json_encode(array('code' => 6, 'message' => 'Failed to upload profile picture.')));
    }
}

// If there are updates to be made
if (!empty($updateFields)) {
    // Remove the trailing comma and space
    $updateFields = rtrim($updateFields, ', ');
    
    // Add userId to parameters and type
    $updateParams[] = $userId;
    $types .= 'i';
    
    // Update the user's profile
    $query = "UPDATE users SET $updateFields WHERE UserID = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    // Bind parameters dynamically
    mysqli_stmt_bind_param($stmt, $types, ...$updateParams);
    
    $success = mysqli_stmt_execute($stmt);
    
    if (!$success) {
        http_response_code(500);
        die(json_encode(array('code' => 7, 'message' => 'Failed to update profile: ' . mysqli_error($conn))));
    }
}

mysqli_close($conn);

// Return success
die(json_encode(array(
    'code' => 0, 
    'message' => 'Profile updated successfully',
    'profilePic' => $profilePicUrl
))); 