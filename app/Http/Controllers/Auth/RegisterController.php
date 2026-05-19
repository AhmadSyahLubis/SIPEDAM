<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;

class RegisterController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function __invoke(RegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => $data,
        ], 201);
    }
}
