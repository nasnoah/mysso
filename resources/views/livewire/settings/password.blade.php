<section class="w-full">
    @include('partials.settings-heading')

    @if ($hasPassword)
        <livewire:settings.password.update-password />
    @else
        <livewire:settings.password.set-password />
    @endif
</section>
