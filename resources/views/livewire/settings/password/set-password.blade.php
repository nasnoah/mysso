<x-settings.layout :heading="__('Set password')" 
    :subheading="$this->isSettingPassword
        ? __('Ensure your account is using a long, random password to stay secure')
        : __('To enable password-based log in and full access for your account, you need to set a password')">

    @if (!$this->isSettingPassword)
        <flux:button variant="primary" type="button" class="w-fit" wire:click="toggleSettingPassword">{{ __('Set a password') }}</flux:button>
    @else
        <form method="POST" wire:submit="setPassword" class="mt-6 space-y-6">
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="password"
            />
            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirm Password')"
                type="password"
                required
                autocomplete="password"
            />

            <div class="flex items-center gap-2">
                <div class="flex items-center justify-end">
                    <flux:button type="button" class="w-full" wire:click="toggleSettingPassword">{{ __('Cancel') }}</flux:button>
                </div>

                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="password-set">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    @endif
</x-settings.layout>