<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

if (!isset($_GET['noteId'])) {
    die(json_encode(array('code' => 1, 'message' => 'Missing note ID')));
}

$noteId = intval($_GET['noteId']);

// Get note owner
$conn = get_conn();
$query = "SELECT UserID FROM Notes WHERE NoteID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $noteId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$note = mysqli_fetch_assoc($result);

if (!$note || $note['UserID'] != $_SESSION['userId']) {
    die(json_encode(array('code' => 2, 'message' => 'Unauthorized access')));
}

// Get sharing information
$query = "SELECT u.Email, sn.Role 
          FROM SharedNotes sn 
          JOIN Users u ON sn.Collaborator = u.UserID 
          WHERE sn.NoteID = ? AND sn.OwnerID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'ii', $noteId, $_SESSION['userId']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$sharing_info = array();
while ($row = mysqli_fetch_assoc($result)) {
    $sharing_info[] = array(
        'email' => $row['Email'],
        'role' => $row['Role']
    );
}

mysqli_close($conn);

die(json_encode(array(
    'code' => 0,
    'message' => 'Sharing information retrieved successfully',
    'sharing_info' => $sharing_info
))); 