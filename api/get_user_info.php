<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username']) || !isset($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(array('code' => 3, 'message' => 'Authentication required')));
}

// Return the current user's information
die(json_encode(array(
    'code' => 0,
    'userId' => $_SESSION['userId'],
    'username' => $_SESSION['username']
)));
