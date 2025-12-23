@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Customer Reviews</h2>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Review</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($reviews as $review)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $review->product->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $review->name ?? 'Guest' }}</div>
                    <div class="text-xs text-gray-500">{{ $review->customer ? $review->customer->email : '' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex text-yellow-500">
                        @for($i = 0; $i < $review->rating; $i++)
                            ★
                        @endfor
                        @for($i = $review->rating; $i < 5; $i++)
                            <span class="text-gray-300">★</span>
                        @endfor
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold">{{ $review->title }}</div>
                    <div class="text-sm text-gray-600 truncate max-w-xs">{{ $review->body }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $review->status === 'approved' ? 'bg-green-100 text-green-800' : 
                           ($review->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($review->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('PUT')
                        @if($review->status !== 'approved')
                            <button name="status" value="approved" class="text-green-600 hover:text-green-900 mr-2">Approve</button>
                        @endif
                        @if($review->status !== 'rejected')
                            <button name="status" value="rejected" class="text-red-600 hover:text-red-900 mr-2">Reject</button>
                        @endif
                    </form>
                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this review?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-gray-400 hover:text-gray-600">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No reviews found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
