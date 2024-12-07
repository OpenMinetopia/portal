@extends('layouts.app')

@section('title', 'Edit Profile')
@section('header', 'Profile Settings')

@section('content')
<div class="space-y-10 divide-y divide-gray-900/10 dark:divide-gray-700">
    <!-- Profile Information -->
    <div class="grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
        <div class="px-4 sm:px-0">
            <h2 class="text-base font-semibold leading-7 text-gray-900 dark:text-white">Profile Information</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">Update your account's profile information and email address.</p>
        </div>

        <form method="post" action="{{ route('profile.update') }}" class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-800 rounded-lg md:col-span-2">
            @csrf
            @method('patch')
            
            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="col-span-full">
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Name</label>
                        <div class="mt-2">
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 dark:bg-gray-700">
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-full">
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Email address</label>
                        <div class="mt-2">
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 dark:bg-gray-700">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-full">
                        <label for="minecraft_username" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Minecraft Username</label>
                        <div class="mt-2">
                            <input type="text" id="minecraft_username" value="{{ $user->minecraft_username }}" disabled
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-600 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 dark:border-gray-700 px-4 py-4 sm:px-8">
                <button type="submit" 
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save
                </button>
            </div>
        </form>
    </div>

    <!-- Update Password -->
    <div class="grid grid-cols-1 gap-x-8 gap-y-8 pt-10 md:grid-cols-3">
        <div class="px-4 sm:px-0">
            <h2 class="text-base font-semibold leading-7 text-gray-900 dark:text-white">Update Password</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">Ensure your account is using a long, random password to stay secure.</p>
        </div>

        <form method="post" action="{{ route('profile.password') }}" class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-800 rounded-lg md:col-span-2">
            @csrf
            @method('put')

            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="col-span-full">
                        <label for="current_password" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Current Password</label>
                        <div class="mt-2">
                            <input type="password" name="current_password" id="current_password" 
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 dark:bg-gray-700">
                        </div>
                        @error('current_password')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-full">
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">New Password</label>
                        <div class="mt-2">
                            <input type="password" name="password" id="password" 
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 dark:bg-gray-700">
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-full">
                        <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Confirm Password</label>
                        <div class="mt-2">
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 dark:bg-gray-700">
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 dark:border-gray-700 px-4 py-4 sm:px-8">
                <button type="submit" 
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 