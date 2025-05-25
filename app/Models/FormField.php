<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Konstanta untuk tipe field yang tersedia
     */
    public const TYPE_TEXT = 'text';
    public const TYPE_TEXTAREA = 'textarea';
    public const TYPE_SELECT = 'select';
    public const TYPE_RADIO = 'radio';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_FILE = 'file';
    public const TYPE_DATE = 'date';
    public const TYPE_NUMBER = 'number';
    public const TYPE_URL = 'url';

    /**
     * Daftar tipe field yang memerlukan options
     */
    public const TYPES_WITH_OPTIONS = [
        self::TYPE_SELECT,
        self::TYPE_RADIO,
        self::TYPE_CHECKBOX,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_form_id',
        'label',
        'name',
        'type',
        'options',
        'help_text',
        'validation_rules',
        'is_required',
        'is_enabled',
        'order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
        'is_enabled' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['label', 'name', 'type', 'options', 'validation_rules', 'help_text', 'order', 'is_required', 'is_enabled'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the document form that owns the field.
     */
    public function documentForm(): BelongsTo
    {
        return $this->belongsTo(DocumentForm::class);
    }

    /**
     * Get the validation rules as a string array.
     */
    public function getValidationRulesArray(): array
    {
        $rules = [];
        
        // Tambahkan required rule jika field wajib diisi
        if ($this->is_required) {
            $rules[] = 'required';
        }
        
        // Tambahkan rules berdasarkan tipe field
        switch ($this->type) {
            case self::TYPE_TEXT:
            case self::TYPE_TEXTAREA:
                $rules[] = 'string';
                break;
            case self::TYPE_NUMBER:
                $rules[] = 'numeric';
                if (isset($this->validation_rules['min'])) {
                    $rules[] = 'min:' . $this->validation_rules['min'];
                }
                if (isset($this->validation_rules['max'])) {
                    $rules[] = 'max:' . $this->validation_rules['max'];
                }
                break;
            case self::TYPE_DATE:
                $rules[] = 'date';
                if (isset($this->validation_rules['min'])) {
                    $rules[] = 'after:' . $this->validation_rules['min'];
                }
                if (isset($this->validation_rules['max'])) {
                    $rules[] = 'before:' . $this->validation_rules['max'];
                }
                break;
            case self::TYPE_SELECT:
            case self::TYPE_RADIO:
                if (!empty($this->options)) {
                    $rules[] = Rule::in(collect($this->options)->pluck('value')->toArray());
                }
                break;
            case self::TYPE_CHECKBOX:
                $rules[] = 'array';
                if (!empty($this->options)) {
                    $rules[] = Rule::in(collect($this->options)->pluck('value')->toArray());
                }
                break;
            case self::TYPE_FILE:
                $rules[] = 'file';
                if (isset($this->validation_rules['mimes'])) {
                    $rules[] = 'mimes:' . $this->validation_rules['mimes'];
                }
                if (isset($this->validation_rules['max'])) {
                    $rules[] = 'max:' . $this->validation_rules['max'];
                }
                break;
            case self::TYPE_URL:
                $rules[] = 'url';
                break;
        }
        
        return $rules;
    }
} 