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
} 