<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends BaseResetPassword
{
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Nieuw wachtwoord instellen')
            ->line('Iemand heeft een nieuw wachtwoord aangevraagd voor je account op '.config('app.name').'.')
            ->action('Nieuw wachtwoord instellen', $url)
            ->line('Deze link werkt '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' minuten.')
            ->line('Heb je dit niet zelf aangevraagd? Dan hoef je niets te doen; je wachtwoord blijft hetzelfde.');
    }
}
