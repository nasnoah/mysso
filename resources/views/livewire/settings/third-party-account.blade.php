<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Third-Party Account')" :subheading="__('Manage your third-party accounts linked to your account')">
    
        @if (!$this->isSelected)
            <div class="space-y-4">
                @if (session('sso-succeded'))
                    <flux:callout variant="success" icon="check-circle" :heading="session('sso-succeded')" />
                @endif
                @if (session('sso-failed'))
                    <flux:callout variant="danger" icon="x-circle" :heading="session('sso-failed')" />
                @endif
    
                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($this->identityProviders as $identityProvider)
                        <flux:button variant="primary" type="button" wire:click="select('{{ $identityProvider->id }}')">
                            <div class="flex items-center gap-2 select-none">
                                {{-- <img src="{{ asset('images/'.$identityProvider->provider_name->value.'-logo.blade.php') }}" alt="" class="w-6 h-6"> --}}
                                <x-dynamic-component :component="'mysso.icons.'.$identityProvider->provider_name->value.'-logo'" class="w-6 h-6"/>
                                <span class="text-base">{{ __($identityProvider->provider_name->label()) }}</span>
                            </div>
                        </flux:button>
                    @endforeach
            
                    @if ($this->identityProviders->filter(fn ($provider) => in_array($provider->provider_name->value, array_column(App\Enums\ProviderName::cases(), 'value')))->count() < count(App\Enums\ProviderName::cases()))
                        <flux:modal.trigger name="link-new" class="flex items-center gap-1">
                            <flux:button :variant="count($this->identityProviders) ? null : 'primary'" type="button" x-on:click.prevent="$dispatch('open-modal', 'link-new')">
                                <div class="flex items-center gap-2 select-none">
                                    <span class="">{{ __('Link '.(count($this->identityProviders) ? 'new' : 'an').' account') }}</span>
                                </div>
                            </flux:button>
                        </flux:modal.trigger>
        
                        <flux:modal name="link-new" :show="$errors->isNotEmpty()" focusable class="max-w-lg min-w-96 space-y-4">
                            <div>
                                <flux:heading size="lg">{{ __('Link '.(count($this->identityProviders) ? 'new' : 'an').' account') }}</flux:heading>
                            </div>
                            
                            <flux:callout color="blue" icon="information-circle" heading="Only third-party account that has same email as your account's email can be linked" />
        
                            <div class="w-full mt-2 space-y-2">
                                @foreach (App\Enums\ProviderName::cases() as $providerName)
                                    @continue($this->identityProviders->pluck('provider_name')->contains($providerName))

                                    <x-mysso.sso-button :providerName="$providerName" title="Link to" />
                                @endforeach
                            </div>
                        </flux:modal>
                    @endif
                </div>
            </div>
        @else
            <div class="space-y-4">
                <flux:heading size="xl">
                    {{ $this->selectedProvider->provider_name->label() }}
                </flux:heading>
                
                <table>
                    <tr>
                        <td class="py-1 pr-4">Access given on:</td>
                        <td class="py-1 pl-4">{{ $this->selectedProvider->created_at->format('F j, '.($this->selectedProvider->created_at->year == now()->year ? 'h:i A' : 'Y')) }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-4">Last log in on:</td>
                        <td class="py-1 pl-4">{{ $this->selectedProvider->updated_at->format('F j, '.($this->selectedProvider->updated_at->year == now()->year ? 'h:i A' : 'Y')) }}</td>
                    </tr>
                </table>

                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-end">
                        <flux:button type="button" class="w-full" wire:click="deselect">{{ __('Back') }}</flux:button>
                    </div>

                    <flux:modal.trigger name="confirm-unlink" class="flex items-center gap-1">
                        <div class="flex items-center justify-end">
                            <flux:button variant="danger" type="button" class="w-full" x-on:click.prevent="$dispatch('open-modal', 'confirm-unlink')" :disabled="!isset(auth()->user()->password)">{{ __('Unlink account') }}</flux:button>
                        </div>
                    </flux:modal.trigger>
                </div>

                <flux:modal name="confirm-unlink" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
                    <form method="POST" wire:submit="unlinkProvider" class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ __('Are you sure you want to unlink this account?') }}</flux:heading>

                            <flux:subheading>
                                {{ __('Once this account is unlinked, you no longer able to log in using this account. Please enter your password to confirm you would like to permanently unlink this account.') }}
                            </flux:subheading>
                        </div>

                        <flux:input wire:model="password" :label="__('Password')" type="password" />

                        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                            <flux:modal.close>
                                <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                            </flux:modal.close>

                            <flux:button variant="danger" type="submit">{{ __('Unlink account') }}</flux:button>
                        </div>
                    </form>
                </flux:modal>

                @if (!isset(auth()->user()->password))
                    <flux:text class="text-xs -mt-2">
                        <span>Please</span> 
                        <flux:link :href="route('user-password.edit')" wire:navigate>{{ __('set a password') }}</flux:link> 
                        <span>to unlink account</span>
                    </flux:text>
                @endif
            </div>
        @endif

    </x-settings.layout>
</section>