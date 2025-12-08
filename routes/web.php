<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Socialite\Socialite;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    // dd($googleUser);

    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name'              => $googleUser->getName(),
        ]
    );

    $user->markEmailAsVerified();

    $user->identityProviders()->updateOrCreate(
        ['provider_name' => 'google'],
        [
            'provider_id'               => $googleUser->getId(),
            'provider_email'            => $googleUser->getEmail(),
            'provider_avatar'           => $googleUser->getAvatar(),
            'provider_token'            => $googleUser->token,
            'provider_refresh_token'    => $googleUser->refreshToken,
        ]
    );

    Auth::login($user);

    return redirect('/dashboard');
});
