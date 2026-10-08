# Hosting the multi-tenant portal

One Laravel app serves every portal. A request's host picks the tenant
(`domains` table); each tenant has its own MySQL database on the same server.
The OMT website creates, changes, pauses and deletes tenants through the signed
provisioning API on the central domain; nothing else does.

## Site

- One Shipways site on omt-web-1, domains `*.mtportal.nl` (wildcard certificate),
  `central.mtportal.nl`, plus each custom domain the website adds.
- Central database: `tenants`, `domains`, `cache` (the provisioning API's nonces
  and migration locks). The database user needs access to the central database
  only; each tenant connects with its own credentials.
- `php artisan storage:link` once: tenant files are served from
  `storage/app/public/tenants/{tenant id}` through `public/storage`.

## Deploy script

```sh
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force          # central database
php artisan tenants:migrate-safe     # every tenant; one failure never stops the rest
php artisan optimize
```

`tenants:migrate-safe` prints a table per tenant and exits non-zero if any tenant
failed, after trying all of them. `--tenants=<id>` limits it to some tenants.

## Environment

See `.env.example`. The ones that matter for tenancy:

| Variable | Value |
| --- | --- |
| `DB_*` | the central database |
| `TENANCY_CENTRAL_DOMAINS` | `central.mtportal.nl` (comma separated) |
| `PORTAL_PROVISIONING_SECRET` | same value as the website's |
| `SESSION_DRIVER` | `database` (tenant database) |
| `CACHE_STORE` | `file` (a directory per tenant) |
| `QUEUE_CONNECTION` | `sync` |
| `TENANT_URL_SCHEME` | `https` |
| `ADMIN_CLAIM_TTL_MINUTES` | `30` |

Leave `PLUGIN_API_URL`, `PLUGIN_API_KEY`, `MINECRAFT_API_KEY` and
`MC_SERVER_ADDRESS` empty: they come from the tenant, and a tenant without a key
never falls back to these.

## Moving an existing portal in

The existing per-portal databases become the tenant databases as they are. Their
`migrations` table already lists every file in `database/migrations/tenant`
(same names as before), so nothing re-runs; a PUT from the website reports
`"migrations": {"ran": []}`.

Uploaded files (plot listing images) have to be copied by hand, once:

```sh
cp -a <old site>/storage/app/public/. storage/app/public/tenants/<tenant id>/
```

## Running a command for one tenant

Commands such as `user:set-admin` run in the central context by default. Wrap
them: `php artisan tenants:run user:set-admin --tenants=<id>`.
