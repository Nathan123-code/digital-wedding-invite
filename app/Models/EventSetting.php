<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSetting extends Model
{
    protected $fillable = [
        'bride_name',
        'groom_name',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
