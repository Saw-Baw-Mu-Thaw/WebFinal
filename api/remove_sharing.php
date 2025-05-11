<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports DELETE')));
}

if (!isset($_GET['noteId']) || !isset($_GET['email'])) {
    die(json_encode(array('code' => 1, 'message' => 'Missing required fields')));
}

$noteId = intval($_GET['noteId']);
$email = $_GET['email'];

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

// Remove sharing permission
$query = "DELETE FROM SharedNotes WHERE NoteID = ? AND OwnerID = ? AND Collaborator = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'iii', $noteId, $_SESSION['userId'], $collaborator['UserID']);
$result = mysqli_stmt_execute($stmt);

mysqli_close($conn);

if (!$result) {
    die(json_encode(array('code' => 5, 'message' => 'Failed to remove sharing permissions')));
}

die(json_encode(array('code' => 0, 'message' => 'Sharing permissions removed successfully'))); 