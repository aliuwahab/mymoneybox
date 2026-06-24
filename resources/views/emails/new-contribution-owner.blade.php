<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New contribution received</title>
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
  .body { padding: 32px; }
  .amount-band { background: #E6F1EB; border: 1px solid #90C7A9; border-radius: 10px; padding: 20px 24px; margin-bottom: 24px; text-align: center; }
  .amount-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #154F3A; margin-bottom: 4px; }
  .amount-value { font-size: 36px; font-weight: 700; color: #1B6B4E; letter-spacing: -0.02em; line-height: 1; }
  .amount-sub { font-size: 12px; color: #2E8E60; margin-top: 4px; }
  h2 { margin: 0 0 8px; font-size: 20px; font-weight: 400; color: #15140F; letter-spacing: -0.01em; font-family: Georgia, 'Times New Roman', serif; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; }
  .meta-label { color: #9C998F; font-size: 13px; white-space: nowrap; padding: 10px 16px 10px 16px; vertical-align: top; }
  .meta-value { color: #15140F; font-weight: 500; font-size: 13px; text-align: right; padding: 10px 16px 10px 12px; vertical-align: top; }
  .meta-table { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; margin-bottom: 24px; border-collapse: collapse; width: 100%; }
  .meta-row-divider { border-top: 1px solid #E6E3DC; }
  .progress-wrap { background: #ECEAE3; border-radius: 99px; height: 6px; margin: 4px 0 2px; overflow: hidden; }
  .progress-fill { background: #1B6B4E; height: 6px; border-radius: 99px; }
  .cta { display: inline-block; background: #15140F; color: #FFFFFF; text-decoration: none; padding: 12px 24px; border-radius: 7px; font-size: 14px; font-weight: 600; }
  .footer { padding: 20px 32px; border-top: 1px solid #E6E3DC; }
  .footer p { font-size: 12px; color: #9C998F; margin: 0; }
  .footer a { color: #1B6B4E; text-decoration: none; }
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
    <h2>New contribution 🎉</h2>
    <p>Great news, <strong>{{ explode(' ', $moneyBox->user?->name ?? 'there')[0] }}</strong>! Someone just contributed to your PiggyBox.</p>

    <div class="amount-band">
      <div class="amount-label">New contribution</div>
      <div class="amount-value">{{ $moneyBox->formatAmount($contribution->amount) }}</div>
      <div class="amount-sub">from {{ $contribution->getDisplayName() }}</div>
    </div>

    <table class="meta-table" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td class="meta-label">PiggyBox</td>
        <td class="meta-value">{{ $moneyBox->title }}</td>
      </tr>
      <tr class="meta-row-divider">
        <td class="meta-label">Contributor</td>
        <td class="meta-value">{{ $contribution->getDisplayName() }}</td>
      </tr>
      @if($contribution->contributor_email && !$contribution->is_anonymous)
      <tr class="meta-row-divider">
        <td class="meta-label">Email</td>
        <td class="meta-value">{{ $contribution->contributor_email }}</td>
      </tr>
      @endif
      @if($contribution->message)
      <tr class="meta-row-divider">
        <td class="meta-label">Message</td>
        <td class="meta-value" style="font-style:italic;">"{{ $contribution->message }}"</td>
      </tr>
      @endif
      <tr class="meta-row-divider">
        <td class="meta-label">Total raised</td>
        <td class="meta-value" style="color:#1B6B4E;font-weight:600;">{{ $moneyBox->formatAmount($moneyBox->total_contributions) }}</td>
      </tr>
      @if($moneyBox->goal_amount)
      <tr class="meta-row-divider">
        <td class="meta-label" style="padding-bottom:4px;">Progress to goal</td>
        <td class="meta-value" style="padding-bottom:4px;">{{ number_format($moneyBox->getProgressPercentage(), 1) }}%</td>
      </tr>
      <tr>
        <td colspan="2" style="padding: 0 16px 10px;">
          <div class="progress-wrap">
            <div class="progress-fill" style="width:{{ min(100, $moneyBox->getProgressPercentage()) }}%;"></div>
          </div>
          <span style="font-size:11px;color:#9C998F;">Goal: {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}</span>
        </td>
      </tr>
      @endif
      <tr class="meta-row-divider">
        <td class="meta-label">Reference</td>
        <td class="meta-value" style="font-family:monospace;font-size:12px;">{{ $contribution->payment_reference }}</td>
      </tr>
    </table>

    <a href="{{ route('money-boxes.show', $moneyBox) }}" class="cta">View your PiggyBox →</a>
  </div>

  <div class="footer">
    <p>You received this because you own the "<strong>{{ $moneyBox->title }}</strong>" PiggyBox on <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>. <a href="{{ route('profile.edit') }}">Manage notifications</a></p>
  </div>
</div>
</body>
</html>
