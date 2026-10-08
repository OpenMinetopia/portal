<?php

namespace App\Services\Tenancy;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Portal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * The one way to become admin of a fresh portal: a short-lived, one-time link.
 * Hosted portals get it from the OMT website (the hash lives on the tenant); a
 * self-hosted portal makes one with `php artisan portal:admin-link` (the hash lives
 * in the cache). Only a hash is ever stored.
 */
class AdminClaim
{
    public const SESSION_KEY = 'admin_claim_token';

    private const CACHE_KEY = 'portal:admin-claim';

    /** Makes a new token (replacing any earlier one) and returns it in plain text. */
    public static function issue(?Tenant $tenant, ?int $minutes = null): string
    {
        $token = Str::random(48);
        $minutes ??= $tenant ? config('tenancy.admin_claim_ttl_minutes') : config('portal.admin_link_hours') * 60;
        $expiresAt = now()->addMinutes($minutes);

        if ($tenant) {
            $tenant->forceFill([
                'admin_claim_token_hash' => self::hash($token),
                'admin_claim_expires_at' => $expiresAt,
            ])->save();
        } else {
            Cache::put(self::CACHE_KEY, ['hash' => self::hash($token), 'expires_at' => $expiresAt->getTimestamp()], $expiresAt);
        }

        return $token;
    }

    public static function url(?Tenant $tenant, string $token): string
    {
        if ($tenant) {
            return config('tenancy.tenant_url_scheme').'://'.$tenant->primaryDomain().'/beheerder-worden/'.$token;
        }

        return rtrim((string) config('app.url'), '/').'/beheerder-worden/'.$token;
    }

    public static function isValid(?Tenant $tenant, string $token): bool
    {
        if (! $tenant) {
            $claim = Cache::get(self::CACHE_KEY);

            return is_array($claim)
                && hash_equals($claim['hash'], self::hash($token))
                && $claim['expires_at'] > now()->getTimestamp();
        }

        $tenant = $tenant->fresh();

        return $tenant?->admin_claim_token_hash !== null
            && hash_equals($tenant->admin_claim_token_hash, self::hash($token))
            && $tenant->admin_claim_expires_at?->isFuture();
    }

    /** Uses up the token and makes the user an admin. False when it was invalid, expired or already used. */
    public static function consume(?Tenant $tenant, string $token, User $user): bool
    {
        $claimed = $tenant ? self::claimOnTenant($tenant, $token) : self::claimInCache($token);

        if (! $claimed) {
            return false;
        }

        $adminRole = Role::where('is_admin', true)->firstOrFail();
        $user->roles()->syncWithoutDetaching([$adminRole->id]);

        return true;
    }

    /** After logging in or registering: finish a claim that was started as a guest. */
    public static function consumeFromSession(Request $request, User $user): void
    {
        $token = $request->session()->pull(self::SESSION_KEY);

        if (! is_string($token)) {
            return;
        }

        if (self::consume(tenant(), $token, $user)) {
            $request->session()->flash('success', 'Je bent nu beheerder van dit portaal.');
        } else {
            $request->session()->flash('error', 'De beheerderslink is verlopen of al gebruikt. '.self::howToGetANewLink());
        }
    }

    public static function howToGetANewLink(): string
    {
        return Portal::hosted()
            ? 'Vraag op de OpenMinetopia-website een nieuwe aan.'
            : 'Maak een nieuwe met php artisan portal:admin-link.';
    }

    private static function claimOnTenant(Tenant $tenant, string $token): bool
    {
        // A conditional update, so two simultaneous requests cannot both use it.
        return Tenant::query()
            ->whereKey($tenant->getTenantKey())
            ->where('admin_claim_token_hash', self::hash($token))
            ->where('admin_claim_expires_at', '>', now())
            ->update(['admin_claim_token_hash' => null, 'admin_claim_expires_at' => null]) === 1;
    }

    private static function claimInCache(string $token): bool
    {
        // Under a lock, so two simultaneous requests cannot both use it.
        return (bool) Cache::lock(self::CACHE_KEY.':lock', 10)->block(5, function () use ($token) {
            if (! self::isValid(null, $token)) {
                return false;
            }

            Cache::forget(self::CACHE_KEY);

            return true;
        });
    }

    private static function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
