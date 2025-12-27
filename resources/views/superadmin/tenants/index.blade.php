@extends('layouts.superadmin')

@section('header', 'Tenant Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Tenants</h2>
        <p class="text-gray-500 text-sm">Manage enrolled stores and access their dashboards.</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-visible">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-4 border-b border-gray-100">ID</th>
                <th class="p-4 border-b border-gray-100">Store Name</th>
                <th class="p-4 border-b border-gray-100">Domains</th>
                <th class="p-4 border-b border-gray-100">Created At</th>
                <th class="p-4 border-b border-gray-100 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tenants as $tenant)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 text-gray-500 text-xs font-mono">
                    {{ $tenant->id }}
                </td>
                <td class="p-4">
                    <div class="font-medium text-gray-900">
                        <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="hover:text-blue-600 hover:underline">
                            {{ $tenant->data['store_name'] ?? $tenant->name ?? 'N/A' }}
                        </a>
                    </div>
                    <div class="text-xs text-gray-500">{{ $tenant->email ?? '' }}</div>
                </td>
                <td class="p-4 text-gray-700 text-sm">
                    @foreach($tenant->domains as $domain)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $domain->domain }}
                        </span>
                    @endforeach
                </td>
                <td class="p-4 text-gray-500 text-sm">
                    {{ $tenant->created_at->format('M d, Y') }}
                </td>
                <td class="p-4 text-right">
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Actions
                            <svg class="-mr-1 ml-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute right-0 z-[60] mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" 
                             style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('superadmin.tenants.impersonate', $tenant) }}" target="_blank" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">
                                    Login as Client
                                </a>
                                <a href="#" @click.prevent="$dispatch('open-upgrade-modal', { id: '{{ $tenant->id }}', plan: '{{ $tenant->plan_id }}', name: '{{ $tenant->name ?? 'Store' }}' }); open = false" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">
                                    Upgrade Plan
                                </a>
                                
                                <form action="{{ route('superadmin.tenants.suspend', $tenant) }}" method="POST" x-show="!{{ $tenant->is_suspended ? 'true' : 'false' }}">
                                    @csrf
                                    <button type="submit" class="text-yellow-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                                        Suspend Tenant
                                    </button>
                                </form>

                                <form action="{{ route('superadmin.tenants.unsuspend', $tenant) }}" method="POST" x-show="{{ $tenant->is_suspended ? 'true' : 'false' }}">
                                    @csrf
                                    <button type="submit" class="text-green-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                                        Activate Tenant
                                    </button>
                                </form>

                                <form action="{{ route('superadmin.tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('Are you sure you want to DELETE this tenant? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                                        Delete Tenant
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">
                    No tenants found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

    <!-- Upgrade/Downgrade Modal -->
    <div x-data="{ open: false, tenantId: '', currentPlan: '', tenantName: '' }" 
         @open-upgrade-modal.window="open = true; tenantId = $event.detail.id; currentPlan = $event.detail.plan; tenantName = $event.detail.name"
         x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" x-transition.scale class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="'{{ url('superadmin/tenants') }}/' + tenantId + '/plan'" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Change Plan for <span x-text="tenantName"></span>
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Select the new subscription plan for this tenant. This will update their feature access immediately.
                                    </p>
                                    
                                    <div class="mt-4">
                                        <label for="plan_id" class="block text-sm font-medium text-gray-700">Subscription Plan</label>
                                        <select id="plan_id" name="plan_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" x-model="currentPlan">
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}">{{ $plan->name }} - {{ $plan->price }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Update Plan
                        </button>
                        <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
