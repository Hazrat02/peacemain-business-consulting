<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_section_id',
        'title',
        'slug',
        'description',
        'is_required',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $document): void {
            if (! $document->slug) {
                $document->slug = Str::slug($document->title) . '-' . Str::lower(Str::random(6));
            }
        });
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(DocumentSection::class, 'document_section_id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(DocumentRule::class)->orderBy('id');
    }

    public function requiredUsers(): HasMany
    {
        return $this->hasMany(UserRequiredDocument::class);
    }
}

