<?php
session_start();

require 'skeletondb.php'; // just in case

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

$id = $_SESSION['currNoteID'];
$action = $_SESSION['action'];

$title = get_note_title($id);

// need to check if shared note or whatnot
// username might be different
$location = "../notes/" . $_SESSION['username'] . '/' . $title . '.txt';
$file = fopen($location, 'r');

$contents = fread($file, filesize($location));

die(json_encode(array('code' => 0, 'title' => $title, 'contents' => $contents, 'action' => $action)));
