<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Plugin\PluginConnectionCheck;
use App\Services\Tenancy\AdminClaim;
use App\Services\Tenancy\TenantMigrator;
use App\Tenancy\TenantStorageBootstrapper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * The provisioning API. The OMT website is the source of truth for tenants; it
 * calls these endpoints, signed, on the central domain only.
 */
class TenantController extends Controller
{
    private const HOSTNAME = '/^(?=.{1,253}$)(?!-)[a-z0-9-]{1,63}(?<!-)(\.(?!-)[a-z0-9-]{1,63}(?<!-))+$/';

    public function __construct(private TenantMigrator $migrator) {}

    /** Idempotent upsert of the tenant's full desired state, then its pending migrations. */
    public function upsert(Request $request, string $tenantId): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in([Tenant::ACTIVE, Tenant::INACTIVE])],
            'domains' => ['required', 'array', 'min:1'],
            'domains.*' => ['required', 'string', 'distinct', 'regex:'.self::HOSTNAME, Rule::notIn(config('tenancy.central_domains'))],
            'primary_domain' => ['required', 'string', 'in_array:domains.*'],
            'database' => ['required', 'array'],
            'database.name' => ['required', 'string', 'max:64'],
            'database.username' => ['required', 'string', 'max:64'],
            'database.password' => ['present', 'nullable', 'string'],
            'plugin' => ['present', 'nullable', 'array'],
            'plugin.url' => ['nullable', 'string', 'max:255', 'url:http,https'],
            'plugin.api_key' => ['nullable', 'string', 'max:255'],
            'minecraft_api_key' => ['nullable', 'string', 'max:255'],
            'server_address' => ['nullable', 'string', 'max:255'],
        ]);

        $domains = array_map('strtolower', $data['domains']);
        $primary = strtolower($data['primary_domain']);

        $taken = Domain::query()->whereIn('domain', $domains)->where('tenant_id', '!=', $tenantId)->value('domain');

        if ($taken) {
            return response()->json(['message' => "The domain {$taken} belongs to another tenant.", 'domain' => $taken], 409);
        }

        $tenant = DB::connection(config('tenancy.database.central_connection'))->transaction(function () use ($tenantId, $data, $domains, $primary) {
            $tenant = Tenant::find($tenantId) ?? new Tenant(['id' => $tenantId]);

            $tenant->forceFill([
                'name' => $data['name'],
                'status' => $data['status'],
                'tenancy_db_name' => $data['database']['name'],
                'tenancy_db_username' => $data['database']['username'],
                'tenancy_db_password' => $data['database']['password'] ?? '',
                'plugin_api_url' => $data['plugin']['url'] ?? null,
                'plugin_api_key' => $data['plugin']['api_key'] ?? null,
                'minecraft_api_key' => $data['minecraft_api_key'] ?? null,
                'server_address' => $data['server_address'] ?? null,
            ])->save();

            $tenant->domains()->whereNotIn('domain', $domains)->delete();

            foreach ($domains as $domain) {
                $tenant->domains()->updateOrCreate(['domain' => $domain], ['is_primary' => $domain === $primary]);
            }

            return $tenant;
        });

        $migration = $this->migrator->migrate($tenant);

        if (! $migration['ok']) {
            report(new \RuntimeException("Migrating tenant {$tenant->id} failed: {$migration['error']}"));

            return response()->json([
                'message' => 'The tenant was saved, but its migrations failed.',
                'error' => 'migration_failed',
                'tenant' => $this->view($tenant->fresh()),
            ], 500);
        }

        return response()->json($this->view($tenant->fresh()) + ['migrations' => ['ran' => $migration['ran']]]);
    }

    public function status(Request $request, string $tenantId): JsonResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in([Tenant::ACTIVE, Tenant::INACTIVE])]]);

        $tenant = $this->find($tenantId);
        $tenant->forceFill(['status' => $data['status']])->save();

        return response()->json($this->view($tenant));
    }

    public function pluginCheck(string $tenantId, PluginConnectionCheck $check): JsonResponse
    {
        return response()->json($check->check($this->find($tenantId)));
    }

    public function adminClaim(string $tenantId): JsonResponse
    {
        $tenant = $this->find($tenantId);

        if (! $tenant->primaryDomain()) {
            return response()->json(['message' => 'The tenant has no domain.'], 422);
        }

        $token = AdminClaim::issue($tenant);

        return response()->json([
            'url' => AdminClaim::url($tenant, $token),
            'expires_at' => $tenant->admin_claim_expires_at->toIso8601String(),
        ]);
    }

    /** Removes the tenant, its domains and its files. The database itself is dropped through Shipways. */
    public function destroy(string $tenantId): Response
    {
        $tenant = $this->find($tenantId);

        $tenant->domains()->delete();
        $tenant->delete();

        File::deleteDirectory(TenantStorageBootstrapper::publicPath($tenant->id));
        File::deleteDirectory(TenantStorageBootstrapper::cachePath($tenant->id));

        return response()->noContent();
    }

    public function show(string $tenantId): JsonResponse
    {
        $tenant = $this->find($tenantId);

        $health = ['database' => false, 'pending_migrations' => null, 'users' => null, 'error' => null];

        try {
            $health = $tenant->run(fn () => [
                'database' => true,
                'pending_migrations' => count($this->migrator->pending()),
                'users' => User::count(),
                'error' => null,
            ]);
        } catch (Throwable $exception) {
            $health['error'] = $exception->getMessage();
        }

        return response()->json($this->view($tenant) + ['health' => $health]);
    }

    private function find(string $tenantId): Tenant
    {
        $tenant = Tenant::with('domains')->find($tenantId);

        abort_if($tenant === null, 404, 'Unknown tenant.');

        return $tenant;
    }

    private function view(Tenant $tenant): array
    {
        $tenant->load('domains');

        return [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'status' => $tenant->status,
            'domains' => $tenant->domains->pluck('domain')->values()->all(),
            'primary_domain' => $tenant->primaryDomain(),
            'database' => ['name' => $tenant->tenancy_db_name, 'username' => $tenant->tenancy_db_username],
            'plugin' => ['url' => $tenant->plugin_api_url, 'has_api_key' => filled($tenant->plugin_api_key)],
            'has_minecraft_api_key' => filled($tenant->minecraft_api_key),
            'server_address' => $tenant->server_address,
            'created_at' => $tenant->created_at?->toIso8601String(),
            'updated_at' => $tenant->updated_at?->toIso8601String(),
        ];
    }
}
