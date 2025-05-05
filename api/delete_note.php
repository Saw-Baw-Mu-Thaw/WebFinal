<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports DELETE')));
}

if (!isset($_GET['noteId']) && !isset($_GET['title'])) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Missing Information')));
}

$id = intval($_GET['noteId']);
$title = $_GET['title'];
// delete file
$res1 = unlink('../notes/' . $_SESSION['username'] . '/' . $title . '.txt');

// delete db entries
$res2 = delete_note($id);

if (!$res1 || !$res2) {
    die(json_encode(array('code' => 2, 'message' => 'Could not delete note')));
} else {
    die(json_encode(array('code' => 0, 'message' => 'Note deleted successfully')));
}
