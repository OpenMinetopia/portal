<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Minetopia Panel') }} - @yield('title', 'Dashboard')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
        }
        
        /* Liquid Glass Background - Enhanced Light Mode */
        .liquid-glass-bg {
            background: linear-gradient(135deg, 
                rgba(240, 245, 251, 0.9) 0%, 
                rgba(224, 237, 252, 0.95) 25%,
                rgba(196, 218, 249, 0.98) 50%,
                rgba(224, 237, 252, 0.95) 75%,
                rgba(240, 245, 251, 0.9) 100%
            );
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -10;
        }
        
        /* Dark mode background */
        .dark .liquid-glass-bg {
            background: linear-gradient(135deg, 
                rgba(30, 41, 59, 0.9) 0%, 
                rgba(15, 23, 42, 0.95) 25%,
                rgba(2, 6, 23, 0.98) 50%,
                rgba(15, 23, 42, 0.95) 75%,
                rgba(30, 41, 59, 0.9) 100%
            );
        }
        
        /* Animated Background Orbs - Enhanced Light Mode */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(40px);
            pointer-events: none;
            z-index: -1;
            animation: float 8s ease-in-out infinite;
        }
        
        .bg-orb:nth-child(1) {
            top: 10%;
            left: 10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
            animation-delay: 0s;
        }
        
        .bg-orb:nth-child(2) {
            top: 60%;
            right: 10%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
            animation-delay: -3s;
        }
        
        .bg-orb:nth-child(3) {
            bottom: 20%;
            left: 30%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.08) 0%, transparent 70%);
            animation-delay: -6s;
        }
        
        /* Dark mode orbs */
        .dark .bg-orb:nth-child(1) {
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
        }
        
        .dark .bg-orb:nth-child(2) {
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
        }
        
        .dark .bg-orb:nth-child(3) {
            background: radial-gradient(circle, rgba(168, 85, 247, 0.08) 0%, transparent 70%);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(90deg); }
            50% { transform: translateY(-40px) rotate(180deg); }
            75% { transform: translateY(-20px) rotate(270deg); }
        }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }
        
        .custom-scrollbar {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        /* Glass Cards - Enhanced Light Mode */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            box-shadow: 
                0 12px 40px rgba(0, 0, 0, 0.15),
                0 4px 16px rgba(59, 130, 246, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }
        
        /* Dark mode glass cards */
        .dark .glass-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        /* Enhanced Glass Hover Effect for Light Mode */
        .glass-card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .glass-card-hover:hover {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 
                0 16px 50px rgba(0, 0, 0, 0.2),
                0 6px 20px rgba(59, 130, 246, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 1);
        }
        
        .dark .glass-card-hover:hover {
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 
                0 12px 40px rgba(0, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
        
        /* Floating Animation */
        .float-animation {
            animation: gentle-float 6s ease-in-out infinite;
        }
        
        @keyframes gentle-float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        /* Navigation Item Styles */
        .nav-item {
            background: transparent;
            border: 1px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Light mode navigation */
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(59, 130, 246, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 
                0 4px 16px rgba(59, 130, 246, 0.15),
                0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .nav-item.active {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 
                0 6px 20px rgba(59, 130, 246, 0.2),
                0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Dark mode navigation */
        .dark .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: none;
        }
        
        .dark .nav-item.active {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-full bg-transparent relative overflow-x-hidden" x-data="{
    sidebarOpen: false,
    darkMode: localStorage.theme === 'dark',
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.theme = this.darkMode ? 'dark' : 'light';
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}">
    <!-- Fixed Background -->
    <div class="liquid-glass-bg"></div>
    
    <!-- Animated Background Orbs -->
    <div class="bg-orb"></div>
    <div class="bg-orb"></div>
    <div class="bg-orb"></div>
    
    <div class="min-h-full relative">
        @include('portal.layouts.v2.partials.sidebar')

        <div class="lg:pl-80 min-h-screen">
            @include('portal.layouts.v2.partials.header')

            <main class="py-8 relative">
                <div class="mx-auto max-w-7xl px-8 sm:px-10 lg:px-12">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Version Switcher -->
    <div class="fixed bottom-8 right-8 z-50 float-animation">
        <div class="glass-card rounded-xl p-4 shadow-2xl">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-8 h-8 bg-emerald-500/20 rounded-lg">
                    <span class="text-xs font-bold text-emerald-300">V2</span>
                </div>
                <a href="{{ request()->fullUrlWithQuery(['layout' => 'v1']) }}" 
                   class="text-xs text-slate-300 hover:text-emerald-300 font-medium transition-colors duration-200 hover:underline">
                    Switch to V1
                </a>
            </div>
        </div>
    </div>
    
    @stack('scripts')
    <x-notification/>
</body>
</html> 