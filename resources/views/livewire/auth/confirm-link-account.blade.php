<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Add new log in method')" 
        :description="__(
            $provider['email'].' is an existing email for '.$user->name.'\'s account.
            Do you want to enable '.App\Enums\ProviderName::from($provider['name'])->label().' log in for '.$user->name.'\'s account?'
        )" />

    <flux:button variant="primary" type="button" class="w-full" data-test="link-account-button"
        wire:click="linkAccount">
        <span>{{ __('Link account') }}</span>
    </flux:button>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
        <span>{{ __('Or, return to') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('log in') }}</flux:link>
    </div>
</div>