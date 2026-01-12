@extends('admin.layout')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Outstanding Payments</h2>
</div>

<outstanding-payments 
    :receivables='@json($receivables->items())'
    :payables='@json($payables->items())'
    :unallocated-payments='@json($unallocatedPayments->items())'
    :customers='@json($customers)'
    :suppliers='@json($suppliers)'
    :receivables-total="{{ $receivables->total() }}"
    :payables-total="{{ $payables->total() }}"
    :unallocated-total="{{ $unallocatedPayments->total() }}"
    :receivables-has-pages="{{ $receivables->hasPages() ? 'true' : 'false' }}"
    :payables-has-pages="{{ $payables->hasPages() ? 'true' : 'false' }}"
    :unallocated-has-pages="{{ $unallocatedPayments->hasPages() ? 'true' : 'false' }}"
    currency-symbol="{{ app('tenant')->data['currency_symbol'] ?? '₦' }}"
    tenant-slug="{{ app('tenant')->slug }}"
    csrf-token="{{ csrf_token() }}"
    initial-tab="{{ request('active_tab', 'receivables') }}"
>
    <template v-slot:receivables-pagination>
        {{ $receivables->appends(['active_tab' => 'receivables'])->links() }}
    </template>
    <template v-slot:payables-pagination>
        {{ $payables->appends(['active_tab' => 'payables'])->links() }}
    </template>
    <template v-slot:unallocated-pagination>
        {{ $unallocatedPayments->appends(['active_tab' => 'unallocated'])->links() }}
    </template>
</outstanding-payments>
</outstanding-payments>
@endsection
