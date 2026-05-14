<?php

namespace App\Http\Controllers;

use App\Models\MoneyBoxWithdrawal;
use App\Models\PiggyBoxWithdrawal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $moneyBoxWithdrawals = MoneyBoxWithdrawal::query()
            ->where('user_id', auth()->id())
            ->when($status, fn ($query) => $query->where('status', $status))
            ->with(['moneyBox', 'withdrawalAccount', 'notes'])
            ->latest()
            ->get()
            ->map(fn (MoneyBoxWithdrawal $withdrawal) => $this->present($withdrawal, 'money-box'));

        $piggyBoxWithdrawals = PiggyBoxWithdrawal::query()
            ->where('user_id', auth()->id())
            ->when($status, fn ($query) => $query->where('status', $status))
            ->with(['piggyBox', 'withdrawalAccount', 'notes'])
            ->latest()
            ->get()
            ->map(fn (PiggyBoxWithdrawal $withdrawal) => $this->present($withdrawal, 'piggy-wallet'));

        $withdrawals = $moneyBoxWithdrawals
            ->concat($piggyBoxWithdrawals)
            ->sortByDesc('requested_at')
            ->values();

        $allWithdrawals = $this->allWithdrawals();

        $stats = [
            'total_requested' => $allWithdrawals->sum('amount'),
            'total_fees' => $allWithdrawals->sum('fee'),
            'total_net' => $allWithdrawals->sum('net_amount'),
            'pending_count' => $allWithdrawals->whereIn('status_value', ['pending', 'in_review', 'approved'])->count(),
        ];

        return view('withdrawals.index', compact('withdrawals', 'stats', 'status'));
    }

    public function show(string $type, int $withdrawal)
    {
        $record = $this->resolveWithdrawal($type, $withdrawal);

        $record->load(['withdrawalAccount', 'processedBy', 'notes.user']);

        if ($type === 'money-box') {
            $record->load('moneyBox');
        } else {
            $record->load('piggyBox');
        }

        return view('withdrawals.show', [
            'withdrawal' => $record,
            'item' => $this->present($record, $type),
            'notes' => $record->notes()->with('user')->oldest()->get(),
        ]);
    }

    public function storeNote(Request $request, string $type, int $withdrawal)
    {
        $record = $this->resolveWithdrawal($type, $withdrawal);

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
        ]);

        $record->notes()->create([
            'user_id' => auth()->id(),
            'note' => $validated['note'],
            'is_admin' => false,
        ]);

        return redirect()
            ->route('withdrawals.show', [$type, $record->id])
            ->with('success', 'Comment added.');
    }

    private function resolveWithdrawal(string $type, int $id): Model
    {
        abort_unless(in_array($type, ['money-box', 'piggy-wallet'], true), 404);

        $model = $type === 'money-box'
            ? MoneyBoxWithdrawal::class
            : PiggyBoxWithdrawal::class;

        return $model::query()
            ->where('user_id', auth()->id())
            ->findOrFail($id);
    }

    private function allWithdrawals(): Collection
    {
        $moneyBoxWithdrawals = MoneyBoxWithdrawal::query()
            ->where('user_id', auth()->id())
            ->get()
            ->map(fn (MoneyBoxWithdrawal $withdrawal) => $this->present($withdrawal, 'money-box'));

        $piggyBoxWithdrawals = PiggyBoxWithdrawal::query()
            ->where('user_id', auth()->id())
            ->get()
            ->map(fn (PiggyBoxWithdrawal $withdrawal) => $this->present($withdrawal, 'piggy-wallet'));

        return $moneyBoxWithdrawals->concat($piggyBoxWithdrawals);
    }

    private function present(MoneyBoxWithdrawal|PiggyBoxWithdrawal $withdrawal, string $type): array
    {
        $source = $type === 'money-box' ? $withdrawal->moneyBox : $withdrawal->piggyBox;
        $sourceTitle = $source?->title ?? ($type === 'money-box' ? 'PiggyBox' : 'Piggy Wallet');
        $sourceUrl = $type === 'money-box' && $source
            ? route('money-boxes.show', $source)
            : route('piggy.my-piggy-box');

        return [
            'id' => $withdrawal->id,
            'type' => $type,
            'type_label' => $type === 'money-box' ? 'PiggyBox' : 'Piggy Wallet',
            'source_title' => $sourceTitle,
            'source_url' => $sourceUrl,
            'reference' => $withdrawal->reference,
            'status' => $withdrawal->status,
            'status_value' => $withdrawal->status->value,
            'status_label' => $withdrawal->status->label(),
            'amount' => (float) $withdrawal->amount,
            'fee' => (float) $withdrawal->fee,
            'net_amount' => (float) $withdrawal->net_amount,
            'amount_display' => $withdrawal->formatAmount(),
            'fee_display' => $withdrawal->formatFee(),
            'net_display' => $withdrawal->formatNetAmount(),
            'account' => $withdrawal->withdrawalAccount?->getDisplayName() ?? 'No account',
            'requested_at' => $withdrawal->created_at,
            'processed_at' => $withdrawal->processed_at,
            'disbursed_at' => $withdrawal->disbursed_at,
            'notes_count' => $withdrawal->notes_count ?? $withdrawal->notes?->count() ?? 0,
            'details_url' => route('withdrawals.show', [$type, $withdrawal->id]),
        ];
    }
}
