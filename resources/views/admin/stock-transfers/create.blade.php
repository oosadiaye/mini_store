@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Create Stock Transfer</h2>
            <p class="text-sm text-gray-600 mt-1">Transfer inventory between warehouses</p>
        </div>
        <a href="{{ route('admin.stock-transfers.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Transfers
        </a>
    </div>

    <stock-transfer-form
        :initial-products='@json($products)'
        :initial-warehouses='@json($warehouses)'
        tenant-slug="{{ $tenant->slug }}">
    </stock-transfer-form>
</div>


@endsection
