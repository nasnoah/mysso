<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ProviderName;
use Illuminate\Http\Request;
use Laravel\Socialite\Socialite;
use App\Http\Controllers\Controller;
use Exception;

class FacebookController extends Controller
{
    public function redirect() {
        return Socialite::driver(ProviderName::FACEBOOK->value)->redirect();
    }

    public function callback() {
        try {
            $facebookUser = Socialite::driver(ProviderName::FACEBOOK->value)->user();
    
            dd($facebookUser);
        }
        catch (Exception $e) {
            return to_route('login')
                ->with('sso-failed', 'We could not validate the response from your identity provider. Please try again or use another log in or sign up options.');
        }
    }

    public function confirm() {
        
    }
}
