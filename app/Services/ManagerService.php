<?php

namespace App\Services;

use App\Models\User\Manager;

class ManagerService implements ManagerServiceInterface
{
    public function destroy(Manager $manager): bool
    {
        return $manager->delete();
    }
}
