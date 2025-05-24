@if (!empty($payments) && count($payments))
    <h5>Transactions ({{ count($payments) }})</h5>
    @foreach ($payments as $payment)
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title">Charge #{{ $payment['id'] }}</h6>
                <p class="card-text mb-1"><strong>Amount:</strong> ${{ number_format($payment['amount'] / 100, 2) }}</p>
                <p class="card-text mb-1"><strong>Status:</strong> {{ ucfirst($payment['status']) }}</p>
                @if (!empty($payment['customer']))
                    <p class="card-text mb-1"><strong>Customer:</strong> {{ $payment['customer'] }}</p>
                @endif
                <p class="card-text"><strong>Date:</strong> {{ \Carbon\Carbon::createFromTimestamp($payment['created'])->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    @endforeach
@else
    <div class="alert alert-info">No transactions found for this contact.</div>
@endif
