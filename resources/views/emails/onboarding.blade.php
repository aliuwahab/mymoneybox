<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('app.name') }}</title>
<style>
  body { margin: 0; padding: 0; background: #F3F1EB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E6E3DC; }
  .header { background: #15140F; padding: 28px 32px; }
  .logo-name { color: #FAFAF7; font-size: 15px; font-weight: 600; letter-spacing: -0.01em; vertical-align: middle; margin-left: 10px; }
  .hero { padding: 32px 32px 0; }
  .hero-tag { display: inline-block; background: #E6F1EB; color: #1B6B4E; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; padding: 4px 10px; border-radius: 99px; margin-bottom: 14px; }
  h2 { margin: 0 0 10px; font-size: 22px; font-weight: 400; color: #15140F; letter-spacing: -0.01em; font-family: Georgia, 'Times New Roman', serif; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; }
  .body { padding: 24px 32px 32px; }
  .product-card { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 10px; padding: 18px 20px; margin: 0 0 14px; }
  .product-title { font-size: 14px; font-weight: 700; color: #15140F; margin-bottom: 4px; }
  .product-desc { font-size: 13px; color: #6B6862; line-height: 1.5; }
  .cta { display: inline-block; background: #15140F; color: #FFFFFF; text-decoration: none; padding: 13px 26px; border-radius: 7px; font-size: 14px; font-weight: 600; margin: 8px 0; }
  .idea-list { padding: 0; margin: 0 0 16px; list-style: none; }
  .idea-list li { font-size: 14px; color: #6B6862; line-height: 1.6; padding: 6px 0; border-bottom: 1px solid #F0EDE6; display: flex; gap: 10px; }
  .idea-list li:last-child { border-bottom: none; }
  .idea-dot { color: #1B6B4E; font-weight: 700; flex-shrink: 0; }
  .link-box { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; padding: 12px 16px; margin: 20px 0 0; }
  .link-box-label { font-size: 11px; color: #9C998F; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px; }
  .link-box a { color: #1B6B4E; text-decoration: none; font-size: 13px; font-weight: 500; word-break: break-all; }
  .divider { border: none; border-top: 1px solid #E6E3DC; margin: 20px 0; }
  .footer { padding: 20px 32px; border-top: 1px solid #E6E3DC; }
  .footer p { font-size: 12px; color: #9C998F; margin: 0; }
  .footer a { color: #1B6B4E; text-decoration: none; }
  @media only screen and (max-width: 600px) {
    .wrap { margin: 0 !important; border-radius: 0 !important; border-left: none !important; border-right: none !important; }
    .header { padding: 20px !important; }
    .hero, .body { padding: 20px !important; }
    .footer { padding: 16px 20px !important; }
    .cta { display: block !important; text-align: center !important; }
  }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <span style="display:inline-block;width:32px;height:32px;background:#1B6B4E;border-radius:8px;text-align:center;line-height:32px;font-weight:700;font-size:16px;color:#FAFAF7;font-family:Arial,sans-serif;vertical-align:middle;">M</span>
    <span class="logo-name">MyPiggyBox</span>
  </div>

@php $firstName = explode(' ', $user->name)[0]; @endphp

@if($step === 'onboarding_1d')
  <div class="hero">
    <div class="hero-tag">Welcome</div>
    <h2>You're all set, {{ $firstName }}!</h2>
    <p>Great to have you on MyPiggyBox. You now have access to two powerful tools for collecting and receiving money from the people around you.</p>
  </div>
  <div class="body">
    <div class="product-card">
      <div class="product-title">PiggyBox — for events &amp; goals</div>
      <div class="product-desc">Create a dedicated collection page for any occasion: a birthday fund, charity drive, team gift, travel savings, wedding, and more. Share the link and start collecting contributions instantly.</div>
    </div>
    <div class="product-card">
      <div class="product-title">PiggyWallet — your personal donation link</div>
      <div class="product-desc">Your PiggyWallet is a permanent personal link people can use to send you money directly — no event needed. Share it once, use it forever.</div>
    </div>
    <p style="margin-top:20px;">Ready to create your first PiggyBox? It takes less than two minutes.</p>
    <a href="{{ route('money-boxes.create') }}" class="cta">Create your first PiggyBox →</a>
    <div class="link-box">
      <div class="link-box-label">Your PiggyWallet link</div>
      <a href="{{ route('piggy.show', ['code' => $user->piggy_code]) }}">{{ route('piggy.show', ['code' => $user->piggy_code]) }}</a>
    </div>
  </div>

@elseif($step === 'onboarding_3d')
  <div class="hero">
    <div class="hero-tag">PiggyWallet</div>
    <h2>Your PiggyWallet is already active</h2>
    <p>Hi {{ $firstName }}, your personal donation link is live and ready to receive money from anyone — no setup needed.</p>
  </div>
  <div class="body">
    <p>Share your PiggyWallet link with friends, family, or on social media and people can send you money with a few taps. It works 24/7, and every contribution lands directly in your account.</p>
    <div class="link-box" style="margin-bottom:20px;">
      <div class="link-box-label">Your PiggyWallet link</div>
      <a href="{{ route('piggy.show', ['code' => $user->piggy_code]) }}">{{ route('piggy.show', ['code' => $user->piggy_code]) }}</a>
    </div>
    <hr class="divider">
    <p style="font-size:13px;font-weight:600;color:#15140F;">Have a specific goal in mind?</p>
    <p>Create a PiggyBox for a particular cause — a birthday, fundraiser, team outing, or any event where you want to track contributions toward a goal.</p>
    <a href="{{ route('money-boxes.create') }}" class="cta">Create a PiggyBox →</a>
  </div>

@elseif($step === 'onboarding_7d')
  <div class="hero">
    <div class="hero-tag">Get inspired</div>
    <h2>Ideas for your first PiggyBox</h2>
    <p>Hi {{ $firstName }}, not sure where to start? Here are some ways people use PiggyBox every day.</p>
  </div>
  <div class="body">
    <ul class="idea-list">
      <li><span class="idea-dot">→</span><span><strong style="color:#15140F;">Birthday fund</strong> — Let friends and family contribute toward a memorable gift instead of guessing what you want.</span></li>
      <li><span class="idea-dot">→</span><span><strong style="color:#15140F;">Team or office gift</strong> — Collect contributions from colleagues for a colleague's send-off, baby shower, or celebration.</span></li>
      <li><span class="idea-dot">→</span><span><strong style="color:#15140F;">Travel savings</strong> — Open a shared box for a group trip and track contributions from everyone going.</span></li>
      <li><span class="idea-dot">→</span><span><strong style="color:#15140F;">Charity or community drive</strong> — Raise funds for a local cause, school project, or community initiative.</span></li>
      <li><span class="idea-dot">→</span><span><strong style="color:#15140F;">Wedding or event fund</strong> — Give guests a simple, modern way to contribute toward your big day.</span></li>
    </ul>
    <p>Pick one that fits and set it up in minutes.</p>
    <a href="{{ route('money-boxes.create') }}" class="cta">Start your PiggyBox →</a>
    <div class="link-box">
      <div class="link-box-label">Your PiggyWallet link</div>
      <a href="{{ route('piggy.show', ['code' => $user->piggy_code]) }}">{{ route('piggy.show', ['code' => $user->piggy_code]) }}</a>
    </div>
  </div>

@elseif($step === 'onboarding_30d')
  <div class="hero">
    <div class="hero-tag">One month in</div>
    <h2>Let's put MyPiggyBox to work</h2>
    <p>Hi {{ $firstName }}, you've been with us for a month — and we'd love to help you make the most of your account.</p>
  </div>
  <div class="body">
    <p>MyPiggyBox gives you two ways to collect money from the people around you:</p>
    <div class="product-card">
      <div class="product-title">PiggyBox</div>
      <div class="product-desc">Create a collection page for any goal, event, or cause. Set a target, share your link, and watch contributions come in.</div>
    </div>
    <div class="product-card">
      <div class="product-title">PiggyWallet</div>
      <div class="product-desc">Your permanent personal link for receiving money directly — already active and waiting to be shared.</div>
    </div>
    <p style="margin-top:20px;">It only takes a couple of minutes to get started. One small step today could make a real difference.</p>
    <a href="{{ route('money-boxes.create') }}" class="cta">Create your PiggyBox today →</a>
    <div class="link-box">
      <div class="link-box-label">Your PiggyWallet link</div>
      <a href="{{ route('piggy.show', ['code' => $user->piggy_code]) }}">{{ route('piggy.show', ['code' => $user->piggy_code]) }}</a>
    </div>
  </div>

@elseif($step === 'onboarding_90d')
  <div class="hero">
    <div class="hero-tag">A note from us</div>
    <h2>Whenever you're ready, we're here</h2>
    <p>Hi {{ $firstName }}, we haven't heard from you in a while — and that's completely fine.</p>
  </div>
  <div class="body">
    <p>No pressure. Life gets busy. But your MyPiggyBox account is still here, and your PiggyWallet is still active and ready to receive contributions whenever you need it.</p>
    <p>If there's ever a goal you want to work toward, an event you're planning, or someone you want to help — we're just a click away.</p>
    <a href="{{ route('money-boxes.create') }}" class="cta">Get started →</a>
    <div class="link-box">
      <div class="link-box-label">Your PiggyWallet link</div>
      <a href="{{ route('piggy.show', ['code' => $user->piggy_code]) }}">{{ route('piggy.show', ['code' => $user->piggy_code]) }}</a>
    </div>
  </div>
@endif

  <div class="footer">
    <p>You're receiving this because you have an account on <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>. Your PiggyWallet is always active at <a href="{{ route('piggy.show', ['code' => $user->piggy_code]) }}">{{ route('piggy.show', ['code' => $user->piggy_code]) }}</a>.</p>
  </div>
</div>
</body>
</html>