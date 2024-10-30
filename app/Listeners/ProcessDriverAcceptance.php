<?php

namespace App\Listeners;

use App\Events\RideRequestedForDriver;
use App\Jobs\CheckIfDriverHasAccepted;

class ProcessDriverAcceptance
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RideRequestedForDriver $event): void
    {
        CheckIfDriverHasAccepted::dispatch($event->ride)->delay(now()->addMinutes(1));
    }
}
