<x-layouts.guest>
    <div class="wallet-lookup">
        <style>
            .wallet-lookup {
                --bg: #FAFAF7;
                --panel: #FFFFFF;
                --border: #E6E3DC;
                --border-2: #D9D6CE;
                --sidebar-2: #ECEAE3;
                --fg: #15140F;
                --fg-2: #6B6862;
                --fg-3: #9C998F;
                --accent: #1B6B4E;
                --accent-hover: #154F3A;
                --accent-soft: #E6F1EB;
                --info: #2F6FA8;
                --info-soft: #E6EFF7;
                --danger: #B5311E;
                --danger-soft: #FBEAE6;
                --radius: 10px;
                --radius-sm: 6px;
                --shadow-1: 0 1px 0 rgba(20,18,12,.04), 0 1px 2px rgba(20,18,12,.04);
                background: var(--bg);
                color: var(--fg);
                min-height: calc(100vh - 56px);
                padding: 36px 18px 72px;
                font-size: 14px;
                line-height: 1.5;
                overflow-x: hidden;
            }

            .wallet-lookup * { box-sizing: border-box; }
            .wallet-lookup input,
            .wallet-lookup button { font: inherit; color: inherit; }

            .wallet-lookup .wrap {
                max-width: 1120px;
                margin: 0 auto;
            }

            .wallet-lookup .page-head {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 24px;
                margin-bottom: 24px;
            }

            .wallet-lookup .page-title {
                font-family: "Instrument Serif", Georgia, serif;
                font-size: 38px;
                line-height: 1.05;
                letter-spacing: 0;
                margin: 0;
                font-weight: 400;
            }

            .wallet-lookup .page-sub {
                color: var(--fg-2);
                font-size: 13.5px;
                margin-top: 6px;
            }

            .wallet-lookup .pub-shell {
                background: #F7F5EF;
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 28px;
                display: grid;
                grid-template-columns: minmax(0, 1.1fr) minmax(300px, .9fr);
                gap: 24px;
                align-items: stretch;
                min-width: 0;
            }

            .wallet-lookup .card {
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                box-shadow: var(--shadow-1);
            }

            .wallet-lookup .card-body { padding: 18px; }
            .wallet-lookup .row { display: flex; align-items: center; gap: 10px; }
            .wallet-lookup .col { display: flex; flex-direction: column; gap: 10px; }
            .wallet-lookup .muted { color: var(--fg-2); }
            .wallet-lookup .tiny { font-size: 11.5px; color: var(--fg-3); }
            .wallet-lookup .mono {
                font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, monospace;
                font-variant-numeric: tabular-nums;
            }

            .wallet-lookup .pill {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-size: 11.5px;
                font-weight: 500;
                padding: 2px 8px;
                border-radius: 999px;
                background: var(--info-soft);
                color: var(--info);
                width: fit-content;
            }

            .wallet-lookup .pill .dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: currentColor;
            }

            .wallet-lookup .field {
                display: grid;
                gap: 6px;
            }

            .wallet-lookup .label {
                font-size: 12.5px;
                color: var(--fg-2);
                font-weight: 500;
            }

            .wallet-lookup .input {
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: var(--radius-sm);
                padding: 12px 14px;
                font-size: 22px;
                font-weight: 700;
                letter-spacing: .14em;
                text-align: center;
                text-transform: uppercase;
                color: var(--fg);
                width: 100%;
                transition: border-color .12s, box-shadow .12s;
            }

            .wallet-lookup .input:focus {
                outline: 0;
                border-color: var(--accent);
                box-shadow: 0 0 0 3px var(--accent-soft);
            }

            .wallet-lookup .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                min-height: 38px;
                padding: 8px 14px;
                border-radius: var(--radius-sm);
                font-size: 13px;
                font-weight: 500;
                border: 1px solid var(--border);
                background: var(--panel);
                color: var(--fg);
                box-shadow: var(--shadow-1);
                transition: background .12s, border-color .12s, transform .08s;
                text-decoration: none;
            }

            .wallet-lookup .btn:hover { background: #FBFAF6; border-color: var(--border-2); }
            .wallet-lookup .btn:active { transform: translateY(.5px); }
            .wallet-lookup .btn.primary {
                background: var(--accent);
                color: #fff;
                border-color: var(--accent);
            }
            .wallet-lookup .btn.primary:hover {
                background: var(--accent-hover);
                border-color: var(--accent-hover);
            }

            .wallet-lookup .wallet-hero {
                min-height: 100%;
                border-radius: 10px;
                background: linear-gradient(135deg, #1B6B4E 0%, #2E8E6C 100%);
                color: rgba(255,255,255,.95);
                position: relative;
                overflow: hidden;
                padding: 22px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                isolation: isolate;
            }

            .wallet-lookup .wallet-hero::before {
                content: "";
                position: absolute;
                inset: 0;
                background:
                    linear-gradient(to right, rgba(255,255,255,.08) 1px, transparent 1px) 0 0 / 34px 34px,
                    linear-gradient(to bottom, rgba(255,255,255,.08) 1px, transparent 1px) 0 0 / 34px 34px;
                opacity: .45;
                z-index: -1;
            }

            .wallet-lookup .wallet-mark {
                font-family: "Instrument Serif", Georgia, serif;
                font-size: 64px;
                line-height: 1;
                margin: 54px 0;
                text-align: center;
            }

            .wallet-lookup .code-chip {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                width: fit-content;
                border: 1px solid rgba(255,255,255,.24);
                background: rgba(255,255,255,.09);
                border-radius: 999px;
                padding: 5px 10px;
                color: rgba(255,255,255,.78);
                font-size: 11px;
                letter-spacing: .08em;
                text-transform: uppercase;
                backdrop-filter: blur(6px);
            }

            .wallet-lookup .error-box {
                background: var(--danger-soft);
                border: 1px solid rgba(181,49,30,.22);
                color: var(--danger);
                border-radius: var(--radius-sm);
                padding: 10px 12px;
                font-size: 13px;
            }

            @media (max-width: 900px) {
                .wallet-lookup .pub-shell { grid-template-columns: 1fr; }
            }

            @media (max-width: 640px) {
                .wallet-lookup { padding: 24px 12px 56px; }
                .wallet-lookup .pub-shell { padding: 16px; }
                .wallet-lookup .page-head {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 14px;
                }
                .wallet-lookup .page-head .btn { width: 100%; }
                .wallet-lookup .page-title { font-size: 32px; }
                .wallet-lookup .input { font-size: 18px; letter-spacing: .1em; }
                .wallet-lookup .wallet-hero { min-height: 260px; }
                .wallet-lookup .wallet-mark { margin: 36px 0; }
            }
        </style>

        <div class="wrap">
            <div class="page-head">
                <div>
                    <h1 class="page-title">Find a Piggy Wallet</h1>
                    <div class="page-sub">Enter a Piggy Wallet code and send a gift in a few taps.</div>
                </div>
                <a href="{{ route('home') }}" class="btn">Back home</a>
            </div>

            <div class="pub-shell">
                <div class="col" style="gap: 16px;">
                    <span class="pill"><span class="dot"></span>Piggy Wallet lookup</span>

                    <div>
                        <h2 style="font-family: 'Instrument Serif', Georgia, serif; font-size: 36px; line-height: 1.1; margin: 8px 0 8px; letter-spacing: 0; font-weight: 400;">Send a gift with their code</h2>
                        <p class="muted" style="font-size: 14px; line-height: 1.55; max-width: 540px; margin: 0;">
                            Ask your friend for their unique Piggy Wallet code, then we will take you straight to their public wallet page.
                        </p>
                    </div>

                    @if (session('error'))
                        <div class="error-box">{{ session('error') }}</div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('piggy.find') }}" method="POST" class="col" style="gap: 16px;">
                                @csrf

                                <div class="field">
                                    <label for="piggy_code" class="label">Piggy Wallet code</label>
                                    <input
                                        type="text"
                                        name="piggy_code"
                                        id="piggy_code"
                                        placeholder="AJVS8"
                                        maxlength="10"
                                        required
                                        class="input mono"
                                        value="{{ old('piggy_code') }}"
                                        autocomplete="off"
                                        autocapitalize="characters"
                                        oninput="this.value = this.value.toUpperCase()"
                                    >
                                    @error('piggy_code')
                                        <div class="tiny" style="color: var(--danger);">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn primary" style="width: 100%; padding: 11px 16px; font-size: 14px;">
                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                    Find Piggy Wallet
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body row" style="align-items: flex-start;">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent); flex: none; margin-top: 2px;"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            <div>
                                <div style="font-size: 13px; font-weight: 500;">Codes are private shortcuts</div>
                                <div class="tiny">A code opens the exact Piggy Wallet page, where you can review the recipient before sending a gift.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wallet-hero">
                    <div class="code-chip"><span class="mono">AJVS8</span> example code</div>
                    <div class="wallet-mark">PW</div>
                    <div>
                        <div style="font-size: 13px; font-weight: 500; color: #fff;">Piggy Wallet public page</div>
                        <div style="font-size: 12px; color: rgba(255,255,255,.72); max-width: 320px;">The next step uses the same secure gift form, recent gifts panel, and QR card.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
