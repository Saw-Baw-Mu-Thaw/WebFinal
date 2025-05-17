<?php
// Include Composer autoloader
require 'vendor/autoload.php';
require 'skeletondb.php';

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the Ratchet WebSocket library is installed
if (!class_exists('\\Ratchet\\Server\\IoServer')) {
    die("Ratchet WebSocket library not found. Please install it using Composer:\ncomposer require cboden/ratchet\n");
}

// Import required classes from Ratchet
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

/**
 * WebSocket component for real-time note collaboration
 */
class CollaborationServer implements MessageComponentInterface
{
    protected $clients;
    protected $notes;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->notes = [];
        echo "Collaboration server started.\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);

        // Validate the message
        if (!$data || !isset($data->action)) {
            echo "Invalid message received\n";
            return;
        }

        echo "Client {$from->resourceId} sent: {$data->action}\n";

        switch ($data->action) {
            case 'join':
                $this->handleJoin($from, $data);
                break;

            case 'update':
                $this->handleUpdate($from, $data);
                break;

            case 'cursor':
                $this->handleCursor($from, $data);
                break;

            default:
                echo "Unknown action: {$data->action}\n";
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Remove connection from any note room
        foreach ($this->notes as $noteId => $participants) {
            if (isset($participants[$conn->resourceId])) {
                // Notify others that user left
                $username = $participants[$conn->resourceId]['username'];
                unset($this->notes[$noteId][$conn->resourceId]);

                $this->broadcastToNote($noteId, [
                    'action' => 'user_left',
                    'username' => $username
                ], $conn);

                echo "User {$username} left note {$noteId}\n";
            }
        }

        // Remove connection
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Handle user joining a note
     */
    protected function handleJoin(ConnectionInterface $conn, $data)
    {
        if (!isset($data->noteId) || !isset($data->userId) || !isset($data->username)) {
            return;
        }

        $noteId = $data->noteId;
        $userId = $data->userId;
        $username = $data->username;

        // Check if user has rights to edit this note
        if (!$this->canUserEdit($userId, $noteId)) {
            $conn->send(json_encode([
                'action' => 'error',
                'message' => 'You do not have permission to edit this note'
            ]));
            return;
        }

        // Initialize note room if not exists
        if (!isset($this->notes[$noteId])) {
            $this->notes[$noteId] = [];
        }

        // Add user to note room
        $this->notes[$noteId][$conn->resourceId] = [
            'conn' => $conn,
            'userId' => $userId,
            'username' => $username
        ];

        // Get current participants
        $participants = [];
        foreach ($this->notes[$noteId] as $id => $user) {
            if ($id != $conn->resourceId) {
                $participants[] = [
                    'userId' => $user['userId'],
                    'username' => $user['username']
                ];
            }
        }

        // Confirm join
        $conn->send(json_encode([
            'action' => 'joined',
            'noteId' => $noteId,
            'participants' => $participants
        ]));

        // Notify others
        $this->broadcastToNote($noteId, [
            'action' => 'user_joined',
            'userId' => $userId,
            'username' => $username
        ], $conn);

        echo "User {$username} joined note {$noteId}\n";
    }

    /**
     * Handle note updates
     */
    protected function handleUpdate(ConnectionInterface $from, $data)
    {
        if (!isset($data->noteId) || !isset($data->content) || !isset($data->title)) {
            return;
        }

        $noteId = $data->noteId;

        // Check if this connection is in the note room
        if (!isset($this->notes[$noteId]) || !isset($this->notes[$noteId][$from->resourceId])) {
            return;
        }

        $userId = $this->notes[$noteId][$from->resourceId]['userId'];
        $username = $this->notes[$noteId][$from->resourceId]['username'];

        // Broadcast the update to other participants
        $this->broadcastToNote($noteId, [
            'action' => 'update',
            'content' => $data->content,
            'title' => $data->title,
            'userId' => $userId,
            'username' => $username
        ], $from);

        echo "User {$username} updated note {$noteId}\n";
    }

    /**
     * Handle cursor position updates
     */
    protected function handleCursor(ConnectionInterface $from, $data)
    {
        if (!isset($data->noteId) || !isset($data->position)) {
            return;
        }

        $noteId = $data->noteId;

        // Check if this connection is in the note room
        if (!isset($this->notes[$noteId]) || !isset($this->notes[$noteId][$from->resourceId])) {
            return;
        }

        $userId = $this->notes[$noteId][$from->resourceId]['userId'];
        $username = $this->notes[$noteId][$from->resourceId]['username'];

        // Broadcast cursor position to other participants
        $this->broadcastToNote($noteId, [
            'action' => 'cursor',
            'position' => $data->position,
            'userId' => $userId,
            'username' => $username
        ], $from);
    }

    /**
     * Broadcast a message to all participants in a note
     */
    protected function broadcastToNote($noteId, $data, ConnectionInterface $except = null)
    {
        if (!isset($this->notes[$noteId])) {
            return;
        }

        $message = json_encode($data);

        foreach ($this->notes[$noteId] as $id => $user) {
            if (!$except || $user['conn'] !== $except) {
                $user['conn']->send($message);
            }
        }
    }

    /**
     * Check if a user can edit a note
     */
    protected function canUserEdit($userId, $noteId)
    {
        // Check if user is the owner
        if (is_note_owner($userId, $noteId)) {
            return true;
        }

        // Check if user has EDITOR role
        $role = get_role($userId, $noteId);
        return $role === 'EDITOR';
    }
}

// Run the server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new CollaborationServer()
        )
    ),
    8080
);

echo "WebSocket server started on port 8080\n";
$server->run();