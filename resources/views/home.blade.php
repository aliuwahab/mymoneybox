<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>{{ config('app.name') }} — Collect contributions, beautifully.</title>
    <style>
        :root {
            --bg:           #FAFAF7;
            --bg-2:         #F3F1EB;
            --panel:        #FFFFFF;
            --border:       #E6E3DC;
            --border-2:     #D9D6CE;
            --fg:           #15140F;
            --fg-2:         #5C5A54;
            --fg-3:         #908D83;
            --accent:       #1B6B4E;
            --accent-hover: #154F3A;
            --accent-soft:  #E6F1EB;
            --accent-deep:  #0E3C2C;
            --radius:       12px;
            --shadow-1:     0 1px 0 rgba(20,18,12,.04), 0 1px 2px rgba(20,18,12,.04);
            --shadow-2:     0 1px 0 rgba(20,18,12,.04), 0 12px 32px -10px rgba(20,18,12,.14);
            --shadow-pop:   0 28px 80px -20px rgba(20,18,12,.30), 0 6px 18px rgba(20,18,12,.08);
            --container:    1180px;
        }
        *, ::before, ::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; overflow-x: hidden; }
        body {
            background: var(--bg);
            color: var(--fg);
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            font-feature-settings: 'cv11', 'ss01', 'tnum';
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            font-size: 15px;
            line-height: 1.55;
        }
        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; cursor: pointer; }
        ::selection { background: var(--accent); color: #fff; }
        .serif { font-family: 'Instrument Serif', Georgia, serif; font-weight: 400; }
        .mono  { font-family: 'JetBrains Mono', ui-monospace, monospace; font-variant-numeric: tabular-nums; }
        .tnum  { font-variant-numeric: tabular-nums; }
        .wrap  { max-width: var(--container); margin: 0 auto; padding: 0 28px; }

        /* NAV */
        .nav {
            position: sticky; top: 0; z-index: 50;
            background: rgba(250,250,247,0.88);
            backdrop-filter: saturate(180%) blur(14px);
            -webkit-backdrop-filter: saturate(180%) blur(14px);
            border-bottom: 1px solid var(--border);
        }
        .nav-inner { display: flex; align-items: center; gap: 28px; height: 64px; }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 600; letter-spacing: -0.01em; }
        .brand-mark {
            width: 28px; height: 28px; border-radius: 7px;
            background: var(--fg); color: var(--bg);
            display: grid; place-items: center;
            font-weight: 700; font-size: 13px; letter-spacing: -0.02em;
        }
        .nav-links { display: flex; gap: 22px; margin-left: 16px; }
        .nav-links a { color: var(--fg-2); font-size: 14px; font-weight: 500; transition: color .15s; }
        .nav-links a:hover { color: var(--fg); }
        .nav-cta { margin-left: auto; display: flex; align-items: center; gap: 8px; }

        /* BUTTONS */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 16px; border-radius: 8px;
            font-size: 14px; font-weight: 500;
            border: 1px solid var(--border);
            background: var(--panel); color: var(--fg);
            box-shadow: var(--shadow-1);
            transition: background .14s, border-color .14s, transform .08s;
            cursor: pointer;
        }
        .btn:hover { background: #FBFAF6; border-color: var(--border-2); }
        .btn:active { transform: translateY(0.5px); }
        .btn.primary { background: var(--accent); color: #fff; border-color: var(--accent); }
        .btn.primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
        .btn.lg { padding: 12px 22px; font-size: 15px; border-radius: 9px; }
        .btn.ghost { background: transparent; border-color: transparent; box-shadow: none; }
        .btn.ghost:hover { background: rgba(0,0,0,0.04); }
        .btn svg { width: 16px; height: 16px; }
        .btn-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

        /* EYEBROW PILL */
        .eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 5px 11px 5px 8px;
            background: var(--panel); border: 1px solid var(--border);
            border-radius: 999px; font-size: 12px; font-weight: 500;
            color: var(--fg-2); box-shadow: var(--shadow-1);
        }
        .eyebrow .dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft);
        }

        /* HERO */
        .hero { padding: 80px 0 60px; position: relative; overflow: hidden; }
        .hero::before {
            content: ''; position: absolute; inset: -40px 0 0 0; pointer-events: none;
            background:
                radial-gradient(60% 50% at 50% 0%, rgba(27,107,78,0.06), transparent 70%),
                linear-gradient(to right, rgba(0,0,0,0.025) 1px, transparent 1px) 0 0 / 64px 64px,
                linear-gradient(to bottom, rgba(0,0,0,0.025) 1px, transparent 1px) 0 0 / 64px 64px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0.4) 60%, transparent 100%);
            -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0.4) 60%, transparent 100%);
        }
        .hero-grid { display: grid; grid-template-columns: 1.05fr 1fr; gap: 56px; align-items: center; position: relative; }
        .h1 {
            font-family: 'Instrument Serif', serif; font-weight: 400;
            font-size: clamp(48px, 6vw, 76px); line-height: 1.02;
            letter-spacing: -0.018em; margin: 20px 0 18px;
        }
        .h1 em { font-style: italic; color: var(--accent); }
        .lead { font-size: 18px; line-height: 1.55; color: var(--fg-2); max-width: 520px; margin: 0 0 28px; }
        .hero-cta { display: flex; gap: 10px; align-items: center; margin-bottom: 26px; }
        .hero-meta { display: flex; gap: 22px; align-items: center; color: var(--fg-3); font-size: 13px; }
        .hero-meta .check { display: inline-flex; align-items: center; gap: 6px; }
        .check-ic {
            width: 16px; height: 16px; border-radius: 50%;
            background: var(--accent-soft); color: var(--accent);
            display: grid; place-items: center;
        }

        /* HERO VISUAL */
        .visual { position: relative; height: 540px; }
        .v-card {
            position: absolute;
            background: var(--panel); border: 1px solid var(--border);
            border-radius: 16px; box-shadow: var(--shadow-pop); overflow: hidden;
        }
        .v-card.main { top: 30px; right: -10px; left: 60px; height: 480px; transform: rotate(0.5deg); }
        .v-card.float { top: -10px; left: -20px; width: 280px; height: 150px; transform: rotate(-3deg); z-index: 2; }
        .v-card.float-2 { bottom: -10px; right: 30px; width: 240px; height: 110px; transform: rotate(2deg); z-index: 2; }
        .vc-head { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--border); }
        .vc-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--border-2); }
        .vc-dot.r { background: #E67866; } .vc-dot.y { background: #E6B66A; } .vc-dot.g { background: #88B59A; }
        .vc-title { margin-left: 8px; font-size: 12px; color: var(--fg-3); font-weight: 500; }
        .vc-body { padding: 22px; }
        .vc-cover {
            height: 130px; border-radius: 10px;
            background: linear-gradient(135deg, #1B6B4E 0%, #2E8E6C 100%);
            display: grid; place-items: center;
            color: rgba(255,255,255,0.95);
            font-family: 'Instrument Serif', serif; font-size: 44px; letter-spacing: 0.02em;
            margin-bottom: 16px; position: relative; overflow: hidden;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
        }
        .vc-cover::after {
            content: 'Wedding · Public'; position: absolute; left: 12px; bottom: 10px;
            font-family: 'Inter', sans-serif; font-size: 10.5px;
            color: rgba(255,255,255,0.85); letter-spacing: 0.06em; text-transform: uppercase;
            z-index: 2;
        }
        .vc-cover-img {
            position: absolute; inset: 0; width: 100%; height: 100%;
            object-fit: cover; z-index: 0;
        }
        .vc-cover-img + .vc-cover-initials {
            position: absolute; right: 14px; top: 12px;
            width: 44px; height: 44px; border-radius: 10px;
            background: rgba(14,60,44,0.78);
            border: 1.5px solid rgba(232,199,122,0.9);
            color: #fff; font-family: 'Instrument Serif', serif; font-size: 20px;
            display: grid; place-items: center; z-index: 2;
            backdrop-filter: blur(4px);
        }
        .vc-cover-img ~ .vc-cover-shade {
            position: absolute; inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(14,60,44,0.55) 100%);
            z-index: 1;
        }
        .vc-h2 { font-size: 18px; font-weight: 600; letter-spacing: -0.01em; }
        .vc-sub { font-size: 12.5px; color: var(--fg-2); margin: 2px 0 14px; }
        .vc-row { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px; }
        .vc-amt { font-weight: 600; font-variant-numeric: tabular-nums; font-size: 15px; }
        .vc-amt .muted { color: var(--fg-3); font-weight: 400; }
        .progress { height: 6px; background: var(--bg-2); border-radius: 999px; overflow: hidden; }
        .progress > span { display: block; height: 100%; background: var(--accent); border-radius: 999px; }
        .vc-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: 18px; }
        .vc-stat-label { font-size: 11px; color: var(--fg-3); }
        .vc-stat-val { font-size: 15px; font-weight: 600; font-variant-numeric: tabular-nums; }
        .float-row { display: flex; align-items: center; gap: 10px; padding: 14px 16px; }
        .avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--accent); color: white;
            display: grid; place-items: center;
            font-size: 11.5px; font-weight: 600; flex: none;
        }
        .ping {
            width: 8px; height: 8px; border-radius: 50%; background: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-soft);
            animation: ping 1.6s infinite;
        }
        @keyframes ping {
            0%   { box-shadow: 0 0 0 0 rgba(27,107,78,.4); }
            70%  { box-shadow: 0 0 0 8px rgba(27,107,78,0); }
            100% { box-shadow: 0 0 0 0 rgba(27,107,78,0); }
        }

        /* LOGOS */
        .logos { padding: 50px 0 40px; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); background: var(--bg-2); }
        .logos-label { text-align: center; color: var(--fg-3); font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 28px; }
        .logos-row { display: grid; grid-template-columns: repeat(var(--trusted-cols, 6), minmax(0, 1fr)); gap: 20px 32px; align-items: center; justify-items: center; color: var(--fg-2); }
        .logo { font-family: 'Instrument Serif', serif; font-size: 22px; letter-spacing: 0; opacity: 0.6; transition: opacity .15s; text-align: center; overflow-wrap: anywhere; }
        .logo.alt { font-family: 'Inter'; font-weight: 600; letter-spacing: -0.02em; font-size: 18px; }
        .logo.mono { font-family: 'JetBrains Mono'; font-size: 14px; letter-spacing: 0.02em; font-weight: 500; }
        .logo:hover { opacity: 0.95; }

        /* SECTION SHELL */
        .section { padding: 100px 0; }
        .section-head { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: end; margin-bottom: 56px; }
        .section-head.center { grid-template-columns: 1fr; text-align: center; max-width: 720px; margin: 0 auto 56px; }
        .section-title { font-family: 'Instrument Serif', serif; font-size: clamp(36px, 4.4vw, 52px); line-height: 1.05; letter-spacing: -0.015em; margin: 12px 0 0; }
        .section-sub { color: var(--fg-2); font-size: 16px; line-height: 1.6; max-width: 480px; }
        .section-head.center .section-sub { margin: 16px auto 0; }
        .kicker { color: var(--accent); font-size: 13px; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase; }

        /* FEATURES BENTO */
        .feat-grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 20px; }
        .feat {
            background: var(--panel); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 28px;
            position: relative; overflow: hidden;
            min-height: 280px; display: flex; flex-direction: column;
        }
        .feat .ic {
            width: 40px; height: 40px; border-radius: 10px;
            background: var(--accent-soft); color: var(--accent);
            display: grid; place-items: center; margin-bottom: 18px;
        }
        .feat h3 { font-size: 19px; font-weight: 600; letter-spacing: -0.01em; margin: 0 0 8px; }
        .feat p { color: var(--fg-2); font-size: 14px; margin: 0; max-width: 340px; line-height: 1.6; }
        .feat-visual { margin-top: auto; padding-top: 22px; }
        .feat.f-wide { grid-column: span 7; }
        .feat.f-narrow { grid-column: span 5; }
        .feat.f-third { grid-column: span 4; }
        .vmock { background: var(--bg-2); border-radius: 8px; border: 1px solid var(--border); padding: 14px; }
        .mini-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid var(--border); }
        .mini-row:last-child { border-bottom: 0; }
        .mini-row .av { width: 24px; height: 24px; border-radius: 50%; background: var(--accent); color: #fff; font-size: 10px; font-weight: 600; display: grid; place-items: center; flex: none; }
        .mini-row .name { font-size: 13px; font-weight: 500; }
        .mini-row .meta { font-size: 11.5px; color: var(--fg-3); }
        .mini-row .amt { margin-left: auto; font-weight: 600; font-variant-numeric: tabular-nums; font-size: 13px; }
        .qr {
            width: 88px; height: 88px; border-radius: 8px;
            background:
                radial-gradient(circle at 12% 12%, var(--fg) 3px, transparent 4px) 0 0 / 12px 12px,
                radial-gradient(circle at 12% 12%, var(--fg) 3px, transparent 4px) 6px 6px / 12px 12px,
                var(--panel);
            border: 1px solid var(--border);
            position: relative; flex: none;
        }
        .qr::before, .qr::after { content: ''; position: absolute; width: 22px; height: 22px; border: 3px solid var(--fg); border-radius: 3px; background: var(--panel); }
        .qr::before { top: 4px; left: 4px; }
        .qr::after  { top: 4px; right: 4px; }
        .chips { display: flex; flex-wrap: wrap; gap: 6px; }
        .chip { font-size: 11.5px; font-weight: 500; padding: 4px 9px; border-radius: 999px; background: var(--bg-2); border: 1px solid var(--border); color: var(--fg-2); }
        .chip.on { background: var(--accent-soft); color: var(--accent); border-color: transparent; }
        .bars { display: flex; gap: 6px; align-items: flex-end; height: 80px; }
        .bars > i { flex: 1; display: block; background: var(--accent-soft); border-radius: 3px 3px 0 0; }
        .bars > i.peak { background: var(--accent); }

        /* HOW IT WORKS */
        .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .step { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); padding: 32px 28px; }
        .step-num { font-family: 'Instrument Serif', serif; font-size: 56px; line-height: 1; color: var(--accent); margin-bottom: 16px; }
        .step h4 { font-size: 18px; font-weight: 600; margin: 0 0 8px; letter-spacing: -0.005em; }
        .step p { color: var(--fg-2); font-size: 14px; margin: 0; line-height: 1.6; }

        /* QUOTE / METRICS */
        .quote-section { background: var(--accent-deep); color: #F5F1EA; }
        .quote-section .wrap { padding-top: 100px; padding-bottom: 100px; }
        .quote-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
        .quote-text { font-family: 'Instrument Serif', serif; font-size: clamp(28px, 3.4vw, 42px); line-height: 1.15; letter-spacing: -0.01em; }
        .quote-text em { font-style: italic; color: #B8E6CB; }
        .quote-by { display: flex; align-items: center; gap: 12px; margin-top: 32px; color: rgba(245,241,234,0.75); font-size: 13px; }
        .quote-by .avatar { background: #B8E6CB; color: var(--accent-deep); }
        .quote-by b { color: #fff; font-weight: 500; }
        .quote-aside { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .qstat { border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 22px; background: rgba(255,255,255,0.03); }
        .qstat .num { font-family: 'Instrument Serif', serif; font-size: 48px; letter-spacing: -0.02em; color: #fff; line-height: 1; font-variant-numeric: tabular-nums; }
        .qstat .lab { margin-top: 10px; font-size: 12.5px; color: rgba(245,241,234,0.7); line-height: 1.45; }

        /* PRICING */
        .pricing { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; align-items: stretch; max-width: 860px; margin: 0 auto; }
        .plan { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); padding: 32px 28px; display: flex; flex-direction: column; }
        .plan.featured { background: var(--fg); color: var(--bg); border-color: var(--fg); position: relative; box-shadow: var(--shadow-2); }
        .plan.featured p { color: rgba(255,255,255,0.7); }
        .plan.featured .price-num { color: #fff; }
        .plan.featured .ic-check { background: rgba(184,230,203,0.2); color: #B8E6CB; }
        .plan-name { font-size: 13px; font-weight: 500; letter-spacing: 0.08em; text-transform: uppercase; color: var(--accent); margin-bottom: 12px; }
        .plan.featured .plan-name { color: #B8E6CB; }
        .plan h3 { font-family: 'Instrument Serif', serif; font-size: 28px; font-weight: 400; margin: 0 0 6px; letter-spacing: -0.01em; }
        .plan p { color: var(--fg-2); margin: 0 0 22px; font-size: 14px; }
        .price { display: flex; align-items: baseline; gap: 6px; margin-bottom: 22px; }
        .price-num { font-family: 'Instrument Serif', serif; font-size: 52px; letter-spacing: -0.02em; line-height: 1; font-variant-numeric: tabular-nums; }
        .price-unit { font-size: 14px; color: var(--fg-2); }
        .plan ul { list-style: none; padding: 0; margin: 0 0 28px; display: flex; flex-direction: column; gap: 10px; }
        .plan li { display: flex; gap: 10px; align-items: flex-start; font-size: 14px; }
        .plan li .ic-check { width: 18px; height: 18px; border-radius: 50%; background: var(--accent-soft); color: var(--accent); display: grid; place-items: center; flex: none; margin-top: 1px; }
        .plan .label { font-size: 12px; color: var(--fg-3); margin-top: auto; }
        .plan.featured .label { color: rgba(255,255,255,0.45); }

        /* FAQ */
        .faq { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; }
        .faq-list { display: flex; flex-direction: column; }
        .faq-item { border-bottom: 1px solid var(--border); padding: 18px 0; }
        .faq-item summary { list-style: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 500; font-size: 15.5px; letter-spacing: -0.005em; }
        .faq-item summary::-webkit-details-marker { display: none; }
        .faq-item summary svg { transition: transform .2s; flex-shrink: 0; }
        .faq-item[open] summary svg { transform: rotate(45deg); }
        .faq-item p { color: var(--fg-2); font-size: 14.5px; line-height: 1.6; margin: 12px 0 0; max-width: 520px; }

        /* FINAL CTA */
        .final-cta { background: var(--bg-2); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
        .final-cta .wrap { padding-top: 90px; padding-bottom: 90px; text-align: center; }
        .final-cta h2 { font-family: 'Instrument Serif', serif; font-size: clamp(44px, 5.2vw, 68px); line-height: 1.05; letter-spacing: -0.018em; margin: 0 0 20px; }
        .final-cta h2 em { font-style: italic; color: var(--accent); }
        .final-cta p { color: var(--fg-2); font-size: 17px; max-width: 540px; margin: 0 auto 30px; }

        /* FOOTER */
        footer { padding: 64px 0 40px; color: var(--fg-2); }
        .foot-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 56px; }
        .foot-grid h6 { font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--fg-3); margin: 0 0 14px; }
        .foot-grid ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
        .foot-grid li a { font-size: 14px; color: var(--fg-2); transition: color .15s; }
        .foot-grid li a:hover { color: var(--fg); }
        .foot-tagline { font-family: 'Instrument Serif', serif; font-size: 20px; line-height: 1.35; max-width: 280px; margin: 14px 0 0; letter-spacing: -0.005em; color: var(--fg); }
        .foot-bot { display: flex; justify-content: space-between; align-items: center; padding-top: 28px; border-top: 1px solid var(--border); font-size: 12.5px; color: var(--fg-3); }
        .foot-bot .legal { display: flex; gap: 22px; }

        /* FEATURED CAMPAIGN */
        .featured-section {
            padding: 100px 0 90px;
            background:
                radial-gradient(60% 50% at 80% 0%, rgba(27,107,78,0.06), transparent 60%),
                radial-gradient(40% 40% at 10% 100%, rgba(184,129,13,0.05), transparent 60%),
                var(--bg-2);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        .featured-head { display: grid; grid-template-columns: 1fr auto; gap: 40px; align-items: end; margin-bottom: 48px; }
        .featured-head .section-title { margin-top: 12px; max-width: 720px; }
        .featured-head-meta { display: flex; align-items: center; gap: 18px; color: var(--fg-3); font-size: 13px; padding-bottom: 8px; }
        .featured-head-meta a { color: var(--fg); font-weight: 500; border-bottom: 1px solid var(--border-2); padding-bottom: 1px; }
        .featured-head-meta a:hover { color: var(--accent); border-bottom-color: var(--accent); }
        .eyebrow.live .dot { animation: live-pulse 1.8s ease-in-out infinite; }
        @keyframes live-pulse {
            0%, 100% { box-shadow: 0 0 0 3px var(--accent-soft); }
            50%       { box-shadow: 0 0 0 6px rgba(27,107,78,0.18); }
        }
        .featured { display: grid; grid-template-columns: 1.15fr 1fr; gap: 0; background: var(--panel); border: 1px solid var(--border); border-radius: 18px; overflow: hidden; box-shadow: var(--shadow-2); position: relative; }
        .featured::before { content: ''; position: absolute; left: 56.5%; top: 24px; bottom: 24px; width: 1px; background: var(--border); z-index: 1; pointer-events: none; }
        .featured-cover { position: relative; min-height: 540px; background: #0E3C2C; color: #F5F1EA; padding: 28px; display: flex; flex-direction: column; overflow: hidden; isolation: isolate; }
        .featured-cover .cover-illus { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
        .featured-cover .cover-illus svg { width: 100%; height: 100%; display: block; }
        .featured-cover .cover-photo {
            position: absolute; top: 80px; right: 36px; z-index: 1;
            width: 150px; height: 150px; border-radius: 14px;
            border: 2px solid #E8C77A;
            box-shadow: 0 18px 40px -10px rgba(0,0,0,0.55), 0 0 0 4px rgba(232,199,122,0.12);
            overflow: hidden; transform: rotate(2.5deg);
            background: #0E3C2C;
        }
        .featured-cover .cover-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
        @media (max-width: 980px) {
            .featured-cover .cover-photo { top: 60px; right: 20px; width: 110px; height: 110px; }
        }
        .featured-cover .cover-top { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; }
        .live-badge { display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: #F5F1EA; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.18); padding: 6px 11px 6px 9px; border-radius: 999px; backdrop-filter: blur(6px); }
        .live-badge .live-dot { width: 7px; height: 7px; border-radius: 50%; background: #FF6B5C; box-shadow: 0 0 0 3px rgba(255,107,92,0.25); animation: live-pulse-red 1.6s ease-in-out infinite; }
        @keyframes live-pulse-red {
            0%, 100% { box-shadow: 0 0 0 3px rgba(255,107,92,0.25); }
            50%       { box-shadow: 0 0 0 7px rgba(255,107,92,0.05); }
        }
        .cover-counter { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: rgba(245,241,234,0.7); font-variant-numeric: tabular-nums; }
        .featured-cover .cover-overlay { position: relative; z-index: 2; margin-top: auto; }
        .cover-eyebrow { font-size: 11px; font-weight: 500; letter-spacing: 0.18em; text-transform: uppercase; color: rgba(245,241,234,0.65); margin-bottom: 14px; }
        .cover-title { font-family: 'Instrument Serif', serif; font-weight: 400; font-size: clamp(40px, 5.4vw, 68px); line-height: 0.96; letter-spacing: -0.018em; color: #fff; margin: 0 0 18px; max-width: 92%; }
        .cover-title em { font-style: italic; color: #E8C77A; }
        .cover-byline { display: flex; align-items: center; gap: 10px; color: rgba(245,241,234,0.75); font-size: 13px; }
        .cover-byline b { color: #fff; font-weight: 500; }
        .featured-body { padding: 36px 40px 32px; display: flex; flex-direction: column; gap: 22px; position: relative; z-index: 1; }
        .featured-tags { display: flex; flex-wrap: wrap; gap: 6px; }
        .ftag { font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 999px; background: var(--accent-soft); color: var(--accent); }
        .ftag.muted { background: var(--bg-2); color: var(--fg-2); }
        .featured-story { color: var(--fg); font-size: 15.5px; line-height: 1.6; margin: 0; max-width: 460px; }
        .featured-story p { margin: 0 0 12px; }
        .featured-story p:last-child { margin: 0; color: var(--fg-2); font-size: 14.5px; }
        .featured-progress { background: var(--bg); border: 1px solid var(--border); border-radius: 12px; padding: 18px 18px 20px; }
        .fp-row { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 12px; }
        .fp-amount { font-family: 'Instrument Serif', serif; font-size: 36px; line-height: 1; letter-spacing: -0.01em; color: var(--fg); font-variant-numeric: tabular-nums; }
        .fp-goal { font-size: 13px; color: var(--fg-3); }
        .fp-goal b { color: var(--fg); font-weight: 500; }
        .fp-pct { font-size: 13px; font-weight: 600; color: var(--accent); font-variant-numeric: tabular-nums; }
        .fp-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border); }
        .fp-stat .num { font-weight: 600; font-size: 16px; font-variant-numeric: tabular-nums; letter-spacing: -0.005em; }
        .fp-stat .lab { font-size: 11.5px; color: var(--fg-3); margin-top: 2px; }
        .featured-cta { display: flex; gap: 10px; flex-wrap: wrap; }
        .featured-cta .btn { flex: 1; min-width: 0; justify-content: center; }
        .featured-recent { display: flex; align-items: center; gap: 14px; padding-top: 16px; border-top: 1px solid var(--border); margin-top: 4px; }
        .av-stack { display: flex; }
        .av-stack > div { width: 30px; height: 30px; border-radius: 50%; background: var(--accent); color: white; display: grid; place-items: center; font-size: 11px; font-weight: 600; border: 2px solid var(--panel); margin-left: -8px; flex: none; }
        .av-stack > div:first-child { margin-left: 0; }
        .av-stack > div.more { background: var(--bg-2); color: var(--fg-2); font-size: 10px; }
        .recent-text { font-size: 12.5px; color: var(--fg-2); }
        .recent-text b { color: var(--fg); font-weight: 500; }

        /* PLACEMENT CAMPAIGNS */
        .placements-section { padding: 0 0 100px; }
        .placements-head { display: grid; grid-template-columns: 1fr auto; gap: 40px; align-items: end; margin-bottom: 40px; }
        .placements-head .section-title { margin-top: 12px; max-width: 540px; }
        .placements-sub { color: var(--fg-2); font-size: 15px; max-width: 360px; padding-bottom: 6px; line-height: 1.55; }
        .placements-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .placement { background: var(--panel); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; transition: transform .25s cubic-bezier(.2,.7,.2,1), box-shadow .25s, border-color .2s; cursor: pointer; text-align: left; color: inherit; }
        .placement:hover { transform: translateY(-3px); box-shadow: var(--shadow-2); border-color: var(--border-2); }
        .placement:hover .pl-cover-illus { transform: scale(1.05); }
        .placement:hover .pl-arrow { background: var(--fg); color: var(--bg); transform: rotate(-45deg); }
        .pl-cover { position: relative; height: 180px; overflow: hidden; isolation: isolate; }
        .pl-cover-illus { position: absolute; inset: 0; transition: transform .8s cubic-bezier(.2,.7,.2,1); }
        .pl-cover-illus svg { width: 100%; height: 100%; display: block; }
        .pl-cover-overlay { position: absolute; left: 18px; right: 18px; bottom: 16px; display: flex; align-items: center; justify-content: space-between; z-index: 2; }
        .pl-cat { display: inline-flex; align-items: center; gap: 6px; font-size: 10.5px; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase; color: #fff; background: rgba(0,0,0,0.28); border: 1px solid rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 999px; backdrop-filter: blur(6px); }
        .pl-days { font-size: 11.5px; color: rgba(255,255,255,0.85); background: rgba(0,0,0,0.28); padding: 4px 9px; border-radius: 999px; backdrop-filter: blur(6px); font-variant-numeric: tabular-nums; }
        .pl-arrow { position: absolute; top: 14px; right: 14px; width: 30px; height: 30px; border-radius: 50%; background: rgba(255,255,255,0.95); color: var(--fg); display: grid; place-items: center; z-index: 3; transition: background .25s, color .25s, transform .25s; }
        .pl-body { padding: 20px 22px 22px; display: flex; flex-direction: column; gap: 12px; flex: 1; }
        .pl-title { font-family: 'Instrument Serif', serif; font-weight: 400; font-size: 24px; line-height: 1.1; letter-spacing: -0.01em; margin: 0; }
        .pl-story { color: var(--fg-2); font-size: 13.5px; line-height: 1.55; margin: 0; }
        .pl-progress { margin-top: auto; }
        .pl-progress .progress { margin-bottom: 8px; }
        .pl-meta { display: flex; align-items: baseline; justify-content: space-between; font-size: 12.5px; font-variant-numeric: tabular-nums; }
        .pl-meta .raised { font-weight: 600; color: var(--fg); }
        .pl-meta .right { color: var(--fg-3); }
        .pl-creator { display: flex; align-items: center; gap: 8px; padding-top: 10px; border-top: 1px solid var(--border); font-size: 12px; color: var(--fg-2); }
        .pl-creator b { color: var(--fg); font-weight: 500; }
        .placements-foot { display: flex; justify-content: center; margin-top: 40px; }
        .placements-foot .btn { padding-left: 22px; padding-right: 22px; }

        /* EVENTS SECTION */
        .events-section { padding: 0 0 100px; }
        .events-head { display: grid; grid-template-columns: 1fr auto; gap: 40px; align-items: end; margin-bottom: 40px; }
        .events-head .section-title { margin-top: 12px; max-width: 540px; }
        .events-sub { color: var(--fg-2); font-size: 15px; max-width: 360px; padding-bottom: 6px; line-height: 1.55; }
        .events-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }

        .ev-card { position: relative; border-radius: 14px; border: 1px solid var(--border); background: var(--panel); overflow: hidden; display: flex; flex-direction: column; cursor: pointer; transition: transform .25s cubic-bezier(.2,.7,.2,1), box-shadow .25s, border-color .2s; }
        .ev-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-2); border-color: var(--border-2); }
        .ev-cover { position: relative; height: 180px; overflow: hidden; isolation: isolate; }
        .ev-cover img { width: 100%; height: 100%; object-fit: cover; transition: transform .8s cubic-bezier(.2,.7,.2,1); display: block; }
        .ev-card:hover .ev-cover img { transform: scale(1.05); }
        .ev-cover-grad { position: absolute; inset: 0; }
        .ev-date-chip { position: absolute; top: 14px; left: 14px; background: #fff; border-radius: 8px; padding: 5px 9px; text-align: center; min-width: 42px; box-shadow: 0 2px 8px rgba(0,0,0,.18); z-index: 2; }
        .ev-date-chip .month { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #9C998F; line-height: 1; }
        .ev-date-chip .day { font-size: 18px; font-weight: 700; color: #15140F; line-height: 1.1; }
        .ev-body { padding: 18px 20px 20px; display: flex; flex-direction: column; gap: 10px; flex: 1; }
        .ev-title { font-family: 'Instrument Serif', serif; font-weight: 400; font-size: 21px; line-height: 1.15; letter-spacing: -.01em; margin: 0; }
        .ev-meta { display: flex; flex-direction: column; gap: 4px; }
        .ev-meta-row { display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--fg-3); }
        .ev-meta-row svg { flex-none; }
        .ev-price { margin-top: auto; display: flex; align-items: center; justify-content: space-between; padding-top: 12px; border-top: 1px solid var(--border); }
        .ev-price-from { font-size: 11px; color: var(--fg-3); margin-bottom: 1px; }
        .ev-price-amount { font-size: 16px; font-weight: 700; color: #15140F; }
        .ev-arrow { width: 30px; height: 30px; border-radius: 50%; background: var(--bg); border: 1px solid var(--border); display: grid; place-items: center; color: var(--fg); transition: background .2s, color .2s, transform .2s; }
        .ev-card:hover .ev-arrow { background: var(--fg); color: var(--bg); transform: rotate(-45deg); }

        /* Ticket hover overlay */
        .ev-ticket-overlay { position: absolute; inset: 0; display: flex; align-items: flex-end; padding: 10px; opacity: 0; transform: translateY(16px); transition: opacity .28s ease, transform .28s ease; pointer-events: none; z-index: 10; }
        .ev-card:hover .ev-ticket-overlay { opacity: 1; transform: translateY(0); pointer-events: auto; }
        .ev-ticket { background: #FAFAF7; border-radius: 10px; width: 100%; box-shadow: 0 8px 32px rgba(20,18,12,.28); overflow: hidden; }
        .ev-ticket-top { padding: 13px 16px 10px; }
        .ev-ticket-event { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #9C998F; margin-bottom: 3px; }
        .ev-ticket-name { font-family: 'Instrument Serif', serif; font-size: 17px; font-weight: 400; color: #15140F; line-height: 1.2; margin-bottom: 8px; }
        .ev-ticket-info { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
        .ev-ticket-field { display: flex; flex-direction: column; gap: 1px; }
        .ev-ticket-label { font-size: 9.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: #9C998F; }
        .ev-ticket-value { font-size: 12px; font-weight: 500; color: #15140F; }
        .ev-ticket-sep { height: 0; margin: 0 -16px; border-top: 1px dashed #D9D6CE; }
        .ev-ticket-sep::before, .ev-ticket-sep::after { display: none; }
        .ev-ticket-bottom { padding: 10px 16px 13px; display: flex; flex-direction: column; align-items: center; gap: 5px; }
        .ev-barcode { display: flex; align-items: flex-end; gap: 1.5px; height: 36px; }
        .ev-barcode span { background: #15140F; border-radius: 1px; }
        .ev-ticket-code { font-family: 'SF Mono', 'Fira Code', monospace; font-size: 10.5px; letter-spacing: .12em; color: #6B6862; }

        /* EVENTS HERO */
        .events-hero {
            padding: 80px 0 60px;
            background: var(--bg-2);
            border-top: 1px solid var(--border);
            position: relative; overflow: hidden;
        }
        .events-hero::before {
            content: ''; position: absolute; inset: -40px 0 0 0; pointer-events: none;
            background: radial-gradient(60% 50% at 50% 0%, rgba(180,83,9,0.055), transparent 70%);
        }
        .ev-em { font-style: italic; color: #D97706; }
        .ev-ping { background: #B45309; box-shadow: 0 0 0 4px rgba(180,83,9,0.18); animation: ping 1.6s infinite; }

        /* RESPONSIVE */
        @media (max-width: 980px) {
            .hero-grid, .section-head, .faq, .quote-grid { grid-template-columns: 1fr; gap: 32px; }
            .visual { height: 450px; }
            .v-card.main { left: 0; right: 0; top: 20px; height: auto; min-height: 410px; }
            .v-card.float, .v-card.float-2 { display: none; }
            .feat.f-wide, .feat.f-narrow, .feat.f-third { grid-column: span 12; }
            .steps, .pricing { grid-template-columns: 1fr; }
            .foot-grid { grid-template-columns: 1fr 1fr; }
            .logos-row { grid-template-columns: repeat(var(--trusted-tablet-cols, 3), minmax(0, 1fr)); }
            .featured { grid-template-columns: 1fr; }
            .featured::before { display: none; }
            .featured-cover { min-height: 340px; }
            .featured-head, .placements-head, .events-head { grid-template-columns: 1fr; }
            .placements-grid, .events-grid { grid-template-columns: 1fr; }
            .events-hero { padding: 60px 0 44px; }
        }
        @media (max-width: 640px) {
            body { font-size: 14px; }
            .nav-links { display: none; }
            .nav-inner { height: 58px; gap: 12px; }
            .brand { gap: 8px; min-width: 0; }
            .brand span:last-child { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .nav-cta { gap: 6px; }
            .nav-cta .btn.ghost { display: none; }
            .nav-cta .btn { padding: 7px 10px; font-size: 13px; white-space: nowrap; }
            .logos-row { grid-template-columns: repeat(var(--trusted-mobile-cols, 2), minmax(0, 1fr)); }
            .wrap { padding: 0 16px; }
            .hero { padding: 56px 0 44px; }
            .hero-grid { gap: 28px; }
            .h1 { font-size: clamp(42px, 14vw, 56px); }
            .lead { font-size: 16px; }
            .hero-cta { flex-direction: column; align-items: stretch; }
            .hero-cta .btn { justify-content: center; }
            .hero-meta { flex-direction: column; align-items: flex-start; gap: 10px; }
            .visual { height: 430px; }
            .v-card.main { min-height: 400px; }
            .vc-head { padding: 12px 14px; }
            .vc-title { font-size: 11px; overflow-wrap: anywhere; }
            .vc-body { padding: 18px; }
            .vc-cover { height: 118px; }
            .vc-stats { gap: 10px; margin-top: 16px; }
            .vc-stat-label { font-size: 10.5px; }
            .vc-stat-val { font-size: 14px; }
            .section { padding: 72px 0; }
            .section-head,
            .section-head.center { margin-bottom: 36px; }
            .feat { min-height: auto; padding: 22px; }
            .feat-visual[style] { flex-wrap: wrap; }
            .steps { gap: 14px; }
            .step { padding: 24px 22px; }
            .quote-aside { grid-template-columns: 1fr; }
            .quote-section .wrap { padding-top: 72px; padding-bottom: 72px; }
            .qstat .num { font-size: 40px; }
            .pricing { gap: 14px; }
            .plan { padding: 26px 22px; }
            .price { flex-wrap: wrap; }
            .price-num { font-size: 44px; }
            .faq { gap: 32px; }
            .faq-item summary { align-items: flex-start; gap: 12px; }
            .featured-section { padding: 72px 0; }
            .featured-body { padding: 26px 22px; }
            .featured-cover { min-height: 300px; padding: 22px; }
            .cover-title { font-size: clamp(36px, 12vw, 52px); max-width: 100%; }
            .fp-row { align-items: flex-start; gap: 10px; flex-wrap: wrap; }
            .fp-stats { grid-template-columns: 1fr; gap: 10px; }
            .featured-recent { align-items: flex-start; }
            .placements-section { padding-bottom: 72px; }
            .placement { cursor: pointer; }
            .events-section { padding-bottom: 72px; }
            .ev-ticket-overlay { display: none; }
            .events-hero { padding: 48px 0 36px; }
            .foot-grid { grid-template-columns: 1fr; gap: 28px; margin-bottom: 36px; }
            .foot-bot { flex-direction: column; align-items: flex-start; gap: 14px; }
            .foot-bot .legal { flex-wrap: wrap; gap: 14px; }
            footer { padding: 52px 0 32px; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
    <div class="wrap nav-inner">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark">M</span>
            <span>MyPiggyBox</span>
        </a>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#how">How it works</a>
            <a href="#cases">Use cases</a>
            <a href="{{ route('browse') }}">Campaigns</a>
            <a href="/events">Events</a>
            <a href="#pricing">Pricing</a>
            <a href="#faq">FAQ</a>
        </div>
        <div class="nav-cta">
            @auth
                <a class="btn ghost" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="btn primary" href="{{ route('money-boxes.create') }}">Create a PiggyBox</a>
            @else
                <a class="btn ghost" href="{{ route('login') }}">Sign in</a>
                <a class="btn primary" href="{{ route('register') }}">Get started</a>
            @endauth
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="wrap hero-grid">
        <div>
            <span class="eyebrow"><span class="dot"></span> New · QR contributions in 3 taps</span>
            <h1 class="h1">Collect for what matters, <em>beautifully</em>.</h1>
            <p class="lead">MyPiggyBox is the modern way to gather contributions for weddings, medical care, scholarships and team causes — with a link, a QR code, and zero awkwardness.</p>
            <div class="hero-cta">
                <a class="btn primary lg" href="{{ route('register') }}">
                    Start a PiggyBox — free
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
                <a class="btn lg" href="#how">See how it works</a>
            </div>
            <div class="hero-meta">
                <span class="check">
                    <span class="check-ic"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span>
                    Free to start
                </span>
                <span class="check">
                    <span class="check-ic"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span>
                    No card required
                </span>
                <span class="check">
                    <span class="check-ic"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span>
                    Mobile money + card
                </span>
            </div>
        </div>

        <!-- Product visual -->
        <div class="visual">
            <div class="v-card main">
                <div class="vc-head">
                    <span class="vc-dot r"></span><span class="vc-dot y"></span><span class="vc-dot g"></span>
                    <span class="vc-title">mypiggybox.com / kwame-adwoa-wedding</span>
                </div>
                <div class="vc-body">
                    <div class="vc-cover">
                        K&amp;A
                    </div>
                    <div class="vc-h2">Kwame &amp; Adwoa's Wedding Fund</div>
                    <div class="vc-sub">87 contributors · ends Jun 14</div>
                    <div class="vc-row">
                        <span class="vc-amt">₵18,420 <span class="muted">of ₵25,000</span></span>
                        <span class="vc-amt tnum">73%</span>
                    </div>
                    <div class="progress"><span style="width:73%"></span></div>
                    <div class="vc-stats">
                        <div><div class="vc-stat-label">Contributors</div><div class="vc-stat-val">87</div></div>
                        <div><div class="vc-stat-label">Avg. gift</div><div class="vc-stat-val">₵212</div></div>
                        <div><div class="vc-stat-label">Remaining</div><div class="vc-stat-val">₵6,580</div></div>
                    </div>
                </div>
            </div>

            <div class="v-card float">
                <div class="float-row">
                    <div class="avatar">YM</div>
                    <div style="flex:1">
                        <div style="font-size:13px; font-weight:500">Yaw Mensah just gave</div>
                        <div style="font-size:11.5px; color:var(--fg-3)">"All the best!" · 12 min ago</div>
                    </div>
                    <div style="font-weight:600; font-variant-numeric:tabular-nums">₵250</div>
                </div>
                <div style="height:1px; background:var(--border)"></div>
                <div class="float-row">
                    <span class="ping"></span>
                    <div style="font-size:12.5px; color:var(--fg-2)">Live · 3 contributions this hour</div>
                </div>
            </div>

            <div class="v-card float-2">
                <div class="float-row">
                    <div class="qr" style="width:60px; height:60px"></div>
                    <div>
                        <div style="font-size:12.5px; font-weight:500">Scan to contribute</div>
                        <div style="font-size:11px; color:var(--fg-3)">PNG · SVG · PDF</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- EVENTS HERO -->
<section class="events-hero">
    <div class="wrap hero-grid">
        <div>
            <span class="eyebrow"><span class="dot" style="background:#D97706;box-shadow:0 0 0 3px rgba(217,119,6,0.2)"></span> New · Sell tickets in minutes</span>
            <h1 class="h1" style="margin-top:20px">Host events people <em class="ev-em">actually attend</em>.</h1>
            <p class="lead">Create your event page, set ticket types, and go live in minutes. Attendees pay by mobile money or card and get their ticket instantly — no app download needed.</p>
            <div class="hero-cta">
                @auth
                    <a class="btn primary lg" href="{{ route('events.create') }}" style="background:#B45309;border-color:#B45309">
                        Create an event — free
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                @else
                    <a class="btn primary lg" href="{{ route('register') }}" style="background:#B45309;border-color:#B45309">
                        Create an event — free
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                @endauth
                <a class="btn lg" href="/events">Browse events</a>
            </div>
            <div class="hero-meta">
                <span class="check">
                    <span class="check-ic" style="background:rgba(180,83,9,0.1);color:#B45309"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span>
                    Free to create
                </span>
                <span class="check">
                    <span class="check-ic" style="background:rgba(180,83,9,0.1);color:#B45309"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span>
                    Instant ticket delivery
                </span>
                <span class="check">
                    <span class="check-ic" style="background:rgba(180,83,9,0.1);color:#B45309"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span>
                    Mobile money + card
                </span>
            </div>
        </div>

        <!-- Events visual -->
        <div class="visual">
            <div class="v-card main">
                <div class="vc-head">
                    <span class="vc-dot r"></span><span class="vc-dot y"></span><span class="vc-dot g"></span>
                    <span class="vc-title">mypiggybox.com / liberated-men-conference-2026</span>
                </div>
                <div class="vc-body">
                    <div class="vc-cover" style="background:linear-gradient(135deg,#78350F 0%,#B45309 55%,#1B6B4E 100%)">
                        <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" style="position:relative;z-index:1"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2"/><path d="M12 7V5a2 2 0 0 0-4 0v2"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="16" x2="12" y2="16"/></svg>
                    </div>
                    <div class="vc-h2">Liberated Men Conference 2026</div>
                    <div class="vc-sub">AICC, Accra · Sat 28 Jun, 9:00am</div>
                    <div class="vc-row">
                        <span class="vc-amt">248 <span class="muted">of 500 tickets sold</span></span>
                        <span class="vc-amt tnum">49%</span>
                    </div>
                    <div class="progress"><span style="width:49%;background:#B45309"></span></div>
                    <div class="vc-stats">
                        <div><div class="vc-stat-label">Tickets sold</div><div class="vc-stat-val">248</div></div>
                        <div><div class="vc-stat-label">Revenue</div><div class="vc-stat-val">₵7,440</div></div>
                        <div><div class="vc-stat-label">Remaining</div><div class="vc-stat-val">252</div></div>
                    </div>
                </div>
            </div>

            <div class="v-card float">
                <div class="float-row">
                    <div class="avatar" style="background:#B45309">KA</div>
                    <div style="flex:1">
                        <div style="font-size:13px;font-weight:500">Kwame just bought 2 tickets</div>
                        <div style="font-size:11.5px;color:var(--fg-3)">General Admission · just now</div>
                    </div>
                    <div style="font-weight:600;font-variant-numeric:tabular-nums">₵400</div>
                </div>
                <div style="height:1px;background:var(--border)"></div>
                <div class="float-row">
                    <span class="ping ev-ping"></span>
                    <div style="font-size:12.5px;color:var(--fg-2)">Live · 12 tickets sold this hour</div>
                </div>
            </div>

            <div class="v-card float-2">
                <div class="float-row">
                    <div style="width:44px;height:44px;border-radius:8px;background:linear-gradient(135deg,#78350F,#B45309);flex:none;display:grid;place-items:center">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2z"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <div>
                        <div style="font-size:12.5px;font-weight:500">Ticket confirmed</div>
                        <div style="font-size:11px;color:var(--fg-3);font-family:'JetBrains Mono',monospace;letter-spacing:.06em">TKT-A7F2-9C1E</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $trustedLogos = $trustedLogos ?? collect();
@endphp

@if($trustedLogos->isNotEmpty())
    <!-- TRUST LOGOS -->
    <section class="logos">
        <div class="wrap">
            <div class="logos-label">Trusted by communities, families, and teams across West Africa</div>
            <div class="logos-row" style="--trusted-cols: {{ min(max($trustedLogos->count(), 1), 6) }}; --trusted-tablet-cols: {{ min(max($trustedLogos->count(), 1), 3) }}; --trusted-mobile-cols: {{ min(max($trustedLogos->count(), 1), 2) }};">
                @foreach($trustedLogos as $trustedLogo)
                    <span class="logo">{{ $trustedLogo->name }}</span>
                @endforeach
            </div>
        </div>
    </section>
@endif

<!-- FEATURED CAMPAIGN -->
<livewire:featured-campaign />

<!-- FEATURES -->
<section class="section" id="features">
    <div class="wrap">
        <div class="section-head">
            <div>
                <span class="kicker">Why MyPiggyBox</span>
                <h2 class="section-title">Built for clarity. Designed for trust.</h2>
            </div>
            <p class="section-sub">Every detail — from contribution rules to receipts — is engineered to remove friction so giving feels effortless on both sides.</p>
        </div>

        <div class="feat-grid">
            <!-- Wide: rules -->
            <div class="feat f-wide">
                <div class="ic">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9Z"/><path d="M3 7.5 12 12l9-4.5"/><path d="M12 12v9"/></svg>
                </div>
                <h3>One PiggyBox, every kind of giving</h3>
                <p>Fixed amounts, ranges, anonymous gifts, deadlines — set the rules once and your contributors get a frictionless flow that adapts to your goal.</p>
                <div class="feat-visual">
                    <div class="vmock">
                        <div class="chips" style="margin-bottom:12px">
                            <span class="chip on">Variable amount</span>
                            <span class="chip">Range</span>
                            <span class="chip">Fixed</span>
                            <span class="chip">Minimum</span>
                        </div>
                        <div class="chips">
                            <span class="chip">Anonymous allowed</span>
                            <span class="chip on">Contributor's choice</span>
                            <span class="chip">Require name</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Narrow: share -->
            <div class="feat f-narrow">
                <div class="ic">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3"/><path d="M21 14v3"/><path d="M14 21h7"/></svg>
                </div>
                <h3>Share anywhere instantly</h3>
                <p>Auto-generated link, QR code, and WhatsApp-ready post for every PiggyBox. Print it, scan it, send it.</p>
                <div class="feat-visual" style="display:flex; gap:14px; align-items:center">
                    <div class="qr"></div>
                    <div>
                        <div class="chips" style="margin-bottom:8px"><span class="chip on">WhatsApp</span><span class="chip on">Link</span></div>
                        <div class="chips"><span class="chip">Twitter</span><span class="chip">Email</span><span class="chip">PDF</span></div>
                    </div>
                </div>
            </div>

            <!-- Third: contributors -->
            <div class="feat f-third">
                <div class="ic">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7"/><path d="M21.5 20c0-3-1.7-5.2-4.5-5.8"/></svg>
                </div>
                <h3>Know every giver</h3>
                <p>A clean ledger of every contribution — name, message, method, amount. Export to CSV anytime.</p>
                <div class="feat-visual">
                    <div class="vmock">
                        <div class="mini-row"><div class="av">YM</div><div><div class="name">Yaw Mensah</div><div class="meta">MTN MoMo</div></div><div class="amt">₵250</div></div>
                        <div class="mini-row"><div class="av" style="background:var(--bg-2);color:var(--fg-3)">·</div><div><div class="name">Anonymous</div><div class="meta">Card</div></div><div class="amt">₵50</div></div>
                        <div class="mini-row"><div class="av">EA</div><div><div class="name">Esi Asante</div><div class="meta">Vodafone</div></div><div class="amt">₵500</div></div>
                    </div>
                </div>
            </div>

            <!-- Third: payments -->
            <div class="feat f-third">
                <div class="ic">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><circle cx="17" cy="14.5" r="1"/></svg>
                </div>
                <h3>Local payments, no surprises</h3>
                <p>MTN MoMo, Vodafone Cash, AirtelTigo, cards. Payouts to your wallet, receipts to contributors.</p>
                <div class="feat-visual">
                    <div class="chips">
                        <span class="chip on">MTN MoMo</span>
                        <span class="chip on">Vodafone Cash</span>
                        <span class="chip on">AirtelTigo</span>
                        <span class="chip on">Visa</span>
                        <span class="chip on">Mastercard</span>
                        <span class="chip">Bank transfer</span>
                    </div>
                </div>
            </div>

            <!-- Third: analytics -->
            <div class="feat f-third">
                <div class="ic">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V8"/><path d="M10 20V4"/><path d="M16 20v-8"/><path d="M22 20H2"/></svg>
                </div>
                <h3>Real-time analytics</h3>
                <p>Track progress, top contributors, and where shares convert best — clear charts, no clutter.</p>
                <div class="feat-visual">
                    <div class="vmock">
                        <div class="bars">
                            <i style="height:30%"></i><i style="height:48%"></i><i style="height:38%"></i>
                            <i style="height:62%"></i><i style="height:78%"></i><i style="height:54%"></i>
                            <i style="height:68%" class="peak"></i><i style="height:88%" class="peak"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section" id="how" style="background:var(--bg-2); border-top:1px solid var(--border); border-bottom:1px solid var(--border)">
    <div class="wrap">
        <div class="section-head center">
            <span class="kicker">How it works</span>
            <h2 class="section-title">From idea to first contribution in under a minute.</h2>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-num">01</div>
                <h4>Create your PiggyBox</h4>
                <p>Name it, set a goal (or don't), choose how contributors give — fixed, range, or any amount.</p>
            </div>
            <div class="step">
                <div class="step-num">02</div>
                <h4>Share the link or QR</h4>
                <p>Drop the auto-generated link in WhatsApp, print the QR on invitations, or post it anywhere.</p>
            </div>
            <div class="step">
                <div class="step-num">03</div>
                <h4>Watch it fill up</h4>
                <p>Contributions land in real time. Withdraw to mobile money or your bank whenever you want.</p>
            </div>
        </div>
    </div>
</section>

<x-use-cases-bento />

<!-- QUOTE / METRICS -->
<section class="quote-section">
    <div class="wrap">
        <div class="quote-grid">
            <div>
                <span class="kicker" style="color:#B8E6CB">In their words</span>
                <div class="quote-text" style="margin-top:18px">
                    "We raised <em>₵42,000</em> for our wedding in three weeks. No awkward bank details, no chasing — just a QR on the invitation and a link in the family group chat."
                </div>
                <div class="quote-by">
                    <div class="avatar">EA</div>
                    <div>
                        <b>Esi &amp; Adjei</b>
                        <div>Married February 2026 · Kumasi, Ghana</div>
                    </div>
                </div>
            </div>
            <div class="quote-aside">
                <div class="qstat"><div class="num">₵4.2M</div><div class="lab">Contributed to date across all MyPiggyBox communities</div></div>
                <div class="qstat"><div class="num">12k+</div><div class="lab">Active PiggyBoxes — weddings, medical, education, more</div></div>
                <div class="qstat"><div class="num">2.4×</div><div class="lab">More raised vs. ad-hoc bank transfers, on average</div></div>
                <div class="qstat"><div class="num">48s</div><div class="lab">Median time to set up your first PiggyBox from sign-up</div></div>
            </div>
        </div>
    </div>
</section>

<!-- PRICING -->
<section class="section" id="pricing">
    <div class="wrap">
        <div class="section-head center">
            <span class="kicker">Pricing</span>
            <h2 class="section-title">Simple, transparent, and free to start.</h2>
            <p class="section-sub">No subscription. A small platform fee on successful contributions covers payment processing and infrastructure. You only pay when you raise.</p>
        </div>
        <div class="pricing">
            <div class="plan">
                <div class="plan-name">Personal</div>
                <h3>Starter</h3>
                <p>For one-off events and personal causes.</p>
                <div class="price"><span class="price-num">1.5%</span><span class="price-unit">per contribution</span></div>
                <ul>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> Unlimited PiggyBoxes</li>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> Mobile money + card payments</li>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> QR code, share kit</li>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> CSV export</li>
                </ul>
                <a class="btn" style="justify-content:center; width:100%" href="{{ route('register') }}">Get started</a>
                <div class="label" style="margin-top:12px">No monthly fee</div>
            </div>

            <div class="plan featured">
                <div class="plan-name">Custom</div>
                <h3>Community &amp; organisations</h3>
                <p>For organisations, congregations, NGOs, and recurring causes that need custom setup.</p>
                <div class="price"><span class="price-num">Custom</span></div>
                <ul>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> Everything in Starter</li>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> Custom domain &amp; branding</li>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> Multi-admin teams</li>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> Advanced analytics</li>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> Embeddable website widget</li>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> Priority support</li>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> Volume pricing</li>
                    <li><span class="ic-check"><svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span> SSO, API, and custom integrations</li>
                </ul>
                <a class="btn primary" style="justify-content:center; width:100%; background:#fff; color:var(--fg); border-color:#fff" href="{{ route('contact') }}">Talk to sales</a>
                <div class="label" style="margin-top:12px">Tailored pricing and SLAs available</div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section" id="faq" style="padding-top:0">
    <div class="wrap">
        <div class="faq">
            <div>
                <span class="kicker">Common questions</span>
                <h2 class="section-title" style="margin-top:12px">Everything you'd want to ask, before signing up.</h2>
                <p class="section-sub" style="margin-top:18px">Still curious? Reach our team at <a style="color:var(--accent); font-weight:500" href="{{ route('contact') }}">support@mypiggybox.com</a> — we usually reply quickly.</p>
            </div>
            <div class="faq-list">
                <details class="faq-item" open>
                    <summary>How quickly do funds land in my account?
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    </summary>
                    <p>Mobile money contributions settle in your wallet within minutes. Card payments are batched and paid out on the next business day. You can withdraw at any time.</p>
                </details>
                <details class="faq-item">
                    <summary>Can contributors give anonymously?
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    </summary>
                    <p>Yes. Each PiggyBox can require names, allow anonymity, or leave it to the contributor's choice. Anonymous gifts still appear in your ledger with amount and method, just no identifying info.</p>
                </details>
                <details class="faq-item">
                    <summary>What if I don't reach my goal?
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    </summary>
                    <p>You keep every contribution. Goals are guideposts, not commitments — there's no penalty for raising less than you hoped.</p>
                </details>
                <details class="faq-item">
                    <summary>Is it secure? How is my data handled?
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    </summary>
                    <p>Payments are processed by PCI-DSS Level 1 providers. We never store card numbers. Two-factor authentication is available for every account, and all data is encrypted in transit and at rest.</p>
                </details>
                <details class="faq-item">
                    <summary>Do you charge a monthly subscription?
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    </summary>
                    <p>No. Personal is entirely usage-based, so you only pay a small fee per successful contribution. Custom plans are tailored for teams and organisations that need advanced setup.</p>
                </details>
            </div>
        </div>
    </div>
</section>

<!-- PLACEMENT CAMPAIGNS -->
<livewire:placement-campaigns />

<!-- EVENTS -->
@if(isset($featuredEvents) && $featuredEvents->count() > 0)
<section class="events-section">
    <div class="wrap">
        <div class="events-head">
            <div>
                <span class="kicker">Upcoming Events</span>
                <h2 class="section-title">Don't miss what's happening near you.</h2>
                <p class="events-sub">Discover live events, buy tickets, and attend in person — all through MyPiggyBox.</p>
            </div>
            <div>
                <a href="/events" class="btn">View all events
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <div class="events-grid">
            @foreach($featuredEvents as $event)
            @php
                $coverUrl = $event->getCoverImageUrl();
                $eventDate = $event->event_date instanceof \Carbon\Carbon
                    ? $event->event_date
                    : \Carbon\Carbon::parse($event->event_date);
                $minPrice = $event->ticketTypes->min('price');
                $displayCode = strtoupper(substr(md5($event->slug), 0, 4) . '-' . substr(md5($event->slug), 4, 4) . '-' . substr(md5($event->slug), 8, 4));
                $barsHtml = '';
                $slugLen = strlen($event->slug);
                for ($bi = 0; $bi < 32; $bi++) {
                    $char = ord($event->slug[$bi % $slugLen]);
                    $w = ($char % 3) + 1;
                    $h = 18 + ($bi % 5 === 0 ? 14 : ($bi % 3 === 0 ? 8 : ($bi % 2 === 0 ? 4 : 0)));
                    $barsHtml .= "<span style=\"width:{$w}px;height:{$h}px\"></span>";
                }
            @endphp
            <a href="/events/{{ $event->slug }}" class="ev-card" style="text-decoration:none;color:inherit">
                {{-- Cover --}}
                <div class="ev-cover">
                    @if($coverUrl)
                        <img src="{{ $coverUrl }}" alt="{{ $event->title }}" loading="lazy">
                    @else
                        <div style="position:absolute;inset:0;background:linear-gradient(135deg,#78350F 0%,#B45309 55%,#1B6B4E 100%)"></div>
                    @endif
                    <div class="ev-cover-grad" style="background:linear-gradient(to top,rgba(15,14,8,.68) 0%,transparent 60%)"></div>
                    <div class="ev-date-chip">
                        <div class="month">{{ $eventDate->format('M') }}</div>
                        <div class="day">{{ $eventDate->format('d') }}</div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="ev-body">
                    <h3 class="ev-title">{{ $event->title }}</h3>
                    <div class="ev-meta">
                        <div class="ev-meta-row">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                            {{ $event->venue ?? 'Venue TBA' }}
                        </div>
                        <div class="ev-meta-row">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $eventDate->format('D, d M Y') }} &middot; {{ $eventDate->format('g:ia') }}
                        </div>
                    </div>
                    <div class="ev-price">
                        <div>
                            <div class="ev-price-from">From</div>
                            <div class="ev-price-amount">
                                @if($minPrice && $minPrice > 0)
                                    ₵{{ number_format($minPrice, 2) }}
                                @else
                                    Free
                                @endif
                            </div>
                        </div>
                        <div class="ev-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Ticket hover overlay --}}
                <div class="ev-ticket-overlay">
                    <div class="ev-ticket">
                        <div class="ev-ticket-top">
                            <div class="ev-ticket-event">Event Ticket</div>
                            <div class="ev-ticket-name">{{ Str::limit($event->title, 38) }}</div>
                            <div class="ev-ticket-info">
                                <div class="ev-ticket-field">
                                    <div class="ev-ticket-label">Date</div>
                                    <div class="ev-ticket-value">{{ $eventDate->format('d M Y') }}</div>
                                </div>
                                <div class="ev-ticket-field">
                                    <div class="ev-ticket-label">Time</div>
                                    <div class="ev-ticket-value">{{ $eventDate->format('g:i A') }}</div>
                                </div>
                                <div class="ev-ticket-field">
                                    <div class="ev-ticket-label">Venue</div>
                                    <div class="ev-ticket-value">{{ Str::limit($event->venue ?? 'TBA', 18) }}</div>
                                </div>
                                <div class="ev-ticket-field">
                                    <div class="ev-ticket-label">Price</div>
                                    <div class="ev-ticket-value">@if($minPrice && $minPrice > 0)₵{{ number_format($minPrice, 2) }}@else Free @endif</div>
                                </div>
                            </div>
                        </div>
                        <div class="ev-ticket-sep"></div>
                        <div class="ev-ticket-bottom">
                            <div class="ev-barcode">{!! $barsHtml !!}</div>
                            <div class="ev-ticket-code">{{ $displayCode }}</div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="placements-foot">
            <a href="/events" class="btn">Browse all events
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>
@endif

<!-- FINAL CTA -->
<section class="final-cta">
    <div class="wrap">
        <h2>Start your first PiggyBox <em>today.</em></h2>
        <p>It takes about a minute. No credit card, no commitment. Just a clean link, a QR code, and somewhere for the love to land.</p>
        <div class="btn-row" style="justify-content:center">
            <a class="btn primary lg" href="{{ route('register') }}">
                Create a PiggyBox
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
            <a class="btn lg" href="{{ route('dashboard') }}">Go to dashboard</a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="wrap">
        <div class="foot-grid">
            <div>
                <a href="{{ route('home') }}" class="brand"><span class="brand-mark">M</span><span>MyPiggyBox</span></a>
                <p class="foot-tagline">Collect for what matters — beautifully, transparently, together.</p>
            </div>
            <div>
                <h6>Product</h6>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how">How it works</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                </ul>
            </div>
            <div>
                <h6>Use cases</h6>
                <ul>
                    <li><a href="#cases">Weddings</a></li>
                    <li><a href="#cases">Medical</a></li>
                    <li><a href="#cases">Scholarships</a></li>
                    <li><a href="#cases">Community</a></li>
                </ul>
            </div>
            <div>
                <h6>Company</h6>
                <ul>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('careers') }}">Careers</a></li>
                    <li><a href="{{ route('press') }}">Press</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <div>
                <h6>Legal</h6>
                <ul>
                    <li><a href="{{ route('privacy') }}">Privacy</a></li>
                    <li><a href="{{ route('terms') }}">Terms</a></li>
                    <li><a href="{{ route('security') }}">Security</a></li>
                    <li><a href="{{ route('status') }}">Status</a></li>
                </ul>
            </div>
        </div>
        <div class="foot-bot">
            <div>&copy; {{ date('Y') }} {{ config('app.name') }}. Made with care in Accra.</div>
            <div class="legal">
                <a href="{{ route('privacy') }}">Privacy</a>
                <a href="{{ route('terms') }}">Terms</a>
                <a href="{{ route('cookies') }}">Cookies</a>
            </div>
        </div>
    </div>
</footer>

@vite(['resources/js/app.js'])
</body>
</html>
