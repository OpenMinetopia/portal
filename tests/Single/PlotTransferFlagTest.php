<?php

use App\Demo\FakeMojangApiService;
use App\Demo\FakePluginApiService;
use App\Models\PlotListing;
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

// Plots with the plugin flag openmt-transfer on DENY (/plot transfer false) may not be sold or rented.

const FLAG_OWNER = '88888888-aaaa-4aaa-8aaa-888888888888';
const FLAG_BUYER = '99999999-bbbb-4bbb-8bbb-999999999999';
const FLAG_URL = 'http://portaal.test';
const DENY = ['openmt-transfer' => 'DENY'];

beforeEach(function () {
    Http::preventStrayRequests();
    $this->app->bind(PluginApiService::class, FakePluginApiService::class);
    $this->app->bind(MojangApiService::class, FakeMojangApiService::class);

    $this->bank = new FakeBankingService();
    $this->bank->addAccount('acc-flag-owner', FLAG_OWNER, 0);
    $this->bank->addAccount('acc-flag-buyer', FLAG_BUYER, 100000);
    $this->app->instance(BankingService::class, $this->bank);

    $this->plots = new FakePlotService();
    $this->plots->addPlot('gemeentehuis', [FLAG_OWNER], [], DENY);
    $this->plots->addPlot('rijtjeshuis', [FLAG_OWNER], [], ['openmt-transfer' => 'ALLOW']);
    $this->app->instance(PlotService::class, $this->plots);

    PortalFeature::where('key', 'broker')->update(['is_enabled' => true]);

    $this->owner = flagUser('Eigenaar', FLAG_OWNER);
});

function flagUser(string $name, string $uuid): User
{
    return User::create([
        'name' => $name, 'email' => Str::lower($name).'@example.com', 'minecraft_username' => $name,
        'minecraft_uuid' => str_replace('-', '', $uuid), 'minecraft_plain_uuid' => $uuid,
        'password' => Hash::make('geheim123'), 'token' => Str::random(32), 'minecraft_verified' => true,
    ]);
}

it('reads the flag: only DENY blocks, a missing flag does not', function () {
    expect(PlotService::isTransferable(['flags' => DENY]))->toBeFalse()
        ->and(PlotService::isTransferable(['flags' => ['openmt-transfer' => 'deny']]))->toBeFalse()
        ->and(PlotService::isTransferable(['flags' => ['openmt-transfer' => 'ALLOW']]))->toBeTrue()
        ->and(PlotService::isTransferable(['flags' => []]))->toBeTrue();
});

it('does not let an owner sell or rent out a plot that may not be transferred', function () {
    $this->actingAs($this->owner);

    $this->get(FLAG_URL.'/portal/plots/gemeentehuis')->assertOk()
        ->assertSee('Niet overdraagbaar')->assertDontSee('Plot Verkopen')->assertDontSee('Plot Verhuren');
    $this->get(FLAG_URL.'/portal/plots/rijtjeshuis')->assertOk()->assertSee('Plot Verkopen');

    $this->get(FLAG_URL.'/plots/gemeentehuis/verkopen')->assertRedirect()->assertSessionHas('error', PlotService::NOT_TRANSFERABLE);
    $this->post(FLAG_URL.'/plots/gemeentehuis/verkopen', [
        'price' => 1000, 'description' => 'Te koop', 'payout_bank_account_uuid' => 'acc-flag-owner',
    ])->assertSessionHas('error', PlotService::NOT_TRANSFERABLE);

    $this->get(FLAG_URL.'/plots/gemeentehuis/verhuren')->assertRedirect()->assertSessionHas('error', PlotService::NOT_TRANSFERABLE);
    $this->post(FLAG_URL.'/plots/gemeentehuis/verhuren', [
        'price' => 100, 'period' => 'week', 'description' => 'Te huur', 'payout_bank_account_uuid' => 'acc-flag-owner',
    ])->assertSessionHas('error', PlotService::NOT_TRANSFERABLE);

    expect(PlotListing::count())->toBe(0)->and(PlotRentalOffer::count())->toBe(0);
});

it('skips such plots when listing several at once', function () {
    $this->actingAs($this->owner)->get(FLAG_URL.'/portal/plots')->assertOk()
        ->assertSee('value="rijtjeshuis"', false)
        ->assertDontSee('value="gemeentehuis"', false);

    $this->post(FLAG_URL.'/plots/meerdere', [
        'plots' => ['gemeentehuis', 'rijtjeshuis'], 'type' => 'sale', 'price' => 2500, 'description' => 'Huis',
        'instant_buy' => '1', 'payout_bank_account_uuid' => 'acc-flag-owner',
    ])->assertRedirect();

    expect(PlotListing::pluck('plot_name')->all())->toBe(['rijtjeshuis']);
});

it('does not let an admin sell or rent out such a plot for the server either', function () {
    $admin = flagUser('Beheerder', '12121212-cccc-4ccc-8ccc-121212121212');
    $admin->roles()->attach(Role::where('slug', 'admin')->value('id'));
    $this->actingAs($admin);

    $this->get(FLAG_URL.'/portal/admin/plots/gemeentehuis')->assertOk()->assertSee('Niet overdraagbaar')->assertDontSee('Te koop zetten');
    $this->get(FLAG_URL.'/portal/admin/plots/gemeentehuis/verkopen')->assertRedirect()->assertSessionHas('error', PlotService::NOT_TRANSFERABLE);
    $this->get(FLAG_URL.'/portal/admin/plots/gemeentehuis/verhuren')->assertRedirect()->assertSessionHas('error', PlotService::NOT_TRANSFERABLE);
});

it('refuses a purchase or rental when the flag was turned off after listing', function () {
    $listing = PlotListing::create([
        'plot_name' => 'rijtjeshuis', 'seller_id' => $this->owner->id, 'payout_bank_account_uuid' => 'acc-flag-owner',
        'price' => 1000, 'description' => 'Te koop', 'status' => 'active', 'instant_buy' => true,
        'min_x' => 0, 'min_y' => 60, 'min_z' => 0, 'max_x' => 9, 'max_y' => 80, 'max_z' => 9,
    ]);
    $offer = PlotRentalOffer::create([
        'plot_name' => 'rijtjeshuis', 'landlord_id' => $this->owner->id, 'payout_bank_account_uuid' => 'acc-flag-owner',
        'price' => 100, 'period' => 'week', 'description' => 'Te huur', 'status' => 'active',
        'min_x' => 0, 'min_y' => 60, 'min_z' => 0, 'max_x' => 9, 'max_y' => 80, 'max_z' => 9,
    ]);

    $this->plots->plots['rijtjeshuis']['flags'] = DENY;
    $this->actingAs(flagUser('Koper', FLAG_BUYER));

    $this->post(FLAG_URL."/plots/te-koop/{$listing->id}/kopen", ['buyer_bank_account_uuid' => 'acc-flag-buyer', 'terms' => '1'])
        ->assertSessionHas('error', PlotService::NOT_TRANSFERABLE);
    $this->post(FLAG_URL."/plots/te-huur/{$offer->id}/huren", ['renter_bank_account_uuid' => 'acc-flag-buyer', 'terms' => '1'])
        ->assertSessionHas('error');

    expect($listing->fresh()->status)->toBe('active')
        ->and($this->bank->accounts['acc-flag-buyer']['balance'])->toEqual(100000)
        ->and($this->plots->members('rijtjeshuis'))->toBe([]);
});
