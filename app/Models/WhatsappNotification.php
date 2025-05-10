<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WhatsappNotification extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'template',
        'event_type',
        'is_active',
        'variables',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'variables' => 'array',
    ];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'template', 'event_type', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    /**
     * Parse template with variables
     *
     * @param array $data
     * @return string
     */
    public function parseTemplate(array $data): string
    {
        $template = $this->template;
        
        foreach ($data as $key => $value) {
            $template = str_replace("{{" . $key . "}}", $value, $template);
        }
        
        return $template;
    }
} 