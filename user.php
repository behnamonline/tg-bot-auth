<?php
header('Content-Type: application/json');
$token = $_GET['token'] ?? '';
$jsonFile = '.tokens.json';

if (!$token || !file_exists($jsonFile)) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$tokens = json_decode(file_get_contents($jsonFile), true);

if (isset($tokens[$token]) && $tokens[$token]['status'] === 'loggedIn') {
    echo json_encode([
        "status" => "success",
        "userData" => $tokens[$token]['userData']
    ]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid token"]);