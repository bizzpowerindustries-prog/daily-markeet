@extends('layouts.app')

@section('title', 'Login - DailyMarkeet.pk')

@section('content')
<div x-data="loginPage()" x-init="init()" class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold">Welcome Back!</h2>
            <p class="text-gray-500 text-sm mt-1">Login to your DailyMarkeet account</p>
        </div>
        
        <!-- Login Form -->
        <form @submit.prevent="login" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" x-model="email" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" x-model="password" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary-500">
            </div>
            
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" class="rounded border-gray-300">
                    Remember me
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-primary-500 hover:underline">Forgot password?</a>
            </div>
            
            <button type="submit" 
                    class="w-full bg-primary-500 text-white py-2.5 rounded-lg hover:bg-primary-600 transition font-semibold"
                    :disabled="isLoading">
                <span x-show="!isLoading">Login</span>
                <span x-show="isLoading"><i class="fas fa-spinner fa-spin"></i> Logging in...</span>
            </button>
        </form>
        
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Or continue with</span>
            </div>
        </div>
        
        <!-- Social Login -->
        <div class="space-y-3">
            <button @click="googleLogin" 
                    class="w-full flex items-center justify-center gap-3 border border-gray-300 py-2.5 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59A14.5 14.5 0 019.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                <span>Continue with Google</span>
            </button>
            
            <button @click="showPhoneLogin = true" 
                    class="w-full flex items-center justify-center gap-3 border border-gray-300 py-2.5 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-phone text-green-500"></i>
                <span>Continue with Phone</span>
            </button>
        </div>
        
        <p class="text-center text-sm text-gray-500 mt-6">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-primary-500 hover:underline">Sign up</a>
        </p>
    </div>
</div>

@push('scripts')
<script>
    function loginPage() {
        return {
            email: '',
            password: '',
            isLoading: false,
            
            init() {
                // Check if already logged in
                if (window.firebase.auth.currentUser) {
                    window.location.href = '{{ route("home") }}';
                }
            },
            
            login() {
                this.isLoading = true;
                const { auth, signInWithEmailAndPassword } = window.firebase;
                
                signInWithEmailAndPassword(auth, this.email, this.password)
                    .then((userCredential) => {
                        const token = userCredential.user.getIdToken();
                        return token;
                    })
                    .then((token) => {
                        // Send token to backend
                        return fetch('{{ route("api.auth.verify") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ firebase_token: token })
                        });
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            window.location.href = '{{ route("home") }}';
                        } else {
                            this.isLoading = false;
                            toastr.error(data.message);
                        }
                    })
                    .catch((error) => {
                        this.isLoading = false;
                        toastr.error(error.message);
                    });
            },
            
            googleLogin() {
                const { auth, signInWithPopup, googleProvider } = window.firebase;
                
                signInWithPopup(auth, googleProvider)
                    .then((result) => {
                        return result.user.getIdToken();
                    })
                    .then((token) => {
                        return fetch('{{ route("api.auth.verify") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ firebase_token: token })
                        });
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            window.location.href = '{{ route("home") }}';
                        }
                    })
                    .catch((error) => {
                        toastr.error(error.message);
                    });
            }
        }
    }
</script>
@endpush
