<?php

namespace App\Service;

use App\Entity\User;
use Firebase\JWT\JWT;

class JWTManager
{
    private string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function createJWT(User $user): string
    {
        $payload = [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'exp' => (new \DateTime('+1 hour'))->getTimestamp(),
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}