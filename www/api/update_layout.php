<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

if (!isset($_GET['layout'])) {
    die(json_encode(array('code' => 1, 'message' => 'Missing information')));
}

if (empty($_GET['layout'])) {
    die(json_encode(array('code' => 2, 'message' => 'Lack of information')));
}

$layout = $_GET['layout'];
$userId = $_SESSION['userId'];

$res = update_layout($userId, $layout);

if (!$res) {
    die(json_encode(array('code' => 3, 'message' => 'Layout couldn\'t be changed')));
}

die(json_encode(array('code' => 0, 'message' => 'Layout changed successfully')));
