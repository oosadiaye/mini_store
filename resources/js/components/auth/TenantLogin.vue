<template>
    <div class="font-sans antialiased bg-gray-950 text-white min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Decoration -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute top-[-20%] right-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-20%] left-[-10%] w-[50%] h-[50%] bg-purple-600/10 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <a href="/">
                    <div v-if="tenant && tenantLogoUrl" class="bg-white/10 p-3 rounded-xl backdrop-blur-sm border border-white/10">
                        <img :src="tenantLogoUrl" :alt="tenant.name" class="h-16 w-auto object-contain">
                    </div>
                    <div v-else class="bg-white/10 p-3 rounded-full backdrop-blur-sm border border-white/10">
                        <svg class="h-10 w-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </a>
            </div>
            
            <h2 class="text-center text-3xl font-bold tracking-tight text-white">
                {{ tenant ? tenant.name : 'Admin Portal' }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                {{ tenant ? 'Sign in to your dashboard' : 'Secure access for platform administrators' }}
            </p>
        </div>

        <div class="relative z-10 mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 py-8 px-4 shadow-2xl rounded-2xl sm:px-10">
                
                <!-- Session Status -->
                <div v-if="status" class="mb-6 rounded-lg bg-green-500/10 border border-green-500/20 p-4 text-green-400 text-sm">
                    {{ status }}
                </div>

                <!-- Validation Errors -->
                <div v-if="errors && Object.keys(errors).length > 0" class="mb-6 rounded-lg bg-red-500/10 border border-red-500/20 p-4">
                    <ul class="list-disc list-inside text-sm text-red-400">
                        <li v-for="(error, key) in errors" :key="key">{{ error[0] || error }}</li>
                    </ul>
                </div>

                <form class="space-y-6" :action="targetRoute" method="POST">
                    <input type="hidden" name="_token" :value="csrfToken">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300">Email Address</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required v-model="form.email" autofocus
                                class="appearance-none block w-full px-3 py-2 bg-gray-900/50 border-2 border-gray-700 rounded-lg shadow-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm text-white transition-colors"
                                placeholder="you@example.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                        <div class="mt-1 relative">
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password" v-model="form.password"
                                class="appearance-none block w-full px-3 py-2 bg-gray-900/50 border-2 border-gray-700 rounded-lg shadow-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm text-white transition-colors pr-10">
                             <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-300 focus:outline-none">
                                <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox" v-model="form.remember"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-700 bg-gray-900 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-400">
                                Remember me
                            </label>
                        </div>

                        <div v-if="routes.passwordRequest" class="text-sm">
                            <a :href="routes.passwordRequest" class="font-medium text-blue-400 hover:text-blue-300 transition">
                                Forgot password?
                            </a>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-lg shadow-blue-600/20 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:scale-[1.02]">
                            Sign In
                        </button>
                    </div>
                </form>
                
                <div v-if="tenant && routes.supportInfo" class="mt-4 pt-4 border-t border-gray-700 text-center">
                    <p class="text-sm text-gray-400">Cannot login?</p>
                    <a :href="routes.supportInfo" class="mt-1 inline-block text-sm font-medium text-blue-400 hover:text-blue-300 transition">
                        Submit a Ticket
                    </a>
                </div>
                
            </div>
            
            <div class="mt-6 text-center">
                 <a href="/" class="text-xs text-gray-500 hover:text-gray-300 transition flex items-center justify-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Landing Page
                 </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        tenant: {
            type: Object,
            default: null
        },
        tenantLogoUrl: {
            type: String,
            default: null
        },
        routes: {
            type: Object,
            required: true
        },
        oldEmail: {
            type: String,
            default: ''
        },
        errors: {
            type: Object,
            default: () => ({})
        },
        status: {
            type: String,
            default: null
        },
        csrfToken: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            form: {
                email: this.oldEmail,
                password: '',
                remember: false
            },
            showPassword: false
        }
    },
    computed: {
        targetRoute() {
            return this.routes.login;
        }
    }
}
</script>
