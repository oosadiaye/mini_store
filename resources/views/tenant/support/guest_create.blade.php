<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Support - {{ $tenant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-gray-800 p-8 rounded-xl border border-gray-700 shadow-2xl">
        <div>
            @if(!empty($tenant->data['logo']))
                <img class="mx-auto h-12 w-auto" src="{{ '/storage/' . $tenant->data['logo'] }}" alt="{{ $tenant->name }}">
            @else
                <h2 class="mx-auto text-3xl font-bold text-center text-blue-500">{{ $tenant->name }}</h2>
            @endif
            <h2 class="mt-6 text-center text-2xl font-bold tracking-tight">Contact Support</h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Please provide details below. We'll get back to you shortly.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-md text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('tenant.support.guest.store', ['tenant' => $tenant->slug]) }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                    <input id="name" name="name" type="text" required class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm" placeholder="Your Name">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm" placeholder="you@example.com">
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-300">Category</label>
                    <select id="category_id" name="category_id" class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-300">Subject</label>
                    <input id="subject" name="subject" type="text" required class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm" placeholder="Brief summary of issue">
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-300">Priority</label>
                    <select id="priority" name="priority" class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-300">Message</label>
                    <textarea id="message" name="message" rows="4" required class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm" placeholder="Describe your issue detailed..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-between space-x-4">
                 <a href="{{ route('tenant.login', ['tenant' => $tenant->slug]) }}" class="text-sm font-medium text-gray-400 hover:text-white transition">Back to Login</a>
                <button type="submit" class="group relative flex justify-center py-2 px-6 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    Submit Ticket
                </button>
            </div>
        </form>
    </div>
</body>
</html>
