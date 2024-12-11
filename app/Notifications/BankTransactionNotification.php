<?php

namespace App\Notifications;

use App\Models\BankTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class BankTransactionNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected BankTransaction $transaction,
        protected bool $isReceiver
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        if ($this->isReceiver) {
            return new DatabaseMessage([
                'title' => 'Geld ontvangen',
                'message' => "Je hebt € " . number_format($this->transaction->amount, 2, ',', '.') . " ontvangen van {$this->transaction->fromUser->minecraft_username}",
                'action_text' => 'Bekijk details',
                'action_url' => route('portal.bank-accounts.show', $this->transaction->to_account_uuid),
                'type' => 'bank_transaction_received'
            ]);
        }

        return new DatabaseMessage([
            'title' => 'Geld overgemaakt',
            'message' => "Je hebt € " . number_format($this->transaction->amount, 2, ',', '.') . " overgemaakt naar {$this->transaction->toUser->minecraft_username}",
            'action_text' => 'Bekijk details',
            'action_url' => route('portal.bank-accounts.show', $this->transaction->from_account_uuid),
            'type' => 'bank_transaction_sent'
        ]);
    }
} 