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

if (!property_exists($input, 'text')) {
    http_response_code(400);
    die(json_encode(array('code' => 1, 'message' => 'Lack of information')));
}

if (empty($input->text)) {
    die(json_encode(array('code' => 1, 'message' => 'Missing Information')));
}

$userId = $_SESSION['userId'];
$text = $input->text;

$notes = get_notes($userId);

$array = array();

if (str_contains($text, '#')) { // for searching labels
    foreach ($notes as $note) {
        $labels = get_labels($userId, $note['NoteID']);
        $text = str_replace("#", "", $text);
        foreach ($labels as $lbl) {
            $label = $lbl['Label'];
            if (str_contains($label, $text)) {
                $startIdx = strpos(strtolower($label), strtolower($text));
                $str = substr_replace($label, "<mark>", $startIdx, 0); // throw away variable
                $endIdx = strpos(strtolower($str), strtolower($text)) + strlen($text);
                $headText = substr_replace($str, "</mark>", $endIdx, 0);
                $temp = array('headingText' => $note['Title'], 'pText' => "#" . $headText, 'noteId' => $note['NoteID'], 'locked' => isLockedNote($note['NoteID']));
                array_push($array, $temp);
            }
        }
    }
} else {
    foreach ($notes as $note) {
        $content = file_get_contents($note['Location']);
        $res1 = str_contains(strtolower($note['Title']), strtolower($text));
        $res2 = str_contains(strtolower($content), strtolower($text));
        if ($res1 || $res2) {
            if ($res1) { // for searching titles
                $startIdx = strpos(strtolower($note['Title']), strtolower($text));
                $str = substr_replace($note['Title'], "<mark>", $startIdx, 0); // throw away variable
                $endIdx = strpos(strtolower($str), strtolower($text)) + strlen($text);
                $headText = substr_replace($str, "</mark>", $endIdx, 0);
                $temp = array('headingText' => $headText, 'pText' => "", 'noteId' => $note['NoteID'], 'locked' => isLockedNote($note['NoteID']));
                array_push($array, $temp);
            } else { // for searching contents
                $startIdx = strpos(strtolower($content), strtolower($text)); // substring here
                $str = substr_replace($content, "<mark>", $startIdx, 0);
                $endIdx = strpos(strtolower($str), strtolower($text)) + strlen($text);
                $str = substr_replace($str, "</mark>", $endIdx, 0);
                $startIdx = strpos(strtolower($str), strtolower($text));
                $offset = $startIdx - 20 < 0 ? 0 : $startIdx - 20;
                $length = $endIdx + 20 > strlen($content) ? strlen($content) : $endIdx + 20;
                $pText = substr($str, $offset, $length);
                $temp = array('headingText' => $note['Title'], 'pText' => $pText,  'noteId' => $note['NoteID'], 'locked' => isLockedNote($note['NoteID']));
                array_push($array, $temp);
            }
        }
    }
}

die(json_encode(array('code' => 0, 'searches' => $array)));
