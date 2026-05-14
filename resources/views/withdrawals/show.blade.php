<x-layouts.app :title="$item['reference']">
    @php
        $pillClass = match($item['status_value']) {
            'disbursed' => 'pill-ok',
            'rejected', 'failed' => 'pill-danger',
            'approved' => 'pill-info',
            default => 'pill-warn',
        };
    @endphp

    <div class="page-wrap max-w-[980px]">
        <div class="mb-4">
            <a href="{{ route('withdrawals.index') }}" wire:navigate class="btn btn-ghost btn-sm text-[#6B6862]">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                All withdrawals
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center flex-wrap gap-2 mb-2">
                    <span class="pill {{ $pillClass }}"><span class="pill-dot"></span>{{ $item['status_label'] }}</span>
                    <span class="pill pill-muted">{{ $item['type_label'] }}</span>
                </div>
                <h1 class="page-title" style="font-size:1.875rem;">{{ $item['reference'] }}</h1>
                <p class="text-[13px] text-[#6B6862] mt-1">
                    Requested from <a href="{{ $item['source_url'] }}" wire:navigate class="text-primary-600 hover:underline">{{ $item['source_title'] }}</a>
                    on {{ $item['requested_at']->format('M d, Y g:ia') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-4">
            <div class="space-y-4">
                <div class="card">
                    <div class="card-head"><div class="card-title">Payout breakdown</div></div>
                    <div class="card-body space-y-3 text-[13px]">
                        <div class="flex justify-between gap-4">
                            <span class="text-[#6B6862]">Withdrawal amount reserved from balance</span>
                            <span class="font-semibold text-[#15140F] tnum">{{ $item['amount_display'] }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-[#6B6862]">MyPiggyBox platform fee</span>
                            <span class="font-semibold text-red-600 tnum">- {{ $item['fee_display'] }}</span>
                        </div>
                        <div class="flex justify-between gap-4 pt-3 border-t border-[#E6E3DC]">
                            <span class="font-medium text-[#15140F]">Net amount sent to your account</span>
                            <span class="font-semibold text-primary-600 tnum">{{ $item['net_display'] }}</span>
                        </div>
                        <p class="tiny pt-1">
                            The full withdrawal amount is deducted from your available balance. The platform fee is retained by MyPiggyBox, and only the net amount is paid out.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Comments</div>
                        <span class="pill">{{ $notes->count() }}</span>
                    </div>
                    <div class="card-body space-y-4">
                        @if($withdrawal->user_note)
                            <div class="rounded-[8px] border border-[#E6E3DC] bg-[#FAFAF7] p-3">
                                <div class="text-[12px] font-medium text-[#15140F]">Original request note</div>
                                <p class="text-[13px] text-[#6B6862] mt-1 whitespace-pre-line">{{ $withdrawal->user_note }}</p>
                            </div>
                        @endif

                        @forelse($notes as $note)
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full {{ $note->is_admin ? 'bg-[#15140F] text-white' : 'bg-primary-600 text-white' }} grid place-items-center text-[11px] font-semibold flex-none">
                                    {{ $note->user?->initials() ?? '?' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center flex-wrap gap-2">
                                        <span class="text-[13px] font-medium text-[#15140F]">{{ $note->user?->name ?? 'User' }}</span>
                                        <span class="pill {{ $note->is_admin ? 'pill-info' : 'pill-muted' }}">{{ $note->is_admin ? 'Admin' : 'Owner' }}</span>
                                        <span class="tiny">{{ $note->created_at->format('M d, Y g:ia') }}</span>
                                    </div>
                                    <p class="text-[13px] text-[#6B6862] mt-1 whitespace-pre-line">{{ $note->note }}</p>
                                </div>
                            </div>
                        @empty
                            @unless($withdrawal->user_note)
                                <p class="tiny">No comments yet.</p>
                            @endunless
                        @endforelse

                        <form method="POST" action="{{ route('withdrawals.notes.store', [$item['type'], $withdrawal->id]) }}" class="pt-3 border-t border-[#E6E3DC] space-y-2">
                            @csrf
                            <label for="note" class="text-[13px] font-medium text-[#6B6862]">Add a comment</label>
                            <textarea id="note" name="note" rows="3" required class="@error('note') border-red-400 ring-1 ring-red-400/20 @enderror" placeholder="Ask a question or add context for the admin team.">{{ old('note') }}</textarea>
                            @error('note')
                                <p class="text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="btn btn-primary">Post comment</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="card">
                    <div class="card-head"><div class="card-title">Status details</div></div>
                    <div class="card-body space-y-3 text-[13px]">
                        <div>
                            <div class="tiny">Payout account</div>
                            <div class="font-medium text-[#15140F] mt-0.5">{{ $item['account'] }}</div>
                        </div>
                        <div>
                            <div class="tiny">Processed by</div>
                            <div class="font-medium text-[#15140F] mt-0.5">{{ $withdrawal->processedBy?->name ?? 'Not processed yet' }}</div>
                        </div>
                        <div>
                            <div class="tiny">Processed at</div>
                            <div class="font-medium text-[#15140F] mt-0.5">{{ $withdrawal->processed_at?->format('M d, Y g:ia') ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="tiny">Disbursed at</div>
                            <div class="font-medium text-[#15140F] mt-0.5">{{ $withdrawal->disbursed_at?->format('M d, Y g:ia') ?? '—' }}</div>
                        </div>
                        @if($withdrawal->transaction_reference)
                            <div>
                                <div class="tiny">Transaction reference</div>
                                <code class="text-[12px] text-[#15140F] font-mono">{{ $withdrawal->transaction_reference }}</code>
                            </div>
                        @endif
                        @if($withdrawal->rejection_reason)
                            <div class="rounded-[8px] border border-red-200 bg-red-50 p-3">
                                <div class="text-[12px] font-medium text-red-800">Rejection reason</div>
                                <p class="text-[13px] text-red-700 mt-1">{{ $withdrawal->rejection_reason }}</p>
                            </div>
                        @endif
                        @if($withdrawal->failure_reason)
                            <div class="rounded-[8px] border border-red-200 bg-red-50 p-3">
                                <div class="text-[12px] font-medium text-red-800">Failure reason</div>
                                <p class="text-[13px] text-red-700 mt-1">{{ $withdrawal->failure_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
