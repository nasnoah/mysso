<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdentityProvider extends Model
{
    protected $fillable = [
        'user_id',
        'provider_id',
        'provider_name',
        'provider_email',
        'provider_avatar',
        'provider_token',
        'provider_refresh_token',
    ];

    public function scopeGoogle($query) {
        return $query->where('provider_name', 'google');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
