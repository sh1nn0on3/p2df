<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForensicReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'email_id',
        'investigator_id',
        'decryption_request_id',
        'title',
        'severity',
        'findings',
        'analysis',
        'recommendations',
        'related_logs',
        'status',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'related_logs' => 'array',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Report belongs to an email
     */
    public function email()
    {
        return $this->belongsTo(Email::class);
    }

    /**
     * Relationship: Report belongs to an investigator
     */
    public function investigator()
    {
        return $this->belongsTo(User::class, 'investigator_id');
    }

    /**
     * Relationship: Report belongs to a decryption request
     */
    public function decryptionRequest()
    {
        return $this->belongsTo(DecryptionRequest::class, 'decryption_request_id');
    }

    /**
     * Scope: Get draft reports
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope: Get completed reports
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if report is draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if report is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Mark report as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Get severity badge color
     */
    public function getSeverityBadgeColor(): string
    {
        return match($this->severity) {
            'low' => 'success',
            'medium' => 'info',
            'high' => 'warning',
            'critical' => 'danger',
            default => 'secondary',
        };
    }
}
