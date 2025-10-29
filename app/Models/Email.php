<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'from',
        'to',
        'subject',
        'body_encrypted',
        'aes_key_encrypted_admin',
        'hash',
        // Metadata fields
        'date_sent',
        'date_received',
        'cc',
        'bcc',
        'reply_to',
        'message_id',
        'headers',
        'sender_ip',
        'attachments_info',
        'mailer',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date_sent' => 'datetime',
        'date_received' => 'datetime',
        'headers' => 'array',
        'attachments_info' => 'array',
    ];

    /**
     * Relationship: Email has many decryption requests
     */
    public function decryptionRequests()
    {
        return $this->hasMany(DecryptionRequest::class);
    }

    /**
     * Scope: Search emails by keyword
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('from', 'like', "%{$keyword}%")
                ->orWhere('to', 'like', "%{$keyword}%")
                ->orWhere('subject', 'like', "%{$keyword}%")
                ->orWhere('cc', 'like', "%{$keyword}%")
                ->orWhere('message_id', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->where('date_sent', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date_sent', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Scope: Filter by sender IP
     */
    public function scopeSenderIp($query, $ip)
    {
        return $query->where('sender_ip', $ip);
    }

    /**
     * Get formatted date sent
     */
    public function getFormattedDateSentAttribute()
    {
        return $this->date_sent ? $this->date_sent->format('Y-m-d H:i:s') : null;
    }

    /**
     * Get formatted date received
     */
    public function getFormattedDateReceivedAttribute()
    {
        return $this->date_received ? $this->date_received->format('Y-m-d H:i:s') : null;
    }
}
