<?php

namespace App\Services\Tenancy;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * The one way to become admin of a fresh portal: a short-lived, one-time link the
 * OMT website asks for on behalf of the portal's owner. Only a hash is stored.
 */
class AdminClaim
{
    public const SESSION_KEY = 'admin_claim_token';

    /** Makes a new token (replacing any earlier one) and returns it in plain text. */
    public static function issue(Tenant $tenant): string
    {
        $token = Str::random(48);

        $tenant->forceFill([
            'admin_claim_token_hash' => self::hash($token),
            'admin_claim_expires_at' => now()->addMinutes(config('tenancy.admin_claim_ttl_minutes')),
        ])->save();

        return $token;
    }

    public static function url(Tenant $tenant, string $token): string
    {
        return config('tenancy.tenant_url_scheme').'://'.$tenant->primaryDomain().'/beheerder-worden/'.$token;
    }

    public static function isValid(Tenant $tenant, string $token): bool
    {
        $tenant = $tenant->fresh();

        return $tenant?->admin_claim_token_hash !== null
            && hash_equals($tenant->admin_claim_token_hash, self::hash($token))
            && $tenant->admin_claim_expires_at?->isFuture();
    }

    /** Uses up the token and makes the user an admin. False when it was invalid, expired or already used. */
    public static function consume(Tenant $tenant, string $token, User $user): bool
    {
        // A conditional update, so two simultaneous requests cannot both use it.
        $claimed = Tenant::query()
            ->whereKey($tenant->getTenantKey())
            ->where('admin_claim_token_hash', self::hash($token))
            ->where('admin_claim_expires_at', '>', now())
            ->update(['admin_claim_token_hash' => null, 'admin_claim_expires_at' => null]) === 1;

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

        if (! is_string($token) || ! tenancy()->initialized) {
            return;
        }

        if (self::consume(tenant(), $token, $user)) {
            $request->session()->flash('success', 'Je bent nu beheerder van dit portaal.');
        } else {
            $request->session()->flash('error', 'De beheerderslink is verlopen of al gebruikt. Vraag op de OpenMinetopia-website een nieuwe aan.');
        }
    }

    private static function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
