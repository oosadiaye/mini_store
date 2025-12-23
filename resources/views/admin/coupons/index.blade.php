@extends('admin.layout')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Coupons</h2>
        <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition">
            <i class="fas fa-plus mr-2"></i> Create Coupon
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Discount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($coupons as $coupon)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ $coupon->code }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($coupon->type === 'percent')
                                {{ $coupon->value }}% Off
                            @else
                                ${{ number_format($coupon->value, 2) }} Off
                            @endif
                            @if($coupon->min_spend)
                                <div class="text-xs text-gray-500">Min: ${{ number_format($coupon->min_spend, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $coupon->end_date ? $coupon->end_date->format('M d, Y') : 'Never' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete coupon?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-ticket-alt text-4xl mb-3 text-gray-300"></i>
                            <p>No coupons found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $coupons->links() }}
        </div>
    </div>
</div>
@endsection
