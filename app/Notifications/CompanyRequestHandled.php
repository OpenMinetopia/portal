<?php

namespace App\Notifications;

use App\Models\CompanyRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CompanyRequestHandled extends Notification
{
    use Queueable;

    public function __construct(
        protected CompanyRequest $companyRequest
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        $status = $this->companyRequest->status === 'approved' ? 'goedgekeurd' : 'afgewezen';
        
        return new DatabaseMessage([
            'title' => "Bedrijfs aanvraag {$status}",
            'message' => "Je aanvraag voor {$this->companyRequest->name} is {$status}.",
            'action_text' => 'Bekijk details',
            'action_url' => route('portal.companies.request-details', $this->companyRequest),
            'type' => 'company_request_' . $this->companyRequest->status
        ]);
    }
} 