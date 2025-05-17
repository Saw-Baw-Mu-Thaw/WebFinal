<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

if (!isset($_SESSION['username']) || !isset($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(array('code' => 3, 'message' => 'Authentication required')));
}

if (!isset($_GET['noteId']) || empty($_GET['noteId'])) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Note ID parameter is required')));
}

$noteId = $_GET['noteId'];
$userId = $_SESSION['userId'];

// Check if the user is the owner of the note
if (!is_note_owner($userId, $noteId)) {
    die(json_encode(array('code' => 6, 'message' => 'You are not the owner of this note')));
}

// Get all collaborators
$collaborators = get_collaborators($noteId, $userId);

die(json_encode(array('code' => 0, 'collaborators' => $collaborators))); 