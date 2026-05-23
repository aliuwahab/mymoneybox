<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your PiggyBox is live!</title>
<style>
  body { margin: 0; padding: 0; background: #F3F1EB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E6E3DC; }
  .header { background: #15140F; padding: 28px 32px; }
  .logo-name { color: #FAFAF7; font-size: 15px; font-weight: 600; letter-spacing: -0.01em; vertical-align: middle; margin-left: 10px; }
  @media only screen and (max-width: 600px) {
    .wrap { margin: 0 !important; border-radius: 0 !important; border-left: none !important; border-right: none !important; }
    .header { padding: 20px !important; }
    .body, .hero { padding: 20px !important; }
    .footer { padding: 16px 20px !important; }
    .amount-value { font-size: 28px !important; }
    .cta, .cta-outline { display: block !important; text-align: center !important; margin-bottom: 8px !important; }
    .cta-row { flex-direction: column !important; }
  }
  .hero { padding: 32px; border-bottom: 1px solid #E6E3DC; }
  .hero-tag { display: inline-block; background: #E6F1EB; color: #1B6B4E; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; padding: 4px 10px; border-radius: 99px; margin-bottom: 14px; }
  h2 { margin: 0 0 8px; font-size: 22px; font-weight: 700; color: #15140F; letter-spacing: -0.01em; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; }
  .box-card { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 10px; padding: 18px 20px; margin: 0 0 20px; }
  .box-name { font-size: 16px; font-weight: 700; color: #15140F; margin-bottom: 4px; }
  .box-sub { font-size: 13px; color: #6B6862; }
  .body { padding: 28px 32px 32px; }
  .step { display: flex; gap: 14px; margin-bottom: 18px; align-items: flex-start; }
  .step-num { width: 28px; height: 28px; background: #E6F1EB; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #1B6B4E; flex-shrink: 0; margin-top: 1px; }
  .step-title { font-size: 13px; font-weight: 600; color: #15140F; margin-bottom: 2px; }
  .step-desc { font-size: 13px; color: #6B6862; }
  .cta-row { margin-top: 24px; display: flex; gap: 10px; flex-wrap: wrap; }
  .cta { display: inline-block; background: #1B6B4E; color: #FFFFFF; text-decoration: none; padding: 12px 22px; border-radius: 7px; font-size: 14px; font-weight: 600; }
  .cta-outline { display: inline-block; background: transparent; color: #1B6B4E; text-decoration: none; padding: 12px 22px; border-radius: 7px; font-size: 14px; font-weight: 600; border: 1px solid #90C7A9; }
  .link-box { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; padding: 12px 16px; margin-top: 20px; font-size: 12px; color: #6B6862; }
  .link-box a { color: #1B6B4E; text-decoration: none; font-size: 13px; font-weight: 500; word-break: break-all; }
  .footer { padding: 20px 32px; border-top: 1px solid #E6E3DC; }
  .footer p { font-size: 12px; color: #9C998F; margin: 0; }
  .footer a { color: #1B6B4E; text-decoration: none; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <span style="display:inline-block;width:32px;height:32px;background:#1B6B4E;border-radius:8px;text-align:center;line-height:32px;font-weight:700;font-size:16px;color:#FAFAF7;font-family:Arial,sans-serif;vertical-align:middle;">M</span>
    <span class="logo-name">MyPiggyBox</span>
  </div>

  <div class="hero">
    <div class="hero-tag">🎉 PiggyBox live</div>
    <h2>Your PiggyBox is ready to collect!</h2>
    <p>Hi {{ explode(' ', $moneyBox->user->name)[0] }}, your PiggyBox has been created and is ready to share.</p>

    <div class="box-card">
      <div class="box-name">{{ $moneyBox->title }}</div>
      @if($moneyBox->description)
        <div class="box-sub">{{ Str::limit($moneyBox->description, 120) }}</div>
      @endif
      <div style="margin-top:10px;display:flex;gap:12px;font-size:12px;color:#9C998F;">
        <span>{{ ucfirst($moneyBox->visibility->value) }}</span>
        <span>·</span>
        <span>{{ ucfirst($moneyBox->amount_type->value) }} contributions</span>
        @if($moneyBox->goal_amount)
          <span>·</span>
          <span>Goal: {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}</span>
        @endif
      </div>
    </div>
  </div>

  <div class="body">
    <p style="font-size:13px;font-weight:600;color:#15140F;margin-bottom:14px;">Next steps to get contributions flowing:</p>

    <div class="step">
      <div class="step-num">1</div>
      <div>
        <div class="step-title">Share your link</div>
        <div class="step-desc">Send your PiggyBox link to friends, family, or supporters via WhatsApp, social media, or email.</div>
      </div>
    </div>

    <div class="step">
      <div class="step-num">2</div>
      <div>
        <div class="step-title">Download your QR code</div>
        <div class="step-desc">Print or share your QR code so people can contribute instantly by scanning it.</div>
      </div>
    </div>

    <div class="step">
      <div class="step-num">3</div>
      <div>
        <div class="step-title">Add a withdrawal account</div>
        <div class="step-desc">Set up your mobile money or bank account in settings so you can withdraw when you're ready.</div>
      </div>
    </div>

    <div class="link-box">
      <div style="margin-bottom:4px;color:#9C998F;font-size:11px;text-transform:uppercase;letter-spacing:0.06em;">Your PiggyBox link</div>
      <a href="{{ route('box.show', $moneyBox->slug) }}">{{ route('box.show', $moneyBox->slug) }}</a>
    </div>

    <div class="cta-row">
      <a href="{{ route('money-boxes.show', $moneyBox) }}" class="cta">Manage PiggyBox →</a>
      <a href="{{ route('money-boxes.share', $moneyBox) }}" class="cta-outline">Share &amp; QR code</a>
    </div>
  </div>

  <div class="footer">
    <p>You're receiving this because you created a PiggyBox on <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>. <a href="{{ route('profile.edit') }}">Manage notifications</a></p>
  </div>
</div>
</body>
</html>
