<?php

namespace App\Livewire\Settings;

use App\Http\Controllers\Auth\IdentityProviderController;
use App\Models\IdentityProvider;
use Illuminate\Support\Collection;
use Livewire\Component;

class ThirdPartyAccount extends Component
{
    public bool $isSelected;

    public ?Collection $identityProviders;
    public ?IdentityProvider $selectedProvider;

    public string $password = '';

    public function mount(): void
    {
        $this->isSelected = false;
        
        $this->identityProviders = auth()->user()->identityProviders;
        $this->selectedProvider = null;
    }

    public function select($providerId): void
    {
        $this->isSelected = true;
        $this->selectedProvider = $this->identityProviders->firstWhere('id', $providerId);
    }

    public function deselect(): void
    {
        $this->isSelected = false;
        $this->selectedProvider = null;
    }

    public function unlinkProvider()
    {
        if (!$this->selectedProvider) return;

        $this->validate([
            'password'  => ['required', 'string', 'current_password'],
        ]);

        (new IdentityProviderController)->unlink(provider: $this->selectedProvider->provider_name);

        return to_route('third-party-account.edit')->with('sso-succeded', 'Successfully unlinked your '.$this->selectedProvider->provider_name->label().' account.');
    }
}
