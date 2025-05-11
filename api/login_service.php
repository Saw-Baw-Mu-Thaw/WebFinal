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

if (!property_exists($input, 'username') || !property_exists($input, 'password')) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Lack of information')));
}

if (empty($input->username) || empty($input->password)) {
    die(json_encode(array('code' => 1, 'message' => 'Missing Information')));
}

$username = $input->username;
$password = $input->password;

// Debug information
error_log("Login attempt - Username: " . $username);

$val = authenticate($username, $password);
$res = $val[0];

if ($res) {
    $_SESSION['username'] = $username;
    $_SESSION['userId'] = $val[1];
    error_log("Login successful for user: " . $username);
    header(http_response_code(200));
    echo json_encode(array('code' => 0));
} else {
    error_log("Login failed for user: " . $username);
    echo json_encode(array('code' => 5, 'message' => 'Incorrect Username or Password'));
}
