<?php

use App\Demo\FakeMojangApiService;
use App\Demo\FakePluginApiService;
use App\Models\PlotListing;
use App\Models\PlotRental;
use App\Models\PlotRentalOffer;
use App\Models\PortalFeature;
use App\Models\Role;
use App\Models\User;
use App\Services\MojangApiService;
use App\Services\Plugin\BankingService;
use App\Services\Plugin\PlotService;
use App\Services\Plugin\PluginApiService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Fakes\FakeBankingService;
use Tests\Fakes\FakePlotService;

const OWNER_UUID = '33333333-cccc-4ccc-8ccc-333333333333';
const TENANT_UUID = '44444444-dddd-4ddd-8ddd-444444444444';
const STRANGER_UUID = '55555555-eeee-4eee-8eee-555555555555';
const BASE = 'http://portaal.test';

beforeEach(function () {
    Http::preventStrayRequests();
    $this->travelTo(now()->setDate(2026, 10, 1)->setTime(12, 0));

    // Everything else the pages ask the plugin comes from the demo fake.
    $this->app->bind(PluginApiService::class, FakePluginApiService::class);
    $this->app->bind(MojangApiService::class, FakeMojangApiService::class);

    $this->bank = new FakeBankingService();
    $this->bank->addAccount('acc-owner', OWNER_UUID, 0);
    $this->bank->addAccount('acc-tenant', TENANT_UUID, 1000);
    $this->bank->addAccount('acc-stranger', STRANGER_UUID, 1000);
    $this->app->instance(BankingService::class, $this->bank);

    $this->plots = new FakePlotService();
    $this->plots->addPlot('kade_1', [OWNER_UUID]);
    $this->plots->addPlot('stadhuis', []);
    $this->app->instance(PlotService::class, $this->plots);

    PortalFeature::where('key', 'broker')->update(['is_enabled' => true]);

    $this->owner = flowUser('Eigenaar', OWNER_UUID);
    $this->renter = flowUser('Huurder', TENANT_UUID);
    $this->stranger = flowUser('Vreemde', STRANGER_UUID);
});

function flowUser(string $name, string $uuid): User
{
    return User::create([
        'name' => $name,
        'email' => Str::lower($name).'@example.com',
        'minecraft_username' => $name,
        'minecraft_uuid' => str_replace('-', '', $uuid),
        'minecraft_plain_uuid' => $uuid,
        'password' => Hash::make('geheim123'),
        'token' => Str::random(32),
        'minecraft_verified' => true,
    ]);
}

function offerKade(User $owner, array $attributes = []): PlotRentalOffer
{
    return PlotRentalOffer::create($attributes + [
        'plot_name' => 'kade_1', 'landlord_id' => $owner->id, 'payout_bank_account_uuid' => 'acc-owner',
        'price' => 150, 'period' => 'week', 'description' => 'Pand aan de kade',
        'min_x' => 0, 'min_y' => 60, 'min_z' => 0, 'max_x' => 9, 'max_y' => 80, 'max_z' => 9, 'status' => 'active',
    ]);
}

it('lets an owner offer a plot for rent and a player rent it', function () {
    $this->actingAs($this->owner)->get(BASE.'/portal/plots/kade_1')->assertOk()->assertSee('Plot Verhuren');
    $this->get(BASE.'/plots/kade_1/verhuren')->assertOk()->assertSee('Plot te huur zetten');

    $this->post(BASE.'/plots/kade_1/verhuren', [
        'price' => 150, 'period' => 'week', 'description' => 'Pand aan de kade', 'payout_bank_account_uuid' => 'acc-owner',
    ])->assertRedirect(BASE.'/portal/plots/kade_1');

    $offer = PlotRentalOffer::sole();
    expect($offer->status)->toBe('active')->and($offer->is_server_listing)->toBeFalse();

    // Buying and renting are tabs, so many sale listings never push the rentals out of sight.
    $this->actingAs($this->renter)->get(BASE.'/plots/te-koop')->assertOk()->assertSee('Te huur')->assertDontSee('€ 150,00 per week');
    $this->actingAs($this->renter)->get(BASE.'/plots/te-koop?soort=huur')->assertOk()->assertSee('kade_1')->assertSee('€ 150,00 per week');
    $this->get(BASE."/plots/te-huur/{$offer->id}/huren")->assertOk()->assertSee('automatisch');

    $this->post(BASE."/plots/te-huur/{$offer->id}/huren", ['renter_bank_account_uuid' => 'acc-tenant', 'terms' => '1'])
        ->assertRedirect(BASE.'/plots/huur');

    expect($this->plots->members('kade_1'))->toBe([TENANT_UUID])
        ->and($this->bank->balance('acc-tenant'))->toBe(850.0)
        ->and($this->bank->balance('acc-owner'))->toBe(150.0)
        ->and($offer->fresh()->status)->toBe('rented');

    $this->get(BASE.'/plots/huur')->assertOk()->assertSee('kade_1')->assertSee('Betaald tot')->assertSee('08-10-2026 12:00');
    $this->actingAs($this->owner)->get(BASE.'/plots/huur')->assertOk()->assertSee('Plots die ik verhuur')->assertSee('Huurder');
    $this->get(BASE.'/plots/te-koop?soort=huur')->assertDontSee('/plots/te-huur/'.$offer->id.'/huren');
});

it('does not let a player rent with someone else\'s account or without enough money', function () {
    $offer = offerKade($this->owner);

    $this->actingAs($this->renter)
        ->post(BASE."/plots/te-huur/{$offer->id}/huren", ['renter_bank_account_uuid' => 'acc-stranger', 'terms' => '1'])
        ->assertSessionHas('error');

    $this->bank->accounts['acc-tenant']['balance'] = 100;
    $this->post(BASE."/plots/te-huur/{$offer->id}/huren", ['renter_bank_account_uuid' => 'acc-tenant', 'terms' => '1'])
        ->assertSessionHas('error');

    expect($this->bank->withdrawals())->toBe([])
        ->and($this->plots->members('kade_1'))->toBe([])
        ->and($offer->fresh()->status)->toBe('active');
});

it('does not let an owner rent out their own plot to themselves or pay out to a foreign account', function () {
    $offer = offerKade($this->owner);

    $this->actingAs($this->owner)->get(BASE."/plots/te-huur/{$offer->id}/huren")->assertRedirect(BASE.'/plots/te-koop?soort=huur');

    $offer->update(['status' => 'cancelled']);
    $this->post(BASE.'/plots/kade_1/verhuren', [
        'price' => 150, 'period' => 'week', 'description' => 'x', 'payout_bank_account_uuid' => 'acc-stranger',
    ])->assertSessionHasErrors('payout_bank_account_uuid');

    $this->actingAs($this->stranger)->get(BASE.'/plots/kade_1/verhuren')->assertRedirect(BASE.'/portal/plots/kade_1');
});

it('rents or sells a plot, never both', function () {
    $listing = PlotListing::create([
        'plot_name' => 'kade_1', 'seller_id' => $this->owner->id, 'payout_bank_account_uuid' => 'acc-owner',
        'price' => 5000, 'description' => 'Te koop', 'min_x' => 0, 'min_y' => 0, 'min_z' => 0, 'max_x' => 1, 'max_y' => 1, 'max_z' => 1,
        'instant_buy' => true, 'status' => 'active',
    ]);
    $offer = offerKade($this->owner);

    $this->actingAs($this->renter)->post(BASE."/plots/te-huur/{$offer->id}/huren", ['renter_bank_account_uuid' => 'acc-tenant', 'terms' => '1']);

    expect($listing->fresh()->status)->toBe('cancelled');

    $this->actingAs($this->owner)->get(BASE.'/plots/kade_1/verkopen')->assertRedirect(BASE.'/portal/plots/kade_1')->assertSessionHas('error');
    $this->post(BASE.'/plots/kade_1/verkopen', ['price' => 1, 'description' => 'x', 'payout_bank_account_uuid' => 'acc-owner'])
        ->assertSessionHas('error');
    expect(PlotListing::where('status', 'active')->count())->toBe(0);
});

it('lets the renter and the landlord give notice, nobody else', function () {
    $offer = offerKade($this->owner);
    $this->actingAs($this->renter)->post(BASE."/plots/te-huur/{$offer->id}/huren", ['renter_bank_account_uuid' => 'acc-tenant', 'terms' => '1']);
    $rental = PlotRental::sole();

    $this->actingAs($this->stranger)->post(BASE."/plots/huur/{$rental->id}/opzeggen")->assertForbidden();

    $this->actingAs($this->renter)->post(BASE."/plots/huur/{$rental->id}/opzeggen")->assertSessionHas('success');
    expect($rental->fresh()->ends_at->toDateTimeString())->toBe('2026-10-08 12:00:00')
        ->and($rental->fresh()->cancelled_by)->toBe('renter');

    $this->get(BASE.'/plots/huur')->assertSee('Opgezegd')->assertSee('Eindigt op');
    $this->actingAs($this->owner)->post(BASE."/plots/huur/{$rental->id}/opzeggen")->assertSessionHas('error');
});

it('lets an overdue renter pay right away', function () {
    $offer = offerKade($this->owner);
    $this->actingAs($this->renter)->post(BASE."/plots/te-huur/{$offer->id}/huren", ['renter_bank_account_uuid' => 'acc-tenant', 'terms' => '1']);
    $rental = PlotRental::sole();

    $this->bank->accounts['acc-tenant']['balance'] = 0;
    $this->travelTo('2026-10-08 12:00:00');
    $this->artisan('plots:process-rentals')->assertSuccessful();

    $this->get(BASE.'/plots/huur')->assertSee('Betaling achterstallig')->assertSee('15-10-2026 12:00')->assertSee('Nu betalen');

    $this->post(BASE."/plots/huur/{$rental->id}/betalen", ['renter_bank_account_uuid' => 'acc-tenant'])->assertSessionHas('error');

    $this->bank->accounts['acc-tenant']['balance'] = 200;
    $this->post(BASE."/plots/huur/{$rental->id}/betalen", ['renter_bank_account_uuid' => 'acc-tenant'])->assertSessionHas('success');

    expect($rental->fresh()->isOverdue())->toBeFalse()
        ->and($rental->fresh()->paid_until->toDateTimeString())->toBe('2026-10-15 12:00:00')
        ->and($this->bank->balance('acc-tenant'))->toBe(50.0);
});

it('does not let the landlord remove the renter as a plain member', function () {
    $offer = offerKade($this->owner);
    $this->actingAs($this->renter)->post(BASE."/plots/te-huur/{$offer->id}/huren", ['renter_bank_account_uuid' => 'acc-tenant', 'terms' => '1']);

    $this->actingAs($this->owner)->delete(BASE.'/portal/plots/kade_1/members', ['user_uuid' => TENANT_UUID])->assertSessionHas('error');

    expect($this->plots->members('kade_1'))->toBe([TENANT_UUID]);
});

it('lets an admin rent out a server plot without a payout account', function () {
    $this->owner->roles()->attach(Role::where('is_admin', true)->firstOrFail());

    $this->actingAs($this->owner)->get(BASE.'/portal/admin/plots/stadhuis')->assertOk()->assertSee('Verhuren');
    $this->get(BASE.'/portal/admin/plots/stadhuis/verhuren')->assertOk()->assertSee('Geen uitbetaling');
    $this->post(BASE.'/portal/admin/plots/stadhuis/verhuren', ['price' => 75, 'period' => 'month', 'description' => 'Kantoor in het stadhuis'])
        ->assertRedirect(BASE.'/portal/admin/plots/stadhuis');

    $offer = PlotRentalOffer::sole();
    expect($offer->is_server_listing)->toBeTrue()->and($offer->payout_bank_account_uuid)->toBeNull();

    $this->actingAs($this->renter)->post(BASE."/plots/te-huur/{$offer->id}/huren", ['renter_bank_account_uuid' => 'acc-tenant', 'terms' => '1'])
        ->assertRedirect(BASE.'/plots/huur');

    expect($this->bank->deposits())->toBe([])
        ->and(PlotRental::sole()->paid_until->toDateTimeString())->toBe('2026-11-01 12:00:00');

    $this->actingAs($this->owner)->get(BASE.'/plots/huur')->assertSee('stadhuis')->assertSee('Huurder');
});

it('hides renting while the makelaar is off, but keeps my rentals reachable', function () {
    $offer = offerKade($this->owner);
    $this->actingAs($this->renter)->post(BASE."/plots/te-huur/{$offer->id}/huren", ['renter_bank_account_uuid' => 'acc-tenant', 'terms' => '1']);
    PortalFeature::where('key', 'broker')->update(['is_enabled' => false]);

    $this->get(BASE.'/plots/te-koop')->assertNotFound();
    $this->get(BASE.'/plots/huur')->assertOk()->assertSee('kade_1');
    $this->post(BASE.'/plots/huur/'.PlotRental::sole()->id.'/opzeggen')->assertSessionHas('success');
});
