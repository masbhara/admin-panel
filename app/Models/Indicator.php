<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Indicator extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'current_value',
        'target_value',
        'unit',
        'type'
    ];

    protected $casts = [
        'current_value' => 'float',
        'target_value' => 'float',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'current_value', 'target_value', 'unit', 'type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
