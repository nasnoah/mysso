@props([
    'providerName'  => App\Enums\ProviderName::GOOGLE,
    'title'         => 'Continue with',    
])

<form method="GET" action="{{ route('auth.provider.redirect', $providerName->value) }}">
    <flux:button variant="primary" type="submit" class="w-full" data-test="{{ $providerName->value }}-sso-button">
        <div class="flex items-center gap-2">
            {{-- <img src="{{ asset('images/'.$providerName->value.'-logo.blade.php') }}" alt="" class="w-5 h-5"> --}}
            <x-dynamic-component :component="'mysso.icons.'.$providerName->value.'-logo'" class="w-5 h-5"/>
            <span>{{ __($title.' '.$providerName->label()) }}</span>
        </div>
    </flux:button>
</form>