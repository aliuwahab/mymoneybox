<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your ticket for {{ $eventBox->title }}</title>
<style>
  body { margin: 0; padding: 0; background: #F3F1EB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E6E3DC; }
  .header { background: #15140F; padding: 28px 32px; }
  .logo-name { color: #FAFAF7; font-size: 15px; font-weight: 600; letter-spacing: -0.01em; }
  @media only screen and (max-width: 600px) {
    .wrap { margin: 0 !important; border-radius: 0 !important; border-left: none !important; border-right: none !important; }
    .header { padding: 20px !important; }
    .body { padding: 20px !important; }
    .footer { padding: 16px 20px !important; }
    .ticket-code { font-size: 20px !important; }
  }
  .body { padding: 32px; }
  .confirmed-badge { display: inline-block; background: #E6F1EB; color: #154F3A; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 5px 12px; border-radius: 20px; border: 1px solid #90C7A9; margin-bottom: 20px; }
  h2 { margin: 0 0 8px; font-size: 20px; font-weight: 400; color: #15140F; letter-spacing: -0.01em; font-family: Georgia, 'Times New Roman', serif; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; }
  .event-band { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 10px; padding: 18px 20px; margin-bottom: 24px; }
  .event-band-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #9C998F; margin-bottom: 6px; }
  .event-band-title { font-size: 16px; font-weight: 700; color: #15140F; margin-bottom: 6px; }
  .event-band-meta { font-size: 13px; color: #6B6862; }
  .event-band-meta span { margin-right: 12px; }
  .qr-section { text-align: center; margin-bottom: 24px; }
  .qr-section img { border: 1px solid #E6E3DC; border-radius: 10px; }
  .code-band { background: #15140F; border-radius: 10px; padding: 20px 24px; text-align: center; margin-bottom: 24px; }
  .code-band-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #9C998F; margin-bottom: 8px; }
  .ticket-code { font-family: 'Courier New', Courier, monospace; font-size: 26px; font-weight: 700; color: #FAFAF7; letter-spacing: 0.12em; }
  .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  .meta-table td { padding: 9px 0; border-bottom: 1px solid #F0EDE7; font-size: 13.5px; vertical-align: top; }
  .meta-table td:first-child { color: #9C998F; width: 40%; }
  .meta-table td:last-child { color: #15140F; font-weight: 500; text-align: right; }
  .meta-table tr:last-child td { border-bottom: none; }
  .info-box { background: #FFF8E7; border: 1px solid #F0D980; border-radius: 8px; padding: 14px 16px; margin-bottom: 24px; }
  .info-box p { font-size: 13px; color: #6B5900; margin: 0; line-height: 1.5; }
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
    <div class="confirmed-badge">&#10003; Ticket Confirmed</div>

    <h2>You're in, {{ explode(' ', $ticket->buyer_name)[0] }}! See you at {{ $eventBox->title }}.</h2>
    <p>Your ticket has been confirmed and is ready to use at the entrance. Keep this email handy.</p>

    <div class="event-band">
      <div class="event-band-label">Event details</div>
      <div class="event-band-title">{{ $eventBox->title }}</div>
      <div class="event-band-meta">
        <span>{{ $eventBox->event_date->format('D, M j, Y \· g:ia') }}</span>
        @if($eventBox->venue)
          <span>{{ $eventBox->venue }}</span>
        @endif
      </div>
    </div>

    <div class="qr-section">
      <img src="{{ $message->embedData($qrCodeData, 'ticket-qr.png', 'image/png') }}" width="200" height="200" alt="Ticket QR Code" />
      <p style="margin-top:10px;font-size:12px;color:#9C998F;">Scan this code at the entrance</p>
    </div>

    <div class="code-band">
      <div class="code-band-label">Your ticket code</div>
      <div class="ticket-code">{{ $ticket->code }}</div>
    </div>

    <table class="meta-table">
      <tr>
        <td>Ticket holder</td>
        <td>{{ $ticket->buyer_name }}</td>
      </tr>
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
        <td>GH&#x20B5; {{ number_format((float) $ticket->amount, 2) }}</td>
      </tr>
      <tr>
        <td>Reference</td>
        <td style="font-family:'Courier New',Courier,monospace;font-size:12px;">{{ $ticket->payment_reference }}</td>
      </tr>
    </table>

    <div class="info-box">
      <p>&#x26A0;&#xFE0F; Present this QR code <strong>or</strong> your ticket code at the entrance. Each ticket is single-use and cannot be transferred.</p>
    </div>
  </div>

  <div class="footer">
    <p>Questions? Email us at <a href="mailto:support@mypiggybox.com">support@mypiggybox.com</a> quoting your reference <strong>{{ $ticket->payment_reference }}</strong>. This ticket was issued by <a href="{{ config('app.url') }}">MyPiggyBox</a>.</p>
  </div>
</div>
</body>
</html>