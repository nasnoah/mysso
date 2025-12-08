<form method="GET" action="{{ route('auth.google.redirect') }}">
    <flux:button variant="primary" type="submit" class="w-full" data-test="google-sso-button">
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/google-logo.svg') }}" alt="" class="w-5 h-5">
            <span>{{ __('Continue with Google') }}</span>
        </div>
    </flux:button>
</form>