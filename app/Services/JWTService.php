<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class JWTService
{
    private string $key;
    private string $algo;

    public function __construct()
    {
        $this->key = env('JWT_KEY');
        $this->algo = env('JWT_ALGO');
    }

    public function regenerateToken(User $user): string
    {
        return $this->encode([
            'user' => [
                'id' => $user->id
            ]
        ]);
    }

    public function encode(array $payload): string
    {
        return JWT::encode($payload, $this->key, $this->algo);
    }

    public function decode(string $jwt): stdClass
    {
        return JWT::decode($jwt, new Key($this->key, $this->algo));
    }

    public function check(string $jwt): bool
    {
        try {
            $this->decode($jwt);
            return true;
        } catch (Exception) {
            return false;
        }
    }
}
