<?php
session_start();

require 'skeletondb.php';

// no one should access this page via browser
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    die();
}

if (isset($_POST['action']) && !empty($_POST['action'])) {
    $old = $_POST['old'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $location = 'notes/' . $title . '.txt';

    if ($_POST['titleChanged'] === 'true') {
        if (file_exists($location) == true) {
            $array = array("success" => false, "reason" => "File name taken");
            header('Content-Type: application/json');
            echo json_encode($array);
            die();
        }
        // create that file
        write_file($location, $content);
        // delete the old file
        $fileres = unlink("notes/" . $old . ".txt");
        // update db entry
        $res = update_note($old, $title, $_SESSION['username']);

        if ($res == false || $fileres == false) {
            $array = array("success" => false, "reason" => "Database update error");
            header('Content-Type: application/json');
            echo json_encode($array);
            die();
        }
    } else {
        write_file($location, $content);
    }

    $array  = array("success" => true);
    header('Content-Type: application/json');
    echo json_encode($array);
}

function write_file($location, $content)
{
    $file = fopen($location, 'w');
    $result = fwrite($file, $content);
    if ($result === false) {
        $array = array("success" => false, "reason" => "Cannot write content");
        header('Content-Type: application/json');
        echo json_encode($array);
        die();
    }
    fclose($file);
}
