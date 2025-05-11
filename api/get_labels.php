<?php
session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(array('code' => 4, 'message' => 'This API only supports GET')));
}

if (!isset($_GET['noteId'])) {
    die(json_encode(array('code' => 1, 'message' => 'Lack of information')));
}

if(empty($_GET['noteId'])) {
    die(json_encode(array('code' => 2, 'message' => 'Missing Information')));
}

if(isset($_GET['noteID']))
$noteId = intval($_GET['noteId']);
$userId = $_SESSION['userId'];

$labels = get_labels($userId, $noteId);

die(json_encode(array('code' => 0, 'labels' => $labels)));