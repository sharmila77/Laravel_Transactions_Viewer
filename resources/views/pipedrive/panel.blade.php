<!DOCTYPE html>
<html>
<head>
    <title>Stripe Panel</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .nav-link {
            cursor: pointer;
        }
    </style>
</head>
<body>
@php
    $tab = request()->query('tab', 'invoices');
    $personId = request()->query('personId');
@endphp

<div class="container mt-3">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'invoices' ? 'active' : '' }}"
               onclick="changeTab('invoices')">
                Invoices
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'payments' ? 'active' : '' }}"
               onclick="changeTab('payments')">
                Transactions
            </a>
        </li>
    </ul>

    <div class="mt-3">
        {!! $data !!}
    </div>
</div>
<script>
    function changeTab(tab) {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.location.href = url.toString();
    }
</script>
<script type="module">
    import PipedriveAppExtensionsSDK from 'https://cdn.skypack.dev/@pipedrive/app-extensions-sdk';

    const isEmbedded = window !== window.parent;

    if (isEmbedded) {
        const sdk = new PipedriveAppExtensionsSDK();

        sdk.initialize().then(() => {
            console.log('SDK initialized, context:', sdk.context);
        }).catch(err => {
            console.error('SDK initialization error:', err);
            alert('Failed to load Pipedrive panel.');
        });
    } else {
        console.log('Not embedded in Pipedrive, skipping SDK init.');
    }
</script>
</body>
</html>
