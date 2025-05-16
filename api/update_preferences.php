<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

// Debug information for troubleshooting
$debug = array(
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'post_data' => $_POST,
    'session' => $_SESSION
);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports POST', 'debug' => $debug)));
}

// Get userId from username if needed
require_once 'user_helper.php';
ensure_user_id();

if (!isset($_SESSION['userId'])) {
    die(json_encode(array('code' => 5, 'message' => 'User not logged in', 'debug' => $debug)));
}

$userId = $_SESSION['userId'];
$fontSize = isset($_POST['fontSize']) ? intval($_POST['fontSize']) : 16;
$noteColor = $_POST['noteColor'] ?? '#ffffff';

// Validate font size - now accepting numeric values in a range
if ($fontSize < 10 || $fontSize > 30) {
    // If outside valid range, default to 16px
    $fontSize = 16;
}

// Validate color (simple hex color validation)
if (!preg_match('/^#[a-f0-9]{6}$/i', $noteColor)) {
    die(json_encode(array('code' => 3, 'message' => 'Invalid color format', 'debug' => $debug)));
}

// Update preferences in database - passing the raw font size value, not a name
$res = update_preferences_numeric($userId, $fontSize, $noteColor);

if (!$res) {
    die(json_encode(array('code' => 1, 'message' => 'Failed to update preferences', 'debug' => $debug)));
}

echo json_encode(array('code' => 0, 'message' => 'Preferences updated successfully')); 