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
$userId = $_SESSION['userId'];


$title = get_note_title($id);

// need to check if shared note or whatnot
// might not be creator
$location = get_location($id);
$role = "EDITOR";
if (is_shared_note($userId, $id)) {
    $role = get_role($userId, $id);
}

$contents = file_get_contents($location);

$labels = get_labels($userId, $id);

die(json_encode(array('code' => 0, 'title' => $title, 'contents' => $contents, 'action' => $action, 'role' => $role, 'labels' => $labels)));
