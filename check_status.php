<?php
header('Content-Type: application/json');
$token = $_GET['token'] ?? '';
$jsonFile = '.tokens.json';

if ($token && file_exists($jsonFile)) {
    $tokens = json_decode(file_get_contents($jsonFile), true);
    if (isset($tokens[$token])) {
        echo json_encode($tokens[$token]);
        exit;
    }
}
echo json_encode(["status" => "pending"]);