<x-layouts.app :title="__('Withdrawals')">
    @php
        $sym = auth()->user()->country?->currency_symbol ?? '₵';
        $statusOptions = [
            'pending' => 'Pending',
            'in_review' => 'In Review',
            'approved' => 'Approved',
            'disbursed' => 'Disbursed',
            'rejected' => 'Rejected',
            'failed' => 'Failed',
        ];
    @endphp

    <div class="page-wrap max-w-[1280px]">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6">
            <div>
                <h1 class="page-title" style="font-size:1.875rem;">Withdrawals</h1>
                <p class="text-[13px] text-[#6B6862] mt-1">Track every PiggyBox and Piggy Wallet withdrawal request.</p>
            </div>
            <a href="{{ route('settings.withdrawal-accounts') }}" wire:navigate class="btn">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Withdrawal accounts
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
            <div class="stat-card">
                <div class="stat-label">Requested</div>
                <div class="stat-value">{{ $sym }}{{ number_format($stats['total_requested'], 2) }}</div>
                <div class="stat-delta">Gross amount reserved</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Platform fees</div>
                <div class="stat-value">{{ $sym }}{{ number_format($stats['total_fees'], 2) }}</div>
                <div class="stat-delta">Kept from withdrawals</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Net payout</div>
                <div class="stat-value text-primary-600">{{ $sym }}{{ number_format($stats['total_net'], 2) }}</div>
                <div class="stat-delta">Sent to accounts</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">In progress</div>
                <div class="stat-value tnum">{{ number_format($stats['pending_count']) }}</div>
                <div class="stat-delta">Pending, review, approved</div>
            </div>
        </div>

        <div class="tabs mb-4">
            <a href="{{ route('withdrawals.index') }}" wire:navigate class="tab {{ blank($status) ? 'active' : '' }}">All</a>
            @foreach($statusOptions as $value => $label)
                <a href="{{ route('withdrawals.index', ['status' => $value]) }}" wire:navigate class="tab {{ $status === $value ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-title">Withdrawal requests</div>
                <span class="pill">{{ $withdrawals->count() }} {{ Str::plural('request', $withdrawals->count()) }}</span>
            </div>

            @if($withdrawals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Source</th>
                                <th class="num">Requested</th>
                                <th class="num">Fee</th>
                                <th class="num">Net</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawals as $item)
                                @php
                                    $pillClass = match($item['status_value']) {
                                        'disbursed' => 'pill-ok',
                                        'rejected', 'failed' => 'pill-danger',
                                        'approved' => 'pill-info',
                                        default => 'pill-warn',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <code class="text-[11.5px] font-mono text-[#6B6862]">{{ $item['reference'] }}</code>
                                        @if($item['notes_count'] > 0)
                                            <div class="tiny mt-0.5">{{ $item['notes_count'] }} {{ Str::plural('comment', $item['notes_count']) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-medium text-[#15140F]">{{ $item['source_title'] }}</div>
                                        <div class="tiny">{{ $item['type_label'] }}</div>
                                    </td>
                                    <td class="num font-semibold text-[#15140F]">{{ $item['amount_display'] }}</td>
                                    <td class="num text-[#6B6862]">{{ $item['fee_display'] }}</td>
                                    <td class="num text-primary-600 font-semibold">{{ $item['net_display'] }}</td>
                                    <td><span class="pill {{ $pillClass }}"><span class="pill-dot"></span>{{ $item['status_label'] }}</span></td>
                                    <td class="text-[#6B6862] text-[12px]">{{ $item['requested_at']->format('M d, Y') }}</td>
                                    <td class="text-right">
                                        <a href="{{ $item['details_url'] }}" wire:navigate class="btn btn-sm">Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card-body text-center py-12">
                    <div class="text-[14px] font-medium text-[#15140F] mb-1">No withdrawals yet</div>
                    <p class="tiny">When you request money from a PiggyBox or Piggy Wallet, it will appear here.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
