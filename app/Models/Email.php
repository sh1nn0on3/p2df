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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
                ->orWhere('subject', 'like', "%{$keyword}%");
        });
    }
}
