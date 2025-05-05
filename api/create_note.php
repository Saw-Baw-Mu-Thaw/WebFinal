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

if (!property_exists($input, 'title')) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Lack of information')));
}

if (empty($input->title)) {
    die(json_encode(array('code' => 1, 'message' => 'Missing Information')));
}

$title = $input->title;
$location = '../notes/' . $_SESSION['username'] . '/' . $title . '.txt';
$result = file_exists($location);

if ($result) {
    die(json_encode(array('code' => 3, 'message' => 'File with same name already exists')));
} else {
    // check if directory exists
    $res = is_dir('../notes/' . $_SESSION['username']);
    if ($res == false) {
        mkdir('../notes/' . $_SESSION['username']);
    }
    // write the file
    $file = fopen($location, 'w');
    fclose($file);
    // add db entry
    $res = create_note($_SESSION['username'], $_SESSION['userId'], $title);

    fclose($file);

    if ($res == false) {
        die(json_encode(array('code' => 5, 'message' => 'Something went wrong')));
    }

    $conn = get_conn();
    $_SESSION['currNoteID'] = get_id($_SESSION['userId'], $title, $conn);
    $_SESSION['action'] = 'CREATE';
    mysqli_close($conn);

    echo json_encode(array('code' => 0, 'message' => 'Note created successfully'));
}
