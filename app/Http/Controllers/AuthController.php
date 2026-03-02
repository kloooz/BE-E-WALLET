<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController
{
    use ApiResponse;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $token = $this->authService->register($data);
        return $this->success('Registration successful', ['token' => $token->plainTextToken], 201);
    }

    /**
     * Login an existing user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $token = $this->authService->login($data);
        if (!$token) {
            return $this->error('Invalid credentials', null, 401);
        }
        return $this->success('Login successful', ['token' => $token->plainTextToken]);
    }
}
?>
