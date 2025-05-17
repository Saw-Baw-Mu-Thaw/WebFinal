<?php
session_start();
header('Content-Type: application/json');

// Only allow authenticated users
if (!isset($_SESSION['username'])) {
    echo json_encode([
        'code' => 1,
        'message' => 'Authentication required'
    ]);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "notes_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        'code' => 1,
        'message' => 'Database connection failed'
    ]);
    exit;
}

// Get the user's ID
$userID = $_SESSION['userID'];

// Get JSON data from the request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['notes']) || !is_array($data['notes'])) {
    echo json_encode([
        'code' => 1,
        'message' => 'Invalid request data'
    ]);
    exit;
}

$notes = $data['notes'];
$results = [];

// Process each note
foreach ($notes as $note) {
    // Validate the note data
    if (!isset($note['id']) || !isset($note['action'])) {
        $results[] = [
            'id' => isset($note['id']) ? $note['id'] : 'unknown',
            'success' => false,
            'message' => 'Invalid note data'
        ];
        continue;
    }

    $noteId = $note['id'];
    $action = $note['action'];

    // Skip notes with positive IDs and 'create' action (they're already on the server)
    if ($action === 'create' && $noteId > 0) {
        $results[] = [
            'id' => $noteId,
            'success' => true,
            'message' => 'Note already exists on server'
        ];
        continue;
    }

    // Handle different actions
    switch ($action) {
        case 'create':
            if (!isset($note['title']) || !isset($note['content'])) {
                $results[] = [
                    'id' => $noteId,
                    'success' => false,
                    'message' => 'Missing title or content for create action'
                ];
                continue 2;
            }

            $title = $conn->real_escape_string($note['title']);
            $content = $conn->real_escape_string($note['content']);
            
            $sql = "INSERT INTO notes (UserID, Title, Content, CreatedAt) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $userID, $title, $content);
            
            if ($stmt->execute()) {
                $newId = $stmt->insert_id;
                $results[] = [
                    'id' => $noteId,
                    'success' => true,
                    'message' => 'Note created successfully',
                    'serverNoteId' => $newId
                ];
            } else {
                $results[] = [
                    'id' => $noteId,
                    'success' => false,
                    'message' => 'Failed to create note: ' . $stmt->error
                ];
            }
            break;

        case 'update':
            if (!isset($note['title']) || !isset($note['content'])) {
                $results[] = [
                    'id' => $noteId,
                    'success' => false,
                    'message' => 'Missing title or content for update action'
                ];
                continue 2;
            }

            // For temporary IDs (negative), we need to map to real server ID
            $serverNoteId = $noteId > 0 ? $noteId : (isset($note['serverNoteId']) ? $note['serverNoteId'] : null);
            
            if (!$serverNoteId || $serverNoteId < 0) {
                $results[] = [
                    'id' => $noteId,
                    'success' => false,
                    'message' => 'Invalid server note ID for update'
                ];
                continue 2;
            }

            $title = $conn->real_escape_string($note['title']);
            $content = $conn->real_escape_string($note['content']);
            
            $sql = "UPDATE notes SET Title = ?, Content = ?, ModifiedAt = NOW() WHERE NoteID = ? AND UserID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssii", $title, $content, $serverNoteId, $userID);
            
            if ($stmt->execute()) {
                $results[] = [
                    'id' => $noteId,
                    'success' => true,
                    'message' => 'Note updated successfully'
                ];
            } else {
                $results[] = [
                    'id' => $noteId,
                    'success' => false,
                    'message' => 'Failed to update note: ' . $stmt->error
                ];
            }
            break;

        case 'delete':
            // For temporary IDs (negative), we need to map to real server ID
            $serverNoteId = $noteId > 0 ? $noteId : (isset($note['serverNoteId']) ? $note['serverNoteId'] : null);
            
            if (!$serverNoteId || $serverNoteId < 0) {
                $results[] = [
                    'id' => $noteId,
                    'success' => false,
                    'message' => 'Invalid server note ID for delete'
                ];
                continue 2;
            }

            $sql = "DELETE FROM notes WHERE NoteID = ? AND UserID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $serverNoteId, $userID);
            
            if ($stmt->execute()) {
                $results[] = [
                    'id' => $noteId,
                    'success' => true,
                    'message' => 'Note deleted successfully'
                ];
            } else {
                $results[] = [
                    'id' => $noteId,
                    'success' => false,
                    'message' => 'Failed to delete note: ' . $stmt->error
                ];
            }
            break;

        default:
            $results[] = [
                'id' => $noteId,
                'success' => false,
                'message' => 'Unknown action: ' . $action
            ];
    }
}

// Return the results
echo json_encode([
    'code' => 0,
    'message' => 'Sync completed',
    'results' => $results
]);

$conn->close();
?> 