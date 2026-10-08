<?php

namespace App\Services\Plugin;

use App\Support\Portal;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * The HTTP client for calls to the plugin. Hosted portals go through the address
 * guard, because portal owners fill in the URL; a self-hosted portal talks to its
 * own plugin, usually on 127.0.0.1 or the local network, and its URL comes from
 * the server's own .env, so it is not guarded.
 */
class PluginHttp
{
    public function __construct(private PluginAddressGuard $guard) {}

    /** @throws PluginAddressException in hosted mode, for an address that is not allowed */
    public function client(string $url): PendingRequest
    {
        if (Portal::hosted()) {
            return $this->guard->client($url);
        }

        return Http::timeout(PluginAddressGuard::TIMEOUT)->connectTimeout(PluginAddressGuard::TIMEOUT)->withoutRedirecting();
    }
}
