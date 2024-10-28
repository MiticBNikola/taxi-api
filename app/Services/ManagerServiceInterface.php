<?php

namespace App\Services;

use App\Models\User\Manager;

interface ManagerServiceInterface
{
    public function destroy(Manager $manager): bool;
}
