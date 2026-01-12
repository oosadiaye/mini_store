@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Product Enquiries</h2>
            <p class="text-sm text-gray-500 mt-1">Manage customer product questions</p>
        </div>
        @if($pendingCount > 0)
            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                {{ $pendingCount }} Pending
            </span>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or message..." 
                class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            
            <select name="status" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>

            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Enquiries Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($enquiries as $enquiry)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $enquiry->customer_name }}</div>
                        <div class="text-xs text-gray-500">{{ $enquiry->customer_email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $enquiry->product->name }}</div>
                        <div class="text-xs text-gray-500">SKU: {{ $enquiry->product->sku }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700 max-w-xs truncate">{{ $enquiry->message }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($enquiry->status === 'pending')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($enquiry->status === 'replied')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Replied</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Closed</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $enquiry->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-question-circle text-4xl mb-3 text-gray-300"></i>
                        <p>No enquiries found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $enquiries->links() }}
        </div>
    </div>
</div>
@endsection
