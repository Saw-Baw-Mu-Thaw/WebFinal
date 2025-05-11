<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports POST')));
}

$input = json_decode(file_get_contents('php://input'));

if (is_null($input)) {
    die(json_encode(array('code' => 2, 'message' => 'Only support JSON')));
}

if (!property_exists($input, 'noteId') || !property_exists($input, 'email') || !property_exists($input, 'role')) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Missing required fields')));
}

$noteId = intval($input->noteId);
$email = $input->email;
$role = $input->role;

// Validate role
if (!in_array($role, ['VIEWER', 'EDITOR'])) {
    die(json_encode(array('code' => 3, 'message' => 'Invalid role')));
}

$conn = get_conn();

// Check if user is note owner
$query = "SELECT UserID FROM Notes WHERE NoteID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $noteId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$note = mysqli_fetch_assoc($result);

if (!$note || $note['UserID'] != $_SESSION['userId']) {
    die(json_encode(array('code' => 2, 'message' => 'Unauthorized access')));
}

// Get collaborator's user ID
$query = "SELECT UserID FROM Users WHERE Email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$collaborator = mysqli_fetch_assoc($result);

if (!$collaborator) {
    die(json_encode(array('code' => 4, 'message' => 'User not found')));
}

// Update or insert sharing permission
$query = "INSERT INTO SharedNotes (NoteID, OwnerID, Collaborator, Role) 
          VALUES (?, ?, ?, ?) 
          ON DUPLICATE KEY UPDATE Role = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'iiiss', $noteId, $_SESSION['userId'], $collaborator['UserID'], $role, $role);
$result = mysqli_stmt_execute($stmt);

mysqli_close($conn);

if (!$result) {
    die(json_encode(array('code' => 5, 'message' => 'Failed to update sharing permissions')));
}

die(json_encode(array('code' => 0, 'message' => 'Sharing permissions updated successfully'))); 