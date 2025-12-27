@extends('layouts.superadmin')

@section('header', 'Ticket Categories')

@section('content')
<div class="flex flex-col md:flex-row gap-6">
    <!-- List Categories -->
    <div class="w-full md:w-2/3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Categories</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Assigned Staff</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $category->name }}
                                <p class="text-xs text-gray-500 font-normal">{{ Str::limit($category->description, 50) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($category->assignedStaff)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $category->assignedStaff->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button type="button" onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', '{{ $category->assigned_to }}')" class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>
                                
                                <form action="{{ route('superadmin.ticket-categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No categories found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create/Edit Form -->
    <div class="w-full md:w-1/3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900" id="formTitle">Add Category</h3>
                <button type="button" onclick="resetForm()" id="cancelBtn" class="text-sm text-gray-500 hover:text-gray-700 hidden">Cancel</button>
            </div>
            <div class="p-6">
                <form id="categoryForm" action="{{ route('superadmin.ticket-categories.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" id="name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assign Staff</label>
                            <select name="assigned_to" id="assigned_to" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Start Unassigned --</option>
                                @foreach($staff as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Assigned staff will receive email notifications.</p>
                        </div>

                        <div class="pt-2">
                            <button type="submit" id="submitBtn" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Create Category
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function editCategory(id, name, description, assignedTo) {
        document.getElementById('formTitle').innerText = 'Edit Category';
        document.getElementById('submitBtn').innerText = 'Update Category';
        document.getElementById('cancelBtn').classList.remove('hidden');
        
        const form = document.getElementById('categoryForm');
        form.action = `/superadmin/ticket-categories/${id}`;
        document.getElementById('formMethod').value = 'PUT';
        
        document.getElementById('name').value = name;
        document.getElementById('description').value = description || '';
        document.getElementById('assigned_to').value = assignedTo || '';
    }

    function resetForm() {
        document.getElementById('formTitle').innerText = 'Add Category';
        document.getElementById('submitBtn').innerText = 'Create Category';
        document.getElementById('cancelBtn').classList.add('hidden');
        
        const form = document.getElementById('categoryForm');
        form.action = "{{ route('superadmin.ticket-categories.store') }}";
        document.getElementById('formMethod').value = 'POST';
        
        form.reset();
    }
</script>
@endsection
