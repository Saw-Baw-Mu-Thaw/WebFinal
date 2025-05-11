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

if (!property_exists($input, 'noteId') || !property_exists($input, 'password')) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Lack of information')));
}

if (empty($input->noteId) || empty($input->password)) {
    die(json_encode(array('code' => 1, 'message' => 'Missing Information')));
}

$id = $input->noteId;
$password = password_hash($input->password, PASSWORD_BCRYPT);

$res = add_locked_note($id, $password);

if(!$res){
    die(json_encode(array('code' => 3, 'message' => 'Locked note could not be created')));
}

die(json_encode(array('code' => 0, 'message' => 'Locked note successfully created')));