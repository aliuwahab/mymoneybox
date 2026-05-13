<style>
/* ── use-cases bento — scoped ── */
.uc-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    grid-auto-rows: 220px;
    gap: 16px;
}
.uc-card {
    position: relative;
    overflow: hidden;
    border-radius: 14px;
    border: 1px solid #E6E3DC;
    display: flex;
    cursor: pointer;
    isolation: isolate;
    text-decoration: none;
    color: inherit;
    transition: transform .25s cubic-bezier(.2,.7,.2,1), box-shadow .25s, border-color .2s;
}
.uc-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 1px 0 rgba(20,18,12,.04), 0 12px 32px -10px rgba(20,18,12,.14);
    border-color: #D9D6CE;
}
.uc-card:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px #fff, 0 0 0 5px #1B6B4E;
}
.uc-illus {
    position: absolute; inset: 0;
    z-index: 1; pointer-events: none;
    transition: transform .8s cubic-bezier(.2,.7,.2,1);
}
.uc-illus svg { width: 100%; height: 100%; display: block; }
.uc-card:hover .uc-illus { transform: scale(1.04); }

/* grid spans */
.uc-cs-1 { grid-column: span 5; grid-row: span 2; }
.uc-cs-2 { grid-column: span 4; }
.uc-cs-3 { grid-column: span 3; }
.uc-cs-4 { grid-column: span 4; }
.uc-cs-5 { grid-column: span 3; }
.uc-cs-6 { grid-column: span 4; }
.uc-cs-7 { grid-column: span 4; }
.uc-cs-8 { grid-column: span 4; }

/* corner arrow */
.uc-arrow {
    position: absolute; top: 18px; right: 18px;
    width: 30px; height: 30px; border-radius: 50%;
    border: 1px solid transparent;
    display: grid; place-items: center;
    z-index: 3;
    transition: background .2s, color .2s, transform .25s cubic-bezier(.2,.7,.2,1), border-color .2s;
}
.uc-light .uc-arrow { color: #908D83; }
.uc-dark  .uc-arrow { color: rgba(255,255,255,0.7); }
.uc-light:hover .uc-arrow { background: #15140F; color: #FAFAF7; transform: rotate(-45deg); }
.uc-dark:hover  .uc-arrow { background: #ffffff; color: #15140F;  transform: rotate(-45deg); }

@media (max-width: 980px) {
    .uc-grid { grid-template-columns: repeat(2, 1fr); grid-auto-rows: 200px; }
    .uc-cs-1 { grid-column: span 2; grid-row: span 2; }
    .uc-cs-2,.uc-cs-3,.uc-cs-4,.uc-cs-5,.uc-cs-6,.uc-cs-7,.uc-cs-8 { grid-column: span 1; grid-row: span 1; }
}
</style>

<section class="section" id="cases">
    <div class="wrap">

        {{-- Section header --}}
        <div class="section-head">
            <div>
                <span class="kicker">Use cases</span>
                <h2 class="section-title">Built for any moment worth gathering for.</h2>
            </div>
            <p class="section-sub">From a wedding gift pool to a community library, from medical treatment to your team's offsite — MyMoneyBox adapts to the occasion.</p>
        </div>

        {{-- Bento grid --}}
        <div class="uc-grid">

            {{-- ── 1. Weddings — hero tile, dark emerald ── --}}
            <a href="{{ route('browse', ['category' => 'wedding']) }}"
               class="uc-card uc-cs-1 uc-dark"
               style="background:#0E3C2C"
               aria-label="Weddings & engagements">

                <div class="uc-illus" aria-hidden="true">
                    <svg viewBox="0 0 400 460" preserveAspectRatio="xMidYMid slice">
                        <defs>
                            <radialGradient id="w-glow" cx="50%" cy="35%" r="60%">
                                <stop offset="0%" stop-color="#2E8E6C" stop-opacity="0.55"/>
                                <stop offset="100%" stop-color="#0E3C2C" stop-opacity="0"/>
                            </radialGradient>
                            <linearGradient id="w-ring" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#E8C77A"/>
                                <stop offset="60%" stop-color="#F4E4B4"/>
                                <stop offset="100%" stop-color="#B88A3A"/>
                            </linearGradient>
                        </defs>
                        <rect width="400" height="460" fill="#0E3C2C"/>
                        <rect width="400" height="460" fill="url(#w-glow)"/>
                        <g fill="#ffffff" opacity="0.06">
                            <circle cx="60" cy="80" r="1.2"/><circle cx="120" cy="40" r="1"/><circle cx="320" cy="60" r="1.4"/>
                            <circle cx="370" cy="120" r="1"/><circle cx="40" cy="200" r="1.2"/><circle cx="80" cy="320" r="1"/>
                            <circle cx="350" cy="380" r="1.2"/><circle cx="280" cy="430" r="1"/><circle cx="200" cy="50" r="0.9"/>
                            <circle cx="240" cy="180" r="1"/><circle cx="160" cy="240" r="1"/>
                        </g>
                        <g transform="translate(200 195)">
                            <circle cx="-32" cy="0" r="58" fill="none" stroke="url(#w-ring)" stroke-width="6"/>
                            <circle cx="32"  cy="0" r="58" fill="none" stroke="url(#w-ring)" stroke-width="6"/>
                            <path d="M -65 -15 a 58 58 0 0 1 25 -40" fill="none" stroke="#FFF6DE" stroke-width="2" stroke-linecap="round" opacity="0.7"/>
                            <path d="M 65 -15 a 58 58 0 0 1 -25 -40" fill="none" stroke="#FFF6DE" stroke-width="2" stroke-linecap="round" opacity="0.7"/>
                        </g>
                        <path d="M 80 360 Q 200 320 320 360" fill="none" stroke="#B8E6CB" stroke-width="1" opacity="0.35"/>
                    </svg>
                </div>

                <div style="position:relative;z-index:2;padding:22px 22px 20px;display:flex;flex-direction:column;justify-content:flex-end;width:100%;color:#F5F1EA;">
                    <span style="align-self:flex-start;font-size:10.5px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;padding:4px 9px;border-radius:999px;background:rgba(255,255,255,0.14);border:1px solid rgba(255,255,255,0.16);color:#fff;margin-bottom:auto;">Most popular</span>
                    <h5 style="font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:44px;letter-spacing:-0.01em;line-height:1.05;margin:12px 0 4px;">Weddings &amp; engagements</h5>
                    <div style="color:rgba(245,241,234,0.65);font-size:13px;line-height:1.45;max-width:90%;">Honeymoon funds, gift pools, traditional ceremonies — a graceful way to receive blessings.</div>
                    <div style="display:inline-flex;align-items:baseline;gap:6px;margin-top:14px;font-size:12px;color:rgba(245,241,234,0.6);">
                        <b style="font-family:'Instrument Serif',Georgia,serif;font-size:22px;font-weight:400;color:#fff;letter-spacing:-0.01em;font-variant-numeric:tabular-nums;">₵42k</b> avg. raised per wedding
                    </div>
                </div>

                <div class="uc-arrow">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </div>
            </a>

            {{-- ── 2. Medical care — warm cream ── --}}
            <a href="{{ route('browse', ['category' => 'medical']) }}"
               class="uc-card uc-cs-2 uc-light"
               style="background:#FBF1DD"
               aria-label="Medical & recovery">

                <div class="uc-illus" aria-hidden="true">
                    <svg viewBox="0 0 400 220" preserveAspectRatio="xMidYMid slice">
                        <path d="M 0 160 Q 100 110 200 140 T 400 130 L 400 220 L 0 220 Z" fill="#F4DFA8" opacity="0.55"/>
                        <path d="M 30 140 L 80 140 L 100 100 L 120 180 L 140 60 L 160 140 L 260 140"
                              fill="none" stroke="#B5311E" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <g transform="translate(300 110)">
                            <path d="M 0 18 C -22 -2 -36 -28 -16 -38 C -4 -42 2 -36 4 -28 C 6 -36 12 -42 24 -38 C 44 -28 30 -2 8 18 Z"
                                  fill="#B5311E" opacity="0.9"/>
                            <circle cx="-22" cy="-6" r="5" fill="#fff" opacity="0.45"/>
                        </g>
                    </svg>
                </div>

                <div style="position:relative;z-index:2;padding:22px 22px 20px;display:flex;flex-direction:column;justify-content:flex-end;width:100%;color:#15140F;">
                    <span style="align-self:flex-start;font-size:10.5px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;padding:4px 9px;border-radius:999px;background:#fff;border:1px solid #E6E3DC;color:#5C5A54;margin-bottom:auto;">Care</span>
                    <h5 style="font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:26px;letter-spacing:-0.01em;line-height:1.05;margin:12px 0 4px;">Medical &amp; recovery</h5>
                    <div style="color:#5C5A54;font-size:13px;line-height:1.45;max-width:90%;">Treatments, surgeries, ongoing care.</div>
                </div>

                <div class="uc-arrow">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </div>
            </a>

            {{-- ── 3. Scholarships — slate ── --}}
            <a href="{{ route('browse', ['category' => 'education']) }}"
               class="uc-card uc-cs-3 uc-dark"
               style="background:#2E3A38"
               aria-label="Scholarships">

                <div class="uc-illus" aria-hidden="true">
                    <svg viewBox="0 0 300 220" preserveAspectRatio="xMidYMid slice">
                        <g stroke="#ffffff" stroke-width="1" opacity="0.07">
                            <line x1="0" y1="40" x2="300" y2="40"/>
                            <line x1="0" y1="80" x2="300" y2="80"/>
                            <line x1="0" y1="120" x2="300" y2="120"/>
                            <line x1="0" y1="160" x2="300" y2="160"/>
                            <line x1="0" y1="200" x2="300" y2="200"/>
                        </g>
                        <g transform="translate(150 110)">
                            <rect x="-58" y="20" width="116" height="14" rx="2" fill="#B8E6CB"/>
                            <rect x="-58" y="20" width="6"   height="14" fill="#1B6B4E"/>
                            <rect x="-50" y="4"  width="100" height="14" rx="2" fill="#F4DFA8"/>
                            <rect x="-50" y="4"  width="6"   height="14" fill="#B8810D"/>
                            <rect x="-44" y="-12" width="88" height="14" rx="2" fill="#F5F1EA"/>
                            <rect x="-44" y="-12" width="6"  height="14" fill="#2E3A38"/>
                            <line x1="34" y1="-12" x2="48" y2="20" stroke="#E8C77A" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="48" cy="22" r="3" fill="#E8C77A"/>
                        </g>
                    </svg>
                </div>

                <div style="position:relative;z-index:2;padding:22px 22px 20px;display:flex;flex-direction:column;justify-content:flex-end;width:100%;color:#F5F1EA;">
                    <span style="align-self:flex-start;font-size:10.5px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;padding:4px 9px;border-radius:999px;background:rgba(255,255,255,0.14);border:1px solid rgba(255,255,255,0.16);color:#fff;margin-bottom:auto;">Education</span>
                    <h5 style="font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:26px;letter-spacing:-0.01em;line-height:1.05;margin:12px 0 4px;">Scholarships</h5>
                    <div style="color:rgba(245,241,234,0.65);font-size:13px;line-height:1.45;max-width:90%;">Tuition, books, mentorship.</div>
                </div>

                <div class="uc-arrow">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </div>
            </a>

            {{-- ── 4. Neighbourhood projects — warm sand ── --}}
            <a href="{{ route('browse', ['category' => 'community']) }}"
               class="uc-card uc-cs-4 uc-light"
               style="background:#EDE6D3"
               aria-label="Neighbourhood projects">

                <div class="uc-illus" aria-hidden="true">
                    <svg viewBox="0 0 400 220" preserveAspectRatio="xMidYMid slice">
                        <g fill="#1B6B4E">
                            <path d="M 60 160 L 90 130 L 120 160 L 120 200 L 60 200 Z"/>
                            <rect x="86" y="170" width="10" height="30" fill="#EDE6D3"/>
                        </g>
                        <g fill="#0E3C2C">
                            <path d="M 140 160 L 175 120 L 210 160 L 210 200 L 140 200 Z"/>
                            <rect x="170" y="170" width="12" height="30" fill="#EDE6D3"/>
                            <rect x="148" y="140" width="10" height="10" fill="#EDE6D3"/>
                        </g>
                        <g fill="#883647">
                            <path d="M 230 160 L 260 135 L 290 160 L 290 200 L 230 200 Z"/>
                            <rect x="256" y="172" width="10" height="28" fill="#EDE6D3"/>
                        </g>
                        <g fill="#B8810D">
                            <path d="M 310 160 L 335 138 L 360 160 L 360 200 L 310 200 Z"/>
                            <rect x="330" y="172" width="10" height="28" fill="#EDE6D3"/>
                        </g>
                        <circle cx="340" cy="60" r="20" fill="#F4DFA8" opacity="0.9"/>
                        <path d="M 80 50 q 8 -8 16 0 q 8 -8 16 0" fill="none" stroke="#1B6B4E" stroke-width="1.6" stroke-linecap="round"/>
                        <path d="M 130 70 q 6 -6 12 0 q 6 -6 12 0" fill="none" stroke="#1B6B4E" stroke-width="1.4" stroke-linecap="round" opacity="0.7"/>
                        <line x1="0" y1="200" x2="400" y2="200" stroke="#A89868" stroke-width="1"/>
                    </svg>
                </div>

                <div style="position:relative;z-index:2;padding:22px 22px 20px;display:flex;flex-direction:column;justify-content:flex-end;width:100%;color:#15140F;">
                    <span style="align-self:flex-start;font-size:10.5px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;padding:4px 9px;border-radius:999px;background:#fff;border:1px solid #E6E3DC;color:#5C5A54;margin-bottom:auto;">Community</span>
                    <h5 style="font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:26px;letter-spacing:-0.01em;line-height:1.05;margin:12px 0 4px;">Neighbourhood projects</h5>
                    <div style="color:#5C5A54;font-size:13px;line-height:1.45;max-width:90%;">Libraries, water, clean-ups, repairs.</div>
                </div>

                <div class="uc-arrow">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </div>
            </a>

            {{-- ── 5. Offsites & gifts — rose ── --}}
            <a href="{{ route('browse', ['category' => 'team']) }}"
               class="uc-card uc-cs-5 uc-light"
               style="background:#F4E2E5"
               aria-label="Offsites & gifts">

                <div class="uc-illus" aria-hidden="true">
                    <svg viewBox="0 0 300 220" preserveAspectRatio="xMidYMid slice">
                        <g>
                            <circle cx="70"  cy="120" r="28" fill="#883647"/>
                            <circle cx="120" cy="120" r="28" fill="#B85773"/>
                            <circle cx="170" cy="120" r="28" fill="#E8A4B3"/>
                            <circle cx="220" cy="120" r="28" fill="#883647" opacity="0.75"/>
                            <circle cx="70"  cy="98" r="9" fill="#F4E2E5"/>
                            <circle cx="120" cy="98" r="9" fill="#F4E2E5"/>
                            <circle cx="170" cy="98" r="9" fill="#F4E2E5"/>
                            <circle cx="220" cy="98" r="9" fill="#F4E2E5"/>
                        </g>
                        <g>
                            <rect x="40" y="40" width="4" height="10" rx="1" fill="#1B6B4E" transform="rotate(20 42 45)"/>
                            <rect x="260" y="60" width="4" height="10" rx="1" fill="#B8810D" transform="rotate(-30 262 65)"/>
                            <rect x="150" y="30" width="4" height="10" rx="1" fill="#883647" transform="rotate(45 152 35)"/>
                            <circle cx="90" cy="30" r="2.5" fill="#E8C77A"/>
                            <circle cx="220" cy="40" r="2.5" fill="#1B6B4E"/>
                        </g>
                    </svg>
                </div>

                <div style="position:relative;z-index:2;padding:22px 22px 20px;display:flex;flex-direction:column;justify-content:flex-end;width:100%;color:#15140F;">
                    <span style="align-self:flex-start;font-size:10.5px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;padding:4px 9px;border-radius:999px;background:#fff;border:1px solid #E6E3DC;color:#5C5A54;margin-bottom:auto;">Team</span>
                    <h5 style="font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:26px;letter-spacing:-0.01em;line-height:1.05;margin:12px 0 4px;">Offsites &amp; gifts</h5>
                    <div style="color:#5C5A54;font-size:13px;line-height:1.45;max-width:90%;">Retreats, parties, leaving gifts.</div>
                </div>

                <div class="uc-arrow">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </div>
            </a>

            {{-- ── 6. Birthdays & baby — soft mint ── --}}
            <a href="{{ route('browse', ['category' => 'birthday']) }}"
               class="uc-card uc-cs-6 uc-light"
               style="background:#E8F1E9"
               aria-label="Birthdays & baby">

                <div class="uc-illus" aria-hidden="true">
                    <svg viewBox="0 0 400 220" preserveAspectRatio="xMidYMid slice">
                        <g transform="translate(200 130)">
                            <ellipse cx="0" cy="50" rx="80" ry="6" fill="#1B6B4E" opacity="0.2"/>
                            <rect x="-66" y="10" width="132" height="40" rx="4" fill="#F5F1EA"/>
                            <path d="M -66 14 q 11 8 22 0 t 22 0 t 22 0 t 22 0 t 22 0 t 22 0" fill="none" stroke="#B85773" stroke-width="2"/>
                            <rect x="-44" y="-22" width="88" height="32" rx="4" fill="#F5F1EA"/>
                            <path d="M -44 -18 q 9 6 18 0 t 18 0 t 18 0 t 18 0 t 18 0" fill="none" stroke="#1B6B4E" stroke-width="2"/>
                            <rect x="-2" y="-44" width="4" height="22" rx="1" fill="#B85773"/>
                            <path d="M 0 -52 q -5 -3 -3 -8 q 1 -3 3 -4 q 2 1 3 4 q 2 5 -3 8 Z" fill="#E8A04E"/>
                            <ellipse cx="0" cy="-55" rx="6" ry="9" fill="#FFCB6E" opacity="0.5"/>
                        </g>
                        <g>
                            <circle cx="80" cy="60" r="16" fill="#B85773"/>
                            <line x1="80" y1="76" x2="80" y2="120" stroke="#B85773" stroke-width="1"/>
                            <circle cx="60" cy="90" r="11" fill="#E8C77A"/>
                            <line x1="60" y1="101" x2="60" y2="130" stroke="#B8810D" stroke-width="1"/>
                        </g>
                    </svg>
                </div>

                <div style="position:relative;z-index:2;padding:22px 22px 20px;display:flex;flex-direction:column;justify-content:flex-end;width:100%;color:#15140F;">
                    <span style="align-self:flex-start;font-size:10.5px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;padding:4px 9px;border-radius:999px;background:#fff;border:1px solid #E6E3DC;color:#5C5A54;margin-bottom:auto;">Celebrate</span>
                    <h5 style="font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:26px;letter-spacing:-0.01em;line-height:1.05;margin:12px 0 4px;">Birthdays &amp; baby</h5>
                    <div style="color:#5C5A54;font-size:13px;line-height:1.45;max-width:90%;">Cakes, nursery gifts, milestones.</div>
                </div>

                <div class="uc-arrow">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </div>
            </a>

            {{-- ── 7. Funeral & memorial — deep slate ── --}}
            <a href="{{ route('browse', ['category' => 'funeral']) }}"
               class="uc-card uc-cs-7 uc-dark"
               style="background:#1F2329"
               aria-label="Funeral & memorial">

                <div class="uc-illus" aria-hidden="true">
                    <svg viewBox="0 0 400 220" preserveAspectRatio="xMidYMid slice">
                        <defs>
                            <radialGradient id="moon" cx="50%" cy="50%" r="50%">
                                <stop offset="0%"   stop-color="#F5F1EA"/>
                                <stop offset="100%" stop-color="#A89968"/>
                            </radialGradient>
                        </defs>
                        <circle cx="310" cy="80" r="36" fill="url(#moon)" opacity="0.95"/>
                        <circle cx="300" cy="74" r="34" fill="#1F2329"/>
                        <path d="M 0 140 Q 80 125 160 140 T 320 140 T 480 140" fill="none" stroke="#3A4754" stroke-width="1.4" opacity="0.6"/>
                        <path d="M 0 165 Q 80 150 160 165 T 320 165 T 480 165" fill="none" stroke="#3A4754" stroke-width="1.4" opacity="0.4"/>
                        <path d="M 0 190 Q 80 175 160 190 T 320 190 T 480 190" fill="none" stroke="#3A4754" stroke-width="1.4" opacity="0.3"/>
                        <g transform="translate(110 130)">
                            <ellipse cx="0" cy="0" rx="18" ry="8" fill="#5C6471" opacity="0.6"/>
                            <path d="M 0 -4 q -10 -16 -18 -8 q 0 12 18 12 Z" fill="#E8E2D2" opacity="0.85"/>
                            <path d="M 0 -4 q 10 -16 18 -8 q 0 12 -18 12 Z" fill="#E8E2D2" opacity="0.85"/>
                            <path d="M 0 -8 q -6 -18 0 -22 q 6 4 0 22 Z" fill="#F5F1EA"/>
                        </g>
                        <g fill="#F5F1EA">
                            <circle cx="80"  cy="40" r="1"/>
                            <circle cx="180" cy="30" r="1.2"/>
                            <circle cx="240" cy="50" r="0.8"/>
                            <circle cx="60"  cy="80" r="1"/>
                            <circle cx="370" cy="40" r="1"/>
                        </g>
                    </svg>
                </div>

                <div style="position:relative;z-index:2;padding:22px 22px 20px;display:flex;flex-direction:column;justify-content:flex-end;width:100%;color:#F5F1EA;">
                    <span style="align-self:flex-start;font-size:10.5px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;padding:4px 9px;border-radius:999px;background:rgba(255,255,255,0.14);border:1px solid rgba(255,255,255,0.16);color:#fff;margin-bottom:auto;">Support</span>
                    <h5 style="font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:26px;letter-spacing:-0.01em;line-height:1.05;margin:12px 0 4px;">Funeral &amp; memorial</h5>
                    <div style="color:rgba(245,241,234,0.65);font-size:13px;line-height:1.45;max-width:90%;">Carry the weight together, with dignity.</div>
                </div>

                <div class="uc-arrow">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </div>
            </a>

            {{-- ── 8. Religious causes — warm ochre ── --}}
            <a href="{{ route('browse', ['category' => 'religious']) }}"
               class="uc-card uc-cs-8 uc-light"
               style="background:#E8D7A8"
               aria-label="Religious causes">

                <div class="uc-illus" aria-hidden="true">
                    <svg viewBox="0 0 400 220" preserveAspectRatio="xMidYMid slice">
                        <g transform="translate(200 130)">
                            <path d="M -60 60 L -60 -10 Q -60 -60 0 -60 Q 60 -60 60 -10 L 60 60 Z"
                                  fill="#B88A3A" opacity="0.18"/>
                            <path d="M -60 60 L -60 -10 Q -60 -60 0 -60 Q 60 -60 60 -10 L 60 60"
                                  fill="none" stroke="#8F6620" stroke-width="2"/>
                            <path d="M -36 60 L -36 -4 Q -36 -38 0 -38 Q 36 -38 36 -4 L 36 60"
                                  fill="none" stroke="#8F6620" stroke-width="1.4" opacity="0.7"/>
                            <path d="M -14 60 L -14 24 Q -14 8 0 8 Q 14 8 14 24 L 14 60 Z"
                                  fill="#1F2329" opacity="0.85"/>
                            <g stroke="#E8C77A" stroke-width="1.4" stroke-linecap="round" opacity="0.7">
                                <line x1="-90" y1="-80" x2="-70" y2="-66"/>
                                <line x1="90"  y1="-80" x2="70"  y2="-66"/>
                                <line x1="0" y1="-100" x2="0" y2="-80"/>
                            </g>
                        </g>
                        <line x1="100" y1="190" x2="300" y2="190" stroke="#8F6620" stroke-width="1" opacity="0.4"/>
                    </svg>
                </div>

                <div style="position:relative;z-index:2;padding:22px 22px 20px;display:flex;flex-direction:column;justify-content:flex-end;width:100%;color:#15140F;">
                    <span style="align-self:flex-start;font-size:10.5px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;padding:4px 9px;border-radius:999px;background:#fff;border:1px solid #E6E3DC;color:#5C5A54;margin-bottom:auto;">Faith</span>
                    <h5 style="font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:26px;letter-spacing:-0.01em;line-height:1.05;margin:12px 0 4px;">Religious causes</h5>
                    <div style="color:#5C5A54;font-size:13px;line-height:1.45;max-width:90%;">Building funds, missions, almsgiving.</div>
                </div>

                <div class="uc-arrow">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </div>
            </a>

        </div>{{-- /uc-grid --}}
    </div>{{-- /wrap --}}
</section>
