@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Abandoned Carts</h2>
        <span class="text-sm text-gray-500">Carts inactive for over 1 hour</span>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Coupon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Activity</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($abandonedCarts as $cart)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($cart->customer_email)
                                <div class="text-sm font-medium text-gray-900">{{ $cart->customer_email }}</div>
                            @elseif($cart->customer_id)
                                <div class="text-sm font-medium text-gray-900">Customer #{{ $cart->customer_id }}</div>
                            @else
                                <div class="text-sm text-gray-500">Guest ({{ substr($cart->session_id, 0, 8) }}...)</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $cart->total_items }} item(s)
                            <div class="text-xs text-gray-500 mt-1">
                                @foreach($cart->items->take(2) as $item)
                                    <div>{{ $item->product ? $item->product->name : 'Product Unavailable' }}</div>
                                @endforeach
                                @if($cart->items->count() > 2)
                                    <div class="text-gray-400">+{{ $cart->items->count() - 2 }} more</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            ${{ number_format($cart->total, 2) }}
                            @if($cart->discount_amount > 0)
                                <div class="text-xs text-green-600">-${{ number_format($cart->discount_amount, 2) }} discount</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($cart->coupon)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-mono">{{ $cart->coupon->code }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $cart->updated_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-shopping-cart text-4xl mb-3 text-gray-300"></i>
                            <p>No abandoned carts found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $abandonedCarts->links() }}
        </div>
    </div>
</div>
@endsection
