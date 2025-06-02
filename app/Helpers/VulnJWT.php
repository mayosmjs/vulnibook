<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class VulnJWT
{
    // Hardcoded weak secret
    private static $secret = 'secret';

    // Insecure token generator
    public static function generateToken($user)
    {
        $payload = [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role ?? 'user',
            //No 'exp' or 'iat'
        ];

        return JWT::encode($payload, self::$secret, 'HS256');
    }

    //Insecure decoder (no expiry checks, etc.)
    public static function decodeToken($token)
    {
        return (array) JWT::decode($token, new Key(self::$secret, 'HS256'));
    }
}
