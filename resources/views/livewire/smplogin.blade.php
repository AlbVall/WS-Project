<?php

use function Livewire\Volt\{state, computed, mount};

state([
    'email' => '',
    'password' => '',
    'name' => '',
    'password_confirmation' => '',
    'isLogin' => true,
    'error' => '',
]);

$toggleForm = function() {
    $this->isLogin = !$this->isLogin;
    $this->error = '';
};

$login = function() {
    $this->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    if (auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
        return redirect()->intended('/dashboard');
    }

    $this->error = 'Invalid credentials';
};

$register = function() {
    $this->validate([
        'name' => 'required|min:2',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'password_confirmation' => 'required',
    ]);

    $user = \App\Models\User::create([
        'name' => $this->name,
        'email' => $this->email,
        'password' => bcrypt($this->password),
    ]);

    auth()->login($user);

    return redirect()->intended('/dashboard');
};

?>

<div>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#FDFEFE] via-[#FEFFFF] to-[#FDFEFE] py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#EB7B65]/10 to-[#D66A54]/10"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,#EB7B65/20,transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,#D66A54/20,transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,#EB7B65/15,transparent_70%)]"></div>
        <div class="max-w-md w-full space-y-8 bg-gradient-to-br from-[#FEFFFF] via-[#FDFEFE] to-[#FEFFFF] p-8 rounded-2xl shadow-lg border border-[#25282D]/20 backdrop-blur-sm relative">
            <div class="absolute inset-0 bg-gradient-to-br from-[#EB7B65]/10 to-transparent rounded-2xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,#EB7B65/20,transparent_70%)] rounded-2xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,#D66A54/10,transparent_70%)] rounded-2xl"></div>
            <div class="relative">
                <div class="text-center">
                    <div class="flex justify-center">
                        <div class="h-12 w-12 bg-gradient-to-br from-[#EB7B65] via-[#D66A54] to-[#EB7B65] rounded-xl flex items-center justify-center transform rotate-12 shadow-lg shadow-[#EB7B65]/30 hover:rotate-0 transition-all duration-300 hover:scale-110">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    </div>
                    <h2 class="mt-6 text-3xl font-bold text-[#21272C] tracking-tight">
                        {{ $isLogin ? 'Welcome back' : 'Create your account' }}
                    </h2>
                    <p class="mt-2 text-sm text-[#25282D]">
                        {{ $isLogin ? 'Please enter your credentials to access your account' : 'Join our community and start your journey with us' }}
                    </p>
                </div>

                <form class="mt-8 space-y-6" wire:submit="{{ $isLogin ? 'login' : 'register' }}">
                    @if($error)
                        <div class="rounded-lg bg-red-50 p-4 border border-red-100 shadow-sm animate-shake">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ $error }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-4">
                        @if(!$isLogin)
                            <div>
                                <label for="name" class="block text-sm font-medium text-[#21272C]">Full name</label>
                                <div class="mt-1">
                                    <input wire:model="name" id="name" name="name" type="text" required 
                                        class="appearance-none block w-full px-3 py-2 border border-[#25282D] rounded-lg bg-white/80 shadow-sm placeholder-[#25282D] focus:outline-none focus:ring-2 focus:ring-[#EB7B65] focus:border-[#EB7B65] sm:text-sm transition-all duration-200 ease-in-out backdrop-blur-sm hover:bg-white/90 focus:bg-white" 
                                        placeholder="Enter your full name">
                                </div>
                            </div>
                        @endif
                        <div>
                            <label for="email" class="block text-sm font-medium text-[#21272C]">Email address</label>
                            <div class="mt-1">
                                <input wire:model="email" id="email" name="email" type="email" required 
                                    class="appearance-none block w-full px-3 py-2 border border-[#25282D] rounded-lg bg-white/80 shadow-sm placeholder-[#25282D] focus:outline-none focus:ring-2 focus:ring-[#EB7B65] focus:border-[#EB7B65] sm:text-sm transition-all duration-200 ease-in-out backdrop-blur-sm hover:bg-white/90 focus:bg-white" 
                                    placeholder="Enter your email address">
                            </div>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-[#21272C]">Password</label>
                            <div class="mt-1">
                                <input wire:model="password" id="password" name="password" type="password" required 
                                    class="appearance-none block w-full px-3 py-2 border border-[#25282D] rounded-lg bg-white/80 shadow-sm placeholder-[#25282D] focus:outline-none focus:ring-2 focus:ring-[#EB7B65] focus:border-[#EB7B65] sm:text-sm transition-all duration-200 ease-in-out backdrop-blur-sm hover:bg-white/90 focus:bg-white" 
                                    placeholder="Enter your password">
                            </div>
                        </div>
                        @if(!$isLogin)
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-[#21272C]">Confirm Password</label>
                                <div class="mt-1">
                                    <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" required 
                                        class="appearance-none block w-full px-3 py-2 border border-[#25282D] rounded-lg bg-white/80 shadow-sm placeholder-[#25282D] focus:outline-none focus:ring-2 focus:ring-[#EB7B65] focus:border-[#EB7B65] sm:text-sm transition-all duration-200 ease-in-out backdrop-blur-sm hover:bg-white/90 focus:bg-white" 
                                        placeholder="Confirm your password">
                                </div>
                            </div>
                        @endif
                    </div>

                    <div>
                        <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-[#EB7B65] via-[#D66A54] to-[#EB7B65] hover:from-[#D66A54] hover:via-[#EB7B65] hover:to-[#D66A54] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#EB7B65] transition-all duration-200 ease-in-out transform hover:scale-[1.02] shadow-lg shadow-[#EB7B65]/30 hover:shadow-[#EB7B65]/40 hover:translate-y-[-1px]">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-white/80 group-hover:text-white transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            {{ $isLogin ? 'Sign in to your account' : 'Create your account' }}
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <button wire:click="toggleForm" 
                        class="text-sm font-medium text-[#EB7B65] hover:text-[#D66A54] transition-all duration-200 ease-in-out hover:underline hover:scale-105 inline-block">
                        {{ $isLogin ? 'New to our platform? Create an account' : 'Already have an account? Sign in' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
    </style>
</div>
