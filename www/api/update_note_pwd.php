<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username']) || !isset($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(array('code' => 3, 'message' => 'Authentication required')));
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports POST')));
}

$input = json_decode(file_get_contents('php://input'));

if (is_null($input)) {
    die(json_encode(array('code' => 2, 'message' => 'Only support JSON')));
}

if (!property_exists($input, 'oldPwd') || !property_exists($input, 'newPwd1') 
|| !property_exists($input, 'newPwd2') || !property_exists($input, 'noteId')) {

    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Lack of information')));
}

if (empty($input->oldPwd) || empty($input->newPwd1) 
|| empty($input->newPwd2) || empty($input->noteId)) {

    die(json_encode(array('code' => 1, 'message' => 'Missing Information')));
}

$oldPwd = $input->oldPwd;
$newPwd1 = $input->newPwd1;
$newPwd2 = $input->newPwd2;
$noteId = $input->noteId;

if($newPwd1 !== $newPwd2) {
    die(json_encode(array('code' => 5, 'message' => 'Passwords are not the same')));
}

$hash = get_note_pwd($noteId);
$res = password_verify($oldPwd, $hash);

if (!$res) {
    die(json_encode(array('code' => 2, 'message' => 'Incorrect Password')));
}

$password = password_hash($newPwd1, PASSWORD_BCRYPT);
$res = update_locked_note($noteId, $password);

if(!$res) {
    die(json_encode(array('code' => 2, 'message' => 'Could not change password')));
} 

die(json_encode(array('code' => 0, 'message' => 'Password Changed Successfully')));
