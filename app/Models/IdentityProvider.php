<?php

namespace App\Models;

use App\Enums\ProviderName;
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

    protected $casts = [
        'provider_name' => ProviderName::class,
    ];

    public function scopeGoogle($query) {
        return $query->where('provider_name', ProviderName::GOOGLE);
    }
    
    public function scopeGithub($query) {
        return $query->where('provider_name', ProviderName::GITHUB);
    }

    public function scopeFacebook($query) {
        return $query->where('provider_name', ProviderName::FACEBOOK);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
