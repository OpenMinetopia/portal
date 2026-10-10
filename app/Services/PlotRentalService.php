<?php

namespace App\Services;

use App\Models\PlotListing;
use App\Models\PlotRental;
use App\Models\PlotRentalOffer;
use App\Models\PlotRentalPayment;
use App\Models\User;
use App\Notifications\PlotRentalNotification;
use App\Services\Plugin\BankingService;
use App\Services\Plugin\PlotService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Plot rentals: starting one, the automatic renewal with its grace period, and
 * ending one. The renter becomes a member of the plot, never an owner, so the
 * landlord keeps the plot and the renter cannot sell it or remove the landlord.
 *
 * Money moves through the plugin, which has no transactions. Every period is one
 * plot_rental_payments row, unique per rental and period start, written before
 * the withdrawal: a rerun or a second process finds the row and never charges
 * that period again.
 */
class PlotRentalService
{
    public function __construct(protected BankingService $banking, protected PlotService $plots)
    {
    }

    /**
     * Rents the plot to $renter: pays the first period from their account and makes
     * them a member. Throws PlotRentalException when it cannot.
     */
    public function start(PlotRentalOffer $offer, User $renter, string $accountUuid): PlotRental
    {
        if ($offer->landlord_id === $renter->id && ! $offer->is_server_listing) {
            throw new PlotRentalException('Actie niet mogelijk', 'Je kunt je eigen plot niet huren.');
        }

        $account = collect($this->banking->getPlayerBankAccounts($renter->minecraft_plain_uuid))->firstWhere('uuid', $accountUuid);

        if (! $account || ($account['frozen'] ?? false) || $account['balance'] < $offer->price) {
            throw new PlotRentalException('Onvoldoende saldo', 'De geselecteerde bankrekening heeft onvoldoende saldo.');
        }

        $plot = $this->plots->getPlot($offer->plot_name);
        if (! $plot) {
            throw new PlotRentalException('Er ging iets mis', 'Het plot kon niet worden opgehaald. Probeer het later opnieuw.');
        }
        if ($this->hasAccess($plot, $renter)) {
            throw new PlotRentalException('Actie niet mogelijk', 'Je hebt al toegang tot dit plot.');
        }

        // Reserve the offer first, so two players can never rent it at the same time.
        $reserved = DB::transaction(function () use ($offer) {
            $locked = PlotRentalOffer::whereKey($offer->id)->lockForUpdate()->first();

            if (! $locked || $locked->status !== 'active') {
                return false;
            }

            $locked->update(['status' => 'rented']);

            return true;
        });

        if (! $reserved) {
            throw new PlotRentalException('Plot niet beschikbaar', 'Dit plot is niet meer te huur.');
        }

        $release = fn () => $offer->newQuery()->whereKey($offer->id)->update(['status' => 'active']);
        $price = (float) $offer->price;

        if (! $this->banking->withdraw($accountUuid, $price)) {
            $release();
            throw new PlotRentalException('Betaling mislukt', 'Het afschrijven van de huur is mislukt. Er is niets afgeschreven.');
        }

        if ($offer->payout_bank_account_uuid && ! $this->banking->deposit($offer->payout_bank_account_uuid, $price)) {
            $this->banking->deposit($accountUuid, $price);
            $release();
            throw new PlotRentalException('Betaling mislukt', 'De huur kon niet aan de verhuurder worden overgemaakt. Het bedrag is teruggestort.');
        }

        if (! $this->plots->addMember($offer->plot_name, $renter->minecraft_plain_uuid)) {
            if ($offer->payout_bank_account_uuid) {
                $this->banking->withdraw($offer->payout_bank_account_uuid, $price);
            }
            $this->banking->deposit($accountUuid, $price);
            $release();
            throw new PlotRentalException('Er ging iets mis', 'Je kon niet aan het plot worden toegevoegd. De betaling is teruggestort.');
        }

        $now = now();
        $paidUntil = PlotRental::periodEnd($offer->period, $now);

        $rental = DB::transaction(function () use ($offer, $renter, $accountUuid, $now, $paidUntil) {
            $rental = PlotRental::create([
                'plot_rental_offer_id' => $offer->id,
                'plot_name' => $offer->plot_name,
                'landlord_id' => $offer->landlord_id,
                'renter_id' => $renter->id,
                'is_server_listing' => (bool) $offer->is_server_listing,
                'payout_bank_account_uuid' => $offer->payout_bank_account_uuid,
                'renter_bank_account_uuid' => $accountUuid,
                'price' => $offer->price,
                'period' => $offer->period,
                'status' => 'active',
                'started_at' => $now,
                'paid_until' => $paidUntil,
            ]);

            $rental->payments()->create([
                'period_start' => $now,
                'period_end' => $paidUntil,
                'amount' => $offer->price,
                'bank_account_uuid' => $accountUuid,
                'status' => 'paid',
                'paid_at' => $now,
                'payout_status' => $offer->payout_bank_account_uuid ? 'done' : null,
            ]);

            // Rented or sold, not both: the sale listing comes off the market.
            PlotListing::where('plot_name', $offer->plot_name)->where('status', 'active')->update(['status' => 'cancelled']);

            return $rental;
        });

        $rental->landlord->notify(new PlotRentalNotification($rental, 'rented'));
        $renter->notify(new PlotRentalNotification($rental, 'started'));

        return $rental;
    }

    /**
     * One pass over every active rental: ends the ones whose notice ran out, renews
     * the due ones, ends the ones still unpaid after the grace period, retries
     * failed payouts and removes renters that could not be removed earlier.
     *
     * @return array<string, int>
     */
    public function processDue(): array
    {
        $stats = ['renewed' => 0, 'overdue' => 0, 'evicted' => 0, 'ended' => 0, 'skipped' => 0];

        PlotRental::active()->whereNotNull('ends_at')->where('ends_at', '<=', now())->get()
            ->each(function (PlotRental $rental) use (&$stats) {
                $this->end($rental, $rental->cancelled_by ?? 'renter');
                $stats['ended']++;
            });

        PlotRental::active()->whereNull('ends_at')->where('paid_until', '<=', now())->orderBy('paid_until')->get()
            ->each(function (PlotRental $rental) use (&$stats) {
                $stats[$this->charge($rental)]++;
            });

        PlotRentalPayment::where('status', 'paid')->where('payout_status', 'failed')->with('rental')->get()
            ->each(fn (PlotRentalPayment $payment) => $this->payout($payment));

        PlotRental::where('status', 'ended')->whereNull('access_revoked_at')->where('ended_at', '>=', now()->subDays(PlotRental::GRACE_DAYS))->get()
            ->each(fn (PlotRental $rental) => $this->revokeAccess($rental));

        return $stats;
    }

    /**
     * Charges the period that starts at paid_until. Returns what happened:
     * renewed, overdue (unpaid, within the grace period), evicted or skipped.
     */
    public function charge(PlotRental $rental): string
    {
        $start = $rental->paid_until->copy();
        $end = PlotRental::periodEnd($rental->period, $start);

        $existing = $rental->payments()->where('period_start', $start)->first();

        if ($existing?->status === 'paid') {
            // Paid, but the rental was not moved on (the process stopped in between).
            $this->markPaid($rental, $existing, $end);

            return 'renewed';
        }

        if ($existing) {
            // An earlier attempt may have withdrawn the money already: never charge again.
            Log::warning('Plot rental payment still pending, not charging again', ['rental' => $rental->id, 'period_start' => $start->toDateTimeString()]);

            return 'skipped';
        }

        try {
            $payment = $rental->payments()->create([
                'period_start' => $start,
                'period_end' => $end,
                'amount' => $rental->price,
                'bank_account_uuid' => $rental->renter_bank_account_uuid,
                'status' => 'pending',
            ]);
        } catch (UniqueConstraintViolationException) {
            return 'skipped'; // Another process is charging this period right now.
        }

        $account = $this->banking->getBankAccount($rental->renter_bank_account_uuid);
        $canPay = isset($account['balance'])
            && ! ($account['frozen'] ?? false)
            && (float) $account['balance'] >= (float) $rental->price;

        if (! $canPay || ! $this->banking->withdraw($rental->renter_bank_account_uuid, (float) $rental->price)) {
            $payment->delete();

            return $this->paymentFailed($rental);
        }

        $wasOverdue = $rental->isOverdue();
        $this->markPaid($rental, $payment, $end);
        $this->payout($payment);

        $rental->renter->notify(new PlotRentalNotification($rental, 'renewed'));
        if (! $rental->is_server_listing || $wasOverdue) {
            $rental->landlord->notify(new PlotRentalNotification($rental, 'payment_received'));
        }

        return 'renewed';
    }

    /**
     * The renter pays now, for example during the grace period, and from now on
     * from this account.
     */
    public function payNow(PlotRental $rental, string $accountUuid): string
    {
        $account = collect($this->banking->getPlayerBankAccounts($rental->renter->minecraft_plain_uuid))->firstWhere('uuid', $accountUuid);

        if (! $account) {
            throw new PlotRentalException('Actie niet mogelijk', 'Kies een van je eigen bankrekeningen.');
        }

        $rental->update(['renter_bank_account_uuid' => $accountUuid]);

        if (! $rental->isActive() || $rental->paid_until->isFuture() || $rental->ends_at) {
            return 'unchanged';
        }

        return $this->charge($rental);
    }

    /**
     * Notice from the renter or the landlord. The renter keeps the plot until the
     * paid period ends; an unpaid rental ends right away.
     */
    public function cancel(PlotRental $rental, string $by): void
    {
        if (! $rental->isActive() || $rental->ends_at) {
            return;
        }

        if ($rental->isOverdue() || $rental->paid_until->isPast()) {
            $this->end($rental, $by, notify: false);
        } else {
            $rental->update(['ends_at' => $rental->paid_until, 'cancelled_by' => $by]);
        }

        $by === 'renter'
            ? $rental->landlord->notify(new PlotRentalNotification($rental, 'cancelled_by_renter'))
            : $rental->renter->notify(new PlotRentalNotification($rental, 'cancelled_by_landlord'));
    }

    /**
     * Ends the rental and removes the renter from the plot. After the renter's
     * notice or an eviction the plot goes back on the market; after the
     * landlord's notice it does not.
     */
    public function end(PlotRental $rental, string $reason, bool $notify = true): void
    {
        $ended = DB::transaction(function () use ($rental, $reason) {
            $locked = PlotRental::whereKey($rental->id)->lockForUpdate()->first();

            if (! $locked?->isActive()) {
                return false;
            }

            $locked->update(['status' => 'ended', 'ended_at' => now(), 'end_reason' => $reason]);

            PlotRentalOffer::whereKey($locked->plot_rental_offer_id)
                ->where('status', 'rented')
                ->update(['status' => $reason === 'landlord' ? 'cancelled' : 'active']);

            return true;
        });

        if (! $ended) {
            return;
        }

        $rental->refresh();
        $this->revokeAccess($rental);

        if (! $notify) {
            return;
        }

        if ($reason === 'unpaid') {
            $rental->renter->notify(new PlotRentalNotification($rental, 'evicted'));
            $rental->landlord->notify(new PlotRentalNotification($rental, 'evicted_landlord'));
        } else {
            $rental->renter->notify(new PlotRentalNotification($rental, 'ended'));
            $rental->landlord->notify(new PlotRentalNotification($rental, 'ended_landlord'));
        }
    }

    protected function paymentFailed(PlotRental $rental): string
    {
        if (! $rental->overdue_since) {
            $rental->update(['overdue_since' => now(), 'grace_until' => now()->addDays(PlotRental::GRACE_DAYS)]);

            $rental->renter->notify(new PlotRentalNotification($rental, 'payment_failed'));
            $rental->landlord->notify(new PlotRentalNotification($rental, 'overdue'));

            return 'overdue';
        }

        if (! $rental->grace_until->isFuture()) {
            $this->end($rental, 'unpaid');

            return 'evicted';
        }

        return 'overdue';
    }

    protected function markPaid(PlotRental $rental, PlotRentalPayment $payment, $end): void
    {
        DB::transaction(function () use ($rental, $payment, $end) {
            if ($payment->status !== 'paid') {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payout_status' => $rental->payout_bank_account_uuid ? 'pending' : null,
                ]);
            }

            $rental->update(['paid_until' => $end, 'overdue_since' => null, 'grace_until' => null]);
        });
    }

    /** Pays the landlord. A failed deposit is retried on the next run; a pending one never is. */
    protected function payout(PlotRentalPayment $payment): void
    {
        $account = $payment->rental->payout_bank_account_uuid;

        if (! $account || ! in_array($payment->payout_status, ['pending', 'failed'], true)) {
            return;
        }

        // A retry claims the payment first, so two runs never pay the same one out.
        if ($payment->payout_status === 'failed'
            && ! PlotRentalPayment::whereKey($payment->id)->where('payout_status', 'failed')->update(['payout_status' => 'pending'])) {
            return;
        }
        $payment->payout_status = 'pending';
        $payment->syncOriginalAttribute('payout_status');

        $done = $this->banking->deposit($account, (float) $payment->amount);
        $payment->update(['payout_status' => $done ? 'done' : 'failed']);

        if (! $done) {
            Log::warning('Plot rental payout failed, retrying on the next run', ['payment' => $payment->id, 'rental' => $payment->plot_rental_id]);
        }
    }

    /** Removes the renter from the plot. False when the plugin could not be reached; tried again on the next run. */
    public function revokeAccess(PlotRental $rental): bool
    {
        if ($rental->access_revoked_at) {
            return true;
        }

        $plot = $this->plots->getPlot($rental->plot_name);

        if (! $plot) {
            return false;
        }

        $renter = $rental->renter;
        $isMember = collect($plot['members'] ?? [])->contains(fn ($uuid) => self::sameUuid($uuid, $renter->minecraft_plain_uuid));

        if ($isMember && ! $this->plots->removeMember($rental->plot_name, $renter->minecraft_plain_uuid)) {
            Log::warning('Could not remove plot renter, retrying on the next run', ['rental' => $rental->id, 'plot' => $rental->plot_name]);

            return false;
        }

        $rental->update(['access_revoked_at' => now()]);

        return true;
    }

    protected function hasAccess(array $plot, User $user): bool
    {
        return collect([...($plot['owners'] ?? []), ...($plot['members'] ?? [])])
            ->contains(fn ($uuid) => self::sameUuid($uuid, $user->minecraft_plain_uuid));
    }

    public static function sameUuid(?string $a, ?string $b): bool
    {
        $normalize = fn (?string $uuid) => strtolower(str_replace('-', '', (string) $uuid));

        return $a !== null && $b !== null && $normalize($a) === $normalize($b);
    }

    /** Whether the plot has a rental running right now. */
    public static function isRented(string $plotName): bool
    {
        return PlotRental::active()->where('plot_name', $plotName)->exists();
    }
}
