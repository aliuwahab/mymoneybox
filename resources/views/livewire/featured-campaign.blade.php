@php
    $box = $this->campaign;
    $sym = $box ? $box->getCurrencySymbol() : '₵';
    $pct = $box ? (int) $box->getProgressPercentage() : 0;
    $contributorCount = $box ? $box->contribution_count : 0;
    $daysLeft = ($box && $box->end_date && !$box->is_ongoing)
        ? max(0, (int) now()->diffInDays($box->end_date, false))
        : null;
    $raised = $box ? $box->total_contributions : 0;
    $goal   = $box ? $box->goal_amount : null;
    $initials = $box ? strtoupper(substr($box->user->name ?? 'U', 0, 1) . substr(strstr($box->user->name ?? ' X', ' ') ?: 'X', 1, 1)) : 'MB';
    $categoryName = $box?->category?->name ?? 'Community';
    $location = 'Ghana';
@endphp

@if($box)
<section class="featured-section" id="featured">
    <div class="wrap">
        <div class="featured-head">
            <div>
                <span class="eyebrow live"><span class="dot"></span> Live now · Featured this week</span>
                <h2 class="section-title">Sometimes a story finds you. Here's one worth watching.</h2>
            </div>
            <div class="featured-head-meta">
                <span>Curated by the MyPiggyBox team.</span>
                <a href="#placements">See more campaigns →</a>
            </div>
        </div>

        <article class="featured">
            <!-- LEFT: editorial cover -->
            <a href="{{ $box->getPublicUrl() }}" class="featured-cover" style="text-decoration:none">
                <div class="cover-illus" aria-hidden="true">
                    <svg viewBox="0 0 720 600" preserveAspectRatio="xMidYMid slice">
                        <defs>
                            <radialGradient id="fc-glow" cx="78%" cy="22%" r="62%">
                                <stop offset="0%"  stop-color="#2E8E6C" stop-opacity="0.55"/>
                                <stop offset="55%" stop-color="#1B6B4E" stop-opacity="0.18"/>
                                <stop offset="100%" stop-color="#0E3C2C" stop-opacity="0"/>
                            </radialGradient>
                            <radialGradient id="fc-orb" cx="50%" cy="50%" r="50%">
                                <stop offset="0%"  stop-color="#F4E4B4" stop-opacity="0.9"/>
                                <stop offset="60%" stop-color="#E8C77A" stop-opacity="0.6"/>
                                <stop offset="100%" stop-color="#B88A3A" stop-opacity="0"/>
                            </radialGradient>
                            <linearGradient id="fc-arc" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%"  stop-color="#E8C77A" stop-opacity="0"/>
                                <stop offset="50%" stop-color="#F4E4B4" stop-opacity="0.85"/>
                                <stop offset="100%" stop-color="#E8C77A" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <rect width="720" height="600" fill="#0E3C2C"/>
                        <rect width="720" height="600" fill="url(#fc-glow)"/>
                        <g stroke="#ffffff" stroke-width="0.6" opacity="0.05">
                            <line x1="-100" y1="100" x2="820" y2="-200"/>
                            <line x1="-100" y1="200" x2="820" y2="-100"/>
                            <line x1="-100" y1="300" x2="820" y2="0"/>
                            <line x1="-100" y1="400" x2="820" y2="100"/>
                            <line x1="-100" y1="500" x2="820" y2="200"/>
                            <line x1="-100" y1="600" x2="820" y2="300"/>
                            <line x1="-100" y1="700" x2="820" y2="400"/>
                            <line x1="-100" y1="800" x2="820" y2="500"/>
                        </g>
                        <circle cx="560" cy="160" r="180" fill="url(#fc-orb)"/>
                        <circle cx="560" cy="160" r="44"  fill="#F4E4B4" opacity="0.95"/>
                        <circle cx="560" cy="160" r="22"  fill="#FFFBE8"/>
                        <g fill="none" stroke="url(#fc-arc)" stroke-width="1.4">
                            <ellipse cx="560" cy="160" rx="118" ry="118"/>
                        </g>
                        <g fill="none" stroke="#E8C77A" stroke-width="1" opacity="0.35">
                            <ellipse cx="560" cy="160" rx="170" ry="170"/>
                            <ellipse cx="560" cy="160" rx="226" ry="226"/>
                            <ellipse cx="560" cy="160" rx="290" ry="290"/>
                        </g>
                        <path d="M 60 460 Q 220 320 380 360 T 560 160"
                            fill="none" stroke="#B8E6CB" stroke-width="2" stroke-linecap="round"
                            stroke-dasharray="2 6" opacity="0.85"/>
                        <g>
                            <circle cx="380" cy="360" r="6" fill="#B8E6CB"/>
                            <circle cx="380" cy="360" r="11" fill="none" stroke="#B8E6CB" stroke-width="1" opacity="0.5"/>
                        </g>
                        <g fill="#F4E4B4">
                            <circle cx="120" cy="120" r="1.6"/><circle cx="180" cy="80"  r="1.2"/>
                            <circle cx="240" cy="140" r="1.8"/><circle cx="320" cy="90"  r="1.3"/>
                            <circle cx="100" cy="200" r="1.5"/><circle cx="200" cy="240" r="1.1"/>
                            <circle cx="80"  cy="320" r="1.6"/><circle cx="160" cy="380" r="1.2"/>
                            <circle cx="40"  cy="440" r="1.4"/><circle cx="260" cy="420" r="1.1"/>
                            <circle cx="340" cy="200" r="1.5"/><circle cx="420" cy="120" r="1.3"/>
                            <circle cx="480" cy="60"  r="1.6"/><circle cx="640" cy="320" r="1.4"/>
                            <circle cx="690" cy="240" r="1.2"/><circle cx="620" cy="440" r="1.6"/>
                            <circle cx="540" cy="500" r="1.2"/><circle cx="440" cy="480" r="1.4"/>
                        </g>
                        <g>
                            <path d="M 120 320 l 0 -8 M 120 320 l 0 8 M 120 320 l -8 0 M 120 320 l 8 0"
                                stroke="#F4E4B4" stroke-width="0.9" stroke-linecap="round" opacity="0.7"/>
                            <path d="M 480 60 l 0 -10 M 480 60 l 0 10 M 480 60 l -10 0 M 480 60 l 10 0"
                                stroke="#F4E4B4" stroke-width="0.9" stroke-linecap="round" opacity="0.7"/>
                            <path d="M 340 240 l 0 -6 M 340 240 l 0 6 M 340 240 l -6 0 M 340 240 l 6 0"
                                stroke="#B8E6CB" stroke-width="0.9" stroke-linecap="round" opacity="0.7"/>
                        </g>
                        <path d="M 0 540 Q 360 510 720 540 L 720 600 L 0 600 Z" fill="#000" opacity="0.18"/>
                        <text x="64" y="380" font-family="Instrument Serif, serif" font-style="italic"
                            font-size="200" fill="#1B6B4E" opacity="0.35">{{ strtoupper(substr($box->title, 0, 1)) }}</text>
                        <text x="44" y="62" font-family="JetBrains Mono, monospace" font-size="10"
                            letter-spacing="0.18em" fill="#F5F1EA" opacity="0.55">CAMPAIGN · {{ now()->format('Y / W') }}</text>
                    </svg>
                </div>

                <div class="cover-top">
                    <span class="live-badge">
                        <span class="live-dot"></span> Live · {{ number_format($contributorCount) }} contributors
                    </span>
                    @if($daysLeft !== null)
                    <span class="cover-counter">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        {{ $daysLeft }} days remaining
                    </span>
                    @else
                    <span class="cover-counter">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        Ongoing
                    </span>
                    @endif
                </div>

                <div class="cover-overlay">
                    <div class="cover-eyebrow">{{ $categoryName }}<span class="sep" style="margin:0 8px;opacity:0.5">·</span>{{ $location }}</div>
                    <h3 class="cover-title">{{ Str::limit($box->title, 40) }}</h3>
                    <div class="cover-byline">
                        <div class="avatar" style="width:30px;height:30px;background:#B8E6CB;color:#0E3C2C;font-size:11px">{{ $initials }}</div>
                        <div>Started by <b>{{ $box->user->name }}</b></div>
                    </div>
                </div>
            </a>

            <!-- RIGHT: story column -->
            <div class="featured-body">
                <div class="featured-tags">
                    <span class="ftag">{{ $categoryName }}</span>
                    @if($box->user->currentVerification)
                        <span class="ftag muted">Verified creator</span>
                    @endif
                    <span class="ftag muted">Public · {{ number_format($contributorCount) }} contributors</span>
                </div>

                <div class="featured-story">
                    @if($box->description)
                        <p>{{ Str::limit($box->description, 280) }}</p>
                    @else
                        <p>Help make this campaign a success — every contribution, big or small, brings this goal closer to reality.</p>
                    @endif
                </div>

                <div class="featured-progress">
                    <div class="fp-row">
                        <div>
                            <div class="fp-amount">{{ $box->formatAmount($raised) }}</div>
                            @if($goal)
                                <div class="fp-goal">of <b>{{ $box->formatAmount($goal) }}</b> goal</div>
                            @else
                                <div class="fp-goal">raised so far</div>
                            @endif
                        </div>
                        @if($goal)
                            <div class="fp-pct">{{ $pct }}%</div>
                        @endif
                    </div>
                    @if($goal)
                        <div class="progress"><span style="width:{{ min(100, $pct) }}%"></span></div>
                    @endif
                    <div class="fp-stats">
                        <div class="fp-stat">
                            <div class="num">{{ number_format($contributorCount) }}</div>
                            <div class="lab">Contributors</div>
                        </div>
                        <div class="fp-stat">
                            <div class="num">
                                @if($contributorCount > 0)
                                    {{ $box->formatAmount($raised / $contributorCount) }}
                                @else
                                    —
                                @endif
                            </div>
                            <div class="lab">Avg. gift</div>
                        </div>
                        <div class="fp-stat">
                            <div class="num">
                                @if($daysLeft !== null)
                                    {{ $daysLeft }}d
                                @else
                                    ∞
                                @endif
                            </div>
                            <div class="lab">Remaining</div>
                        </div>
                    </div>
                </div>

                <div class="featured-cta">
                    <a class="btn primary lg" href="{{ $box->getPublicUrl() }}">
                        Contribute now
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                    <a class="btn lg" href="{{ route('money-boxes.share', $box) }}">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="12" r="3"/><circle cx="18" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><path d="M8.6 13.5 15.4 17"/><path d="M15.4 7 8.6 10.5"/></svg>
                        Share
                    </a>
                </div>

                <div class="featured-recent">
                    @php
                        $recentContribs = $box->contributions()->with('user')->latest()->take(5)->get();
                        $avatarColors = ['#1B6B4E', '#B8810D', '#883647', '#3F2A6E', '#2E3A38'];
                    @endphp
                    <div class="av-stack">
                        @foreach($recentContribs->take(5) as $i => $contrib)
                            @php
                                $cName = $contrib->user?->name ?? ($contrib->contributor_name ?? 'Anonymous');
                                $cInitials = strtoupper(substr($cName, 0, 1) . (strstr($cName, ' ') ? substr(strstr($cName, ' '), 1, 1) : substr($cName, 1, 1)));
                                $avColor = $avatarColors[$i % count($avatarColors)];
                            @endphp
                            <div style="background:{{ $avColor }}">{{ $cInitials }}</div>
                        @endforeach
                        @if($contributorCount > 5)
                            <div class="more" style="background:var(--bg-2);color:var(--fg-2);font-size:10px">+{{ number_format($contributorCount - 5) }}</div>
                        @endif
                    </div>
                    <div class="recent-text">
                        @if($contributorCount > 0)
                            <b>{{ number_format($contributorCount) }} {{ Str::plural('person', $contributorCount) }}</b> {{ $contributorCount === 1 ? 'has' : 'have' }} contributed
                        @else
                            Be the first to contribute
                        @endif
                    </div>
                </div>
            </div>
        </article>
    </div>
</section>
@endif
