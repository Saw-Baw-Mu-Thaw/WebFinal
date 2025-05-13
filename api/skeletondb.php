<?php

function get_conn()
{
    $host = "";
    $user = "";
    $password = "";
    $database = "";

    if (getenv('Environment') === 'Testing') {
        $host = "127.0.0.1";
        $user = "root";
        $password = "";
        $database = "skeletondb";
    } else {
        $host = "mysql-server";
        $user = "root";
        $password = getenv('mariadbPwd');
        $database = "skeletondb";
    }
    $conn = mysqli_connect($host, $user, $password, $database);
    if ($conn === false) {
        echo mysqli_connect_error() . '</br>';
        die();
    }
    return $conn;
}

function get_notes($userid)
{
    $conn = get_conn();

    $query = "select T1.NoteID, T1.UserID, T1.Title, T1.Location, T1.ModifiedDate, T1.AttachedImg, T2.Pinned, T2.PinnedTime from 
                (select * from Notes
                where UserID = ? or NoteId in (select NoteID from SharedNotes where Collaborator = ?)) as T1
                cross join 
                (Select UserID, NoteID, Pinned, PinnedTime from PinnedNotes 
                where UserID = ?) as T2 on T2.NoteID = T1.NoteID
                Order By T2.Pinned DESC, T2.PinnedTime DESC, T1.ModifiedDate DESC;";
    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, 'iii', $userid, $userid, $userid);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($conn);
    return $rows;
    // var_dump($rows);
}

function authenticate($username, $password)
{
    $conn = get_conn();

    $statement = mysqli_prepare($conn, "select * from users where Username = ?");
    mysqli_stmt_bind_param($statement, "s", $username);
    mysqli_stmt_execute($statement);
    $res = mysqli_stmt_get_result($statement);


    $row = mysqli_fetch_assoc($res);

    if ($row == null) {
        return false;
    } else if ($row == false) {
        return false;
    }

    mysqli_close($conn);

    // if ($row['Password'] === $password) {
    if (password_verify($password, $row['Password'])) {
        return array(true, $row['UserID']);
    } else {
        return array(false);
    }
}

function isLockedNote($noteID)
{
    $query = "Select 1 from LockedNotes Where ? In (Select NoteID from LockedNotes)";

    $conn = get_conn();
    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, 'i', $noteID);
    mysqli_stmt_execute($statement);
    $res = mysqli_stmt_get_result($statement);
    $row = mysqli_fetch_row($res);

    mysqli_close($conn);

    if ($row != null) {
        return true;
    } else {
        return false;
    }
}

function get_preference($userid)
{

    $conn = get_conn();
    $query = "select * from Preferences where UserId = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $userid);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    mysqli_close($conn);
    return $row;
}


function delete_note($noteID)
{
    $conn = get_conn();

    $statement = mysqli_prepare($conn, "delete from LockedNotes where NoteID = ?");
    mysqli_stmt_bind_param($statement, "i", $noteID);
    $res2 = mysqli_stmt_execute($statement);

    $statement = mysqli_prepare($conn, "delete from SharedNotes where NoteID = ?");
    mysqli_stmt_bind_param($statement, "i", $noteID);
    $res3 = mysqli_stmt_execute($statement);

    $query = "Delete From PinnedNotes Where NoteID = ?";
    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, 'i', $noteID);
    $res4 = mysqli_execute($statement);

    $query = "Delete From Labels Where NoteID = ?";
    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, 'i', $noteID);
    $res5 = mysqli_execute($statement);

    $query = "Delete From Notifications Where NoteID = ?";
    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, 'i', $noteID);
    $res6 = mysqli_execute($statement);

    $statement = mysqli_prepare($conn, "Delete From Notes where NoteID = ?");
    mysqli_stmt_bind_param($statement, "i", $noteID);
    $res1 = mysqli_stmt_execute($statement);

    mysqli_close($conn);

    return $res1 && $res2 && $res3 && $res4 && $res5 && $res6;
}


function create_note($username, $userId, $title)
{
    $conn = get_conn();

    $location = '../notes/' . $username . '/' . str_replace(" ", "", $title)  . '.txt';
    $statement = mysqli_prepare($conn, "Insert into Notes(Title, UserID, Location, ModifiedDate, AttachedImg) Values (?,?,?,NOW(), NULL);");
    mysqli_stmt_bind_param($statement, "sis", $title, $userId, $location);
    $res1 = mysqli_execute($statement);

    // add default pin
    $res2 = add_pin_default($title, $userId, $conn);

    mysqli_close($conn);
    return $res1 && $res2;
}

function add_pin_default($title, $userId, $conn)
{
    $noteId = get_id($userId, $title, $conn);

    $query = "Insert Into PinnedNotes(NoteID, UserID, Pinned, PinnedTime) Values (?,?,0,NULL);";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $noteId, $userId);
    $res = mysqli_execute($stmt);

    return $res;
}

function update_note($noteId, $title, $location)
{
    $conn = get_conn();

    $query = "Update Notes Set Title = ?, Location = ?, ModifiedDate = NOW() Where NoteID = ?;";
    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, "ssi", $title, $location, $noteId);
    $res = mysqli_execute($statement);

    mysqli_close($conn);
    return $res;
}

// updated function
function get_id($userId, $title, $conn)
{
    $query = "Select NoteID From Notes Where Title = ? And UserID = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $title, $userId);
    mysqli_execute($stmt);
    $row = mysqli_stmt_get_result($stmt);
    $res = mysqli_fetch_assoc($row);
    return $res['NoteID'];
}

function add_locked_note($noteId, $password)
{
    $conn = get_conn();

    $query = "Insert into LockedNotes(NoteID, Password) Values (?,?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'is', $noteId, $password);
    $res = mysqli_execute($stmt);
    mysqli_close($conn);
    return $res;
}

function delete_locked_note($noteId)
{
    $conn = get_conn();

    $query = "Delete from LockedNotes Where NoteID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $noteId);
    $res = mysqli_stmt_execute($stmt);

    return $res;
}

function get_note_pwd($noteId)
{
    $conn = get_conn();
    $query = "Select Password From LockedNotes Where NoteID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $noteId);
    mysqli_execute($stmt);
    $row = mysqli_stmt_get_result($stmt);
    $res = mysqli_fetch_assoc($row);
    mysqli_close($conn);
    return $res['Password'];
}

function get_note_title($noteId)
{
    $conn = get_conn();
    $query = "Select Title from Notes Where NoteID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $noteId);
    mysqli_execute($stmt);
    $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return $res['Title'];
}

function pin_note($noteId, $userId)
{
    $conn = get_conn();

    $query = "Update PinnedNotes 
                Set Pinned = 1, PinnedTime = NOW()
                Where UserID = ? AND NoteID = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $userId, $noteId);
    $res = mysqli_execute($stmt);
    return $res;
}

function unpin_note($noteId, $userId)
{
    $conn = get_conn();

    $query = "Update PinnedNotes
                Set Pinned = 0, PinnedTime = NULL
                Where UserID = ? AND NoteID = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $userId, $noteId);
    $res = mysqli_execute($stmt);
    return $res;
}


function get_location($noteId)
{
    $conn = get_conn();

    $query = "Select Location From Notes Where NoteID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $noteId);
    mysqli_execute($stmt);
    $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    mysqli_close($conn);
    return $res['Location'];
}

function is_shared_note($userId, $noteId)
{
    $conn = get_conn();

    $query = "Select 1 from SharedNotes Where NoteID = ? AND Collaborator = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $noteId, $userId);
    mysqli_execute($stmt);
    $res = mysqli_fetch_row(mysqli_stmt_get_result($stmt));

    mysqli_close($conn);
    if ($res != null) {
        return true;
    }
    return false;
}

function get_role($userId, $noteId)
{
    $conn = get_conn();

    $query = "Select Role from SharedNotes Where NoteID = ? AND Collaborator = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $noteId, $userId);
    mysqli_execute($stmt);
    $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    mysqli_close($conn);
    return $res['Role'];
}

function get_owner($noteId)
{
    $conn = get_conn();

    $query = "Select U.Username From Notes as N
                Join Users as U on U.UserID = N.UserID
                Where N.NoteID = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $noteId);
    mysqli_execute($stmt);
    $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    mysqli_close($conn);
    return $res['Username'];
}

function add_label($userId, $noteId, $label)
{
    $conn = get_conn();

    $query = "Insert Into Labels(UserID, NoteID, Label) Values (?,?,?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iis', $userId, $noteId, $label);
    $res = mysqli_execute($stmt);
    mysqli_close($conn);
    return $res;
}

function get_labels($userId, $noteId)
{
    $conn = get_conn();

    $query = "Select LabelID, Label from Labels Where NoteID = ? And UserID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $noteId, $userId);
    mysqli_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_close($conn);
    return $rows;
}


/**
 * Checks if an email belongs to a registered user
 * @param string $email The email to check
 * @return array|false Returns [UserID, Username] if found, false otherwise
 */
function check_user_email($email)
{
    $conn = get_conn();

    $query = "SELECT UserID, Username FROM users WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    mysqli_close($conn);

    if ($user) {
        return $user;
    }
    return false;
}

/**
 * Shares a note with another user
 * @param int $noteId The note ID
 * @param int $ownerId The owner's user ID
 * @param int $collaboratorId The collaborator's user ID
 * @param string $role The role (VIEWER or EDITOR)
 * @return bool True if successful, false otherwise
 */
function share_note($noteId, $ownerId, $collaboratorId, $role)
{
    $conn = get_conn();

    // Check if sharing already exists
    $query = "SELECT 1 FROM SharedNotes WHERE NoteID = ? AND OwnerID = ? AND Collaborator = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iii", $noteId, $ownerId, $collaboratorId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Update existing sharing
        $query = "UPDATE SharedNotes SET Role = ? WHERE NoteID = ? AND OwnerID = ? AND Collaborator = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "siii", $role, $noteId, $ownerId, $collaboratorId);
    } else {
        // Create new sharing entry
        $query = "INSERT INTO SharedNotes (NoteID, OwnerID, Collaborator, Role) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iiis", $noteId, $ownerId, $collaboratorId, $role);
    }

    $result = mysqli_stmt_execute($stmt);

    // If sharing successful, add default pin for the collaborator
    if ($result) {
        $query = "INSERT IGNORE INTO PinnedNotes (NoteID, UserID, Pinned, PinnedTime) VALUES (?, ?, 0, NULL)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $noteId, $collaboratorId);
        mysqli_stmt_execute($stmt);

        // Add notification for the collaborator
        $noteTitle = get_note_title($noteId);
        $ownerUsername = get_username($ownerId);
        $message = "User '$ownerUsername' shared note '$noteTitle' with you";
        add_notification($collaboratorId, $noteId, $message);
    }

    mysqli_close($conn);
    return $result;
}

/**
 * Revoke sharing for a note
 * @param int $noteId The note ID
 * @param int $ownerId The owner's user ID
 * @param int $collaboratorId The collaborator's user ID
 * @return bool True if successful, false otherwise
 */
function revoke_note_sharing($noteId, $ownerId, $collaboratorId)
{
    $conn = get_conn();

    $query = "DELETE FROM SharedNotes WHERE NoteID = ? AND OwnerID = ? AND Collaborator = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iii", $noteId, $ownerId, $collaboratorId);
    $result = mysqli_stmt_execute($stmt);

    mysqli_close($conn);
    return $result;
}

/**
 * Add a notification for a user
 * @param int $userId The user ID
 * @param int $noteId The note ID
 * @param string $message The notification message
 * @return bool True if successful, false otherwise
 */
function add_notification($userId, $noteId, $message)
{
    $conn = get_conn();

    $query = "INSERT INTO notifications (UserID, NoteID, Message, IsRead, CreatedAt) VALUES (?, ?, ?, 0, NOW())";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iis", $userId, $noteId, $message);
    $result = mysqli_stmt_execute($stmt);

    mysqli_close($conn);
    return $result;
}

/**
 * Get unread notifications for a user
 * @param int $userId The user ID
 * @return array Notifications
 */
function get_notifications($userId)
{
    $conn = get_conn();

    $query = "SELECT n.NotificationID, n.NoteID, n.Message, n.IsRead, n.CreatedAt, nt.Title as NoteTitle 
              FROM notifications n 
              JOIN notes nt ON n.NoteID = nt.NoteID 
              WHERE n.UserID = ? 
              ORDER BY n.CreatedAt DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $notifications = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_close($conn);
    return $notifications;
}

/**
 * Mark a notification as read
 * @param int $notificationId The notification ID
 * @return bool True if successful, false otherwise
 */
function mark_notification_read($notificationId)
{
    $conn = get_conn();

    $query = "UPDATE notifications SET IsRead = 1 WHERE NotificationID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $notificationId);
    $result = mysqli_stmt_execute($stmt);

    mysqli_close($conn);
    return $result;
}

/**
 * Get all collaborators for a note
 * @param int $noteId The note ID
 * @param int $ownerId The owner's user ID
 * @return array Collaborators
 */
function get_collaborators($noteId, $ownerId)
{
    $conn = get_conn();

    $query = "SELECT s.Collaborator, s.Role, u.Username, u.Email 
              FROM SharedNotes s 
              JOIN users u ON s.Collaborator = u.UserID 
              WHERE s.NoteID = ? AND s.OwnerID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $noteId, $ownerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $collaborators = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_close($conn);
    return $collaborators;
}

/**
 * Get a username by user ID
 * @param int $userId The user ID
 * @return string The username
 */
function get_username($userId)
{
    $conn = get_conn();

    $query = "SELECT Username FROM users WHERE UserID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    mysqli_close($conn);
    return $user ? $user['Username'] : '';
}

/**
 * Check if a user is the owner of a note
 * @param int $userId The user ID
 * @param int $noteId The note ID
 * @return bool True if owner, false otherwise
 */
function is_note_owner($userId, $noteId)
{
    $conn = get_conn();

    $query = "SELECT 1 FROM notes WHERE NoteID = ? AND UserID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $noteId, $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $isOwner = mysqli_num_rows($result) > 0;

    mysqli_close($conn);
    return $isOwner;
}

function delete_label($labelId)
{
    $conn = get_conn();

    $query = "Delete From Labels Where LabelID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $labelId);
    $res = mysqli_execute($stmt);
    mysqli_close($conn);
    return $res;
}

function update_mode($userId, $mode)
{
    $conn = get_conn();

    $query = "Update Preferences Set Mode = ? Where UserID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $mode, $userId);
    $res = mysqli_execute($stmt);
    mysqli_close($conn);
    return $res;
}

function update_layout($userId, $layout)
{
    $conn = get_conn();

    $query = "Update Preferences Set Layout = ? Where UserID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $layout, $userId);
    $res = mysqli_execute($stmt);
    mysqli_close($conn);
    return $res;
}


function attach_img($noteId, $location)
{
    $conn = get_conn();

    $query = "Update Notes Set AttachedImg = ? Where NoteID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $location, $noteId);
    $res = mysqli_execute($stmt);
    mysqli_close($conn);
    return $res;
}
