<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're offline — MyPiggyBox</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif;
            background: #F3F1EB;
            color: #15140F;
            min-height: 100svh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border: 1px solid #E6E3DC;
            border-radius: 16px;
            padding: 48px 40px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .icon {
            width: 56px;
            height: 56px;
            background: #F3F1EB;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        h1 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        p {
            font-size: 0.875rem;
            color: #6B6862;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .btn {
            display: inline-block;
            background: #15140F;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn:hover { background: #2A2820; }
        .brand {
            font-size: 0.75rem;
            color: #9C998F;
            margin-top: 32px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#9C998F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <line x1="1" y1="1" x2="23" y2="23"/>
                <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/>
                <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/>
                <path d="M10.71 5.05A16 16 0 0 1 22.56 9"/>
                <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/>
                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                <line x1="12" y1="20" x2="12.01" y2="20"/>
            </svg>
        </div>
        <h1>You're offline</h1>
        <p>Check your connection and try again. Pages you've visited recently may still be available.</p>
        <button class="btn" onclick="window.location.reload()">Try again</button>
    </div>
    <div class="brand">MyPiggyBox</div>
</body>
</html>