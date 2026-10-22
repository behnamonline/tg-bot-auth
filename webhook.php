<?php
// تنظیم توکن ربات
$botToken = "YOUR-BOT-TOKEN";

// استخراج seccode (بخش دوم بات توکن بعد از کلن) برای امنیت در متد POST
$tokenParts = explode(':', $botToken);
$secCode = $tokenParts[1] ?? '';

// ۱. اگر صفحه با متد GET اجرا شد، بدون نیاز به seccode وب‌هوق را ست کن
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // بدست آوردن آدرس کامل همین فایل روی هاست همراه با پارامتر امنیتی برای POST
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $currentUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?me=" . $secCode;

    // درخواست به تلگرام برای تنظیم وب‌هوق
    $telegramApiUrl = "https://api.telegram.org/bot{$botToken}/setWebhook?url=" . urlencode($currentUrl);
    $response = file_get_contents($telegramApiUrl);

    header('Content-Type: application/json; charset=utf-8');
    echo $response;
    exit;
}

// ۲. پردازش درخواست‌های POST (با بررسی امنیتی seccode)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_GET['me']) || $_GET['me'] !== $secCode) {
        http_response_code(403);
        exit;
    }

    $content = file_get_contents("php://input");
    $update = json_decode($content, true);

    if (isset($update['message']['text'])) {
        $chatId = $update['message']['chat']['id'];
        $text = $update['message']['text'];
        $user = $update['message']['from'];

        if (strpos($text, '/start ') === 0) {
            $token = trim(str_replace('/start ', '', $text));
            $jsonFile = '.tokens.json';

            if (file_exists($jsonFile)) {
                $tokens = json_decode(file_get_contents($jsonFile), true);

                if (isset($tokens[$token]) && $tokens[$token]['status'] === 'pending') {
                    
                    $tokens[$token] = [
                        "status" => "loggedIn",
                        "userData" => [
                            "id" => $user['id'],
                            "first_name" => $user['first_name'] ?? '',
                            "username" => $user['username'] ?? ''
                        ]
                    ];

                    file_put_contents($jsonFile, json_encode($tokens, JSON_PRETTY_PRINT));

                    // ارسال پیام تایید به تلگرام کاربر
                    $message = "خوش آمدید، به وبسایت بازگردید.";
                    $url = "https://api.telegram.org/bot{$botToken}/sendMessage?chat_id={$chatId}&text=" . urlencode($message);
                    file_get_contents($url);
                }
            }
        }
    }
}