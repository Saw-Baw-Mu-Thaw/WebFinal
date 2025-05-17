<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

if (!isset($_SESSION['username']) || !isset($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(array('code' => 3, 'message' => 'Authentication required')));
}

if (!isset($_GET['email']) || empty($_GET['email'])) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Email parameter is required')));
}

$email = $_GET['email'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(array('code' => 2, 'message' => 'Invalid email format')));
}

// Check if the email exists
$user = check_user_email($email);

if ($user) {
    // Don't share too much information, just confirm email exists
    die(json_encode(array('code' => 0, 'exists' => true)));
} else {
    die(json_encode(array('code' => 5, 'exists' => false, 'message' => 'Email not found')));
} 