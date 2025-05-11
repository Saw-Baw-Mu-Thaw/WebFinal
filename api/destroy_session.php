<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

$res = session_destroy();

if ($res) {
    echo json_encode(array('code' => 0));
}
