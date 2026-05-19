<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;

class LoginController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function __invoke(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => $data,
        ]);
    }
}
