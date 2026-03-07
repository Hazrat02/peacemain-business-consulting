<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserRequiredDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id',
        'is_required',
        'status',
        'latest_submission_id',
        'status_updated_at',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'status_updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function latestSubmission(): BelongsTo
    {
        return $this->belongsTo(DocumentSubmission::class, 'latest_submission_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(DocumentSubmission::class)->orderByDesc('version');
    }

    public function reviewLogs(): HasMany
    {
        return $this->hasMany(DocumentReviewLog::class)->orderByDesc('id');
    }
}

