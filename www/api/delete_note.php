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

$noteId = intval($_GET['noteId']);
$title = $_GET['title'];
$location = get_location($noteId);

if (!is_note_owner($_SESSION['userId'], $noteId)) {
    die(json_encode(array('code' => 6, 'message' => 'You are not the owner of this note')));
}
// delete file
$res1 = unlink($location);

// delete db entries
// due to foreign keys, must delete from
// locked notes table, pinned notes table, shared notes table
// and then notes table.
$res2 = delete_note($noteId);

if (!$res1 || !$res2) {
    die(json_encode(array('code' => 2, 'message' => 'Could not delete note')));
} else {
    die(json_encode(array('code' => 0, 'message' => 'Note deleted successfully')));
}
