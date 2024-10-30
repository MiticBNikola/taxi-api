<?php

namespace App\Listeners;

use App\Events\RequestDriversLocation;
use App\Jobs\GetClosestDriver;

class ProcessDriversLocation
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
    public function handle(RequestDriversLocation $event): void
    {
        GetClosestDriver::dispatch($event->ride)->delay(now()->addSeconds(5));
    }
}
