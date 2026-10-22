<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ورود با تلگرام</title>
    <style>
        body { font-family: Tahoma, sans-serif; text-align: center; margin-top: 50px; background: #f4f4f4; }
        .card { background: white; padding: 30px; border-radius: 8px; display: inline-block; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { background: #0088cc; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
    </style>
</head>
<body>

    <div class="card">
        <h2>ورود به سایت با تلگرام</h2>
        <p>برای ورود روی دکمه زیر کلیک کرده و ربات را استارت کنید:</p>
        
        <a href="#" id="telegram-link" target="_blank" class="btn" style="display:none;">ورود با تلگرام</a>
        <p id="status" style="margin-top: 20px; color: #666;">در حال ایجاد توکن...</p>
    </div>

    <script>
        const botUsername = "YOUR-BOT-USERNAME"; // یوزرنیم ربات شما (بدون @)
        let currentToken = "";

        async function initLogin() {
            try {
                // درخواست به سرور برای دریافت توکن جدید
                let res = await fetch('getToken.php');
                let data = await res.json();
                
                if (data.token) {
                    currentToken = data.token;
                    
                    // تنظیم لینک ربات
                    const link = `tg://resolve?domain=${botUsername}&start=${currentToken}`;
                    document.getElementById('telegram-link').href = link;
                    document.getElementById('telegram-link').style.display = 'inline-block';
                    document.getElementById('status').innerText = '';
                    

                    // بررسی هر ۲ ثانیه وضعیت توکن
                    const checkInterval = setInterval(async () => {
                        try {
                            let statusRes = await fetch('check_status.php?token=' + currentToken);
                            let statusData = await statusRes.json();

                            if (statusData.status === 'loggedIn') {
                                clearInterval(checkInterval);
                                
                                // ذخیره در LocalStorage و انتقال به داشبورد
                                localStorage.setItem('auth_token', currentToken);
                                localStorage.setItem('user_data', JSON.stringify(statusData.userData));

                                document.getElementById('status').style.color = 'green';
                                document.getElementById('status').innerText = 'ورود موفقیت‌آمیز! در حال انتقال...';

                                setTimeout(() => {
                                    window.location.href = 'dashboard.php';
                                }, 1000);
                            }
                        } catch (e) {
                            console.error("خطا در بررسی وضعیت");
                        }
                    }, 2000);
                }
            } catch (e) {
                document.getElementById('status').innerText = 'خطا در ارتباط با سرور';
            }
        }

        initLogin();
    </script>
</body>
</html>