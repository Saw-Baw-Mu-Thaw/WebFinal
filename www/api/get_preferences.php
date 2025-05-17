<?php

session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

$row = get_preference($_SESSION['userId']);

if ($row == false || $row == null) {
    die(json_encode(array("code" => 1, "Message" => "Something went wrong")));
}

header(http_response_code(200));
echo json_encode(array("code" => 0, "FontSize" => $row['FontSize'], "Mode" => $row['Mode'], "Layout" => $row['Layout'], "NoteColor" => $row['NoteColor'] ?? '#ffffff'));
