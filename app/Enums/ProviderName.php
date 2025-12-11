<?php

namespace App\Enums;

use App\Http\Controllers\Auth;

enum ProviderName: string {

    case GOOGLE     = 'google';
    case GITHUB     = 'github';
    case FACEBOOK   = 'facebook';

    public function label(): string {
        return match($this) {
            self::GOOGLE    => 'Google',
            self::GITHUB    => 'GitHub',
            self::FACEBOOK  => 'Facebook',
        };
    }

}