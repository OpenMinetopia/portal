@extends('layouts.app')

@section('title', 'My Plots')
@section('header', 'Plot Management')

@section('content')
<div class="space-y-6">
    <!-- Plot Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($plots as $plot)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $plot->name }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $plot->type }} • {{ $plot->location }}
                        </p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $plot->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                        {{ $plot->status }}
                    </span>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Daily Rent</span>
                        <span class="font-medium text-gray-900 dark:text-white">${{ number_format($plot->daily_rent) }}</span>
                    </div>
                    <div class="flex justify-between text-sm mt-2">
                        <span class="text-gray-500 dark:text-gray-400">Size</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $plot->size }} blocks</span>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-3">
                <a href="#" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 flex items-center justify-between">
                    Manage plot
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection 