<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyron Framework - راه‌اندازی شد</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            width: 100%;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .logo {
            font-size: 60px;
            font-weight: bold;
            background: linear-gradient(135deg, #ffd700 0%, #ff8c00 50%, #ff0000 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 20px;
            letter-spacing: 8px;
        }

        .badge {
            display: inline-block;
            background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
            color: #1a1a2e;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            margin-bottom: 30px;
            font-weight: bold;
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
        }

        .version {
            color: #ff8c00;
            font-size: 18px;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .success-icon {
            font-size: 70px;
            margin-bottom: 20px;
            animation: bounce 1s ease infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }

        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .info-card h3 {
            color: #ff8c00;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .info-card p {
            color: #666;
            font-size: 14px;
        }

        .commands {
            background: #1e1e1e;
            border-radius: 15px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }

        .commands h3 {
            color: #ffd700;
            margin-bottom: 15px;
            text-align: center;
        }

        .command {
            background: #2d2d2d;
            color: #4ec9b0;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .command-icon {
            color: #ffd700;
            font-size: 18px;
        }

        .command-text {
            flex: 1;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #999;
            font-size: 14px;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #d4edda;
            color: #155724;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 14px;
            margin-top: 20px;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            background: #28a745;
            border-radius: 50%;
            animation: pulse 1.5s ease infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @media (max-width: 600px) {
            .card {
                padding: 30px;
            }
            
            .logo {
                font-size: 40px;
                letter-spacing: 4px;
            }
            
            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="success-icon">⚡</div>
            <div class="logo">CYRON</div>
            <div class="badge">PHP FRAMEWORK</div>
            
            <h1>✨ فریمورک با موفقیت اجرا شد! ✨</h1>
            <div class="version">Version 1.0.0</div>
            
            <div class="status">
                <span class="status-dot"></span>
                <span>سیستم در حال اجرا است</span>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <h3>⚡ Fast</h3>
                    <p>سبک و سریع با معماری MVC</p>
                </div>
                <div class="info-card">
                    <h3>🔒 Secure</h3>
                    <p>امنیت بالا در برابر تهدیدها</p>
                </div>
                <div class="info-card">
                    <h3>🎨 Modern</h3>
                    <p>مدرن و به‌روز با آخرین تکنولوژی‌ها</p>
                </div>
                <div class="info-card">
                    <h3>📦 Lightweight</h3>
                    <p>کم حجم و بهینه</p>
                </div>
            </div>

            <div class="commands">
                <h3>🔧 دستورات مفید فریمورک</h3>
                <div class="command">
                    <span class="command-icon">▶</span>
                    <span class="command-text">php cyron make:controller HomeController</span>
                </div>
                <div class="command">
                    <span class="command-icon">▶</span>
                    <span class="command-text">php cyron make:model User</span>
                </div>
                <div class="command">
                    <span class="command-icon">▶</span>
                    <span class="command-text">php cyron make:migration create_users_table</span>
                </div>
                <div class="command">
                    <span class="command-icon">▶</span>
                    <span class="command-text">php cyron migrate</span>
                </div>
                <div class="command">
                    <span class="command-icon">▶</span>
                    <span class="command-text">php cyron route:list</span>
                </div>
                <div class="command">
                    <span class="command-icon">▶</span>
                    <span class="command-text">php cyron serve --port=8000</span>
                </div>
            </div>

            <div class="footer">
                <p>© 2024 Cyron Framework | Created with ❤️ by Aviroon</p>
                <p style="margin-top: 10px; font-size: 12px;">PHP Version: <?php echo phpversion(); ?> | Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Built-in Server'; ?></p>
            </div>
        </div>
    </div>
</body>
</html>