<?php

session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

if (!isset($_GET['id'])) {
    die(json_encode(array('code' => 1, 'message' => 'Missing Information')));
}

$id = intval($_GET['id']);

$res = unpin_note($id, $_SESSION['userId']);

if (!$res) {
    die(json_encode(array('code' => 2, 'message' => 'Couldn\'t unpin note')));
}

die(json_encode(array('code' => 0, 'message' => 'Note unpinned successfully')));