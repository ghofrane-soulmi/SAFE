<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasFactory;

    protected $keyType = 'string'; // UUID type
    protected $primaryKey = 'id';

    protected $fillable = [
        'email', 'password_hash', 'full_name', 'company', 'sector', 'country',
        'role', 'profile_picture', 'linkedin_id', 'bio', 'phone', 'is_verified'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function messagesSent(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function messagesReceived(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function sessions(): BelongsToMany
    {
        return $this->belongsToMany(Session::class, 'user_sessions');
    }
}

