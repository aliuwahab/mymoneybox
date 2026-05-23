<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Share your PiggyBox — MyPiggyBox</title>
<style>
  body { margin: 0; padding: 0; background: #F3F1EB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E6E3DC; }
  .header { background: #15140F; padding: 28px 32px; }
  .logo-name { color: #FAFAF7; font-size: 15px; font-weight: 600; letter-spacing: -0.01em; vertical-align: middle; margin-left: 10px; }
  .hero { padding: 32px; border-bottom: 1px solid #E6E3DC; }
  .hero-tag { display: inline-block; background: #E6F1EB; color: #1B6B4E; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; padding: 4px 10px; border-radius: 99px; margin-bottom: 14px; }
  h2 { margin: 0 0 8px; font-size: 22px; font-weight: 400; color: #15140F; letter-spacing: -0.01em; font-family: Georgia, 'Times New Roman', serif; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; }
  .body { padding: 28px 32px 32px; }
  .box-block { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 10px; padding: 18px 20px; margin-bottom: 16px; }
  .box-block + .box-block { margin-top: 0; }
  .box-name { font-size: 14px; font-weight: 700; color: #15140F; margin-bottom: 12px; }
  .share-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
  .share-btn { display: inline-block; text-decoration: none; padding: 9px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #FFFFFF; }
  .share-btn-wa { background: #25D366; }
  .share-btn-fb { background: #1877F2; }
  .share-btn-tw { background: #15140F; }
  .qr-section { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; }
  .qr-text { font-size: 13px; color: #6B6862; margin-bottom: 12px; }
  .qr-text strong { color: #15140F; display: block; margin-bottom: 4px; font-size: 13px; }
  .qr-links { display: flex; gap: 10px; flex-wrap: wrap; }
  .cta { display: inline-block; background: #15140F; color: #FFFFFF; text-decoration: none; padding: 10px 18px; border-radius: 7px; font-size: 13px; font-weight: 600; }
  .cta-outline { display: inline-block; background: transparent; color: #15140F; text-decoration: none; padding: 10px 18px; border-radius: 7px; font-size: 13px; font-weight: 600; border: 1px solid #D9D6CE; }
  .tips-label { font-size: 13px; font-weight: 600; color: #15140F; margin-bottom: 10px; }
  .tip { display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-start; }
  .tip-dot { width: 22px; height: 22px; background: #E6F1EB; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #1B6B4E; flex-shrink: 0; margin-top: 1px; }
  .tip-text { font-size: 13px; color: #6B6862; line-height: 1.5; }
  .divider { border: none; border-top: 1px solid #E6E3DC; margin: 20px 0; }
  .notice { background: #FAFAF7; border-left: 3px solid #1B6B4E; border-radius: 0 8px 8px 0; padding: 12px 16px; font-size: 13px; color: #6B6862; line-height: 1.5; }
  .notice a { color: #1B6B4E; text-decoration: none; font-weight: 500; word-break: break-all; }
  .footer { padding: 20px 32px; border-top: 1px solid #E6E3DC; }
  .footer p { font-size: 12px; color: #9C998F; margin: 0; }
  .footer a { color: #1B6B4E; text-decoration: none; }
  @media only screen and (max-width: 600px) {
    .wrap { margin: 0 !important; border-radius: 0 !important; border-left: none !important; border-right: none !important; }
    .header { padding: 20px !important; }
    .hero, .body { padding: 20px !important; }
    .footer { padding: 16px 20px !important; }
    .share-buttons { flex-direction: column !important; }
    .share-btn { text-align: center !important; }
    .cta, .cta-outline { display: block !important; text-align: center !important; }
    .qr-links { flex-direction: column !important; }
  }
</style>
</head>
<body>
@php $user = $moneyBoxes->first()->user; $firstName = explode(' ', $user->name)[0]; @endphp
<div class="wrap">
  <div class="header">
    <span style="display:inline-block;width:32px;height:32px;background:#1B6B4E;border-radius:8px;text-align:center;line-height:32px;font-weight:700;font-size:16px;color:#FAFAF7;font-family:Arial,sans-serif;vertical-align:middle;">M</span>
    <span class="logo-name">MyPiggyBox</span>
  </div>

  <div class="hero">
    <div class="hero-tag">{{ $moneyBoxes->count() === 1 ? 'Your PiggyBox is live' : 'Your PiggyBoxes are live' }}</div>
    <h2>Time to get the word out</h2>
    <p>Hi {{ $firstName }}, {{ $moneyBoxes->count() === 1 ? 'your PiggyBox is' : 'your PiggyBoxes are' }} live and ready to receive contributions. The next step is sharing {{ $moneyBoxes->count() === 1 ? 'it' : 'them' }} with your network.</p>
  </div>

  <div class="body">
    <p style="font-size:13px;font-weight:600;color:#15140F;margin-bottom:14px;">Share via social media</p>

    @foreach($moneyBoxes as $box)
    @php
      $boxUrl = route('box.show', $box->slug);
      $waText = urlencode('Check out my PiggyBox "' . $box->title . '" and contribute: ' . $boxUrl);
      $fbUrl  = urlencode($boxUrl);
      $twText = urlencode('Support my PiggyBox "' . $box->title . '" 🐷');
      $twUrl  = urlencode($boxUrl);
    @endphp
    <div class="box-block">
      <div class="box-name">{{ $box->title }}</div>
      <div class="share-buttons">
        <a href="https://wa.me/?text={{ $waText }}" class="share-btn share-btn-wa">WhatsApp</a>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $fbUrl }}" class="share-btn share-btn-fb">Facebook</a>
        <a href="https://twitter.com/intent/tweet?text={{ $twText }}&amp;url={{ $twUrl }}" class="share-btn share-btn-tw">Twitter / X</a>
      </div>
    </div>
    @endforeach

    @if($moneyBoxes->count() === 1)
    @php $box = $moneyBoxes->first(); @endphp
    <div class="qr-section">
      <div class="qr-text">
        <strong>Or share your QR code</strong>
        Let people scan and contribute instantly — great for printing or adding to a post.
      </div>
      <div class="qr-links">
        <a href="{{ route('money-boxes.download-qr', $box) }}" class="cta">Download QR</a>
        <a href="{{ route('money-boxes.share', $box) }}" class="cta-outline">Share page</a>
      </div>
    </div>
    @else
    <p style="font-size:13px;color:#6B6862;margin-top:4px;">You can also download the QR code for each PiggyBox from your <a href="{{ route('dashboard') }}" style="color:#1B6B4E;text-decoration:none;font-weight:500;">dashboard</a>.</p>
    @endif

    <div class="tips-label">Three quick ways to spread the word</div>
    <div class="tip">
      <div class="tip-dot">1</div>
      <div class="tip-text"><strong style="color:#15140F;">Share in a WhatsApp group</strong> — Drop your link in the group where the people who care most about your cause are already gathered.</div>
    </div>
    <div class="tip">
      <div class="tip-dot">2</div>
      <div class="tip-text"><strong style="color:#15140F;">Post to your Instagram story</strong> — Add your PiggyBox link to your story with a link sticker so followers can contribute on the spot.</div>
    </div>
    <div class="tip">
      <div class="tip-dot">3</div>
      <div class="tip-text"><strong style="color:#15140F;">Add the link to your bio</strong> — Put your PiggyBox URL in your Instagram, Twitter, or TikTok bio to keep collecting passively.</div>
    </div>

    <hr class="divider">

    <div class="notice">
      Did you know your personal PiggyWallet link is also active? Share it to receive personal donations directly — no specific goal needed.
      @if($user->piggy_code)
        <br><br>
        <a href="{{ route('piggy.show', ['code' => $user->piggy_code]) }}">{{ route('piggy.show', ['code' => $user->piggy_code]) }}</a>
      @endif
    </div>
  </div>

  <div class="footer">
    <p>You're receiving this because you created a PiggyBox on <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>. Manage your PiggyBoxes from your <a href="{{ route('dashboard') }}">dashboard</a>.</p>
  </div>
</div>
</body>
</html>