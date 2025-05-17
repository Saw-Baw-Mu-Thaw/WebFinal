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

$userId = $_SESSION['userId'];

// Get notifications
$notifications = get_notifications($userId);

die(json_encode(array('code' => 0, 'notifications' => $notifications))); 