<?php

namespace App\Notifications;

use App\Models\PlotListing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlotTransactionNotification extends Notification
{
    use Queueable;

    protected PlotListing $listing;
    protected string $type;

    public function __construct(PlotListing $listing, string $type)
    {
        $this->listing = $listing;
        $this->type = $type; // 'sold' or 'bought'
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $isSeller = $this->type === 'sold';
        
        return [
            'title' => $isSeller ? 'Plot Verkocht!' : 'Plot Gekocht!',
            'message' => $isSeller 
                ? "Je plot {$this->listing->plot_name} is verkocht voor {$this->listing->formatted_price}."
                : "Je hebt het plot {$this->listing->plot_name} gekocht voor {$this->listing->formatted_price}.",
            'plot_name' => $this->listing->plot_name,
            'price' => $this->listing->price,
            'type' => $this->type,
        ];
    }
} 