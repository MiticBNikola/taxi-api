<?php

namespace App\Listeners;

use App\Models\User\Driver;
use App\Services\DriverServiceInterface;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateDriverActivity
{
    private DriverServiceInterface $driverService;

    /**
     * Create the event listener.
     */
    public function __construct(DriverServiceInterface $driverService)
    {
        $this->driverService = $driverService;
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event->user instanceof Driver) {
            $this->driverService->changeActivity(['is_active' => $event instanceof Login], $event->user);
        }
    }
}
