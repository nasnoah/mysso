<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ConfirmLinkAccount extends Component
{
    public array $provider;
    public User $user;

    public function linkAccount() {
        $this->user->identityProviders()->create([
            'provider_name'             => $this->provider['name'],
            'provider_id'               => $this->provider['id'],
            'provider_email'            => $this->provider['email'],
            'provider_avatar'           => $this->provider['avatar'],
            'provider_token'            => $this->provider['token'],
            'provider_refresh_token'    => $this->provider['refresh_token'],
        ]);

        Auth::login($this->user);
            
        return to_route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.confirm-link-account');
    }
}
