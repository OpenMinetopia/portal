@extends('portal.layouts.guest')

@section('title', 'Minecraft-account koppelen')
@section('header', 'Koppel je Minecraft-account')
@section('subheader', 'Drie stappen, en je bent klaar.')

@section('content')
    @php($command = '/koppel '.$token)

    <ol class="space-y-8">
        <li class="flex gap-4">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-gray-100 text-sm font-bold dark:bg-gray-800">1</span>
            <div>
                <h2 class="font-semibold">Start Minecraft</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Join de server op <span class="font-mono text-sm text-gray-900 dark:text-white">{{ config('plugin.server_address') }}</span>.</p>
            </div>
        </li>

        <li class="flex gap-4" x-data="{ copied: false }">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-gray-100 text-sm font-bold dark:bg-gray-800">2</span>
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold">Typ dit commando in de chat</h2>
                <div class="mt-2 flex">
                    <code class="min-w-0 flex-1 break-all rounded-l-md bg-gray-100 px-3 py-2.5 font-mono text-sm dark:bg-gray-800">{{ $command }}</code>
                    <button type="button"
                            @click="navigator.clipboard.writeText(@js($command)).then(() => { copied = true; setTimeout(() => copied = false, 1500) })"
                            class="relative rounded-r-md border-l border-gray-200 bg-gray-100 px-3 text-sm font-medium hover:bg-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                        Kopieer
                        <span x-show="copied" x-cloak x-transition.opacity
                              class="absolute bottom-full left-1/2 mb-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-900 px-2 py-0.5 text-xs text-white dark:bg-white dark:text-gray-900">Gekopieerd</span>
                    </button>
                </div>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Deze code is alleen voor jou. Deel hem niet.</p>
            </div>
        </li>

        <li class="flex gap-4">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-gray-100 text-sm font-bold dark:bg-gray-800">3</span>
            <div class="flex-1">
                <h2 class="font-semibold">Controleer de koppeling</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Commando uitgevoerd? Klik hieronder.</p>
                <form action="{{ route('minecraft.verify') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2.5 font-semibold text-white transition hover:bg-indigo-500 active:translate-y-px dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:hover:text-gray-900">
                        Koppeling controleren
                    </button>
                </form>
            </div>
        </li>
    </ol>

    @if (session('error'))
        <p class="mt-6 rounded-md border-l-4 border-red-600 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-400 dark:bg-red-500/10 dark:text-red-200" role="alert">{{ session('error') }}</p>
    @endif
@endsection
