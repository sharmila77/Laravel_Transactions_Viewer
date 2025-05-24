@if (!empty($invoices) && count($invoices))
    <h5>Invoices ({{ count($invoices) }})</h5>
    @foreach ($invoices as $invoice)
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title">Invoice #{{ $invoice['number'] ?? $invoice['id'] }}</h6>
                <p><strong>Amount:</strong> ${{ number_format($invoice['amount_due'] / 100, 2) }}</p>
                <p><strong>Status:</strong> {{ ucfirst($invoice['status']) }}</p>
                <p><strong>Customer:</strong> {{ $invoice['customer'] ?? 'N/A' }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::createFromTimestamp($invoice['created'])->format('Y-m-d H:i:s') }}</p>

                @if (!empty($invoice['hosted_invoice_url']))
                    @if ($invoice['status'] === 'paid')
                        <a href="{{ $invoice['hosted_invoice_url'] }}" class="btn btn-primary btn-sm" target="_blank">
                            View Receipt
                        </a>
                    @else
                        <a href="{{ $invoice['hosted_invoice_url'] }}" class="btn btn-primary btn-sm" target="_blank">
                            View Invoice Payment Page
                        </a>
                    @endif
                @else
                    <span class="text-muted">No action available</span>
                @endif
            </div>
        </div>
    @endforeach
@else
    <div class="alert alert-info">No invoices found for this contact.</div>
@endif
