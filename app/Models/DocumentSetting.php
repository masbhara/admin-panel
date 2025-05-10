<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DocumentSetting extends Model
{
    use LogsActivity;

    protected $fillable = [
        'submission_deadline',
        'closed_message',
        'is_active',
    ];

    protected $casts = [
        'submission_deadline' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['submission_deadline', 'closed_message', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
} 