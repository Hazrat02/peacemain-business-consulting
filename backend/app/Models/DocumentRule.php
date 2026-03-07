<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'source_country',
        'destination_country',
        'marital_status',
        'passport_type',
        'is_exclusion',
    ];

    protected $casts = [
        'is_exclusion' => 'boolean',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}

