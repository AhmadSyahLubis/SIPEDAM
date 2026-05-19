<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function logout()
    {
        $this->authService->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    public function refresh()
    {
        $data = $this->authService->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Token berhasil diperbarui',
            'data' => $data,
        ]);
    }

    public function me()
    {
        $user = $this->authService->me();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nik' => $user->nik,
                'phone' => $user->phone,
                'address' => $user->address,
                'avatar' => $user->avatar,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
        ]);
    }
}
