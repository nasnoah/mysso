<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Password extends Component
{
    public bool $hasPassword;

    public function mount(): void 
    {
        $this->hasPassword = isset(Auth::user()->password);
    }
}
