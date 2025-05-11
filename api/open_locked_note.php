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

if (!property_exists($input, 'id') || !property_exists($input, 'password')) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Lack of information')));
}

if (empty($input->id) || empty($input->password)) {
    die(json_encode(array('code' => 1, 'message' => 'Missing Information')));
}

$id = $input->id;
$password = $input->password;
$hash = get_note_pwd($id);

$res = password_verify($password, $hash);

if (!$res) {
    die(json_encode(array('code' => 2, 'message' => 'Incorrect Password')));
}

$_SESSION['currNoteID'] = $id;
$_SESSION['action'] = 'EDIT';

die(json_encode(array('code' => 0, 'message' => 'Values set')));
