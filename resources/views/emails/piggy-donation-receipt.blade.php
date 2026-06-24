<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Piggy Wallet gift receipt</title>
<style>
  body { margin: 0; padding: 0; background: #F3F1EB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E6E3DC; }
  .header { background: #15140F; padding: 28px 32px; }
  .logo-name { color: #FAFAF7; font-size: 15px; font-weight: 600; letter-spacing: -0.01em; }
  .body { padding: 32px; }
  .amount-band { background: #E6F1EB; border: 1px solid #90C7A9; border-radius: 10px; padding: 20px 24px; margin-bottom: 24px; text-align: center; }
  .amount-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #154F3A; margin-bottom: 4px; }
  .amount-value { font-size: 36px; font-weight: 700; color: #1B6B4E; letter-spacing: -0.02em; line-height: 1; }
  .amount-sub { font-size: 12px; color: #2E8E60; margin-top: 4px; }
  h2 { margin: 0 0 8px; font-size: 20px; font-weight: 400; color: #15140F; letter-spacing: 0; font-family: Georgia, 'Times New Roman', serif; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; }
  .box-card { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; padding: 16px 18px; margin-bottom: 24px; }
  .box-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #9C998F; margin-bottom: 4px; }
  .box-title { font-size: 15px; font-weight: 600; color: #15140F; }
  .cta { display: inline-block; background: #15140F; color: #FFFFFF; text-decoration: none; padding: 12px 24px; border-radius: 7px; font-size: 14px; font-weight: 600; }
  .footer { padding: 20px 32px; border-top: 1px solid #E6E3DC; }
  .footer p { font-size: 12px; color: #9C998F; margin: 0; }
  .footer a { color: #1B6B4E; text-decoration: none; }
  @media only screen and (max-width: 600px) {
    .wrap { margin: 0 !important; border-radius: 0 !important; border-left: none !important; border-right: none !important; }
    .header, .body, .footer { padding: 20px !important; }
    .amount-value { font-size: 28px !important; }
    .cta { display: block !important; text-align: center !important; }
  }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <table cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
      <tr>
        <td valign="middle" style="padding:0;">
          <span style="display:inline-block;width:32px;height:32px;background:#1B6B4E;border-radius:8px;text-align:center;line-height:32px;font-weight:700;font-size:16px;color:#FAFAF7;font-family:Arial,sans-serif;">M</span>
        </td>
        <td valign="middle" style="padding:0 0 0 10px;">
          <span class="logo-name">MyPiggyBox</span>
        </td>
      </tr>
    </table>
  </div>

  <div class="body">
    <h2>Thank you{{ $donation->getDisplayName() !== 'Anonymous' ? ', ' . explode(' ', $donation->getDisplayName())[0] : '' }}!</h2>
    <p>Your Piggy Wallet gift has been confirmed. Keep this receipt for your records.</p>

    <div class="amount-band">
      <div class="amount-label">Gift amount</div>
      <div class="amount-value">{{ $piggyBox->formatAmount($donation->amount) }}</div>
      <div class="amount-sub">{{ $piggyBox->currency_code }} · Ref: {{ $donation->payment_reference }}</div>
    </div>

    <div class="box-card">
      <div class="box-label">Recipient</div>
      <div class="box-title">{{ $piggyBox->user?->name ?? $piggyBox->title }}</div>
      @if($donation->message)
        <p style="font-size:13px;color:#15140F;margin:10px 0 0;font-style:italic;">"{{ $donation->message }}"</p>
      @endif
    </div>

    <p>The Piggy Wallet owner can now see this gift in their wallet. If you have questions, reference <strong>{{ $donation->payment_reference }}</strong>.</p>

    @if($piggyBox->user?->piggy_code)
      <a href="{{ route('piggy.show', $piggyBox->user?->piggy_code) }}" class="cta">View Piggy Wallet</a>
    @endif
  </div>

  <div class="footer">
    <p>You received this email because you sent a Piggy Wallet gift on <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>.</p>
  </div>
</div>
</body>
</html>
