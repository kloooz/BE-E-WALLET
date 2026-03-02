<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    /**
     * Register a new user and create an associated wallet.
     *
     * @param array $data Validated data from RegisterRequest
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public function register(array $data): \Laravel\Sanctum\NewAccessToken
    {
        // Create user
        $user = User::create([
            'username' => $data['username'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        // Create wallet with zero balance
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        // Generate Sanctum token
        return $user->createToken('auth_token');
    }

    /**
     * Login an existing user and issue a Sanctum token.
     *
     * @param array $data Validated data from LoginRequest
     * @return \Laravel\Sanctum\PersonalAccessToken|null
     */
    public function login(array $data)
    {
        $credentials = ['email' => $data['email'], 'password' => $data['password']];
        if (!Auth::attempt($credentials)) {
            return null;
        }
        $user = Auth::user();
        return $user->createToken('auth_token');
    }
}
?>
