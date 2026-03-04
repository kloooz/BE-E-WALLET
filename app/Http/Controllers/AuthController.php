<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
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

    /**
     * Handle a forgot password request.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->authService->forgotPassword($data);

        return $this->success('Password reset token generated', $result);
    }

    /**
     * Handle a reset password request.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $success = $this->authService->resetPassword($data);

        if (!$success) {
            return $this->error('Invalid token or email', null, 400);
        }

        return $this->success('Password has been successfully reset');
    }
}
?>
