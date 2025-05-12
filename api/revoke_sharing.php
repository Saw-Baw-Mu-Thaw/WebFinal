<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports POST')));
}

if (!isset($_SESSION['username']) || !isset($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(array('code' => 3, 'message' => 'Authentication required')));
}

$input = json_decode(file_get_contents('php://input'));

if (is_null($input)) {
    die(json_encode(array('code' => 2, 'message' => 'Only support JSON')));
}

if (!property_exists($input, 'noteId') || !property_exists($input, 'collaboratorId')) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Lack of information')));
}

$noteId = $input->noteId;
$collaboratorId = $input->collaboratorId;
$ownerId = $_SESSION['userId'];

// Check if the user is the owner of the note
if (!is_note_owner($ownerId, $noteId)) {
    die(json_encode(array('code' => 6, 'message' => 'You are not the owner of this note')));
}

// Revoke sharing
$result = revoke_note_sharing($noteId, $ownerId, $collaboratorId);

if ($result) {
    die(json_encode(array('code' => 0, 'message' => 'Sharing revoked successfully')));
} else {
    die(json_encode(array('code' => 8, 'message' => 'Failed to revoke sharing')));
} 