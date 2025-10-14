<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'public_key_path',
        'private_key_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'private_key_path', // Không expose private key path
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is investigator
     */
    public function isInvestigator(): bool
    {
        return $this->role === 'investigator';
    }

    /**
     * Relationship: User has many decryption requests
     */
    public function decryptionRequests()
    {
        return $this->hasMany(DecryptionRequest::class, 'investigator_id');
    }

    /**
     * Relationship: User has many forensic logs
     */
    public function forensicLogs()
    {
        return $this->hasMany(ForensicLog::class);
    }
}
