<?php

namespace App\Services\Plugin;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * The plugin URL is filled in by portal owners, so it must never let the portal
 * reach something on our own network. The host is resolved once, checked, and the
 * request is pinned to that IP so a second DNS answer cannot point elsewhere.
 */
class PluginAddressGuard
{
    public const TIMEOUT = 5;

    public const MIN_PORT = 1024;

    public const MAX_PORT = 65535;

    private const BLOCKED_RANGES = [
        // Private, loopback, link-local (incl. the 169.254.169.254 metadata service), CGNAT,
        // benchmarking, multicast and reserved.
        '0.0.0.0/8', '10.0.0.0/8', '100.64.0.0/10', '127.0.0.0/8', '169.254.0.0/16', '172.16.0.0/12',
        '192.0.0.0/24', '192.168.0.0/16', '198.18.0.0/15', '224.0.0.0/4', '240.0.0.0/4',
        // The same for IPv6, plus IPv4-mapped and NAT64 addresses that could wrap any of the above.
        '::/128', '::1/128', '::ffff:0:0/96', '64:ff9b::/96', '100::/64', 'fc00::/7', 'fe80::/10', 'ff00::/8',
    ];

    /** @var callable(string): list<string> */
    private $resolver;

    public function __construct(?callable $resolver = null)
    {
        $this->resolver = $resolver ?? static function (string $host): array {
            $ips = [];

            foreach ([DNS_A, DNS_AAAA] as $type) {
                foreach (@dns_get_record($host, $type) ?: [] as $record) {
                    $ips[] = $record['ip'] ?? $record['ipv6'];
                }
            }

            return array_values(array_filter($ips));
        };
    }

    /**
     * An HTTP client for the given base URL, pinned to its checked IP.
     *
     * @throws PluginAddressException
     */
    public function client(string $url): PendingRequest
    {
        [$host, $port, $ip] = $this->resolve($url);

        $client = Http::timeout(self::TIMEOUT)->connectTimeout(self::TIMEOUT)->withoutRedirecting();

        if (! filter_var($host, FILTER_VALIDATE_IP)) {
            $client = $client->withOptions(['curl' => [CURLOPT_RESOLVE => ["{$host}:{$port}:".(str_contains($ip, ':') ? "[{$ip}]" : $ip)]]]);
        }

        return $client;
    }

    /**
     * @return array{0: string, 1: int, 2: string} host, port and the IP to connect to
     *
     * @throws PluginAddressException
     */
    public function resolve(string $url): array
    {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = trim($parts['host'] ?? '', '[]');

        if (! in_array($scheme, ['http', 'https'], true) || $host === '' || isset($parts['user']) || isset($parts['pass'])) {
            throw new PluginAddressException('dns', 'The plugin URL is not a valid http(s) URL.');
        }

        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        if ($port < self::MIN_PORT || $port > self::MAX_PORT) {
            throw new PluginAddressException('blocked_address', 'The plugin port must be between 1024 and 65535.');
        }

        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : ($this->resolver)($host);

        if ($ips === []) {
            throw new PluginAddressException('dns', "{$host} does not resolve.");
        }

        // Every answer must be public, otherwise a round-robin record could sneak one in.
        foreach ($ips as $ip) {
            if (self::isBlocked($ip)) {
                throw new PluginAddressException('blocked_address', "{$host} points to an internal address.");
            }
        }

        return [$host, $port, $ips[0]];
    }

    public static function isBlocked(string $ip): bool
    {
        return ! filter_var($ip, FILTER_VALIDATE_IP) || IpUtils::checkIp($ip, self::BLOCKED_RANGES);
    }
}
