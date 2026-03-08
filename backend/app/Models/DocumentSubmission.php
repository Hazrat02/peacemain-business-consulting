<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_required_document_id',
        'user_id',
        'version',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'review_status',
        'is_seen',
        'seen_at',
        'review_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'is_seen' => 'boolean',
        'seen_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected $appends = [
        'file_url',
    ];

    public function requiredDocument(): BelongsTo
    {
        return $this->belongsTo(UserRequiredDocument::class, 'user_required_document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getFileUrlAttribute(): string
    {
        return route('documents.submissions.download', ['submission' => $this->id]);
    }
}
