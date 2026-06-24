<div>
    @php
        $campaigns = $this->campaigns;
        $coverPalettes = [
            'medical'    => ['bg1' => '#FBF1DD', 'bg2' => '#F4DFA8', 'type' => 'medical'],
            'health'     => ['bg1' => '#FBF1DD', 'bg2' => '#F4DFA8', 'type' => 'medical'],
            'community'  => ['bg1' => '#1B6B4E', 'bg2' => '#0E3C2C', 'type' => 'community'],
            'wedding'    => ['bg1' => '#F4E2E5', 'bg2' => '#E8C9CE', 'type' => 'wedding'],
            'education'  => ['bg1' => '#E8EDF8', 'bg2' => '#C8D4EF', 'type' => 'education'],
            'funeral'    => ['bg1' => '#2A2E38', 'bg2' => '#1A1D24', 'type' => 'funeral'],
            'religious'  => ['bg1' => '#F5EDD6', 'bg2' => '#E8D9B4', 'type' => 'religious'],
        ];

        $avatarColors = ['#1B6B4E', '#B8810D', '#883647', '#3F2A6E', '#2E3A38'];
    @endphp

    @if($campaigns->isNotEmpty())
        <section class="placements-section" id="placements">
            <div class="wrap">
                <div class="placements-head">
                    <div>
                        <span class="kicker">Causes worth supporting</span>
                        <h2 class="section-title">More campaigns happening right now.</h2>
                    </div>
                    <p class="placements-sub">Real people, real stories, real progress. Give once, follow a few, or simply share — every nudge counts.</p>
                </div>

                <div class="placements-grid">
                    @foreach($campaigns as $i => $box)
                        @php
                            $catSlug = strtolower($box->category?->name ?? 'community');
                            $palette = $coverPalettes[$catSlug] ?? $coverPalettes['community'];
                            $pct = (int) $box->getProgressPercentage();
                            $daysLeft = ($box->end_date && !$box->is_ongoing) ? max(0, (int) now()->diffInDays($box->end_date, false)) : null;
                            $initials = strtoupper(substr($box->user?->name ?? 'U', 0, 1) . substr(strstr($box->user?->name ?? ' X', ' ') ?: 'X', 1, 1));
                            $avColor = $avatarColors[$i % count($avatarColors)];
                            $svgId = 'pl' . $i . '_' . uniqid();
                        @endphp
                        <a class="placement" href="{{ $box->getPublicUrl() }}">
                            <div class="pl-cover">
                                <div class="pl-cover-illus" aria-hidden="true">
                                    @if($palette['type'] === 'medical')
                                        <svg viewBox="0 0 400 200" preserveAspectRatio="xMidYMid slice">
                                            <defs>
                                                <linearGradient id="{{ $svgId }}-bg" x1="0" y1="0" x2="1" y2="1">
                                                    <stop offset="0%"  stop-color="{{ $palette['bg1'] }}"/>
                                                    <stop offset="100%" stop-color="{{ $palette['bg2'] }}"/>
                                                </linearGradient>
                                            </defs>
                                            <rect width="400" height="200" fill="url(#{{ $svgId }}-bg)"/>
                                            <path d="M 0 120 L 70 120 L 90 80 L 110 160 L 130 40 L 150 120 L 280 120 L 300 100 L 320 140 L 340 120 L 400 120"
                                                  fill="none" stroke="#B5311E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                            <g transform="translate(340 80)">
                                                <path d="M 0 18 C -20 -2 -34 -26 -14 -36 C -3 -40 2 -34 4 -27 C 6 -34 11 -40 22 -36 C 42 -26 28 -2 8 18 Z" fill="#B5311E"/>
                                                <circle cx="-18" cy="-4" r="4" fill="#fff" opacity="0.5"/>
                                            </g>
                                            <g fill="#B8810D" opacity="0.18">
                                                <circle cx="40" cy="50" r="1.4"/><circle cx="100" cy="30" r="1"/>
                                                <circle cx="180" cy="170" r="1.2"/><circle cx="260" cy="50" r="1.4"/>
                                            </g>
                                        </svg>
                                    @elseif($palette['type'] === 'community')
                                        <svg viewBox="0 0 400 200" preserveAspectRatio="xMidYMid slice">
                                            <defs>
                                                <linearGradient id="{{ $svgId }}-bg" x1="0" y1="0" x2="0" y2="1">
                                                    <stop offset="0%"  stop-color="{{ $palette['bg1'] }}"/>
                                                    <stop offset="100%" stop-color="{{ $palette['bg2'] }}"/>
                                                </linearGradient>
                                            </defs>
                                            <rect width="400" height="200" fill="url(#{{ $svgId }}-bg)"/>
                                            <circle cx="320" cy="60" r="26" fill="#F4E4B4" opacity="0.95"/>
                                            <circle cx="320" cy="60" r="40" fill="#F4E4B4" opacity="0.18"/>
                                            <g fill="none" stroke="#B8E6CB" stroke-width="1.4" stroke-linecap="round" opacity="0.7">
                                                <path d="M 40 150 Q 110 138 180 150 T 320 150"/>
                                                <path d="M 40 170 Q 110 158 180 170 T 320 170" opacity="0.7"/>
                                                <path d="M 40 130 Q 110 118 180 130 T 320 130" opacity="0.5"/>
                                            </g>
                                            <g transform="translate(120 90)">
                                                <path d="M 0 -18 C -12 -4 -14 8 0 14 C 14 8 12 -4 0 -18 Z" fill="#B8E6CB" opacity="0.9"/>
                                                <ellipse cx="-4" cy="0" rx="2" ry="3" fill="#F5F1EA" opacity="0.6"/>
                                            </g>
                                            <g fill="#F4E4B4" opacity="0.7">
                                                <circle cx="60" cy="40" r="1"/><circle cx="200" cy="30" r="1.2"/><circle cx="250" cy="100" r="0.9"/>
                                            </g>
                                        </svg>
                                    @elseif($palette['type'] === 'wedding')
                                        <svg viewBox="0 0 400 200" preserveAspectRatio="xMidYMid slice">
                                            <defs>
                                                <linearGradient id="{{ $svgId }}-bg" x1="0" y1="0" x2="1" y2="1">
                                                    <stop offset="0%"  stop-color="{{ $palette['bg1'] }}"/>
                                                    <stop offset="100%" stop-color="{{ $palette['bg2'] }}"/>
                                                </linearGradient>
                                                <linearGradient id="{{ $svgId }}-ring" x1="0" y1="0" x2="1" y2="1">
                                                    <stop offset="0%"  stop-color="#E8C77A"/>
                                                    <stop offset="55%" stop-color="#F4E4B4"/>
                                                    <stop offset="100%" stop-color="#B88A3A"/>
                                                </linearGradient>
                                            </defs>
                                            <rect width="400" height="200" fill="url(#{{ $svgId }}-bg)"/>
                                            <path d="M 30 30 Q 200 60 370 30" fill="none" stroke="#883647" stroke-width="0.8" opacity="0.5"/>
                                            <g fill="#883647" opacity="0.55">
                                                <path d="M 80 36 l -6 12 l 12 0 Z"/>
                                                <path d="M 140 46 l -6 12 l 12 0 Z" fill="#B85773" opacity="0.65"/>
                                                <path d="M 200 50 l -6 12 l 12 0 Z"/>
                                                <path d="M 260 46 l -6 12 l 12 0 Z" fill="#E8A4B3" opacity="0.7"/>
                                                <path d="M 320 36 l -6 12 l 12 0 Z" opacity="0.55"/>
                                            </g>
                                            <g transform="translate(200 130)">
                                                <circle cx="-22" cy="0" r="36" fill="none" stroke="url(#{{ $svgId }}-ring)" stroke-width="4"/>
                                                <circle cx="22"  cy="0" r="36" fill="none" stroke="url(#{{ $svgId }}-ring)" stroke-width="4"/>
                                                <path d="M -42 -10 a 36 36 0 0 1 15 -24" fill="none" stroke="#FFF6DE" stroke-width="1.5" stroke-linecap="round" opacity="0.75"/>
                                            </g>
                                            <g fill="#B85773" opacity="0.6">
                                                <ellipse cx="70" cy="170" rx="6" ry="3" transform="rotate(20 70 170)"/>
                                                <ellipse cx="100" cy="180" rx="5" ry="2.5" transform="rotate(-15 100 180)"/>
                                                <ellipse cx="320" cy="180" rx="6" ry="3" transform="rotate(35 320 180)"/>
                                                <ellipse cx="350" cy="170" rx="5" ry="2.5" transform="rotate(-20 350 170)"/>
                                            </g>
                                        </svg>
                                    @elseif($palette['type'] === 'education')
                                        <svg viewBox="0 0 400 200" preserveAspectRatio="xMidYMid slice">
                                            <defs>
                                                <linearGradient id="{{ $svgId }}-bg" x1="0" y1="0" x2="1" y2="1">
                                                    <stop offset="0%"  stop-color="{{ $palette['bg1'] }}"/>
                                                    <stop offset="100%" stop-color="{{ $palette['bg2'] }}"/>
                                                </linearGradient>
                                            </defs>
                                            <rect width="400" height="200" fill="url(#{{ $svgId }}-bg)"/>
                                            <g fill="none" stroke="#3F5FA8" stroke-width="2" stroke-linecap="round" opacity="0.7">
                                                <rect x="120" y="60" width="60" height="80" rx="4"/>
                                                <rect x="190" y="50" width="60" height="90" rx="4"/>
                                                <rect x="260" y="70" width="60" height="70" rx="4"/>
                                            </g>
                                            <path d="M 80 140 L 340 140" stroke="#3F5FA8" stroke-width="2" opacity="0.4"/>
                                            <g fill="#3F5FA8" opacity="0.3">
                                                <circle cx="60" cy="50" r="1.4"/><circle cx="370" cy="80" r="1.2"/>
                                            </g>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 400 200" preserveAspectRatio="xMidYMid slice">
                                            <defs>
                                                <linearGradient id="{{ $svgId }}-bg" x1="0" y1="0" x2="1" y2="1">
                                                    <stop offset="0%"  stop-color="#1B6B4E"/>
                                                    <stop offset="100%" stop-color="#0E3C2C"/>
                                                </linearGradient>
                                            </defs>
                                            <rect width="400" height="200" fill="url(#{{ $svgId }}-bg)"/>
                                            <g fill="#B8E6CB" opacity="0.5">
                                                <circle cx="80"  cy="80"  r="40"/>
                                                <circle cx="200" cy="120" r="55"/>
                                                <circle cx="330" cy="70"  r="35"/>
                                            </g>
                                            <g fill="#F4E4B4" opacity="0.3">
                                                <circle cx="60" cy="40" r="1.2"/><circle cx="350" cy="170" r="1.4"/><circle cx="200" cy="30" r="1"/>
                                            </g>
                                        </svg>
                                    @endif
                                </div>
                                <div class="pl-cover-overlay">
                                    <span class="pl-cat">{{ $box->category?->name ?? 'Campaign' }}</span>
                                    @if($daysLeft !== null)
                                        <span class="pl-days">{{ $daysLeft }}d left</span>
                                    @else
                                        <span class="pl-days">Ongoing</span>
                                    @endif
                                </div>
                                <div class="pl-arrow">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                </div>
                            </div>
                            <div class="pl-body">
                                <h4 class="pl-title">{{ Str::limit($box->title, 50) }}</h4>
                                <p class="pl-story">{{ $box->description ? Str::limit($box->description, 120) : 'Help make this cause a reality — every contribution counts.' }}</p>
                                <div class="pl-progress">
                                    @if($box->goal_amount)
                                        <div class="progress"><span style="width:{{ min(100, $pct) }}%"></span></div>
                                    @endif
                                    <div class="pl-meta">
                            <span class="raised">
                                {{ $box->formatAmount($box->total_contributions) }}
                                @if($box->goal_amount)
                                    of {{ $box->formatAmount($box->goal_amount) }}
                                @endif
                            </span>
                                        <span class="right">{{ number_format($box->contribution_count) }} {{ Str::plural('contributor', $box->contribution_count) }}</span>
                                    </div>
                                </div>
                                <div class="pl-creator">
                                    <div class="avatar" style="width:22px;height:22px;font-size:9px;background:{{ $avColor }}">{{ $initials }}</div>
                                    <div>by <b>{{ $box->user?->name ?? 'Anonymous' }}</b></div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="placements-foot">
                    <a class="btn lg" href="{{ route('browse') }}">
                        Browse more campaigns
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>
    @endif

</div>
