<?php
header('Content-Type: application/json');
$jsonFile = '.tokens.json';

// خواندن فایل یا ایجاد آرایه خالی
$tokens = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
if (!is_array($tokens)) {
    $tokens = [];
}

// محاسبه شماره اندیس بعدی (تعداد کلیدها به علاوه یک)
$nextIndex = count($tokens) + 1;

// ساخت توکن دقیقا مطابق فرمت شما: md5("index" + rand + str_shuffle)
$randomString = str_shuffle("abcdedfghijlkmonsdf");
$randomNumber = rand(1000, 9999);
$token = md5($nextIndex . $randomNumber . $randomString);

// ذخیره در فایل با وضعیت pending
$tokens[$token] = ["status" => "pending"];
file_put_contents($jsonFile, json_encode($tokens, JSON_PRETTY_PRINT));

// ارسال توکن به فرانت‌اند
echo json_encode(["token" => $token]);