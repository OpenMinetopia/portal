<?php

namespace App\Console\Commands;

use App\Http\Controllers\Portal\Admin\SystemDebugController;
use Illuminate\Console\Command;

class SystemDebugCommand extends Command
{
    protected $signature = 'debug:system {--show-keys : Show full API keys instead of masked values}';
    protected $description = 'Toon systeem debug informatie in de console';

    public function handle(): int
    {
        // Only allow in non-production environments
        if (app()->environment('production')) {
            $this->error('❌ Debug commando niet beschikbaar in productie omgevingen.');
            return Command::FAILURE;
        }

        $this->info('🔍 OpenMinetopia Portal - Systeem Debug Informatie');
        $this->newLine();

        // Get debug information from the controller
        $controller = new SystemDebugController();
        $debugInfo = $this->getDebugInformation();

        $this->displaySystemInfo($debugInfo['system']);
        $this->displayMinecraftConfig($debugInfo['minecraft']);
        $this->displayDatabaseConfig($debugInfo['database']);
        $this->displayStorageInfo($debugInfo['storage']);
        $this->displayEnvFileInfo($debugInfo['env_file']);
        $this->displaySecurityInfo($debugInfo['security']);
        $this->displayAdditionalServices($debugInfo);

        $this->newLine();
        $this->displayWarnings($debugInfo);

        return Command::SUCCESS;
    }

    private function getDebugInformation(): array
    {
        return [
            'system' => [
                'app_name' => config('app.name'),
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
                'app_url' => config('app.url'),
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
                'absolute_path' => base_path(),
                'timezone' => config('app.timezone'),
            ],
            'database' => [
                'connection' => config('database.default'),
                'host' => config('database.connections.' . config('database.default') . '.host'),
                'port' => config('database.connections.' . config('database.default') . '.port'),
                'database' => config('database.connections.' . config('database.default') . '.database'),
                'username' => config('database.connections.' . config('database.default') . '.username'),
                'password' => $this->maskValue(config('database.connections.' . config('database.default') . '.password')),
            ],
            'minecraft' => [
                'server_address' => config('plugin.server_address'),
                'plugin_api_url' => config('plugin.api.url'),
                'plugin_api_key' => $this->option('show-keys') ? config('plugin.api.key') : $this->maskValue(config('plugin.api.key'), 8, 4),
                'minecraft_api_key' => $this->option('show-keys') ? config('services.minecraft.api_key') : $this->maskValue(config('services.minecraft.api_key'), 8, 4),
                'plugin_api_key_status' => $this->getConfigStatus(config('plugin.api.key')),
                'minecraft_api_key_status' => $this->getConfigStatus(config('services.minecraft.api_key')),
            ],
            'cache' => [
                'driver' => config('cache.default'),
                'prefix' => config('cache.prefix'),
            ],
            'queue' => [
                'driver' => config('queue.default'),
            ],
            'session' => [
                'driver' => config('session.driver'),
                'lifetime' => config('session.lifetime'),
                'encrypt' => config('session.encrypt'),
            ],
            'security' => [
                'app_key_set' => config('app.key') ? 'Set' : 'Not Set',
                'app_key_masked' => $this->maskValue(config('app.key'), 10, 4),
            ],
            'storage' => [
                'disk' => config('filesystems.default'),
                'storage_path' => storage_path(),
                'public_path' => public_path(),
                'storage_writable' => is_writable(storage_path()),
                'cache_writable' => is_writable(storage_path('framework/cache')),
                'logs_writable' => is_writable(storage_path('logs')),
            ],
            'env_file' => [
                'exists' => file_exists(base_path('.env')),
                'readable' => is_readable(base_path('.env')),
                'writable' => is_writable(base_path('.env')),
                'size' => file_exists(base_path('.env')) ? filesize(base_path('.env')) : 0,
                'last_modified' => file_exists(base_path('.env')) ? 
                    date('Y-m-d H:i:s', filemtime(base_path('.env'))) : 'N/A',
            ],
        ];
    }

    private function displaySystemInfo(array $system): void
    {
        $this->info('📊 Systeeminformatie');
        $this->line('─────────────────────────────────────');
        
        $rows = [];
        foreach ($system as $key => $value) {
            $rows[] = [ucwords(str_replace('_', ' ', $key)), $value ?: 'Niet ingesteld'];
        }
        
        $this->table(['Eigenschap', 'Waarde'], $rows);
        $this->newLine();
    }

    private function displayMinecraftConfig(array $minecraft): void
    {
        $needsUpdate = $minecraft['plugin_api_key_status'] === 'needs_update' || 
                      $minecraft['minecraft_api_key_status'] === 'needs_update';
        
        $title = '🎮 Minecraft configuratie';
        if ($needsUpdate) {
            $title .= ' ⚠️  AANDACHT VEREIST';
        } elseif ($minecraft['plugin_api_key_status'] === 'configured' && 
                 $minecraft['minecraft_api_key_status'] === 'configured') {
            $title .= ' ✅ GECONFIGUREERD';
        }
        
        $this->info($title);
        $this->line('─────────────────────────────────────');
        
        $rows = [];
        foreach ($minecraft as $key => $value) {
            if (str_ends_with($key, '_status')) continue;
            
            $status = '';
            if (str_contains($key, 'key')) {
                $statusKey = $key . '_status';
                $statusValue = $minecraft[$statusKey] ?? 'unknown';
                
                switch ($statusValue) {
                    case 'needs_update':
                        $status = ' 🔴 VERANDER-MIJ';
                        break;
                    case 'not_set':
                        $status = ' 🟡 Niet ingesteld';
                        break;
                    case 'configured':
                        $status = ' 🟢 OK';
                        break;
                }
            }
            
            $displayValue = $value ?: 'Niet ingesteld';
            if (str_contains($key, 'key') && $value && $value !== 'Niet ingesteld' && !$this->option('show-keys')) {
                $displayValue .= ' (Gedeeltelijk verborgen)';
            }
            
            $rows[] = [ucwords(str_replace('_', ' ', $key)) . $status, $displayValue];
        }
        
        $this->table(['Eigenschap', 'Waarde'], $rows);
        
        if ($needsUpdate) {
            $this->newLine();
            $this->warn('⚠️  Sommige API keys staan nog ingesteld op "CHANGE-ME".');
            $this->warn('   Genereer juiste tokens met: php artisan tokens:generate');
        }
        
        $this->newLine();
    }

    private function displayDatabaseConfig(array $database): void
    {
        $this->info('🗄️  Database configuratie');
        $this->line('─────────────────────────────────────');
        
        $rows = [];
        foreach ($database as $key => $value) {
            $displayValue = $value ?: 'Niet ingesteld';
            if ($key === 'password' && $value && $value !== 'Niet ingesteld') {
                $displayValue .= ' (Gedeeltelijk verborgen)';
            }
            $rows[] = [ucwords(str_replace('_', ' ', $key)), $displayValue];
        }
        
        $this->table(['Eigenschap', 'Waarde'], $rows);
        $this->newLine();
    }

    private function displayStorageInfo(array $storage): void
    {
        $this->info('📁 Opslag en rechten');
        $this->line('─────────────────────────────────────');
        
        $rows = [];
        foreach ($storage as $key => $value) {
            if (is_bool($value)) {
                $displayValue = $value ? '✅ Ja' : '❌ Nee';
            } else {
                $displayValue = $value ?: 'Niet ingesteld';
            }
            $rows[] = [ucwords(str_replace('_', ' ', $key)), $displayValue];
        }
        
        $this->table(['Eigenschap', 'Waarde'], $rows);
        $this->newLine();
    }

    private function displayEnvFileInfo(array $envFile): void
    {
        $this->info('📄 Environment bestand (.env)');
        $this->line('─────────────────────────────────────');
        
        $rows = [];
        foreach ($envFile as $key => $value) {
            if (is_bool($value)) {
                $displayValue = $value ? '✅ Ja' : '❌ Nee';
            } else {
                $displayValue = $value ?: 'Niet ingesteld';
            }
            $rows[] = [ucwords(str_replace('_', ' ', $key)), $displayValue];
        }
        
        $this->table(['Eigenschap', 'Waarde'], $rows);
        $this->newLine();
    }

    private function displaySecurityInfo(array $security): void
    {
        $this->info('🔒 Beveiligings informatie');
        $this->line('─────────────────────────────────────');
        
        $rows = [];
        foreach ($security as $key => $value) {
            $displayValue = $value ?: 'Niet ingesteld';
            if (str_contains($key, 'key') && $value && $value !== 'Niet ingesteld') {
                $displayValue .= ' (Gedeeltelijk verborgen)';
            }
            $rows[] = [ucwords(str_replace('_', ' ', $key)), $displayValue];
        }
        
        $this->table(['Eigenschap', 'Waarde'], $rows);
        $this->newLine();
    }

    private function displayAdditionalServices(array $debugInfo): void
    {
        $this->info('⚙️  Aanvullende services');
        $this->line('─────────────────────────────────────');
        
        $rows = [];
        
        // Cache
        foreach ($debugInfo['cache'] as $key => $value) {
            $rows[] = ['Cache ' . ucwords(str_replace('_', ' ', $key)), $value ?: 'Niet ingesteld'];
        }
        
        // Queue
        foreach ($debugInfo['queue'] as $key => $value) {
            $rows[] = ['Queue ' . ucwords(str_replace('_', ' ', $key)), $value ?: 'Niet ingesteld'];
        }
        
        // Session
        foreach ($debugInfo['session'] as $key => $value) {
            $displayValue = is_bool($value) ? ($value ? 'Ja' : 'Nee') : ($value ?: 'Niet ingesteld');
            $rows[] = ['Session ' . ucwords(str_replace('_', ' ', $key)), $displayValue];
        }
        
        $this->table(['Service', 'Waarde'], $rows);
        $this->newLine();
    }

    private function displayWarnings(array $debugInfo): void
    {
        $this->warn('⚠️  Beveiligings waarschuwing');
        $this->line('Deze debug informatie bevat gevoelige configuratie-gegevens.');
        $this->line('Deel deze informatie nooit publiekelijk of met onbevoegde personen.');
        
        if (!$this->option('show-keys')) {
            $this->newLine();
            $this->info('💡 Tip: Gebruik --show-keys om volledige API keys te tonen.');
        }
    }

    private function maskValue(?string $value, int $start = 6, int $end = 4): string
    {
        if (empty($value)) {
            return 'Niet ingesteld';
        }

        $length = strlen($value);
        
        if ($length <= ($start + $end)) {
            return str_repeat('*', $length);
        }

        return substr($value, 0, $start) . 
               str_repeat('*', $length - $start - $end) . 
               substr($value, -$end);
    }

    private function getConfigStatus(?string $value): string
    {
        if (empty($value)) {
            return 'not_set';
        }

        if (in_array(strtoupper($value), ['CHANGE-ME', 'CHANGEME', 'PLACEHOLDER', 'YOUR-API-KEY-HERE'])) {
            return 'needs_update';
        }

        return 'configured';
    }
} 