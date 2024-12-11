<?php

namespace App\Notifications;

use App\Models\PermitRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PermitRequestHandled extends Notification
{
    use Queueable;

    public function __construct(
        protected PermitRequest $permitRequest
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        $status = $this->permitRequest->status === 'approved' ? 'goedgekeurd' : 'afgewezen';
        
        return new DatabaseMessage([
            'title' => "Vergunning aanvraag {$status}",
            'message' => "Je aanvraag voor een {$this->permitRequest->type->name} is {$status}.",
            'action_text' => 'Bekijk details',
            'action_url' => route('portal.permits.show', $this->permitRequest),
            'type' => 'permit_request_' . $this->permitRequest->status
        ]);
    }
} 