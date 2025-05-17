<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

// Support both GET and POST for backward compatibility
if ($_SERVER['REQUEST_METHOD'] != 'GET' && $_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET and POST')));
}

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    die(json_encode(array('code' => 1, 'message' => 'Not authorized')));
}

// Handle GET request (original method from noteActions.js)
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET['id'])) {
        die(json_encode(array('code' => 1, 'message' => 'Missing information')));
    }
    
    $id = intval($_GET['id']);
    
    if (isLockedNote($id)) {
        die(json_encode(array('code' => 3, 'message' => 'Note is a locked note')));
    }
    
    $_SESSION['currNoteID'] = $id;
    $_SESSION['action'] = 'OPEN';
    
    die(json_encode(array('code' => 0, 'message' => 'Values set')));
}

// Handle POST request (new method from notifications.js and openNoteById)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'));
    
    if (is_null($input)) {
        die(json_encode(array('code' => 2, 'message' => 'Only support JSON')));
    }
    
    if (property_exists($input, 'noteId') && !empty($input->noteId)) {
        // Open by note ID
        $noteId = $input->noteId;
        
        // Check if note is locked
        if (isLockedNote($noteId)) {
            die(json_encode(array('code' => 3, 'message' => 'Note is a locked note')));
        }
        
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
}
