<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['code' => 1, 'message' => 'Not logged in']);
    exit;
}

// Check if note ID is provided
if (!isset($_GET['noteId'])) {
    echo json_encode(['code' => 1, 'message' => 'Note ID is required']);
    exit;
}

$noteId = $_GET['noteId'];

// Connect to database
require_once 'db_connect.php';

// Get note title and check if user has access to this note
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT n.Title FROM notes n 
                        LEFT JOIN shared_notes sn ON n.NoteID = sn.NoteID AND sn.SharedWith = ?
                        WHERE n.NoteID = ? AND (n.Username = ? OR sn.SharedWith IS NOT NULL)");
$stmt->bind_param("sis", $username, $noteId, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['code' => 1, 'message' => 'Note not found or access denied']);
    exit;
}

$noteData = $result->fetch_assoc();
$title = $noteData['Title'];

// Get labels for this note
$stmt = $conn->prepare("SELECT l.LabelID, l.Label FROM labels l 
                        JOIN note_labels nl ON l.LabelID = nl.LabelID 
                        WHERE nl.NoteID = ?");
$stmt->bind_param("i", $noteId);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
while ($row = $result->fetch_assoc()) {
    $labels[] = $row;
}

// Return the data
echo json_encode([
    'code' => 0,
    'title' => $title,
    'labels' => $labels
]);

$conn->close();
?>
