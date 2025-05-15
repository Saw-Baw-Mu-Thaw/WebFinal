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

if (!property_exists($input, 'noteId') || !property_exists($input, 'email') || !property_exists($input, 'role')) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Lack of information')));
}

$noteId = $input->noteId;
$email = $input->email;
$role = strtoupper($input->role); // VIEWER or EDITOR
$ownerId = $_SESSION['userId'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(array('code' => 2, 'message' => 'Invalid email format')));
}

// Validate role
if ($role !== 'VIEWER' && $role !== 'EDITOR') {
    die(json_encode(array('code' => 2, 'message' => 'Invalid role. Must be VIEWER or EDITOR')));
}

// Check if the note exists and user is the owner
if (!is_note_owner($ownerId, $noteId)) {
    die(json_encode(array('code' => 6, 'message' => 'You are not the owner of this note')));
}

// Find user by email
$collaborator = check_user_email($email);
if (!$collaborator) {
    die(json_encode(array('code' => 5, 'message' => 'Email not found')));
}

// Don't allow sharing with yourself
if ($collaborator['UserID'] == $ownerId) {
    die(json_encode(array('code' => 7, 'message' => 'Cannot share with yourself')));
}

// Share the note
$result = share_note($noteId, $ownerId, $collaborator['UserID'], $role);

if ($result) {
    // Get note title for notification
    $noteTitle = get_note_title($noteId);
    
    // Try to send email notification
    $emailSent = false;
    $subject = "Note Shared With You";
    $body = "Hello,\n\n" . 
            $_SESSION['username'] . " has shared a note titled \"" . $noteTitle . "\" with you.\n\n" .
            "You can view this note by logging into your account.\n\n" .
            "Regards,\nNote Taking App";
    
    // Attempt to send email
    $emailSent = @mail($email, $subject, $body);
    
    die(json_encode(array(
        'code' => 0, 
        'message' => 'Note shared successfully',
        'emailSent' => $emailSent
    )));
} else {
    die(json_encode(array('code' => 8, 'message' => 'Failed to share note')));
} 