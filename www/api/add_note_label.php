<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['code' => 1, 'message' => 'Not logged in']);
    exit;
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

// Check if required fields are provided
if (!isset($data['noteId']) || !isset($data['labelId'])) {
    echo json_encode(['code' => 1, 'message' => 'Note ID and Label ID are required']);
    exit;
}

$noteId = $data['noteId'];
$labelId = $data['labelId'];

// Connect to database
require_once 'db_connect.php';

// Check if user has access to this note
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT NoteID FROM notes WHERE NoteID = ? AND Username = ?");
$stmt->bind_param("is", $noteId, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Check if it's a shared note with edit permissions
    $stmt = $conn->prepare("SELECT NoteID FROM shared_notes WHERE NoteID = ? AND SharedWith = ? AND Permission = 'EDITOR'");
    $stmt->bind_param("is", $noteId, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['code' => 1, 'message' => 'Note not found or access denied']);
        exit;
    }
}

// Check if label exists
$stmt = $conn->prepare("SELECT LabelID FROM labels WHERE LabelID = ? AND Username = ?");
$stmt->bind_param("is", $labelId, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['code' => 1, 'message' => 'Label not found']);
    exit;
}

// Check if label is already assigned to this note
$stmt = $conn->prepare("SELECT * FROM note_labels WHERE NoteID = ? AND LabelID = ?");
$stmt->bind_param("ii", $noteId, $labelId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['code' => 1, 'message' => 'Label already assigned to this note']);
    exit;
}

// Add label to note
$stmt = $conn->prepare("INSERT INTO note_labels (NoteID, LabelID) VALUES (?, ?)");
$stmt->bind_param("ii", $noteId, $labelId);

if ($stmt->execute()) {
    echo json_encode(['code' => 0, 'message' => 'Label added to note']);
} else {
    echo json_encode(['code' => 1, 'message' => 'Failed to add label to note: ' . $conn->error]);
}

$conn->close();
?>
