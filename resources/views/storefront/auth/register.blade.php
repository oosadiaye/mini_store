<x-storefront.layout :config="$config" :menuCategories="$menuCategories">
    <div class="min-h-[70vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-[#0A2540] font-heading">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="{{ route('storefront.login', ['tenant' => app('tenant')->slug]) }}" class="font-medium text-[#0A2540] hover:text-blue-500 transition-colors">
                    sign in to existing account
                </a>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-100">
                <form class="space-y-6" action="{{ route('storefront.register.post', ['tenant' => app('tenant')->slug]) }}" method="POST">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Full Name
                        </label>
                        <div class="mt-1">
                            <input id="name" name="name" type="text" autocomplete="name" required 
                                class="appearance-none block w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#0A2540] focus:border-[#0A2540] sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email address
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                class="appearance-none block w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#0A2540] focus:border-[#0A2540] sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="new-password" required 
                                class="appearance-none block w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#0A2540] focus:border-[#0A2540] sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm Password
                        </label>
                        <div class="mt-1">
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                                class="appearance-none block w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#0A2540] focus:border-[#0A2540] sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#0A2540] hover:bg-[#1a3a5a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0A2540] transition-colors">
                            Register
                        </button>
                    </div>
                </form>

                @if($errors->any())
                    <div class="mt-4 p-4 rounded-md bg-red-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    There were errors with your submission
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-storefront.layout>
