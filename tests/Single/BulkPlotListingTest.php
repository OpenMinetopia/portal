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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Fakes\FakeBankingService;
use Tests\Fakes\FakePlotService;

const BULK_OWNER = '66666666-aaaa-4aaa-8aaa-666666666666';
const BULK_OTHER = '77777777-bbbb-4bbb-8bbb-777777777777';
const BULK_URL = 'http://portaal.test';

beforeEach(function () {
    Http::preventStrayRequests();
    Storage::fake('public');
    $this->app->bind(PluginApiService::class, FakePluginApiService::class);
    $this->app->bind(MojangApiService::class, FakeMojangApiService::class);

    $this->bank = new FakeBankingService();
    $this->bank->addAccount('acc-bulk-owner', BULK_OWNER, 0);
    $this->bank->addAccount('acc-bulk-other', BULK_OTHER, 0);
    $this->app->instance(BankingService::class, $this->bank);

    $this->plots = new FakePlotService();
    $this->plots->addPlot('straat_1', [BULK_OWNER]);
    $this->plots->addPlot('straat_2', [BULK_OWNER]);
    $this->plots->addPlot('straat_3', [BULK_OWNER]);
    $this->plots->addPlot('lid_van', [BULK_OTHER], [BULK_OWNER]);
    $this->plots->addPlot('niet_van_mij', [BULK_OTHER]);
    $this->app->instance(PlotService::class, $this->plots);

    PortalFeature::where('key', 'broker')->update(['is_enabled' => true]);

    $this->owner = bulkUser('Makelaar', BULK_OWNER);
});

function bulkUser(string $name, string $uuid): User
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

function bulkSale(array $plots, array $override = []): array
{
    return $override + [
        'plots' => $plots, 'type' => 'sale', 'price' => 2500, 'description' => 'Rijtjeshuis in de straat',
        'instant_buy' => '1', 'payout_bank_account_uuid' => 'acc-bulk-owner',
    ];
}

it('shows checkboxes for own plots only and a form for the selection', function () {
    $this->actingAs($this->owner)->get(BULK_URL.'/portal/plots')->assertOk()
        ->assertSee('value="straat_1"', false)
        ->assertDontSee('value="lid_van"', false)
        ->assertSee('Alle eigen plots selecteren');

    $this->get(BULK_URL.'/plots/meerdere?plots[]=straat_1&plots[]=straat_2&plots[]=niet_van_mij')->assertOk()
        ->assertSee('2 plots op de markt zetten')
        ->assertSee('straat_2')
        ->assertDontSee('niet_van_mij');

    $this->get(BULK_URL.'/plots/meerdere?plots[]=niet_van_mij')->assertRedirect(BULK_URL.'/portal/plots');
});

it('puts several own plots up for sale with the same settings in one go', function () {
    $this->actingAs($this->owner)
        ->post(BULK_URL.'/plots/meerdere', bulkSale(['straat_1', 'straat_2'], ['image' => UploadedFile::fake()->image('straat.jpg')]))
        ->assertRedirect(BULK_URL.'/portal/plots')
        ->assertSessionHas('success');

    $listings = PlotListing::orderBy('plot_name')->get();
    expect($listings->pluck('plot_name')->all())->toBe(['straat_1', 'straat_2'])
        ->and($listings->pluck('price')->unique()->all())->toBe(['2500.00'])
        ->and($listings->every(fn ($listing) => $listing->seller_id === $this->owner->id
            && $listing->instant_buy && !$listing->is_server_listing
            && $listing->payout_bank_account_uuid === 'acc-bulk-owner'
            && $listing->description === 'Rijtjeshuis in de straat'))->toBeTrue()
        ->and($listings->pluck('image_path')->unique())->toHaveCount(1);

    Storage::disk('public')->assertExists($listings->first()->image_path);
});

it('puts several plots up for rent with a period', function () {
    $this->actingAs($this->owner)
        ->post(BULK_URL.'/plots/meerdere', bulkSale(['straat_1', 'straat_3'], ['type' => 'rent', 'price' => 120, 'period' => 'month']))
        ->assertSessionHas('success');

    $offers = PlotRentalOffer::orderBy('plot_name')->get();
    expect($offers->pluck('plot_name')->all())->toBe(['straat_1', 'straat_3'])
        ->and($offers->pluck('period')->unique()->all())->toBe(['month'])
        ->and($offers->pluck('status')->unique()->all())->toBe(['active'])
        ->and(PlotListing::count())->toBe(0);
});

it('refuses the whole batch when one plot is not the player\'s own', function (string $plot) {
    $this->actingAs($this->owner)
        ->post(BULK_URL.'/plots/meerdere', bulkSale(['straat_1', $plot]))
        ->assertSessionHasErrors('plots');

    expect(PlotListing::count())->toBe(0);
})->with(['someone else\'s plot' => 'niet_van_mij', 'a plot they are only a member of' => 'lid_van', 'a plot that does not exist' => 'verzonnen']);

it('refuses a payout account that is not the player\'s', function () {
    $this->actingAs($this->owner)
        ->post(BULK_URL.'/plots/meerdere', bulkSale(['straat_1'], ['payout_bank_account_uuid' => 'acc-bulk-other']))
        ->assertSessionHasErrors('payout_bank_account_uuid');

    expect(PlotListing::count())->toBe(0);
});

it('skips plots that are already listed or rented and says which', function () {
    PlotListing::create([
        'plot_name' => 'straat_1', 'seller_id' => $this->owner->id, 'payout_bank_account_uuid' => 'acc-bulk-owner',
        'price' => 1, 'description' => 'Al te koop', 'min_x' => 0, 'min_y' => 0, 'min_z' => 0, 'max_x' => 1, 'max_y' => 1, 'max_z' => 1,
        'instant_buy' => true, 'status' => 'active',
    ]);
    $offer = PlotRentalOffer::create([
        'plot_name' => 'straat_2', 'landlord_id' => $this->owner->id, 'payout_bank_account_uuid' => 'acc-bulk-owner',
        'price' => 50, 'period' => 'week', 'description' => 'Verhuurd', 'status' => 'rented',
        'min_x' => 0, 'min_y' => 0, 'min_z' => 0, 'max_x' => 1, 'max_y' => 1, 'max_z' => 1,
    ]);
    PlotRental::create([
        'plot_rental_offer_id' => $offer->id, 'plot_name' => 'straat_2', 'landlord_id' => $this->owner->id,
        'renter_id' => bulkUser('Huurder', BULK_OTHER)->id, 'renter_bank_account_uuid' => 'acc-bulk-other',
        'price' => 50, 'period' => 'week', 'status' => 'active', 'started_at' => now(), 'paid_until' => now()->addWeek(),
    ]);

    $this->actingAs($this->owner)
        ->post(BULK_URL.'/plots/meerdere', bulkSale(['straat_1', 'straat_2', 'straat_3']))
        ->assertSessionHas('success', fn ($flash) => str_contains($flash['message'], 'straat_3')
            && str_contains($flash['message'], 'straat_1 (staat al te koop)')
            && str_contains($flash['message'], 'straat_2 (wordt verhuurd)'));

    expect(PlotListing::where('status', 'active')->pluck('plot_name')->sort()->values()->all())->toBe(['straat_1', 'straat_3'])
        ->and(PlotListing::where('plot_name', 'straat_1')->value('price'))->toBe('1.00');

    // Renting: straat_2 is already rented out, the others are free to rent.
    $this->post(BULK_URL.'/plots/meerdere', bulkSale(['straat_2'], ['type' => 'rent', 'period' => 'week']))
        ->assertSessionHas('error', fn ($flash) => str_contains($flash['message'], 'straat_2 (staat al te huur of wordt verhuurd)'));
});

it('lets an admin list any plots for the server without a payout account', function () {
    $this->owner->roles()->attach(Role::where('is_admin', true)->firstOrFail());

    $this->actingAs($this->owner)->get(BULK_URL.'/portal/admin/plots')->assertOk()
        ->assertSee('value="niet_van_mij"', false)->assertSee('Alle plots selecteren');
    $this->get(BULK_URL.'/portal/admin/plots/meerdere?plots[]=niet_van_mij&plots[]=lid_van')->assertOk()->assertSee('Geen uitbetaling');

    $this->post(BULK_URL.'/portal/admin/plots/meerdere', bulkSale(['niet_van_mij', 'lid_van'], ['payout_bank_account_uuid' => '']))
        ->assertRedirect(BULK_URL.'/portal/admin/plots')
        ->assertSessionHas('success');

    $listings = PlotListing::all();
    expect($listings)->toHaveCount(2)
        ->and($listings->every(fn ($listing) => $listing->is_server_listing && $listing->payout_bank_account_uuid === null))->toBeTrue();

    $this->post(BULK_URL.'/portal/admin/plots/meerdere', bulkSale(['verzonnen']))->assertSessionHasErrors('plots');
});

it('keeps bulk listing for admins only', function () {
    $this->actingAs($this->owner)->post(BULK_URL.'/portal/admin/plots/meerdere', bulkSale(['niet_van_mij']))->assertForbidden();

    expect(PlotListing::count())->toBe(0);
});

it('still puts a single plot up for sale the old way', function () {
    $this->actingAs($this->owner)->get(BULK_URL.'/plots/straat_1/verkopen')->assertOk();

    $this->post(BULK_URL.'/plots/straat_1/verkopen', [
        'price' => 3000, 'description' => 'Los huis', 'instant_buy' => '1', 'payout_bank_account_uuid' => 'acc-bulk-owner',
    ])->assertRedirect(BULK_URL.'/portal/plots/straat_1');

    expect(PlotListing::sole()->only(['plot_name', 'price', 'instant_buy']))->toBe(['plot_name' => 'straat_1', 'price' => '3000.00', 'instant_buy' => true]);
});
