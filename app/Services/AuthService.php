<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'nik' => $data['nik'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role' => 'user',
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, $user);
    }

    public function login(array $credentials): ?array
    {
        if (!$token = auth('api')->attempt($credentials)) {
            return null;
        }

        $user = auth('api')->user();

        return $this->respondWithToken($token, $user);
    }

    public function logout(): void
    {
        auth('api')->logout();
    }

    public function refresh(): array
    {
        $token = auth('api')->refresh();
        $user = auth('api')->user();

        return $this->respondWithToken($token, $user);
    }

    public function me(): User
    {
        return auth('api')->user();
    }

    protected function respondWithToken(string $token, User $user): array
    {
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nik' => $user->nik,
                'phone' => $user->phone,
                'address' => $user->address,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }
}
