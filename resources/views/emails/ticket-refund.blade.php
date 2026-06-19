<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Refund queued for {{ $eventBox->title }}</title>
<style>
  body { margin: 0; padding: 0; background: #F3F1EB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E6E3DC; }
  .header { background: #15140F; padding: 28px 32px; }
  .logo-name { color: #FAFAF7; font-size: 15px; font-weight: 600; letter-spacing: -0.01em; }
  .body { padding: 32px; }
  h2 { margin: 0 0 8px; font-size: 20px; font-weight: 400; color: #15140F; letter-spacing: -0.01em; font-family: Georgia, 'Times New Roman', serif; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; }
  .status-badge { display: inline-block; background: #FFF3CD; color: #7B5800; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 5px 12px; border-radius: 99px; border: 1px solid #F0D980; margin-bottom: 20px; }
  .amount-band { background: #E6F1EB; border: 1px solid #90C7A9; border-radius: 10px; padding: 20px 24px; margin-bottom: 24px; text-align: center; }
  .amount-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #154F3A; margin-bottom: 4px; }
  .amount-value { font-size: 36px; font-weight: 700; color: #1B6B4E; letter-spacing: -0.02em; line-height: 1; }
  .amount-sub { font-size: 12px; color: #2E8E60; margin-top: 4px; }
  .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; }
  .meta-table td { padding: 10px 16px; border-bottom: 1px solid #E6E3DC; font-size: 13px; vertical-align: top; }
  .meta-table tr:last-child td { border-bottom: none; }
  .meta-table td:first-child { color: #9C998F; width: 45%; }
  .meta-table td:last-child { color: #15140F; font-weight: 500; }
  .info-box { background: #FFF8E7; border: 1px solid #F0D980; border-radius: 8px; padding: 14px 16px; margin-bottom: 24px; }
  .info-box p { font-size: 13px; color: #6B5900; margin: 0; line-height: 1.5; }
  .footer { padding: 20px 32px; border-top: 1px solid #E6E3DC; }
  .footer p { font-size: 12px; color: #9C998F; margin: 0; }
  .footer a { color: #1B6B4E; text-decoration: none; }
  @media only screen and (max-width: 600px) {
    .wrap { margin: 0 !important; border-radius: 0 !important; border-left: none !important; border-right: none !important; }
    .header { padding: 20px !important; }
    .body { padding: 20px !important; }
    .footer { padding: 16px 20px !important; }
    .amount-value { font-size: 28px !important; }
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
    <div class="status-badge">Refund Queued</div>

    <h2>Your refund is on its way, {{ explode(' ', $ticket->buyer_name)[0] }}.</h2>
    <p>Your ticket for <strong>{{ $eventBox->title }}</strong> has been voided and a refund has been queued to your mobile money account.</p>

    <div class="amount-band">
      <div class="amount-label">Refund amount</div>
      <div class="amount-value">GH&#x20B5; {{ number_format((float) $refund->refund_amount, 2) }}</div>
      <div class="amount-sub">Ref: {{ $refund->reference }}</div>
    </div>

    <table class="meta-table">
      <tr>
        <td>Event</td>
        <td>{{ $eventBox->title }}</td>
      </tr>
      <tr>
        <td>Ticket code</td>
        <td style="font-family:'Courier New',Courier,monospace;">{{ $ticket->code }}</td>
      </tr>
      <tr>
        <td>Amount paid</td>
        <td>GH&#x20B5; {{ number_format((float) $refund->gross_amount, 2) }}</td>
      </tr>
      @if((float) $refund->charge_amount > 0)
      <tr>
        <td>Processing fee</td>
        <td style="color:#9C998F;">&#8722; GH&#x20B5; {{ number_format((float) $refund->charge_amount, 2) }}</td>
      </tr>
      @endif
      <tr>
        <td>Refund amount</td>
        <td style="color:#1B6B4E;font-weight:600;">GH&#x20B5; {{ number_format((float) $refund->refund_amount, 2) }}</td>
      </tr>
      @if($refund->recipient_account_number)
      <tr>
        <td>Mobile money number</td>
        <td>{{ $refund->recipient_account_number }}</td>
      </tr>
      @endif
      <tr>
        <td>Refund reference</td>
        <td style="font-family:'Courier New',Courier,monospace;font-size:12px;">{{ $refund->reference }}</td>
      </tr>
      @if($refund->reason)
      <tr>
        <td>Reason</td>
        <td>{{ $refund->reason }}</td>
      </tr>
      @endif
    </table>

    <div class="info-box">
      <p>&#x23F3; Refunds are typically processed within <strong>1&#8211;3 business days</strong>. Once sent, you will receive the funds on your mobile money account. Quote reference <strong>{{ $refund->reference }}</strong> if you need to follow up.</p>
    </div>
  </div>

  <div class="footer">
    <p>Questions? Email <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a> with your refund reference <strong>{{ $refund->reference }}</strong>. This notice was sent by <a href="{{ config('app.url') }}">MyPiggyBox</a>.</p>
  </div>
</div>
</body>
</html>