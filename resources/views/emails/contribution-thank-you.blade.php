<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thank you for your contribution</title>
<style>
  body { margin: 0; padding: 0; background: #F3F1EB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E6E3DC; }
  .header { background: #15140F; padding: 28px 32px; }
  .logo { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
  .logo-name { color: #FAFAF7; font-size: 15px; font-weight: 600; letter-spacing: -0.01em; }
  @media only screen and (max-width: 600px) {
    .wrap { margin: 0 !important; border-radius: 0 !important; border-left: none !important; border-right: none !important; }
    .header { padding: 20px !important; }
    .body, .hero { padding: 20px !important; }
    .footer { padding: 16px 20px !important; }
    .amount-value { font-size: 28px !important; }
    .cta, .cta-outline { display: block !important; text-align: center !important; margin-bottom: 8px !important; }
    .cta-row { flex-direction: column !important; }
  }
  .body { padding: 32px; }
  .amount-band { background: #E6F1EB; border: 1px solid #90C7A9; border-radius: 10px; padding: 20px 24px; margin-bottom: 24px; text-align: center; }
  .amount-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #154F3A; margin-bottom: 4px; }
  .amount-value { font-size: 36px; font-weight: 700; color: #1B6B4E; letter-spacing: -0.02em; line-height: 1; }
  .amount-sub { font-size: 12px; color: #2E8E60; margin-top: 4px; }
  h2 { margin: 0 0 8px; font-size: 20px; font-weight: 700; color: #15140F; letter-spacing: -0.01em; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; }
  .box-card { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; padding: 16px 18px; margin-bottom: 24px; }
  .box-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #9C998F; margin-bottom: 4px; }
  .box-title { font-size: 15px; font-weight: 600; color: #15140F; }
  .cta { display: inline-block; background: #1B6B4E; color: #FFFFFF; text-decoration: none; padding: 12px 24px; border-radius: 7px; font-size: 14px; font-weight: 600; }
  .footer { padding: 20px 32px; border-top: 1px solid #E6E3DC; }
  .footer p { font-size: 12px; color: #9C998F; margin: 0; }
  .footer a { color: #1B6B4E; text-decoration: none; }
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
    <h2>Thank you{{ $contribution->getDisplayName() !== 'Anonymous' ? ', ' . explode(' ', $contribution->getDisplayName())[0] : '' }}! 🎉</h2>
    <p>Your contribution has been received and confirmed. Here's a summary:</p>

    <div class="amount-band">
      <div class="amount-label">Amount contributed</div>
      <div class="amount-value">{{ $moneyBox->formatAmount($contribution->amount) }}</div>
      <div class="amount-sub">{{ $moneyBox->currency_code }} · Ref: {{ $contribution->payment_reference }}</div>
    </div>

    <div class="box-card">
      <div class="box-label">PiggyBox</div>
      <div class="box-title">{{ $moneyBox->title }}</div>
      @if($moneyBox->description)
        <p style="font-size:13px;color:#6B6862;margin:6px 0 0;">{{ Str::limit($moneyBox->description, 120) }}</p>
      @endif
    </div>

    @if($contribution->message)
      <div class="box-card" style="margin-bottom:24px;">
        <div class="box-label">Your message</div>
        <p style="font-size:13px;color:#15140F;margin:4px 0 0;font-style:italic;">"{{ $contribution->message }}"</p>
      </div>
    @endif

    <p>The PiggyBox owner has been notified. If you have any questions about your contribution, please reference <strong>{{ $contribution->payment_reference }}</strong>.</p>

    <a href="{{ route('box.show', $moneyBox->slug) }}" class="cta">View the PiggyBox →</a>
  </div>

  <div class="footer">
    <p>You received this email because you made a contribution on <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>. If you didn't make this contribution, please <a href="mailto:{{ config('mail.from.address') }}">contact us</a>.</p>
  </div>
</div>
</body>
</html>
