<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ProviderName;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Laravel\Socialite\Socialite;

class GithubController extends Controller
{
    public function redirect() {
        return Socialite::driver(ProviderName::GITHUB->value)->scopes(['user:email'])->redirect();
    }

    public function callback() {
        try {
            $githubUser = Socialite::driver(ProviderName::GITHUB->value)->user();

            dd($githubUser->getId(), $githubUser->getName(), $githubUser->getEmail(), $githubUser->getAvatar(), $githubUser->token, $githubUser->refreshToken);
        }   
        catch (Exception $e) {
            return to_route('login')
                ->with('sso-failed', 'We could not validate the response from your identity provider. Please try again or use another log in or sign up options.');
        }
    }

    public function confirm() {
        
    }
}
