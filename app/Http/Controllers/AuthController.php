<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JWTService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private JWTService $jwtService)
    {
        //
    }

    public function register(Request $request): JsonResponse
    {
        $dataRequest = [];

        try {
            $dataRequest = $request->validate([
                'email' => ['required', 'email', 'unique:users,email', 'string'],
                'password' => ['required', 'string'],
                'name' => ['required', 'string'],
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'errors' => $validationException->errors()
            ], 400);
        }

        $dataRequest['password'] = Hash::make($dataRequest['password']);
        $user = User::create($dataRequest);

        return response()->json([
            'data' => $user
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $dataRequest = [];

        try {
            $dataRequest = $request->validate([
                'email' => ['required', 'email', 'string', 'exists:users,email'],
                'password' => ['required', 'string'],
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'errors' => $validationException->errors()
            ], 400);
        }

        $user = User::where('email', $dataRequest['email'])->first();

        if (!Hash::check($dataRequest['password'], $user->password)) {
            return response()->json([
                'errors' => [
                    'password' => ['The provided credentials are incorrect.']
                ]
            ], 400);
        }

        $jwtToken = $this->jwtService->regenerateToken($user);

        return response()->json([
            'data' => [
                'jwt_token' => $jwtToken
            ]
        ]);
    }
}
