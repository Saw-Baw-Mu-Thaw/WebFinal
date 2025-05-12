<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports POST')));
}

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    die(json_encode(array('code' => 1, 'message' => 'Not authorized')));
}

$input = json_decode(file_get_contents('php://input'));

if (is_null($input)) {
    die(json_encode(array('code' => 2, 'message' => 'Only support JSON')));
}

if (property_exists($input, 'noteId') && !empty($input->noteId)) {
    // Open by note ID
    $noteId = $input->noteId;
    $_SESSION['currNoteID'] = $noteId;
    $_SESSION['action'] = 'OPEN';
    
    die(json_encode(array('code' => 0)));
} else if (property_exists($input, 'noteTitle') && !empty($input->noteTitle)) {
    // Original functionality - open by note title
    $_SESSION['currNoteTitle'] = $input->noteTitle;
    $_SESSION['action'] = 'OPEN';
    
    die(json_encode(array('code' => 0)));
} else {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Missing note identifier')));
}
