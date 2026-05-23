<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Your Email — MyPiggyBox</title>
<style>
  body { margin: 0; padding: 0; background: #F3F1EB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E6E3DC; }
  .header { background: #15140F; padding: 28px 32px; }
  .logo { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
  .logo-name { color: #FAFAF7; font-size: 15px; font-weight: 600; letter-spacing: -0.01em; }
  .body { padding: 40px 32px 32px; text-align: center; }
  .icon-wrap { display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: #E6F1EB; border-radius: 50%; margin-bottom: 20px; }
  h2 { margin: 0 0 10px; font-size: 22px; font-weight: 700; color: #15140F; letter-spacing: -0.01em; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; }
  .cta { display: inline-block; background: #1B6B4E; color: #FFFFFF; text-decoration: none; padding: 14px 32px; border-radius: 7px; font-size: 15px; font-weight: 600; margin: 8px 0 20px; }
  .expiry-note { font-size: 12px; color: #9C998F; margin: 0; }
  .url-fallback { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; padding: 12px 16px; margin: 20px 0 0; text-align: left; }
  .url-fallback p { font-size: 12px; color: #9C998F; margin: 0 0 4px; }
  .url-fallback a { color: #1B6B4E; text-decoration: none; font-size: 12px; word-break: break-all; }
  .footer { padding: 20px 32px; border-top: 1px solid #E6E3DC; }
  .footer p { font-size: 12px; color: #9C998F; margin: 0; }
  .footer a { color: #1B6B4E; text-decoration: none; }
  @media only screen and (max-width: 600px) {
    .wrap { margin: 0 !important; border-radius: 0 !important; border-left: none !important; border-right: none !important; }
    .header { padding: 20px !important; }
    .body { padding: 28px 20px 24px !important; }
    .footer { padding: 16px 20px !important; }
    .cta { display: block !important; text-align: center !important; }
  }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <span class="logo">
      <img src="{{ config('app.url') }}/apple-touch-icon.png" alt="MyPiggyBox" width="36" height="36" style="border-radius:8px;display:inline-block;vertical-align:middle;">
      <span class="logo-name">MyPiggyBox</span>
    </span>
  </div>

  <div class="body">
    <div class="icon-wrap">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1B6B4E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="4" width="20" height="16" rx="2"/>
        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
      </svg>
    </div>

    <h2>Welcome to MyPiggyBox!</h2>
    <p>Please verify your email address to get started. Click the button below to confirm your account.</p>

    <a href="{{ $url }}" class="cta">Verify Email Address</a>

    <p class="expiry-note">This link expires in 60 minutes.</p>

    <div class="url-fallback">
      <p>If the button doesn't work, copy and paste this link into your browser:</p>
      <a href="{{ $url }}">{{ $url }}</a>
    </div>
  </div>

  <div class="footer">
    <p>If you didn't create an account on <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>, no action is needed — you can safely ignore this email.</p>
  </div>
</div>
</body>
</html>