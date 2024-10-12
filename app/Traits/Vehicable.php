<?php

namespace App\Traits;

trait Vehicable
{
    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->forceFill(['type' => static::class]);
        });
    }

    public static function booted(): void
    {
        static::addGlobalScope(static::class, function ($builder) {
            $builder->where('type', static::class);
        });
    }
}
