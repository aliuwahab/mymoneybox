<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your PiggyBox update</title>
<style>
  body { margin: 0; padding: 0; background: #F3F1EB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E6E3DC; }
  .header { background: #15140F; padding: 28px 32px; }
  .logo-name { color: #FAFAF7; font-size: 15px; font-weight: 600; letter-spacing: -0.01em; vertical-align: middle; margin-left: 10px; }
  .hero { padding: 32px; border-bottom: 1px solid #E6E3DC; }
  .hero-tag { display: inline-block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; padding: 4px 10px; border-radius: 99px; margin-bottom: 14px; }
  .tag-24h { background: #E6F1EB; color: #1B6B4E; }
  .tag-5d  { background: #FEF3C7; color: #92400E; }
  .tag-10d { background: #FEE2E2; color: #991B1B; }
  h2 { margin: 0 0 8px; font-size: 22px; font-weight: 400; color: #15140F; letter-spacing: -0.01em; font-family: Georgia, 'Times New Roman', serif; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; }
  .box-card { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 10px; padding: 18px 20px; margin: 0 0 20px; }
  .box-name { font-size: 16px; font-weight: 700; color: #15140F; margin-bottom: 4px; }
  .box-sub { font-size: 13px; color: #6B6862; }
  .body { padding: 28px 32px 32px; }
  .step { display: flex; gap: 14px; margin-bottom: 18px; align-items: flex-start; }
  .step-num { width: 28px; height: 28px; background: #E6F1EB; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #1B6B4E; flex-shrink: 0; margin-top: 1px; }
  .step-title { font-size: 13px; font-weight: 600; color: #15140F; margin-bottom: 2px; }
  .step-desc { font-size: 13px; color: #6B6862; line-height: 1.5; }
  .cta-row { margin-top: 24px; display: flex; gap: 10px; flex-wrap: wrap; }
  .cta { display: inline-block; background: #15140F; color: #FFFFFF; text-decoration: none; padding: 12px 22px; border-radius: 7px; font-size: 14px; font-weight: 600; }
  .cta-outline { display: inline-block; background: transparent; color: #15140F; text-decoration: none; padding: 12px 22px; border-radius: 7px; font-size: 14px; font-weight: 600; border: 1px solid #D9D6CE; }
  .link-box { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; padding: 12px 16px; margin-top: 20px; }
  .link-box-label { font-size: 11px; color: #9C998F; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px; }
  .link-box a { color: #1B6B4E; text-decoration: none; font-size: 13px; font-weight: 500; word-break: break-all; }
  .stat-row { display: flex; gap: 16px; margin: 0 0 20px; }
  .stat { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; padding: 14px 16px; flex: 1; }
  .stat-val { font-size: 22px; font-weight: 700; color: #15140F; line-height: 1; margin-bottom: 4px; }
  .stat-lbl { font-size: 11px; color: #9C998F; text-transform: uppercase; letter-spacing: 0.06em; }
  .footer { padding: 20px 32px; border-top: 1px solid #E6E3DC; }
  .footer p { font-size: 12px; color: #9C998F; margin: 0; }
  .footer a { color: #1B6B4E; text-decoration: none; }
  @media only screen and (max-width: 600px) {
    .wrap { margin: 0 !important; border-radius: 0 !important; border-left: none !important; border-right: none !important; }
    .header { padding: 20px !important; }
    .body, .hero { padding: 20px !important; }
    .footer { padding: 16px 20px !important; }
    .cta, .cta-outline { display: block !important; text-align: center !important; margin-bottom: 8px !important; }
    .cta-row { flex-direction: column !important; }
    .stat-row { flex-direction: column !important; }
  }
</style>
</head>
<body>
<div class="wrap">

  {{-- Header --}}
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

@php
  $firstName       = explode(' ', $moneyBox->user?->name ?? 'there')[0];
  $boxUrl          = route('box.show', $moneyBox->slug);
  $manageUrl       = route('money-boxes.show', $moneyBox);
  $shareUrl        = route('money-boxes.share', $moneyBox);
  $contributions   = $moneyBox->contributions()->completed()->count();
  $raised          = $moneyBox->getTotalContributions();
  $formattedRaised = $moneyBox->formatAmount($raised);
@endphp

  {{-- Hero --}}
  <div class="hero">
    @if($step === '24h')
      <div class="hero-tag tag-24h">24 hours in</div>
      <h2>Have you shared it yet, {{ $firstName }}?</h2>
      <p>Your PiggyBox has been live for a day. The fastest way to get your first contribution is simple — share your link with the people who know you.</p>
    @elseif($step === '5d')
      <div class="hero-tag tag-5d">Day 5</div>
      <h2>Your PiggyBox is still growing, {{ $firstName }}</h2>
      <p>You're 5 days in. If you haven't shared yet, today is a great time to send your link to family or post it on WhatsApp. Every contribution counts.</p>
    @else
      <div class="hero-tag tag-10d">Day 10</div>
      <h2>One last nudge, {{ $firstName }}</h2>
      <p>It's been 10 days since you created your PiggyBox. There's still time to reach your goal — a single share to the right person can make all the difference.</p>
    @endif

    <div class="box-card">
      <div class="box-name">{{ $moneyBox->title }}</div>
      @if($moneyBox->description)
        <div class="box-sub">{{ Str::limit($moneyBox->description, 120) }}</div>
      @endif
      <div style="margin-top:12px;display:flex;gap:20px;">
        <div>
          <div style="font-size:20px;font-weight:700;color:#15140F;">{{ $contributions }}</div>
          <div style="font-size:11px;color:#9C998F;text-transform:uppercase;letter-spacing:0.06em;">Contributions</div>
        </div>
        <div>
          <div style="font-size:20px;font-weight:700;color:#15140F;">{{ $formattedRaised }}</div>
          <div style="font-size:11px;color:#9C998F;text-transform:uppercase;letter-spacing:0.06em;">Raised</div>
        </div>
        @if($moneyBox->goal_amount)
        <div>
          <div style="font-size:20px;font-weight:700;color:#15140F;">{{ $moneyBox->formatAmount($moneyBox->goal_amount) }}</div>
          <div style="font-size:11px;color:#9C998F;text-transform:uppercase;letter-spacing:0.06em;">Goal</div>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Body --}}
  <div class="body">
    <p style="font-size:13px;font-weight:600;color:#15140F;margin-bottom:14px;">Quick ways to get contributions in:</p>

    <div class="step">
      <div class="step-num">1</div>
      <div>
        <div class="step-title">Share on WhatsApp</div>
        <div class="step-desc">Send your link to a WhatsApp group or your contacts — it takes 10 seconds and usually brings the first few contributions.</div>
      </div>
    </div>

    <div class="step">
      <div class="step-num">2</div>
      <div>
        <div class="step-title">Post on social media</div>
        <div class="step-desc">Instagram, Facebook, X — a quick post with your PiggyBox link and why you're raising reaches people who already support you.</div>
      </div>
    </div>

    <div class="step">
      <div class="step-num">3</div>
      <div>
        <div class="step-title">Share your QR code</div>
        <div class="step-desc">Download your QR code and add it to a flyer, display it at an event, or include it in a message. People can contribute instantly by scanning.</div>
      </div>
    </div>

    @if($step === '10d')
    <div class="step">
      <div class="step-num">4</div>
      <div>
        <div class="step-title">Add a withdrawal account</div>
        <div class="step-desc">Make sure your mobile money or bank account is set up in settings so you can withdraw whenever you're ready.</div>
      </div>
    </div>
    @endif

    <div class="link-box">
      <div class="link-box-label">Your PiggyBox link</div>
      <a href="{{ $boxUrl }}">{{ $boxUrl }}</a>
    </div>

    <div class="cta-row">
      <a href="{{ $manageUrl }}" class="cta">View PiggyBox →</a>
      <a href="{{ $shareUrl }}" class="cta-outline">Share &amp; QR</a>
    </div>
  </div>

  <div class="footer">
    <p>You're receiving this because you created a PiggyBox on <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>. <a href="{{ route('profile.edit') }}">Manage notifications</a></p>
  </div>

</div>
</body>
</html>