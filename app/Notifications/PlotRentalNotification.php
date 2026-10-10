<?php

namespace App\Notifications;

use App\Models\PlotRental;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlotRentalNotification extends Notification
{
    use Queueable;

    public function __construct(protected PlotRental $rental, protected string $type)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $rental = $this->rental;
        $plot = $rental->plot_name;
        $price = "{$rental->formatted_price} {$rental->period_text}";
        $date = fn ($value) => $value?->format('d-m-Y H:i');

        [$title, $message] = match ($this->type) {
            'rented' => ['Plot verhuurd', "{$rental->renter_name} huurt je plot {$plot} voor {$price}."],
            'started' => ['Plot gehuurd', "Je huurt {$plot} voor {$price}. De huur wordt op {$date($rental->paid_until)} automatisch verlengd."],
            'renewed' => ['Huur verlengd', "De huur van {$plot} is betaald ({$rental->formatted_price}). Betaald tot {$date($rental->paid_until)}."],
            'payment_received' => ['Huur ontvangen', "{$rental->renter_name} heeft de huur van {$plot} betaald ({$rental->formatted_price})."],
            'payment_failed' => ['Huur niet betaald', "Je saldo was te laag om de huur van {$plot} ({$rental->formatted_price}) te betalen. Betaal binnen ".PlotRental::GRACE_DAYS." dagen, uiterlijk {$date($rental->grace_until)}, anders verlies je het plot."],
            'overdue' => ['Huurder betaalt niet', "{$rental->renter_name} heeft de huur van {$plot} niet betaald. Zonder betaling eindigt de huur op {$date($rental->grace_until)}."],
            'evicted' => ['Huur beëindigd', "Je hebt de huur van {$plot} niet binnen ".PlotRental::GRACE_DAYS." dagen betaald. De huur is beëindigd en je hebt geen toegang meer tot het plot."],
            'evicted_landlord' => ['Huur beëindigd', "De huur van {$plot} is beëindigd omdat {$rental->renter_name} niet heeft betaald. Het plot staat weer te huur."],
            'cancelled_by_renter' => ['Huur opgezegd', "{$rental->renter_name} heeft de huur van {$plot} opgezegd. De huur eindigt op {$date($rental->ends_at ?? $rental->ended_at)}."],
            'cancelled_by_landlord' => ['Huur opgezegd', "De verhuurder heeft de huur van {$plot} opgezegd. Je kunt het plot gebruiken tot {$date($rental->ends_at ?? $rental->ended_at)}."],
            'ended' => ['Huur afgelopen', "De huur van {$plot} is afgelopen. Je hebt geen toegang meer tot het plot."],
            'ended_landlord' => ['Huur afgelopen', "De huur van {$plot} door {$rental->renter_name} is afgelopen."],
            default => ['Huur', "Er is iets veranderd aan de huur van {$plot}."],
        };

        return [
            'title' => $title,
            'message' => $message,
            'plot_name' => $plot,
            'rental_id' => $rental->id,
            'type' => $this->type,
        ];
    }
}
