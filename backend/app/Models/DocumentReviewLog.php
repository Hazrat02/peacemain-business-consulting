<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentReviewLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_required_document_id',
        'document_submission_id',
        'reviewed_by',
        'action',
        'note',
    ];

    public function requiredDocument(): BelongsTo
    {
        return $this->belongsTo(UserRequiredDocument::class, 'user_required_document_id');
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DocumentSubmission::class, 'document_submission_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

