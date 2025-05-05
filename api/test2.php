<?
require 'skeletondb.php';
$noteId = 10;
$conn = get_conn();
$query = "Select Password From LockedNotes Where NoteID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $noteId);
$res = mysqli_execute($stmt);

echo $res;

$row = mysqli_stmt_get_result($stmt);

echo $row;

$res = mysqli_fetch_assoc($row);

echo $res;
