@extends('portal.layouts.app')

@section('title', 'System Debug Information')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Debug Informatie pagina</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Deze pagina toont configuratie en systeeminformatie voor debugging doeleinden. Deel deze gegevens niet met derden, mits je weet wat je doet.
            <span class="text-red-600 dark:text-red-400 font-medium">Alleen beschikbaar in non-productie omgevingen.</span>
        </p>
    </div>

    <!-- System Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- System -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                    Systeeminformatie
                </h2>
            </div>
            <div class="p-6">
                <dl class="space-y-3">
                    @foreach($debugInfo['system'] as $key => $value)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}:</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono break-all">{{ $value ?: 'Not Set' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <!-- Minecraft Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Minecraft configuratie
                    @if($debugInfo['minecraft']['plugin_api_key_status'] === 'needs_update' || $debugInfo['minecraft']['minecraft_api_key_status'] === 'needs_update')
                        <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                            Aandacht vereist
                        </span>
                    @elseif($debugInfo['minecraft']['plugin_api_key_status'] === 'configured' && $debugInfo['minecraft']['minecraft_api_key_status'] === 'configured')
                        <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                            Geconfigureerd
                        </span>
                    @endif
                </h2>
            </div>
            <div class="p-6">
                <dl class="space-y-3">
                    @foreach($debugInfo['minecraft'] as $key => $value)
                        @if(!str_ends_with($key, '_status'))
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}:
                                    @if(str_contains($key, 'key'))
                                        @php
                                            $statusKey = $key . '_status';
                                            $status = $debugInfo['minecraft'][$statusKey] ?? 'unknown';
                                        @endphp
                                                                                 @if($status === 'needs_update')
                                             <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                                 VERANDER-MIJ
                                             </span>
                                         @elseif($status === 'not_set')
                                             <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full">
                                                 Niet ingesteld
                                             </span>
                                         @elseif($status === 'configured')
                                             <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                                 OK
                                             </span>
                                         @endif
                                    @endif
                                </dt>
                                <dd class="mt-1 text-sm font-mono break-all {{ str_contains($key, 'key') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white' }}">
                                    {{ $value ?: 'Niet Ingesteld' }}
                                    @if(str_contains($key, 'key') && $value !== 'Niet Ingesteld')
                                        <span class="text-xs text-gray-500 ml-2">(Gedeeltelijk verborgen)</span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                    @endforeach
                </dl>

                @if($debugInfo['minecraft']['plugin_api_key_status'] === 'needs_update' || $debugInfo['minecraft']['minecraft_api_key_status'] === 'needs_update')
                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                        <div class="flex">
                            <svg class="w-4 h-4 text-red-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.99-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                                                         <div>
                                 <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Actie vereist</h4>
                                 <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                     Sommige API keys staan nog ingesteld op "CHANGE-ME". Genereer juiste tokens met:<br>
                                     <code class="text-xs bg-red-100 dark:bg-red-800 px-1 py-0.5 rounded">php artisan tokens:generate</code>
                                 </p>
                             </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Database Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                    </svg>
                    Database configuratie
                </h2>
            </div>
            <div class="p-6">
                <dl class="space-y-3">
                    @foreach($debugInfo['database'] as $key => $value)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}:</dt>
                            <dd class="mt-1 text-sm font-mono break-all {{ $key === 'password' ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white' }}">
                                {{ $value ?: 'Niet Ingesteld' }}
                                @if($key === 'password' && $value !== 'Niet Ingesteld')
                                    <span class="text-xs text-gray-500 ml-2">(Gedeeltelijk verborgen)</span>
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <!-- Storage Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                    </svg>
                    Opslag en rechten
                </h2>
            </div>
            <div class="p-6">
                <dl class="space-y-3">
                    @foreach($debugInfo['storage'] as $key => $value)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}:</dt>
                            <dd class="mt-1 text-sm font-mono break-all">
                                @if(is_bool($value))
                                    <span class="px-2 py-1 text-xs rounded-full {{ $value ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $value ? 'Ja' : 'Nee' }}
                                    </span>
                                @else
                                    <span class="text-gray-900 dark:text-white">{{ $value ?: 'Niet Ingesteld' }}</span>
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <!-- Environment File -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Environment bestand (.env)
                </h2>
            </div>
            <div class="p-6">
                <dl class="space-y-3">
                    @foreach($debugInfo['env_file'] as $key => $value)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}:</dt>
                            <dd class="mt-1 text-sm font-mono break-all">
                                @if(is_bool($value))
                                    <span class="px-2 py-1 text-xs rounded-full {{ $value ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $value ? 'Ja' : 'Nee' }}
                                    </span>
                                @else
                                    <span class="text-gray-900 dark:text-white">{{ $value ?: 'Niet Ingesteld' }}</span>
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <!-- Security Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Beveiligings Informatie
                </h2>
            </div>
            <div class="p-6">
                <dl class="space-y-3">
                    @foreach($debugInfo['security'] as $key => $value)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}:</dt>
                            <dd class="mt-1 text-sm font-mono break-all {{ str_contains($key, 'key') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white' }}">
                                {{ $value ?: 'Niet Ingesteld' }}
                                @if(str_contains($key, 'key') && $value !== 'Niet Ingesteld')
                                    <span class="text-xs text-gray-500 ml-2">(Gedeeltelijk Verborgen)</span>
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </div>

    <!-- Additional Services -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <!-- Cache Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-md font-semibold text-gray-900 dark:text-white">Cache</h3>
            </div>
            <div class="p-6">
                <dl class="space-y-2">
                    @foreach($debugInfo['cache'] as $key => $value)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}:</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $value ?: 'Niet Ingesteld' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <!-- Queue Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-md font-semibold text-gray-900 dark:text-white">Queue</h3>
            </div>
            <div class="p-6">
                <dl class="space-y-2">
                    @foreach($debugInfo['queue'] as $key => $value)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}:</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $value ?: 'Niet Ingesteld' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <!-- Session Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-md font-semibold text-gray-900 dark:text-white">Session</h3>
            </div>
            <div class="p-6">
                <dl class="space-y-2">
                    @foreach($debugInfo['session'] as $key => $value)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}:</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                @if(is_bool($value))
                                    {{ $value ? 'Ja' : 'Nee' }}
                                @else
                                    {{ $value ?: 'Niet Ingesteld' }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </div>

    <!-- Warning Footer -->
    <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
        <div class="flex">
            <svg class="w-5 h-5 text-yellow-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.99-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Beveiligings Waarschuwing</h3>
                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                    Deze debug pagina bevat gevoelige configuratie-informatie en is alleen toegankelijk in non-productie omgevingen.
                    API keys en wachtwoorden zijn gedeeltelijk gemaskeerd voor de veiligheid. Deel deze informatie nooit publiekelijk.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
