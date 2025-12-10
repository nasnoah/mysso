<?php

namespace App\Livewire\Settings;

use App\Enums\ProviderName;
use App\Http\Controllers\Auth\GoogleController;
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
        $this->validate([
            'password'  => ['required', 'string', 'current_password'],
        ]);

        if ($this->selectedProvider->provider_name == ProviderName::GOOGLE) {
            (new GoogleController)->unlink();
        }

        return to_route('third-party-account.edit')->with('sso-succeded', 'Successfully unlinked your '.$this->selectedProvider->provider_name->label().' account.');
    }
}
