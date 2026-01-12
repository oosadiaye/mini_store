@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.enquiries.index') }}" class="text-indigo-600 hover:text-indigo-900">
            ← Back to Enquiries
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Enquiry Details</h2>
                    <p class="text-sm text-gray-500 mt-1">Received {{ $enquiry->created_at->diffForHumans() }}</p>
                </div>
                <form action="{{ route('admin.enquiries.status', $enquiry) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status" onchange="this.form.submit()" class="px-3 py-1 border-2 border-gray-300 rounded text-sm">
                        <option value="pending" {{ $enquiry->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="replied" {{ $enquiry->status === 'replied' ? 'selected' : '' }}>Replied</option>
                        <option value="closed" {{ $enquiry->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Product Info -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Product</h3>
            <div class="flex items-center">
                @if($enquiry->product->primaryImage())
                    <img src="{{ $enquiry->product->primaryImage()->url }}" class="w-16 h-16 object-cover rounded mr-3">
                @endif
                <div>
                    <p class="font-medium text-gray-900">{{ $enquiry->product->name }}</p>
                    <p class="text-sm text-gray-500">SKU: {{ $enquiry->product->sku }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Customer Information</h3>
            <div class="space-y-1">
                <p class="text-sm"><span class="font-medium">Name:</span> {{ $enquiry->customer_name }}</p>
                <p class="text-sm"><span class="font-medium">Email:</span> <a href="mailto:{{ $enquiry->customer_email }}" class="text-indigo-600">{{ $enquiry->customer_email }}</a></p>
                @if($enquiry->customer_phone)
                    <p class="text-sm"><span class="font-medium">Phone:</span> {{ $enquiry->customer_phone }}</p>
                @endif
            </div>
        </div>

        <!-- Message -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Customer Message</h3>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $enquiry->message }}</p>
        </div>

        <!-- Admin Reply -->
        @if($enquiry->admin_reply)
            <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                <h3 class="text-sm font-medium text-green-700 mb-2">Your Reply</h3>
                <p class="text-gray-700 whitespace-pre-wrap mb-2">{{ $enquiry->admin_reply }}</p>
                <p class="text-xs text-gray-500">
                    Replied by {{ $enquiry->repliedBy->name }} on {{ $enquiry->replied_at->format('M d, Y \a\t h:i A') }}
                </p>
            </div>
        @endif

        <!-- Reply Form -->
        @if(!$enquiry->admin_reply)
            <div class="px-6 py-4">
                <form action="{{ route('admin.enquiries.reply', $enquiry) }}" method="POST">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reply to Customer</label>
                    <textarea name="admin_reply" rows="6" required
                        class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500"
                        placeholder="Type your reply here...">{{ old('admin_reply') }}</textarea>
                    @error('admin_reply')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <div class="mt-4">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition">
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
