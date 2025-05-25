<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class DocumentForm extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Template types
     */
    public const TEMPLATE_DEFAULT = 'default';
    public const TEMPLATE_ARTICLE = 'article';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'slug',
        'submission_deadline',
        'closed_message',
        'is_active',
        'template_type',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submission_deadline' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * Appended attributes.
     * 
     * @var array
     */
    protected $appends = ['formatted_submission_deadline', 'document_count'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($documentForm) {
            // Generate slug from title if not provided
            if (empty($documentForm->slug)) {
                $documentForm->slug = Str::slug($documentForm->title);
                
                // Make sure slug is unique
                $count = 0;
                $originalSlug = $documentForm->slug;
                while (self::where('slug', $documentForm->slug)->exists()) {
                    $count++;
                    $documentForm->slug = $originalSlug . '-' . $count;
                }
            }
        });
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'submission_deadline', 'closed_message', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user that owns the document form.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the documents associated with this form.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the formatted submission deadline.
     */
    public function getFormattedSubmissionDeadlineAttribute()
    {
        return $this->submission_deadline ? $this->submission_deadline->format('Y-m-d\TH:i') : null;
    }

    /**
     * Get the count of documents for this form.
     */
    public function getDocumentCountAttribute()
    {
        return $this->documents()->count();
    }

    /**
     * Check if the form is currently open for submissions.
     */
    public function isOpen()
    {
        if (!$this->is_active) {
            return false;
        }
        
        if (!$this->submission_deadline) {
            return true;
        }
        
        return now()->lt($this->submission_deadline);
    }

    /**
     * Get the default fields for a template type
     */
    public static function getDefaultFields(string $templateType): array
    {
        $defaultFields = [
            self::TEMPLATE_DEFAULT => [
                [
                    'label' => 'Nama Lengkap',
                    'name' => 'name',
                    'type' => 'text',
                    'is_required' => true,
                    'is_enabled' => true,
                    'help_text' => 'Masukkan nama lengkap Anda',
                    'order' => 0
                ],
                [
                    'label' => 'Nomor WhatsApp',
                    'name' => 'whatsapp',
                    'type' => 'text',
                    'is_required' => true,
                    'is_enabled' => true,
                    'help_text' => '08xxx (gunakan nomor aktif)',
                    'order' => 1
                ],
                [
                    'label' => 'Kota/Kabupaten',
                    'name' => 'city',
                    'type' => 'text',
                    'is_required' => true,
                    'is_enabled' => true,
                    'help_text' => 'Ketik untuk mencari kota/kabupaten...',
                    'order' => 2
                ],
                [
                    'label' => 'Unggah Dokumen',
                    'name' => 'document',
                    'type' => 'file',
                    'is_required' => true,
                    'is_enabled' => true,
                    'help_text' => 'Format: PDF, Word, maksimal 10MB',
                    'validation_rules' => [
                        'mimes' => 'pdf,doc,docx',
                        'max' => 10240
                    ],
                    'order' => 3
                ]
            ],
            self::TEMPLATE_ARTICLE => [
                [
                    'label' => 'Nama Lengkap',
                    'name' => 'name',
                    'type' => 'text',
                    'is_required' => true,
                    'is_enabled' => true,
                    'help_text' => 'Masukkan nama lengkap Anda',
                    'order' => 0
                ],
                [
                    'label' => 'Nomor WhatsApp',
                    'name' => 'whatsapp',
                    'type' => 'text',
                    'is_required' => true,
                    'is_enabled' => true,
                    'help_text' => '08xxx (gunakan nomor aktif)',
                    'order' => 1
                ],
                [
                    'label' => 'Kota/Kabupaten',
                    'name' => 'city',
                    'type' => 'text',
                    'is_required' => true,
                    'is_enabled' => true,
                    'help_text' => 'Ketik untuk mencari kota/kabupaten...',
                    'order' => 2
                ],
                [
                    'label' => 'Unggah Dokumen',
                    'name' => 'document',
                    'type' => 'file',
                    'is_required' => true,
                    'is_enabled' => true,
                    'help_text' => 'Format: PDF, Word, maksimal 10MB',
                    'validation_rules' => [
                        'mimes' => 'pdf,doc,docx',
                        'max' => 10240
                    ],
                    'order' => 3
                ],
                [
                    'label' => 'Tautan / Link Media',
                    'name' => 'media_link',
                    'type' => 'url',
                    'is_required' => true,
                    'is_enabled' => true,
                    'help_text' => 'Masukkan link artikel yang sudah dipublikasi',
                    'order' => 4
                ],
                [
                    'label' => 'Unggah Screenshot',
                    'name' => 'screenshot',
                    'type' => 'file',
                    'is_required' => true,
                    'is_enabled' => true,
                    'help_text' => 'Format: JPG, PNG, maksimal 5MB',
                    'validation_rules' => [
                        'mimes' => 'jpg,jpeg,png',
                        'max' => 5120
                    ],
                    'order' => 5
                ]
            ]
        ];

        return $defaultFields[$templateType] ?? [];
    }

    /**
     * Get the form fields for this document form.
     */
    public function formFields()
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    /**
     * Get the enabled form fields for this document form.
     */
    public function enabledFormFields()
    {
        return $this->formFields()->where('is_enabled', true);
    }
} 