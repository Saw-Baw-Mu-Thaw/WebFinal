<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

$userId = $_SESSION['userId'];
$labels = get_uniq_labels($userId);

die(json_encode(array('code' => 0, 'labels' => $labels)));
