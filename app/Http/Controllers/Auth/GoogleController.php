<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\IdentityProvider;
use Laravel\Socialite\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirect() {
        return Socialite::driver('google')->redirect();
    }

    public function callback() {
        try {
            $googleUser = Socialite::driver('google')->user();
        
            $providerAccount = IdentityProvider::where([
                'provider_name'     => 'google',
                'provider_id'       => $googleUser->getId(),
            ])->first();

            if ($providerAccount && !Auth::check()) { # Login existing account
                $providerAccount->update([
                    'provider_email'            => $googleUser->getEmail(),
                    'provider_avatar'           => $googleUser->getAvatar(),
                    'provider_token'            => $googleUser->token,
                    'provider_refresh_token'    => $googleUser->refreshToken,
                ]);

                $providerAccount->loadMissing(['user']);
                $user = $providerAccount->user;
            }
            else {
                $userAccount = User::where('email', $googleUser->getEmail())->first();

                if ($userAccount) { # No linked provider account, but user account exists
                    $provider = [
                        'name'             => 'google',
                        'id'               => $googleUser->getId(),
                        'email'            => $googleUser->getEmail(),
                        'avatar'           => $googleUser->getAvatar(),
                        'token'            => $googleUser->token,
                        'refresh_token'    => $googleUser->refreshToken,
                    ];

                    if (Auth::check()) { # Link provider to logged in user
                        $currentUser = Auth::user();

                        if ($currentUser->email == $googleUser->getEmail()) {

                            if ($currentUser->identityProviders()->where([
                                'provider_name' => 'google',
                                'provider_id'   => $googleUser->getId(),
                            ])->exists()) {
                                return to_route('third-party-account.edit')
                                    ->with('sso-failed', 'Your Google account is already linked to your account.');
                            }

                            $this->link(user: $currentUser, provider: $provider);

                            return to_route('third-party-account.edit')
                                ->with('sso-succeded', 'Successfully linked your Google account.');
                        }
                        else {
                            return to_route('third-party-account.edit')
                                ->with('sso-failed', 'Failed linked your Google account. The Google\'s email ('.$googleUser->getEmail().') not same as your account\'s email ('.$currentUser->email.'). Please use a matching Google account or update your account\'s email.');
                        }
                    }
                    else { # Link provider account to existing user after confirmation
                        return to_route('auth.google.confirm')
                            ->with(['provider' => $provider, 'user' => $userAccount]);
                    }
                }
                else { # Register new account with provider account
                    $user = User::create([
                        'email' => $googleUser->getEmail(),
                        'name'  => $googleUser->getName(),
                    ]);
                
                    $user->markEmailAsVerified();
                
                    $providerAccount = $user->identityProviders()->create([
                        'provider_name'             => 'google',
                        'provider_id'               => $googleUser->getId(),
                        'provider_email'            => $googleUser->getEmail(),
                        'provider_avatar'           => $googleUser->getAvatar(),
                        'provider_token'            => $googleUser->token,
                        'provider_refresh_token'    => $googleUser->refreshToken,
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

    public function confirm() {
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

    public function unlink() {
        $user = Auth::user();
        $user->identityProviders()->google()->delete();
    }
}
