<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Withdrawal Sent</title>
<style>
  body { margin: 0; padding: 0; background: #F3F1EB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E6E3DC; }
  .header { background: #15140F; padding: 28px 32px; }
  .logo-name { color: #FAFAF7; font-size: 15px; font-weight: 600; letter-spacing: -0.01em; }
  .body { padding: 32px; }
  h2 { margin: 0 0 8px; font-size: 20px; font-weight: 400; color: #15140F; letter-spacing: -0.01em; font-family: Georgia, 'Times New Roman', serif; }
  p { margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #6B6862; }
  .status-badge { display: inline-block; background: #E6F1EB; color: #154F3A; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 5px 12px; border-radius: 99px; border: 1px solid #90C7A9; margin-bottom: 20px; }
  .amount-band { background: #E6F1EB; border: 1px solid #90C7A9; border-radius: 10px; padding: 20px 24px; margin-bottom: 24px; text-align: center; }
  .amount-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #154F3A; margin-bottom: 4px; }
  .amount-value { font-size: 36px; font-weight: 700; color: #1B6B4E; letter-spacing: -0.02em; line-height: 1; }
  .amount-sub { font-size: 12px; color: #2E8E60; margin-top: 4px; }
  .meta-table { background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 8px; padding: 0 16px; margin-bottom: 24px; }
  .meta-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #E6E3DC; font-size: 13px; }
  .meta-row:last-child { border-bottom: none; }
  .meta-label { color: #9C998F; }
  .meta-value { color: #15140F; font-weight: 500; text-align: right; max-width: 60%; }
  .source-badge { display: inline-block; background: #FAFAF7; border: 1px solid #E6E3DC; border-radius: 99px; font-size: 11px; font-weight: 600; padding: 2px 10px; color: #6B6862; }
  .info-box { background: #E6F1EB; border: 1px solid #90C7A9; border-radius: 8px; padding: 14px 16px; margin-bottom: 24px; }
  .info-box p { font-size: 13px; color: #154F3A; margin: 0; line-height: 1.5; }
  .cta { display: inline-block; background: #15140F; color: #FFFFFF; text-decoration: none; padding: 12px 24px; border-radius: 7px; font-size: 14px; font-weight: 600; }
  .footer { padding: 20px 32px; border-top: 1px solid #E6E3DC; }
  .footer p { font-size: 12px; color: #9C998F; margin: 0; }
  .footer a { color: #1B6B4E; text-decoration: none; }
  @media only screen and (max-width: 600px) {
    .wrap { margin: 0 !important; border-radius: 0 !important; border-left: none !important; border-right: none !important; }
    .header { padding: 20px !important; }
    .body { padding: 20px !important; }
    .footer { padding: 16px 20px !important; }
    .amount-value { font-size: 28px !important; }
    .cta { display: block !important; text-align: center !important; }
    .meta-row { flex-direction: column !important; gap: 2px !important; }
    .meta-value { text-align: left !important; max-width: 100% !important; }
  }
</style>
</head>
<body>
@php
    use App\Models\MoneyBoxWithdrawal;
    $isMoneyBox  = $withdrawal instanceof MoneyBoxWithdrawal;
    $firstName   = explode(' ', $withdrawal->user?->name ?? 'there')[0];
    $sourceName  = $isMoneyBox ? ($withdrawal->moneyBox?->title ?? 'Your PiggyBox') : 'PiggyWallet';
    $sourceLabel = $isMoneyBox ? 'PiggyBox' : 'PiggyWallet';
    $netFormatted   = $withdrawal->formatNetAmount();
    $grossFormatted = $withdrawal->formatAmount();
    $feeFormatted   = $withdrawal->formatFee();
    $account = $withdrawal->withdrawalAccount;
@endphp
<div class="wrap">
  <div class="header">
    <table cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
      <tr>
        <td valign="middle" style="padding:0;">
          <span style="display:inline-block;width:32px;height:32px;background:#1B6B4E;border-radius:8px;text-align:center;line-height:32px;font-weight:700;font-size:16px;color:#FAFAF7;font-family:Arial,sans-serif;">M</span>
        </td>
        <td valign="middle" style="padding:0 0 0 10px;">
          <span class="logo-name">MyPiggyBox</span>
        </td>
      </tr>
    </table>
  </div>

  <div class="body">
    <div class="status-badge">&#10003; Sent</div>
    <h2>Your money is on its way, {{ $firstName }}!</h2>
    <p>Your withdrawal has been confirmed and the funds have been sent to your mobile money account.</p>

    <div class="amount-band">
      <div class="amount-label">Amount sent</div>
      <div class="amount-value">{{ $netFormatted }}</div>
      <div class="amount-sub">Net after fees · Ref: {{ $withdrawal->reference }}</div>
    </div>

    <div class="meta-table">
      <div class="meta-row">
        <span class="meta-label">Reference</span>
        <span class="meta-value" style="font-family:monospace;font-size:12px;">{{ $withdrawal->reference }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-label">Source</span>
        <span class="meta-value">
          <span class="source-badge">{{ $sourceLabel }}</span>&nbsp;
          {{ $sourceName }}
        </span>
      </div>
      <div class="meta-row">
        <span class="meta-label">Gross amount</span>
        <span class="meta-value">{{ $grossFormatted }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-label">Fee</span>
        <span class="meta-value">{{ $feeFormatted }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-label">Net amount sent</span>
        <span class="meta-value" style="color:#1B6B4E;font-weight:700;">{{ $netFormatted }}</span>
      </div>
      @if($account)
      <div class="meta-row">
        <span class="meta-label">Sent to</span>
        <span class="meta-value">{{ $account->account_name }}<br><span style="font-family:monospace;font-size:12px;color:#9C998F;">{{ $account->account_number }}</span></span>
      </div>
      @endif
      @if($withdrawal->transaction_reference)
      <div class="meta-row">
        <span class="meta-label">Transaction ID</span>
        <span class="meta-value" style="font-family:monospace;font-size:12px;">{{ $withdrawal->transaction_reference }}</span>
      </div>
      @endif
      <div class="meta-row">
        <span class="meta-label">Disbursed on</span>
        <span class="meta-value">{{ ($withdrawal->disbursed_at ?? now())->format('M j, Y · g:ia') }}</span>
      </div>
    </div>

    <div class="info-box">
      <p>Funds have been sent to your registered mobile money account. Please allow up to 24 hours for the transfer to reflect on your end. If you have not received funds after 24 hours, please <a href="mailto:{{ config('mail.from.address') }}" style="color:#154F3A;font-weight:600;">contact support</a> with your reference number.</p>
    </div>

    <a href="{{ route('dashboard') }}" class="cta">View Withdrawal →</a>
  </div>

  <div class="footer">
    <p>You received this because a withdrawal was disbursed on <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>. Questions? <a href="mailto:{{ config('mail.from.address') }}">Contact support</a>.</p>
  </div>
</div>
</body>
</html>