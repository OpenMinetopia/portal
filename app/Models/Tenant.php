<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * One portal. The id is the website's instance tenant_id; the website creates,
 * changes and deletes tenants through the provisioning API only.
 *
 * @property string $id
 * @property string $name
 * @property string $status
 * @property ?string $plugin_api_url
 * @property ?string $plugin_api_key
 * @property ?string $minecraft_api_key
 * @property ?string $server_address
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public const ACTIVE = 'active';

    public const INACTIVE = 'inactive';

    protected $hidden = ['tenancy_db_password', 'plugin_api_key', 'minecraft_api_key', 'admin_claim_token_hash'];

    protected function casts(): array
    {
        return [
            'tenancy_db_password' => 'encrypted',
            'plugin_api_key' => 'encrypted',
            'minecraft_api_key' => 'encrypted',
            'admin_claim_expires_at' => 'datetime',
        ];
    }

    public static function getCustomColumns(): array
    {
        return [
            'id', 'name', 'status',
            'tenancy_db_name', 'tenancy_db_username', 'tenancy_db_password',
            'plugin_api_url', 'plugin_api_key', 'minecraft_api_key', 'server_address',
            'admin_claim_token_hash', 'admin_claim_expires_at',
            'created_at', 'updated_at',
        ];
    }

    // The id always comes from the website, so there is no generator; it is still a uuid string.
    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE;
    }

    public function primaryDomain(): ?string
    {
        $domains = $this->relationLoaded('domains') ? $this->domains : $this->domains()->get();

        return ($domains->firstWhere('is_primary', true) ?? $domains->first())?->domain;
    }
}
