<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports POST')));
}

if (!isset($_SESSION['username']) || !isset($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(array('code' => 3, 'message' => 'Authentication required')));
}

$input = json_decode(file_get_contents('php://input'));

if (is_null($input)) {
    die(json_encode(array('code' => 2, 'message' => 'Only support JSON')));
}

if (!property_exists($input, 'notificationId')) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Notification ID is required')));
}

$notificationId = $input->notificationId;

// Mark notification as read
$result = mark_notification_read($notificationId);

if ($result) {
    die(json_encode(array('code' => 0, 'message' => 'Notification marked as read')));
} else {
    die(json_encode(array('code' => 8, 'message' => 'Failed to mark notification as read')));
} 