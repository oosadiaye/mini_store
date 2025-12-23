@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Chart of Accounts</h3>
            <p class="text-xs text-gray-500">Manage your financial accounts.</p>
        </div>
        
        <div class="flex items-center space-x-2">
            <!-- Search Form -->
            <form action="{{ route('admin.accounts.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search Code or Name..." 
                    class="pl-8 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 w-64">
                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                @if(request('sort_by'))
                    <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
                @endif
            </form>

            <a href="{{ route('admin.accounts.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
                + Add Account
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @php
                        $sortDir = request('sort_dir') === 'asc' ? 'desc' : 'asc';
                    @endphp
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('admin.accounts.index', ['sort_by' => 'account_code', 'sort_dir' => $sortDir, 'search' => request('search')]) }}" class="flex items-center hover:text-gray-700">
                            Code
                            @if(request('sort_by') === 'account_code')
                                <span class="ml-1">{!! request('sort_dir') === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('admin.accounts.index', ['sort_by' => 'account_name', 'sort_dir' => $sortDir, 'search' => request('search')]) }}" class="flex items-center hover:text-gray-700">
                            Name
                            @if(request('sort_by') === 'account_name')
                                <span class="ml-1">{!! request('sort_dir') === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('admin.accounts.index', ['sort_by' => 'account_type', 'sort_dir' => $sortDir, 'search' => request('search')]) }}" class="flex items-center hover:text-gray-700">
                            Type
                            @if(request('sort_by') === 'account_type')
                                <span class="ml-1">{!! request('sort_dir') === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent Account</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($accounts as $account)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $account->account_code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">{{ $account->account_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 uppercase">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ match($account->account_type) {
                                    'asset' => 'bg-green-100 text-green-800',
                                    'liability' => 'bg-red-100 text-red-800',
                                    'equity' => 'bg-blue-100 text-blue-800',
                                    'revenue' => 'bg-purple-100 text-purple-800',
                                    'expense' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800'
                                } }}">
                                {{ $account->account_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($account->parent)
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">{{ $account->parent->account_code }} - {{ $account->parent->account_name }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                            {{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($account->balance, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.accounts.show', $account->id) }}" class="text-teal-600 hover:text-teal-900 mr-3 font-bold" title="View Ledger">Ledger</a>
                            <a href="{{ route('admin.accounts.edit', $account->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            
                            <a href="{{ route('admin.accounts.copy', $account->id) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="Copy Account">Copy</a>

                            <form action="{{ route('admin.accounts.destroy', $account->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this account?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                            No accounts found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $accounts->links() }}
    </div>
</div>
@endsection
