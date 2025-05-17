<?php

session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports POST')));
}

$input = json_decode(file_get_contents('php://input'));

if (is_null($input)) {
    die(json_encode(array('code' => 2, 'message' => 'Only support JSON')));
}

if (!property_exists($input, 'oldTitle') || !property_exists($input, 'newTitle') || !property_exists($input, "contents")) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Lack of information')));
}

if (empty($input->oldTitle) || empty($input->newTitle) || empty($input->contents)) {
    die(json_encode(array('code' => 1, 'message' => 'Missing Information')));
}

$oldTitle = $input->oldTitle;
$newTitle = $input->newTitle;
$contents = $input->contents;
$noteId = $_SESSION['currNoteID'];
$userId = $_SESSION['userId'];
$location = get_location($noteId);
$shared = is_shared_note($userId, $noteId);

$res1 = true; // file writing result
$res2 = true; // file deleting result
// update file
if ($oldTitle === $newTitle) {
    $file = fopen($location, 'w');
    $res = fwrite($file, $contents);
    ($res !== false) ? $res1 = true : $res1 = false;
    fclose($file);
} else {
    $owner = $_SESSION['username'];
    if ($shared) {
        $owner = get_owner($noteId);
    }

    $oldLocation = $location;
    $location = '../notes/' . $owner . '/' . str_replace(" ", "", $newTitle)  . '.txt';
    $result = file_exists($location);

    if ($result) {
        die(json_encode(array('code' => 3, 'message' => 'File with same name already exists')));
    }
    // check if directory exists
    $res = is_dir('../notes/' . $owner);
    if ($res == false) {
        mkdir('../notes/' . $_SESSION['username']);
    }
    // write the file
    $file = fopen($location, 'w');
    $res = fwrite($file, $contents);
    ($res !== false) ? $res1 = true : $res1 = false;
    fclose($file);

    // delete old file
    $res2 = unlink($oldLocation);
}

// update database entry
$res3 = update_note($noteId, $newTitle, $location);

$res = $res1 && $res2 && $res3;
if (!$res) {
    die(json_encode(array('code' => 5, 'message' => 'Error saving file')));
}
die(json_encode(array('code' => 0, 'message' => 'Note saved successfully')));
