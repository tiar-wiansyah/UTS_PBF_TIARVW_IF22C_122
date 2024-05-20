<?php

namespace App\Guards;

use App\Services\JWTService;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class JWTGuard implements Guard
{
    use GuardHelpers;

    public function __construct(UserProvider $userProvider, private Request $request, private JWTService $jwtService)
    {
        $this->setProvider($userProvider);
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        $token = $this->request->bearerToken() ?? '';

        if ($this->jwtService->check($token)) {
            $userId = $this->jwtService->decode($token)->user->id ?? '';
            $user = $this->provider->retrieveByCredentials(['id' => $userId]);

            if (!$user) {
                return null;
            }

            $this->setUser($user);
            return $this->user;
        }

        return $this->user;
    }

    public function validate(array $credentials = [])
    {
        return $this->provider->validateCredentials($this->user, $credentials);
    }

    public function attempt(array $credentials = [], bool $remember = false): bool
    {
        $this->user = $this->provider->retrieveByCredentials($credentials);

        if (!Hash::check($credentials['password'], $this->user->password)) {
            return false;
        }

        return true;
    }
}
