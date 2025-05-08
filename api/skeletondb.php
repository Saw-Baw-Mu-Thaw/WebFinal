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

    $query = "select * from Notes 
              where UserID = ? or NoteId in (select NoteID from SharedNotes where Collaborator = ?) 
              Order By Pinned DESC, PinnedTime DESC, ModifiedDate DESC";
    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, 'ii', $userid, $userid);
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

    $statement = mysqli_prepare($conn, "Delete From Notes where NoteID = ?");
    mysqli_stmt_bind_param($statement, "i", $noteID);
    $res1 = mysqli_stmt_execute($statement);

    $statement = mysqli_prepare($conn, "delete from LockedNotes where NoteID = ?");
    mysqli_stmt_bind_param($statement, "i", $noteID);
    $res2 = mysqli_stmt_execute($statement);

    $statement = mysqli_prepare($conn, "delete from SharedNotes where NoteID = ?");
    mysqli_stmt_bind_param($statement, "i", $noteID);
    $res3 = mysqli_stmt_execute($statement);

    return $res1 && $res2 && $res3;
}


function create_note($username, $userId, $title)
{
    $conn = get_conn();

    $location = 'notes/' . $username . '/' .  $title . '.txt';
    $statement = mysqli_prepare($conn, "Insert into Notes(Title, UserID, Location, ModifiedDate, AttachedImg) Values (?,?,?,NOW(), NULL);");
    mysqli_stmt_bind_param($statement, "sis", $title, $userId, $location);
    $res = mysqli_execute($statement);
    mysqli_close($conn);
    return $res;
}

// above updated

function update_note($old, $new, $username)
{
    $conn = get_conn();
    $location = 'notes/' . $new . '.txt';

    $id = get_id($username, $old, $conn);

    $query = "Update Notes Set Title = ?, location = ?, date = NOW() Where NoteID = ?;";
    $statement = mysqli_prepare($conn, $query);
    if (mysqli_stmt_bind_param($statement, "ssi", $new, $location, $id)) {
        $res = mysqli_execute($statement);
    } else {
        return false;
    }
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

    $query = "Update Notes 
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

    $query = "Update Notes 
                Set Pinned = 0, PinnedTime = NULL
                Where UserID = ? AND NoteID = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $userId, $noteId);
    $res = mysqli_execute($stmt);
    return $res;
}
