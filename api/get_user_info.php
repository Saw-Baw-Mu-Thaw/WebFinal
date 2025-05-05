<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

if (!isset($_SESSION['username']) && !isset($_SESSION['userId'])) {
    die(json_encode(array('code' => 1, 'message' => 'Not Authenticated')));
}

header(http_response_code(200));
$arr = array('code' => 0, 'username' => $_SESSION['username'], 'userId' => $_SESSION['userId']);
echo json_encode($arr);
