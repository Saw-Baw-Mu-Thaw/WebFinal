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

if (!property_exists($input, 'label')) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Lack of information')));
}

if (empty($input->label)) {
    die(json_encode(array('code' => 1, 'message' => 'Missing Information')));
}

$label = $input->label;
$userId = $_SESSION['userId'];
$noteId = $_SESSION['currNoteID'];

$res = add_label($userId, $noteId, $label);

if(!$res) {
    die(json_encode(array('code' => 3, 'message' => 'Could not add label')));
}

die(json_encode(array('code' => 0, 'message' => 'Label added successfully')));
