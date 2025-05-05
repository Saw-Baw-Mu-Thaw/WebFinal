<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

if (!isset($_GET['id'])) {
    die(json_encode(array('code' => 1, 'message' => 'Missing information')));
}

$id = intval($_GET['id']);

if (isLockedNote($id)) {
    die(json_encode(array('code' => 3, 'message' => 'Note is a locked note')));
}

$_SESSION['currNoteID'] = $id;
$_SESSION['action'] = 'EDIT';

die(json_encode(array('code' => 0, 'message' => 'Values set')));
