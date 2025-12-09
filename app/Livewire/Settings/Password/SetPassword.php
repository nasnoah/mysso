<?php

namespace App\Livewire\Settings\Password;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class SetPassword extends Component
{
    public bool $isSettingPassword;

    public string $password;
    public string $password_confirmation;

    public function mount(): void
    {
        $this->isSettingPassword = false;

        $this->password = '';
        $this->password_confirmation = '';
    }

    public function toggleSettingPassword(): void
    {
        $this->isSettingPassword = !$this->isSettingPassword;
    }

    public function setPassword()
    {
        try {
            $validated = $this->validate([
                'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed']
            ]);
        }
        catch (ValidationException $e) {
            $this->reset('password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('password', 'password_confirmation');
        
        $this->dispatch('password-set');

        return $this->redirect(route('user-password.edit'), navigate: true);
    }
}
