<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SystemDebugController extends Controller
{
    public function index()
    {
        // Only allow access when not in production
        if (app()->environment('production')) {
            abort(404, 'Debug pagina niet beschikbaar in productie');
        }

        $debugInfo = $this->getDebugInformation();

        return view('portal.admin.debug.index', compact('debugInfo'));
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
                'plugin_api_key' => config('plugin.api.key'),
                'minecraft_api_key' => config('services.minecraft.api_key'),
                'plugin_api_key_status' => $this->getConfigStatus(config('plugin.api.key')),
                'minecraft_api_key_status' => $this->getConfigStatus(config('services.minecraft.api_key')),
            ],
            'mail' => [
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.' . config('mail.default') . '.host'),
                'port' => config('mail.mailers.' . config('mail.default') . '.port'),
                'username' => config('mail.mailers.' . config('mail.default') . '.username'),
                'password' => $this->maskValue(config('mail.mailers.' . config('mail.default') . '.password')),
                'encryption' => config('mail.mailers.' . config('mail.default') . '.encryption'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
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
                'csrf_token' => csrf_token(),
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

    private function maskValue(?string $value, int $start = 6, int $end = 4): string
    {
        if (empty($value)) {
            return 'Niet Ingesteld';
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
