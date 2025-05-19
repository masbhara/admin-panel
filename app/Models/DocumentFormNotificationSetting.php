<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFormNotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_form_id',
        'whatsapp_notification_enabled',
        'dripsender_api_key',
        'dripsender_webhook_url',
        'notification_templates',
    ];

    protected $casts = [
        'whatsapp_notification_enabled' => 'boolean',
        'notification_templates' => 'array',
    ];

    public function documentForm()
    {
        return $this->belongsTo(DocumentForm::class);
    }

    /**
     * Get notification template for specific event
     */
    public function getTemplate(string $eventType): ?array
    {
        return $this->notification_templates[$eventType] ?? null;
    }

    /**
     * Set notification template for specific event
     */
    public function setTemplate(string $eventType, array $template): void
    {
        $templates = $this->notification_templates ?? [];
        $templates[$eventType] = $template;
        $this->notification_templates = $templates;
        $this->save();
    }
} 