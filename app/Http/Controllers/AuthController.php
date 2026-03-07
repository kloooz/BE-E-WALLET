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
     * Get the authenticated User.
     */
    public function getProfile(): JsonResponse
    {
        $user = auth()->user();
        return $this->success('User profile retrieved successfully', ['user' => $user]);
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
        $data   = $request->validated();
        $result = $this->authService->login($data);

        if (!$result['success']) {
            return $this->error($result['message'], null, 401);
        }

        return $this->success('Login successful', ['token' => $result['token']->plainTextToken]);
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

    /**
     * Update user pin.
     */
    public function updatePin(Request $request): JsonResponse
    {
        $request->validate([
            'pin' => 'required|string|digits:6',
        ]);

        $user = auth()->user();
        $user->pin = \Illuminate\Support\Facades\Hash::make($request->pin);
        $user->save();

        return $this->success('PIN has been successfully updated');
    }
    /**
     * Update user profile information.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'username' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
        ]);

        $user->update($request->only(['username', 'email', 'phone']));

        return $this->success('Profile updated successfully', ['user' => $user->fresh()]);
    }
}
?>
