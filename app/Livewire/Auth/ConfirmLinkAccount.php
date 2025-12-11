<?php

namespace App\Livewire\Auth;

use App\Http\Controllers\Auth\IdentityProviderController;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ConfirmLinkAccount extends Component
{
    public array $provider;
    public User $user;

    public function linkAccount() {
        (new IdentityProviderController)->link(user: $this->user, provider: $this->provider);
            
        return to_route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.confirm-link-account');
    }
}
