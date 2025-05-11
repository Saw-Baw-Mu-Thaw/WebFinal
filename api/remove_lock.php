<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports DELETE')));
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Missing Information')));
}

$id = intval($_GET['id']);

$res = delete_locked_note($id);

if (!$res) {
    die(json_encode(array('code' => 2, 'message' => 'Could not remove lock')));
}

die(json_encode(array('code' => 0, 'message' => 'Lock removed successfully')));
