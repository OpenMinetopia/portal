<?php

use App\Models\PlotRental;
use App\Models\PlotRentalOffer;
use App\Models\PlotRentalPayment;
use App\Models\Tenant;
use App\Models\User;
use App\Services\PlotRentalService;
use App\Services\Plugin\BankingService;
use App\Services\Plugin\PlotService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\Fakes\FakeBankingService;
use Tests\Fakes\FakePlotService;

const LANDLORD = '11111111-aaaa-4aaa-8aaa-111111111111';
const RENTER = '22222222-bbbb-4bbb-8bbb-222222222222';
const LANDLORD_ACCOUNT = 'acc-landlord';
const RENTER_ACCOUNT = 'acc-renter';

beforeEach(function () {
    $this->travelTo(now()->setDate(2026, 10, 1)->setTime(12, 0));

    $this->bank = new FakeBankingService();
    $this->bank->addAccount(LANDLORD_ACCOUNT, LANDLORD, 0);
    $this->bank->addAccount(RENTER_ACCOUNT, RENTER, 1000);
    $this->app->instance(BankingService::class, $this->bank);

    $this->plots = new FakePlotService();
    $this->plots->addPlot('kade_1', [LANDLORD]);
    $this->app->instance(PlotService::class, $this->plots);

    $this->tenant = $this->createTenant('alpha');
});

function rentalUser(Tenant $tenant, string $name, string $uuid): User
{
    return $tenant->run(fn () => User::create([
        'name' => $name,
        'email' => Str::lower($name).'@example.com',
        'minecraft_username' => $name,
        'minecraft_uuid' => str_replace('-', '', $uuid),
        'minecraft_plain_uuid' => $uuid,
        'password' => Hash::make('geheim123'),
        'token' => Str::random(32),
        'minecraft_verified' => true,
    ]));
}

/** A running weekly rental of €100 on kade_1, started now. */
function startRental(Tenant $tenant, array $offer = []): PlotRental
{
    $landlord = rentalUser($tenant, 'Landlord', LANDLORD);
    $renter = rentalUser($tenant, 'Renter', RENTER);

    return $tenant->run(function () use ($landlord, $renter, $offer) {
        $offer = PlotRentalOffer::create($offer + [
            'plot_name' => 'kade_1', 'landlord_id' => $landlord->id, 'payout_bank_account_uuid' => LANDLORD_ACCOUNT,
            'price' => 100, 'period' => 'week', 'description' => 'Pand aan de kade',
            'min_x' => 0, 'min_y' => 60, 'min_z' => 0, 'max_x' => 9, 'max_y' => 80, 'max_z' => 9,
        ]);

        return app(PlotRentalService::class)->start($offer, $renter, RENTER_ACCOUNT);
    });
}

function processRentals(Tenant $tenant): array
{
    return $tenant->run(fn () => app(PlotRentalService::class)->processDue());
}

function notificationTypes(Tenant $tenant, int $userId): array
{
    return $tenant->run(fn () => User::find($userId)->notifications()->oldest()->get()->pluck('data.type')->all());
}

it('starts a rental: pays the first week up front and makes the renter a member', function () {
    $rental = startRental($this->tenant);

    expect($this->bank->balance(RENTER_ACCOUNT))->toBe(900.0)
        ->and($this->bank->balance(LANDLORD_ACCOUNT))->toBe(100.0)
        ->and($this->plots->members('kade_1'))->toBe([RENTER])
        ->and($this->plots->plots['kade_1']['owners'])->toBe([LANDLORD])
        ->and($this->tenant->run(fn () => $rental->paid_until->toDateTimeString()))->toBe('2026-10-08 12:00:00')
        ->and($this->tenant->run(fn () => PlotRentalOffer::first()->status))->toBe('rented')
        ->and(notificationTypes($this->tenant, $rental->renter_id))->toEqualCanonicalizing(['started'])
        ->and(notificationTypes($this->tenant, $rental->landlord_id))->toEqualCanonicalizing(['rented']);
});

it('renews at the end of the week and never charges a period twice', function () {
    $rental = startRental($this->tenant);

    expect(processRentals($this->tenant)['renewed'])->toBe(0);

    $this->travelTo('2026-10-08 12:00:00');
    expect(processRentals($this->tenant)['renewed'])->toBe(1);
    expect(processRentals($this->tenant)['renewed'])->toBe(0);

    $this->travelTo('2026-10-08 18:30:00');
    processRentals($this->tenant);

    $rental = $this->tenant->run(fn () => $rental->fresh());
    expect($this->bank->balance(RENTER_ACCOUNT))->toBe(800.0)
        ->and($this->bank->balance(LANDLORD_ACCOUNT))->toBe(200.0)
        ->and($this->bank->withdrawals())->toHaveCount(2)
        ->and($this->tenant->run(fn () => $rental->paid_until->toDateTimeString()))->toBe('2026-10-15 12:00:00')
        ->and($this->tenant->run(fn () => $rental->payments()->where('status', 'paid')->count()))->toBe(2)
        ->and(notificationTypes($this->tenant, $rental->renter_id))->toEqualCanonicalizing(['started', 'renewed'])
        ->and(notificationTypes($this->tenant, $rental->landlord_id))->toEqualCanonicalizing(['rented', 'payment_received']);
});

it('renews a monthly rental on the same day next month', function () {
    $rental = startRental($this->tenant, ['period' => 'month']);
    expect($this->tenant->run(fn () => $rental->paid_until->toDateTimeString()))->toBe('2026-11-01 12:00:00');

    $this->travelTo('2026-11-01 13:00:00');
    processRentals($this->tenant);

    expect($this->tenant->run(fn () => $rental->fresh()->paid_until->toDateTimeString()))->toBe('2026-12-01 12:00:00');
});

it('does not charge again when an earlier run stopped halfway through a payment', function () {
    $rental = startRental($this->tenant);

    // A run that wrote its payment row and then died: it may or may not have withdrawn.
    $this->tenant->run(fn () => $rental->payments()->create([
        'period_start' => $rental->paid_until, 'period_end' => $rental->paid_until->copy()->addWeek(),
        'amount' => 100, 'bank_account_uuid' => RENTER_ACCOUNT, 'status' => 'pending',
    ]));

    $this->travelTo('2026-10-08 13:00:00');
    expect(processRentals($this->tenant)['skipped'])->toBe(1);

    expect($this->bank->withdrawals())->toHaveCount(1)
        ->and($this->tenant->run(fn () => $rental->fresh()->overdue_since))->toBeNull();
});

it('gives the renter 7 days when the balance is too low, and renews once they pay', function () {
    $rental = startRental($this->tenant);
    $this->bank->accounts[RENTER_ACCOUNT]['balance'] = 40;

    $this->travelTo('2026-10-08 12:00:00');
    expect(processRentals($this->tenant)['overdue'])->toBe(1);

    $rental = $this->tenant->run(fn () => $rental->fresh());
    expect($this->tenant->run(fn () => $rental->isOverdue()))->toBeTrue()
        ->and($this->tenant->run(fn () => $rental->grace_until->toDateTimeString()))->toBe('2026-10-15 12:00:00')
        ->and($this->plots->members('kade_1'))->toBe([RENTER])
        ->and($this->tenant->run(fn () => PlotRentalPayment::count()))->toBe(1)
        ->and(notificationTypes($this->tenant, $rental->renter_id))->toEqualCanonicalizing(['started', 'payment_failed'])
        ->and(notificationTypes($this->tenant, $rental->landlord_id))->toEqualCanonicalizing(['rented', 'overdue']);

    // Retried every run, without notifying again.
    $this->travelTo('2026-10-10 12:00:00');
    processRentals($this->tenant);
    expect(notificationTypes($this->tenant, $rental->renter_id))->toEqualCanonicalizing(['started', 'payment_failed']);

    $this->bank->accounts[RENTER_ACCOUNT]['balance'] = 150;
    $this->travelTo('2026-10-12 09:00:00');
    expect(processRentals($this->tenant)['renewed'])->toBe(1);

    $rental = $this->tenant->run(fn () => $rental->fresh());
    expect($this->tenant->run(fn () => $rental->isOverdue()))->toBeFalse()
        ->and($rental->grace_until)->toBeNull()
        ->and($this->tenant->run(fn () => $rental->paid_until->toDateTimeString()))->toBe('2026-10-15 12:00:00')
        ->and($this->bank->balance(RENTER_ACCOUNT))->toBe(50.0)
        ->and($this->plots->members('kade_1'))->toBe([RENTER]);
});

it('ends the rental and removes the renter after 7 days without payment', function () {
    $rental = startRental($this->tenant);
    $this->bank->accounts[RENTER_ACCOUNT]['balance'] = 0;

    $this->travelTo('2026-10-08 12:00:00');
    processRentals($this->tenant);

    $this->travelTo('2026-10-15 11:00:00');
    expect(processRentals($this->tenant)['evicted'])->toBe(0)
        ->and($this->plots->members('kade_1'))->toBe([RENTER]);

    $this->travelTo('2026-10-15 12:00:00');
    expect(processRentals($this->tenant)['evicted'])->toBe(1);

    $rental = $this->tenant->run(fn () => $rental->fresh());
    expect($rental->status)->toBe('ended')
        ->and($rental->end_reason)->toBe('unpaid')
        ->and($this->tenant->run(fn () => $rental->access_revoked_at))->not->toBeNull()
        ->and($this->plots->members('kade_1'))->toBe([])
        ->and($this->plots->plots['kade_1']['owners'])->toBe([LANDLORD])
        ->and($this->bank->withdrawals())->toHaveCount(1)
        ->and($this->tenant->run(fn () => PlotRentalOffer::first()->status))->toBe('active')
        ->and(notificationTypes($this->tenant, $rental->renter_id))->toEqualCanonicalizing(['started', 'payment_failed', 'evicted'])
        ->and(notificationTypes($this->tenant, $rental->landlord_id))->toEqualCanonicalizing(['rented', 'overdue', 'evicted_landlord']);

    // Nothing more happens to an ended rental.
    $this->travelTo('2026-11-15 12:00:00');
    expect(array_sum(processRentals($this->tenant)))->toBe(0);
});

it('keeps trying to remove an evicted renter while the plugin is unreachable', function () {
    $rental = startRental($this->tenant);
    $this->bank->accounts[RENTER_ACCOUNT]['balance'] = 0;

    $this->travelTo('2026-10-08 12:00:00');
    processRentals($this->tenant);

    $this->plots->reachable = false;
    $this->travelTo('2026-10-15 12:00:00');
    processRentals($this->tenant);

    expect($this->tenant->run(fn () => $rental->fresh()->status))->toBe('ended')
        ->and($this->tenant->run(fn () => $rental->fresh()->access_revoked_at))->toBeNull();

    $this->plots->reachable = true;
    $this->travelTo('2026-10-15 13:00:00');
    processRentals($this->tenant);

    expect($this->plots->members('kade_1'))->toBe([])
        ->and($this->tenant->run(fn () => $rental->fresh()->access_revoked_at))->not->toBeNull();
});

it('lets the renter give notice: no more charges, access until the paid week ends', function () {
    $rental = startRental($this->tenant);

    $this->travelTo('2026-10-03 12:00:00');
    $this->tenant->run(fn () => app(PlotRentalService::class)->cancel($rental->fresh(), 'renter'));

    expect($this->plots->members('kade_1'))->toBe([RENTER])
        ->and(notificationTypes($this->tenant, $rental->landlord_id))->toEqualCanonicalizing(['rented', 'cancelled_by_renter']);

    $this->travelTo('2026-10-08 12:00:00');
    expect(processRentals($this->tenant)['ended'])->toBe(1);

    $rental = $this->tenant->run(fn () => $rental->fresh());
    expect($rental->status)->toBe('ended')
        ->and($rental->end_reason)->toBe('renter')
        ->and($this->bank->withdrawals())->toHaveCount(1)
        ->and($this->plots->members('kade_1'))->toBe([])
        ->and($this->tenant->run(fn () => PlotRentalOffer::first()->status))->toBe('active');
});

it('takes the plot off the market when the landlord gives notice', function () {
    $rental = startRental($this->tenant);

    $this->tenant->run(fn () => app(PlotRentalService::class)->cancel($rental->fresh(), 'landlord'));
    $this->travelTo('2026-10-08 12:00:00');
    processRentals($this->tenant);

    expect($this->tenant->run(fn () => PlotRentalOffer::first()->status))->toBe('cancelled')
        ->and(notificationTypes($this->tenant, $rental->renter_id))->toEqualCanonicalizing(['started', 'cancelled_by_landlord', 'ended']);
});

it('ends an unpaid rental right away on notice', function () {
    $rental = startRental($this->tenant);
    $this->bank->accounts[RENTER_ACCOUNT]['balance'] = 0;

    $this->travelTo('2026-10-08 12:00:00');
    processRentals($this->tenant);
    $this->tenant->run(fn () => app(PlotRentalService::class)->cancel($rental->fresh(), 'renter'));

    expect($this->tenant->run(fn () => $rental->fresh()->status))->toBe('ended')
        ->and($this->plots->members('kade_1'))->toBe([]);
});

it('renews a server rental without a payout account, the money leaves the economy', function () {
    $rental = startRental($this->tenant, ['is_server_listing' => true, 'payout_bank_account_uuid' => null]);

    $this->travelTo('2026-10-08 12:00:00');
    processRentals($this->tenant);

    expect($this->bank->deposits())->toBe([])
        ->and($this->bank->balance(RENTER_ACCOUNT))->toBe(800.0)
        ->and($this->tenant->run(fn () => $rental->payments()->pluck('payout_status')->all()))->toBe([null, null]);
});

it('retries a failed payout to the landlord without charging the renter again', function () {
    $rental = startRental($this->tenant);

    $this->bank->failDeposits = true;
    $this->travelTo('2026-10-08 12:00:00');
    processRentals($this->tenant);

    expect($this->bank->balance(LANDLORD_ACCOUNT))->toBe(100.0)
        ->and($this->tenant->run(fn () => $rental->payments()->latest('id')->first()->payout_status))->toBe('failed');

    $this->bank->failDeposits = false;
    $this->travelTo('2026-10-08 13:00:00');
    processRentals($this->tenant);
    processRentals($this->tenant);

    expect($this->bank->balance(LANDLORD_ACCOUNT))->toBe(200.0)
        ->and($this->bank->balance(RENTER_ACCOUNT))->toBe(800.0)
        ->and($this->tenant->run(fn () => $rental->payments()->latest('id')->first()->payout_status))->toBe('done');
});

it('lets an overdue renter pay right away from another account', function () {
    $rental = startRental($this->tenant);
    $this->bank->accounts[RENTER_ACCOUNT]['balance'] = 0;
    $this->bank->addAccount('acc-renter-savings', RENTER, 500, 'BUSINESS');

    $this->travelTo('2026-10-08 12:00:00');
    processRentals($this->tenant);

    $result = $this->tenant->run(fn () => app(PlotRentalService::class)->payNow($rental->fresh(), 'acc-renter-savings'));

    $rental = $this->tenant->run(fn () => $rental->fresh());
    expect($result)->toBe('renewed')
        ->and($this->tenant->run(fn () => $rental->isOverdue()))->toBeFalse()
        ->and($rental->renter_bank_account_uuid)->toBe('acc-renter-savings')
        ->and($this->bank->balance('acc-renter-savings'))->toBe(400.0);
});

it('processes rentals in every active tenant from the scheduled command', function () {
    $beta = $this->createTenant('beta');
    $inactive = $this->createTenant('gamma', ['tenancy_db_name' => 'omt_portal_test_missing', 'status' => Tenant::INACTIVE]);

    $alphaRental = startRental($this->tenant);
    $this->bank->accounts[RENTER_ACCOUNT]['balance'] = 1000;
    $this->plots->plots['kade_1']['members'] = []; // The same fake plugin answers for both portals.
    $betaRental = startRental($beta);

    $this->travelTo('2026-10-08 12:00:00');
    $this->artisan('plots:process-rentals')->assertSuccessful();
    $this->artisan('plots:process-rentals')->assertSuccessful();

    expect($this->tenant->run(fn () => $alphaRental->fresh()->paid_until->toDateTimeString()))->toBe('2026-10-15 12:00:00')
        ->and($beta->run(fn () => $betaRental->fresh()->paid_until->toDateTimeString()))->toBe('2026-10-15 12:00:00')
        ->and($this->bank->withdrawals())->toHaveCount(4);
});

it('keeps going when one tenant fails', function () {
    $this->createTenant('broken', ['tenancy_db_name' => 'omt_portal_test_missing', 'name' => 'Aaa kapot']);
    $rental = startRental($this->tenant);

    $this->travelTo('2026-10-08 12:00:00');
    $this->artisan('plots:process-rentals')->assertFailed();

    expect($this->tenant->run(fn () => $rental->fresh()->paid_until->toDateTimeString()))->toBe('2026-10-15 12:00:00');
});

it('is scheduled every hour', function () {
    $event = collect(app(\Illuminate\Console\Scheduling\Schedule::class)->events())
        ->first(fn ($event) => str_contains($event->command, 'plots:process-rentals'));

    expect($event)->not->toBeNull()->and($event->expression)->toBe('0 * * * *');
});
