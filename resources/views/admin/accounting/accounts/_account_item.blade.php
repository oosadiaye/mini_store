<li class="border-b border-gray-50 last:border-0 pb-2">
    <div class="flex items-center justify-between group py-2 hover:bg-gray-50 rounded px-2 transition">
        <div class="flex items-center" style="padding-left: {{ $level * 20 }}px">
            <span class="font-mono text-gray-500 mr-3 text-sm">{{ $account->account_code }}</span>
            <span class="font-medium text-gray-800 {{ $level == 0 ? 'text-base font-bold' : 'text-sm' }}">{{ $account->account_name }}</span>
            <span class="ml-3 px-2 py-0.5 rounded text-xs lowercase 
                {{ $account->account_type == 'asset' ? 'bg-green-100 text-green-800' : '' }}
                {{ $account->account_type == 'liability' ? 'bg-red-100 text-red-800' : '' }}
                {{ $account->account_type == 'equity' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $account->account_type == 'revenue' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $account->account_type == 'expense' ? 'bg-gray-100 text-gray-800' : '' }}
            ">
                {{ $account->account_type }}
            </span>
        </div>
        <div class="opacity-0 group-hover:opacity-100 transition flex space-x-2">
            <a href="{{ route('admin.accounts.edit', $account->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">Edit</a>
        </div>
    </div>

    @if($account->children->count() > 0)
        <ul class="space-y-1 mt-1 border-l border-gray-100 ml-4">
            @foreach($account->children as $child)
                @include('admin.accounting.accounts._account_item', ['account' => $child, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
