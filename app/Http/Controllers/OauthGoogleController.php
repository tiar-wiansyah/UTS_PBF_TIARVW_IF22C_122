<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JWTService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Nette\Utils\Random;

class OauthGoogleController extends Controller
{
    public function __construct(private JWTService $jwtService)
    {
        //
    }

    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): JsonResponse
    {
        $userGoogle = Socialite::driver('google')->user();

        if (!$userGoogle->id) {
            return response()->json([
                'errors' => [
                    'message' => [
                        'Terdapat kesalahan saat Otentikasi.'
                    ]
                ]
            ], 400);
        }

        $user = User::where('google_id', $userGoogle->id)->first();

        if (!$user) {
            $user = new User();
            $user->google_refresh_token = $userGoogle->refreshToken;
            $user->name = $userGoogle->name;
            $user->google_id = $userGoogle->id;
            $user->google_token = $userGoogle->token;
            $user->email = $userGoogle->email;
            $user->password = Hash::make(Random::generate(10));
            $user->save();
        }

        $payload = [
            'user' => [
                'id' => $user->id
            ]
        ];
        $token = $this->jwtService->encode($payload);

        return response()->json([
            'data' => [
                'jwt_token' => $token
            ]
        ]);
    }
}
