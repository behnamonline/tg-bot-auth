<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>داشبورد کاربری</title>
    <style>
        body { font-family: Tahoma, sans-serif; text-align: center; margin-top: 50px; background: #f4f4f4; }
        .card { background: white; padding: 30px; border-radius: 8px; display: inline-block; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-logout { background: #d9534f; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-top: 15px; font-weight: bold; }
        .btn-logout:hover { background: #c9302c; }
    </style>
</head>
<body>

    <div class="card">
        <h1>خوش آمدید!</h1>
        <p id="user-info">در حال بارگذاری مشخصات از سرور...</p>
        
        <!-- دکمه خروج -->
        <br>
        <button class="btn-logout" onclick="logout()">خروج از حساب</button>
    </div>

    <script>
        async function loadUserData() {
            const token = localStorage.getItem('auth_token');

            if (!token) {
                window.location.href = 'login.php';
                return;
            }

            try {
                // درخواست به سرور با توکن موجود در LocalStorage
                let res = await fetch('user.php?token=' + token);
                let data = await res.json();

                if (data.status === 'success') {
                    const user = data.userData;
                    document.getElementById('user-info').innerHTML = `
                        سلام <b>${user.first_name}</b>!<br>
                        شناسه تلگرام: ${user.id}<br>
                        یوزرنیم: @${user.username || 'ندارد'}
                    `;
                } else {
                    logout();
                }
            } catch (e) {
                document.getElementById('user-info').innerText = 'خطا در دریافت اطلاعات کاربر';
            }
        }


        // تابع خروج: پاک کردن لوکال استوریج و هدایت به login.php
        function logout() {
            localStorage.removeItem('auth_token'); // یا localStorage.clear()
            window.location.href = 'login.php';
        }

        loadUserData();
    </script>
</body>
</html>