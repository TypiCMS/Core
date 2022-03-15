<?php

namespace TypiCMS\Modules\Core\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as Base;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Base
{
    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     *
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            app()->getLocale().'::verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey()]
        );
    }
}
