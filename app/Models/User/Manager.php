<?php

namespace App\Models\User;

use App\Traits\Numberable;
use Database\Factories\ManagerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class Manager extends User
{
    use Numberable;

    protected $table = 'managers';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ManagerFactory::new();
    }
}
