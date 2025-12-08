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

            if ($providerAccount) {
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

                if ($userAccount) {
                    $provider = [
                        'name'             => 'google',
                        'id'               => $googleUser->getId(),
                        'email'            => $googleUser->getEmail(),
                        'avatar'           => $googleUser->getAvatar(),
                        'token'            => $googleUser->token,
                        'refresh_token'    => $googleUser->refreshToken,
                    ];

                    return to_route('auth.google.confirm')->with(['provider' => $provider, 'user' => $userAccount]);
                }
                else {
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
}
