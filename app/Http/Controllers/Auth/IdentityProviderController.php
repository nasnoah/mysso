<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ProviderName;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\IdentityProvider;
use Laravel\Socialite\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class IdentityProviderController extends Controller
{
    public function redirect($provider) {
        $provider = ProviderName::from($provider);

        $driver = Socialite::driver($provider->value);

        if ($provider == ProviderName::GITHUB) {
            $driver->scopes(['user:email']);
        }

        return $driver->redirect();
    }

    public function callback($provider) {
        try {
            $provider = ProviderName::from($provider);
            $providerUser = Socialite::driver($provider->value)->user();

            // dd($providerUser);
        
            $providerAccount = IdentityProvider::where([
                'provider_name'     => $provider->value,
                'provider_id'       => $providerUser->getId(),
            ])->first();

            if ($providerAccount && !Auth::check()) { # Login existing account
                $providerAccount->update([
                    'provider_email'            => $providerUser->getEmail(),
                    'provider_avatar'           => $providerUser->getAvatar(),
                    'provider_token'            => $providerUser->token,
                    'provider_refresh_token'    => $providerUser->refreshToken,
                ]);

                $providerAccount->loadMissing(['user']);
                $user = $providerAccount->user;
            }
            else {
                $userAccount = User::where('email', $providerUser->getEmail())->first();

                if ($userAccount) { # No linked provider account, but user account exists
                    $providerData = [
                        'name'             => $provider->value,
                        'id'               => $providerUser->getId(),
                        'email'            => $providerUser->getEmail(),
                        'avatar'           => $providerUser->getAvatar(),
                        'token'            => $providerUser->token,
                        'refresh_token'    => $providerUser->refreshToken,
                    ];

                    if (Auth::check()) { # Link provider to logged in user
                        $currentUser = Auth::user();

                        if ($currentUser->email == $providerUser->getEmail()) {

                            if ($currentUser->identityProviders()->where([
                                'provider_name' => $provider->value,
                                'provider_id'   => $providerUser->getId(),
                            ])->exists()) {
                                return to_route('third-party-account.edit')
                                    ->with('sso-failed', 'Your '.$provider->label().' account is already linked to your account.');
                            }

                            $this->link(user: $currentUser, provider: $providerData);

                            return to_route('third-party-account.edit')
                                ->with('sso-succeded', 'Successfully linked your '.$provider->label().' account.');
                        }
                        else {
                            return to_route('third-party-account.edit')
                                ->with('sso-failed', 'Failed linked your '.$provider->label().' account. The '.$provider->label().'\'s email ('.$providerUser->getEmail().') not same as your account\'s email ('.$currentUser->email.'). Please use a matching '.$provider->label().' account or update your account\'s email.');
                        }
                    }
                    else { # Link provider account to existing user after confirmation
                        return to_route('auth.provider.confirm', $provider->value)
                            ->with(['provider' => $providerData, 'user' => $userAccount]);
                    }
                }
                else { # Register new account with provider account
                    $user = User::create([
                        'email' => $providerUser->getEmail(),
                        'name'  => $providerUser->getName(),
                    ]);
                
                    $user->markEmailAsVerified();
                
                    $providerAccount = $user->identityProviders()->create([
                        'provider_name'             => $provider->value,
                        'provider_id'               => $providerUser->getId(),
                        'provider_email'            => $providerUser->getEmail(),
                        'provider_avatar'           => $providerUser->getAvatar(),
                        'provider_token'            => $providerUser->token,
                        'provider_refresh_token'    => $providerUser->refreshToken,
                    ]);
                }
            }

            Auth::login($user);
        
            return to_route('dashboard');
        }
        catch (Exception $e) {
            return to_route('login')
                ->with('sso-failed', 'We could not validate the response from your identity provider. Please try again or use another log in or sign up options.');
        }
    }

    public function confirm($provider) {
        $provider = session('provider');
        $user = session('user');

        session()->keep(['provider', 'user']);

        if (!$provider || !$user) {
            return to_route('login');
        }

        return view('confirm-link-account', compact(['provider', 'user']));
    }

    public function link($user, $provider) {
        $user->identityProviders()->create([
            'provider_name'             => $provider['name'],
            'provider_id'               => $provider['id'],
            'provider_email'            => $provider['email'],
            'provider_avatar'           => $provider['avatar'],
            'provider_token'            => $provider['token'],
            'provider_refresh_token'    => $provider['refresh_token'],
        ]);

        if (!Auth::check()) {
            Auth::login($user);
        }
    }

    public function unlink($provider) {
        $user = Auth::user();
        $user->identityProviders()->{$provider->value}()->delete();
    }
}
