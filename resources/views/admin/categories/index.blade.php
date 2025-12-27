@extends('admin.layout')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 md:mb-6 gap-3">
    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Categories</h2>
    <div class="flex gap-2 w-full md:w-auto">


        <a href="{{ route('admin.categories.create') }}" class="flex-1 md:flex-none bg-indigo-600 hover:bg-indigo-700 text-white px-4 md:px-6 py-2 rounded-lg transition shadow-md flex items-center justify-center text-sm">
            <i class="fas fa-plus mr-2"></i> Add Category
        </a>
    </div>
</div>



<!-- Desktop Table View -->
<div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parent Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($categories as $category)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($category->image)
                                <img src="{{ tenant_asset($category->image) }}" alt="{{ $category->name }}" class="h-10 w-10 rounded object-cover mr-3">
                            @else
                                <div class="h-10 w-10 bg-gray-200 rounded mr-3 flex items-center justify-center text-gray-400">🏷️</div>
                            @endif
                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $category->parent->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $category->products->count() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($category->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                        @if($category->show_on_storefront)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 ml-1">Storefront</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        No categories found. <a href="{{ route('admin.categories.create') }}" class="text-indigo-600 hover:underline">Create your first category</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Mobile Card View -->
<div class="lg:hidden space-y-3">
    @forelse($categories as $category)
        <div class="bg-white rounded-lg shadow p-3">
            <!-- Card Header -->
            <div class="flex items-start gap-3 mb-3">
                @if($category->image)
                    <img src="{{ tenant_asset($category->image) }}" alt="{{ $category->name }}" class="h-14 w-14 rounded object-cover flex-shrink-0">
                @else
                    <div class="h-14 w-14 bg-gray-200 rounded flex items-center justify-center text-2xl flex-shrink-0">🏷️</div>
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ $category->name }}</h3>
                    <div class="flex flex-wrap gap-1">
                        @if($category->is_active)
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                        @if($category->show_on_storefront)
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Storefront</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Card Body -->
            <div class="space-y-2 py-2 border-t border-gray-100">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Parent Category:</span>
                    <span class="text-gray-900 font-medium">{{ $category->parent->name ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Products:</span>
                    <span class="text-gray-900 font-medium">{{ $category->products->count() }}</span>
                </div>
            </div>
            
            <!-- Card Footer -->
            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.categories.edit', $category) }}" class="flex-1 text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-3 py-2 rounded text-sm font-medium transition">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-sm mb-3">No categories found.</p>
            <a href="{{ route('admin.categories.create') }}" class="text-indigo-600 hover:underline font-medium text-sm">Create your first category</a>
        </div>
    @endforelse
</div>

<div class="mt-4 md:mt-6">
    {{ $categories->links() }}
</div>
@endsection
