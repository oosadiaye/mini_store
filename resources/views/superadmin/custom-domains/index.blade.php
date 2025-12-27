@extends('layouts.superadmin')

@section('header', 'Custom Domain Requests')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested Domain</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($requests as $request)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $request->tenant->name }}</div>
                        <div class="text-sm text-gray-500">{{ $request->tenant->slug }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="http://{{ $request->domain }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900">{{ $request->domain }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($request->status === 'approved')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                        @elseif($request->status === 'rejected')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $request->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if($request->status === 'pending')
                            <form action="{{ route('superadmin.custom-domains.approve', $request->id) }}" method="POST" class="inline-block mr-2">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Approve this domain? Ensure DNS is pointed correctly.')">Approve</button>
                            </form>
                            <form action="{{ route('superadmin.custom-domains.reject', $request->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Reject this request?')">Reject</button>
                            </form>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                        No custom domain requests found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $requests->links() }}
    </div>
</div>
@endsection
