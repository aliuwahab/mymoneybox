<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Your Password — MyPiggyBox</title>
<style>
  body { margin: 0; padding: 0; background: #F3F1EB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E6E3DC; }
  .header { background: #15140F; padding: 28px 32px; }
  .logo { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
  .logo-name { color: #FAFAF7; font-size: 15px; font-weight: 600; letter-spacing: -0.01em; }
  .body { padding: 40px 32px 32px; text-align: center; }
  .icon-wrap { display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: #E6F1EB; border-radius: 50%; margin-bottom: 20px; }
  h2 { margin: 0 0 10px; font-size: 22px; font-weight: 700; color: #15140F; letter-spacing: -0.01em; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; text-align: center; }
  .cta { display: inline-block; background: #1B6B4E; color: #FFFFFF; text-decoration: none; padding: 14px 32px; border-radius: 7px; font-size: 15px; font-weight: 600; margin: 8px 0 20px; }
  .expiry-note { font-size: 12px; color: #9C998F; margin: 0 0 16px; }
  .ignore-note { font-size: 13px; color: #9C998F; background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; padding: 12px 16px; margin-top: 20px; text-align: left; }
  .url-fallback { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; padding: 12px 16px; margin: 20px 0 0; text-align: left; }
  .url-fallback p { font-size: 12px; color: #9C998F; margin: 0 0 4px; text-align: left; }
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
        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
      </svg>
    </div>

    <h2>Reset your password</h2>
    <p>You requested a password reset for your MyPiggyBox account. Click the button below to choose a new password.</p>

    <a href="{{ $url }}" class="cta">Reset Password</a>

    <p class="expiry-note">This link expires in 60 minutes.</p>

    <div class="url-fallback">
      <p>If the button doesn't work, copy and paste this link into your browser:</p>
      <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="ignore-note">
      If you didn't request a password reset, you can safely ignore this email — your password won't change.
    </div>
  </div>

  <div class="footer">
    <p>You received this from <a href="{{ config('app.url') }}">{{ config('app.name') }}</a> because a password reset was requested for your account. <a href="mailto:{{ config('mail.from.address') }}">Contact support</a> if you have concerns.</p>
  </div>
</div>
</body>
</html>