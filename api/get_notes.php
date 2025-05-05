<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

$rows = get_notes($_SESSION["userId"]);

$array = array();

foreach ($rows as $note) {
    $locked = isLockedNote($note['NoteID']);
    $temp = array(
        'NoteID' => $note['NoteID'],
        'Title' => $note['Title'],
        'Location' => $note['Location'],
        'UserID' => $note['UserID'],
        'Locked' => $locked,
        "LastModified" => $note['ModifiedDate'],
        "AttachedImg" => $note['AttachedImg']
    );
    array_push($array, $temp);
}

header(http_response_code(200));
echo json_encode($array);
die();
