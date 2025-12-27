@extends('install.layout')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Server Requirements</h2>
    <p class="text-gray-600 mb-6">Checking if your server meets the requirements for running the application.</p>
</div>

<div class="space-y-4 mb-8">
    @foreach($requirements as $label => $met)
    <div class="flex items-center justify-between p-3 rounded-lg {{ $met ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
        <span class="font-medium {{ $met ? 'text-green-800' : 'text-red-800' }}">{{ $label }}</span>
        @if($met)
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        @else
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        @endif
    </div>
    @endforeach
</div>

<div class="flex justify-end">
    @if($allMet)
        <a href="{{ route('install.environment') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            Next: Environment Configuration &rarr;
        </a>
    @else
        <button disabled class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-400 cursor-not-allowed">
            Please fix issues to continue
        </button>
        <a href="{{ route('install.requirements') }}" class="ml-4 inline-flex items-center px-4 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Check Again
        </a>
    @endif
</div>
@endsection
