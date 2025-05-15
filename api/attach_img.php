<?php

session_start();

require 'skeletondb.php';

header('Content-Type: application/json; charset=utf-8');


if (!file_exists("../images/" . $_SESSION['username'])) {
    mkdir("../images/" . $_SESSION['username']);
}

function isValidExt($destFilepath)
{
    $fileExtension = pathinfo($destFilepath, PATHINFO_EXTENSION);

    $allowed_extension = array('jpg', 'png');
    $validFileExtension = in_array($fileExtension, $allowed_extension);

    return $validFileExtension;
}

function handleUpload()
{
    $uploaded_temp_file = $_FILES['file']['tmp_name'];
    $filename = basename($_FILES['file']['name']);
    $destFile = "../images/" . $_SESSION['username'] . '/' . $filename;

    $MaxSize = 5 * 1024 * 1024 * 1024;

    $isUploadedFile = is_uploaded_file($uploaded_temp_file);
    $valid_size = $_FILES['file']['size'] <= $MaxSize && $_FILES['file']['size'] >= 0;
    $not_exist = !file_exists($destFile);

    if (!$valid_size) {
        $code = 1;
        $response = "File exceeds maximum allowed size ";
    } else if (!$not_exist) {
        $code = 2;
        $response = "This file already exists";
    } else if (!isValidExt($destFile)) {
        $code = 3;
        $response = "This file type is not allowed";
    } else if (!isset($_POST['noteId']) || empty($_POST['noteId'])) {
        $code = 4;
        $response = "Note ID is not set";
    } else {
        move_uploaded_file($uploaded_temp_file, $destFile);

        // update db
        $noteId = $_POST['noteId'];
        $location = './images/' . get_owner($noteId) . '/' . $filename;
        $res = attach_img($noteId, $location);
        if (!$res) {
            die(json_encode(array('code' => 5, 'message' => 'Couldn\'t attach image')));
        }
        $code = 0;
        $response = "File uploaded successfully";
    }

    return array('code' => $code, 'message' => $response);
}

$valid_form_submission = !empty($_FILES);

if ($valid_form_submission) {
    $error = $_FILES['file']['error'];

    if ($error == UPLOAD_ERR_OK) {
        $arr = handleUpload();
        echo json_encode($arr);
    }
    die();
}
